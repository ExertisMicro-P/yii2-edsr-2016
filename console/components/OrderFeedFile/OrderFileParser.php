<?php


/**
 * Description of CustomerFileParser
 *
 * @author helenk
 */
namespace console\components\OrderFeedFile;
use Yii;
use console\components\IDataLineProcessor;
use common\models\AccountSopLookup;
use common\models\DigitalProduct;
use common\models\Account;
use common\components\DigitalPurchaser;
use common\models\Stockroom;
use common\models\StockItem;
use common\models\Orderdetails;
use console\components\FileFeedErrorCodes;
use console\components\FileFeedParser;
use console\components\EDSRException;
use common\models\Customer;

class OrderFileParser extends FileFeedParser implements IDataLineProcessor
{

    private $headermap = array('header' => 0, 'account' => 4, 'SOP' => 5, 'PO' => 7, 'type' => 20);
    private $linemap = array('header' => 0, 'orderlineid' => 1, 'SOP' => 2, 'partcode' => 6, 'type' => 3, 'qty' => 8, 'price' => 9, 'status' => 17);
    private $customermap = array('name' => 12, 'contact' => 11, 'street' => 13, 'town' => 14, 'city' => 16, 'postcode' => 17);
    const STATUS_STOCK_ALLOCATED = 1; // SA occurs AFTER CA according to Ed Pursey (Email 20150122 11:57
    const STATUS_CA = 2; // 2 means Credit Approved (CA'd),
    const STATUS_SHIPPED = 4; //  4 (SHIPPED)
    const headerpattern = "/^H/";
    const linepattern = "/^L/";
    const edsrLinepattern = "/^E/";                         // EDSR sourced line

    const type = "/Non-Inventory/";
    public $account = null; //type model Account
    public $stockroom = null;
    public $stockItem = null;
    public $soplookup = null;
    public $auditlog = array();
    public $new_stockitem_created = true;
    public $line = null;

    private $fromEdsr = false ;     // true when saving an input from EDSR


    /**
     * processLine
     * Reads a line of an Order File Feed.
     * If the line begins with "H" it calls a parser to read the header information processHeaders.
     * If the line begins with "L" it calls a parser to read the order line information  processOrderLine
     *
     * @param type $line
     *
     * @return boolean
     */
    public function processLine($line)
    {

        $this->fromEdsr = false ;
        if (preg_match(self::headerpattern, $line[$this->headermap['header']])) {
            $this->processHeaders($line);
        }
        if (preg_match(self::linepattern, $line[$this->linemap['header']])) {
            $this->line = $line;
            $this->processOrderLine();

        } elseif (preg_match(self::edsrLinepattern, $line[$this->linemap['header']])) {
            $this->line = $line;
            $this->fromEdsr = true ;
            $this->processOrderLine();
        }
    }

    /**
     * IS PO FROM EDSR
     * ===============
     * Just used to avoid having the same check in multiple places
     *
     * @param $po
     *
     * @return bool
     */
    private function _isPoFromEdsr($po) {
        return preg_match('/EDR:/', $po) > 0 ;
    }

    /**
     * IS PO FROM MINDY
     * ================
     * Just used to avoid having the same check in multiple places
     *
     * @param $po
     *
     * @return bool
     */
    private function isPoFromMindy($po) {
        if (!empty($po)) {
            return strpos($po, 'WEB:mindy') !== FALSE ;
        }
        return false;
    }
    
    /**
     * IS SOP on the blacklist
     * ================
     * Sometimes we need to raise an SOP
     * but prevent EDSR from buying the key.
     * If you know the SOP beforehand you can list it in $sopstoignore
     *
     * @param $sop
     *
     * @return bool TRUE id SOP should be igored
     */
    private function isSOPtoBeIgnored($sop) {
        $sopstoignore = array(21279854);
        
        if (!empty($sop) && !empty($sopstoignore)) {
            return array_search($sop, $sopstoignore ) !== FALSE ;
        }
        return false;
    }
    
    

    /**
     * processHeaders
     * Reads the order header.
     * Checks if there is a record in the Account_SOP_Lookup table for the SOP. If not it creates one.
     *
     * @param type $line
     */
    public function processHeaders($line)
    {
        \yii::info(__METHOD__.': '.implode(', ',$line));
        
        $po = $line[$this->headermap['PO']];

        if ($this->isPoFromMindy($po)) {
            \yii::info(__METHOD__.': PO is from Mindy - Ignoring PO '.$po);
            return ;   // do nothing
        }

        //update header to table for easy lookup
        $account_number = $line[$this->headermap['account']];
        //we save the sop against the account to make lookup of orderlines easier.
        $sop             = $line[$this->headermap['SOP']];
        
        // RCH 20160819
        if ($this->isSOPtoBeIgnored($sop)) {
            \yii::info(__METHOD__.': SOP is on hardcoded ignore list - Ignoring SOP '.$sop);
            return ;   // do nothing
        }
        
        $this->soplookup = AccountSopLookup::find()
            ->where(['sop' => $sop])
            ->one();

        // -------------------------------------------------------------------
        // If no match was found by SOP and we're not in the process of creating
        // an input from EDSR, check against the PO, but only if that's
        // in the EDSR internal format.
        // -------------------------------------------------------------------
        if (!isset($this->soplookup->id)) {
            if ($this->_isPoFromEdsr($po)) {
                Yii::info(__METHOD__.': PO is from EDSR');

                $this->soplookup = AccountSopLookup::find()
                                            ->where(['po' => $po])
                                            ->one();
            }
        }

        if (!isset($this->soplookup->id)) {
            \yii::info(__METHOD__.': Creating AccountSopLookup for '.$account_number.' / '.$po);

            $this->soplookup          = new AccountSopLookup();
            $this->soplookup->account = $account_number;
            $this->soplookup->po      = $po;
        }

        // RCH 20151016
        if (!empty($this->soplookup)) {
            Yii::info(__METHOD__.': AccountSopLookup='.print_r($this->soplookup->attributes,true));
        }


        // -------------------------------------------------------------------

        // -------------------------------------------------------------------
        $this->soplookup->sop      = $sop;
        $this->soplookup->contact  = $line[$this->customermap['contact']];
        $this->soplookup->name     = $line[$this->customermap['name']];
        $this->soplookup->town     = $line[$this->customermap['town']];
        $this->soplookup->city     = $line[$this->customermap['city']];
        $this->soplookup->postcode = $line[$this->customermap['postcode']];

        if (!$this->soplookup->save()) {
            $str       = implode(',', $line);
            $this->msg = $str . 'Account does not exist' . ',' . print_r($this->soplookup->getErrors(), true) . ',' . FileFeedErrorCodes::toString(FileFeedErrorCodes::HEADER_SOP_LOOKUP_FAILED);
            throw new EDSRException(FileFeedErrorCodes::SOPLOOKUP_SAVE_FAILED,
                print_r($this->soplookup->getErrors(), true));
        } else {
            \yii::info(__METHOD__.': Saved '.print_r($this->soplookup->attributes,true));
        }

    } // processHeaders

    /**
     * processOrderLine
     * Process a orderline line.
     * Checks if line is an inventory line.
     * If it is it checks in the digital_product table if the partcode is digital.
     * If it is it checks if there is a existing account for the order - if not one is created+user+stockroom
     * Builds a stdClass product and request the ztorm purchase.
     * If the item is purchased OK from ztorm it creates a stock item record and order details record for the order
     * line.
     *
     * @param type $line
     */
    public function processOrderLine()
    {
        $debugvars = get_object_vars($this);
        foreach($debugvars as $obj) {
          
            if ($obj instanceof \yii\db\ActiveRecord) {
                Yii::info(__METHOD__.': '.print_r($obj->attributes,true));
            } else {
                Yii::info(__METHOD__.': '.print_r($obj,true));
            }
        } // foreach

        $this->auditlog = array(); // catches all error exceptions raised for records and writes to audit trial
        
        // RCH 20160218
        // moved these checks outside because we don't need to check if we can purchase the
        // key becuase we either will be ignoring the order completely (Mindy)
        // or tying it up with a previous EDSR purchase (EDSR)
        
        $orderlineid = $this->line[$this->linemap['orderlineid']];
        $sop         = $this->line[$this->linemap['SOP']];
        $partcode    = $this->line[$this->linemap['partcode']];
        $qty         = (int)$this->line[$this->linemap['qty']];
        $po = $this->soplookup->po;

        if ($this->isPoFromMindy($po)) {
            // do nothing

        } elseif($this->isSOPtoBeIgnored($sop)) { // RCH 20160819
            // do nothing
        
        } elseif (!$this->fromEdsr && $this->_isPoFromEdsr($po)) {
            // this is a real file (i.e. not injected from the EDSR Shop
            // and the PO came from EDSR orginally, via Oracle and ECO_NEW_ORDER_STATUS file
            // EDSR should already know about it, so let's find out
            $this->findEdsrSourceOrderAndStockItemsForPartcode($sop, $po, $qty, $partcode, $orderlineid);
            
        } elseif ($this->canOrderStateMeansWePurchaseKeys($this->line)) {  //Product has been shipped so can be processed
            // This is a digital product, purchased via Oracle SFE, or EDI
            // or it could have been injected from EDSR itself (to purchase the key immediately)
            
            if ($this->isProductDigital($partcode)) {
                // -----------------------------------------------------------
                // Verify that the SOP and orderLineId are valid, then check
                // the account exists. Throws exceptions on error
                // -----------------------------------------------------------
                $this->validateInputDetails($sop, $orderlineid);

                //PURCHASE PRODUCT
                $qty              = (int)$this->line[$this->linemap['qty']];
                $i                = 0;
                $this->stockItems = array();
                for ($i = 0; $i < $qty; $i++) {
                    $this->auditlog = array();
                    //We need to run off and purchase this item from Ztorm
                    //first check if we have one ordered by mistake (during testing) with the same partcode.
                    //if we do we can use this and just update who it is for.
                    //This should not happen once in full production
                    $spare_stockitem = false;
                    $this->stockItem = $this->findSpareKeyifAvailable($partcode);
                    /*                     * ***************************************************
                     *  findSpareKeyifAvailable currently refurns FALSE to prevent code in the if statement firing.
                     *  Logic of this code will currently not give correct outcome so disabled.
                     */
                    if ($this->stockItem) { //we found a spare one to use
                        $this->auditlog[] = 'Stock Item can be re-used: ' . \yii\helpers\VarDumper::dumpAsString($this->stockItem->attributes, 9, true);

                        $spare_stockitem                  = true;  //we will not need to purchase this item
                        $this->stockItem->stockroom_id    = $this->stockroom->id; //change stock room
                        $this->stockItem->send_email      = 1;
                        $this->stockItem->spare           = StockItem::KEY_REQUIRES_ATTENTION;  //key will be issued Manually
                        $this->stockItem->timestamp_added = date('Y-m-d H:i:s'); // RCH 20150402
                        //orderdetails they are no longer required we are going to use this stockitem for a
                        // a new order. Even if this fails we still do not require the old details
                        $this->removeOrderDetails();

                        $this->auditlog[] = 'Stock Item re-used: ' . \yii\helpers\VarDumper::dumpAsString($this->stockItem->attributes, 9, true);
                    } else {  //create newstock item
                        $this->auditlog[] = 'New Stock Item for '.$partcode.' / '.$this->soplookup->po;
                        $this->stockItem                    = new StockItem();
                        $this->stockItem->eztorm_product_id = 0;
                        $this->stockItem->eztorm_order_id   = 0;
                        $this->stockItem->productcode       = $partcode;
                        $this->stockItem->stockroom_id      = $this->stockroom->id; //change stock room
                        $this->stockItem->setPrice($this->line[$this->linemap['price']]); //external to db data
                        $this->stockItem->setPo($this->soplookup->po);
                        $this->stockItem->timestamp_added = date('Y-m-d H:i:s'); // RCH 20150331
                    }

                    //Now save the stock item in order to get the stock record id used in creation of StoreOrderID
                    $this->saveStockItem(); //will raise an exception if it cannot save and exit

                    if (!$spare_stockitem) { //purchase a key
                        //We still have to complete the account setup
                        $digitalpurchaser = new DigitalPurchaser();

                       // $digitalpurchaser->getAndSetEztormMemberID($this->stockItem); //will register with eztorm for account
                        $this->new_stockitem_created = $this->purchaseDigitalKey();
                    }//not spare

                    //NEW STOCKITEM PURCHASED
                    //We only want to save item if not already owned by account ** this should never happen **
                    // But need to handle the case where it might
                    if ($this->new_stockitem_created === true || $spare_stockitem == true) {
                        $this->saveStockItem(); //will raise an exception if it cannot save and exit
                        //We create an orderDetails record which will tie stockitem and order together
                        $orderdetails = new Orderdetails();
                        $orderdetails->saveWithAuditTrail('New order for '.$this->soplookup->po);
                        $orderdetails->sop         = $sop;
                        $orderdetails->po          = $this->soplookup->po;
                        $orderdetails->name        = $this->soplookup->name;
                        $orderdetails->contact     = $this->soplookup->contact;
                        $orderdetails->street      = $this->soplookup->street;
                        $orderdetails->town        = $this->soplookup->town;
                        $orderdetails->city        = $this->soplookup->city;
                        $orderdetails->postcode    = $this->soplookup->postcode;
                        $orderdetails->filename    = $this->filename;
                        $orderdetails->orderlineid = $orderlineid;
                        //Finally to tie it all together. Add stockitem id to customer orderdetails rec and save
                        $orderdetails->stock_item_id = $this->stockItem->id;

                        if (!$orderdetails->saveWithAuditTrail('Created order '.$this->soplookup->po)) {
                            //we don't throw exception - this not will not prevent stock items functionality.
                            $str          = implode(',', $this->line);
                            $this->msg    = print_r($this->stockItem->attributes) . 'for account  ' . $this->account->id . ' :' . $str . ',' . print_r($orderdetails->getErrors(), true) . ',' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ORDERDETAILS_SAVE_FAILED);
                            $this->msgs[] = $this->msg;
                            Yii::error($this->msg);
                        }

                    }//new stockitem
                }//end buy qty.
            } //is digital
                
        } else {
            Yii::info('Can Order State prevented ordering');
        }
    }

//processOrderLine

    /**
     * VALIDATE INPUT DETAILS
     * ======================
     * We lookup the SOP first before checking if we have seen this order before because we need the PO
     * in the app log if order already exists.
     *
     * @param $sop
     * @param $orderlineid
     *
     * @throws EDSRException
     * @throws \Exception
     */
    private function validateInputDetails($sop, $orderlineid)
    {
        \Yii::info(__METHOD__."($sop, $orderlineid)");
        $this->soplookup = $this->hasSOPHeaderDetails($sop);    // throws exception if SOP not found
        $this->isNotDuplicateOrderLineID($orderlineid);         // throws exception if orderline is duplicate

        //Check if account already exists
        \Yii::info(__METHOD__.': Searching for '.$this->soplookup->account);
        $this->account = Account::find()
            ->where(['customer_exertis_account_number' => $this->soplookup->account])
            ->one();

        if (!isset($this->account->id)) { //no account yet created
            //we need a new account
            //First lets check this customer is one of our customers
            $this->isExertisCustomer($this->soplookup->account);  //throws exception if not exertis customer
            $this->createCustomerAccountAndStockRoom($this->soplookup->account); //Will raise exception if something goes wrong
        } else {  //we have an account+user+stockroom already so find the stockroom
            $this->stockroom = Stockroom::find()
                ->where(['account_id' => $this->account->id])
                ->one();
        }
    }

    /**
     * FIND EDSR SOURCED ORDER FOR PARTCODE
     * ====================================
     * When an order is originally created on EDSR, it uses dummy SOP and
     * orderlineids, which need to be correctd to match the genuine ones
     * from Oracle.
     *
     * We locate the relevant entries using the PO value, which Oracle doesn't
     * change, plus the corresponding partcode (productcode from the stock item
     * table). Each identified orderitem then has its sop and orderline set to
     * the provided value, which is the same for all records.
     *
     * Nothing in the stock item record needs to be altered.
     *
     * @param $po
     * @param $partcode
     */
    private function findEdsrSourceOrderAndStockItemsForPartcode($sop, $po, $qty, $partcode, $orderlineid) {
        \Yii::info(__METHOD__."($sop, $po, $qty, $partcode, $orderlineid)");
        
        $this->auditlog = array();
        $orderdetails = Orderdetails::find()
                                ->select('orderdetails.id')
                                ->where(['po' => $po, 'productcode' => $partcode])
                                ->joinWith('stockitem')
                                ->all() ;

        if ($qty <> count($orderdetails)) {
            \Yii::info(__METHOD__.": Quantity Mismatch!");
        }

        $this->auditlog[] = 'Updating ' . $qty . ' stockitems from purchase order ' . $po ;

        // -------------------------------------------------------------------
        // gather the ids of each effected record
        // -------------------------------------------------------------------
        $ids = [] ;
        foreach ($orderdetails as $orderdetail) {
            $ids[] = $orderdetail->id ;
        }

        // -------------------------------------------------------------------
        // Update all the tempoaray values in one go
        // -------------------------------------------------------------------
        Orderdetails::updateAll(
            [
                'sop'         => $sop,
                'orderlineid' => $orderlineid,
                'name'        => $this->soplookup->name,
                'contact'     => $this->soplookup->contact,
                'street'      => $this->soplookup->street,
                'town'        => $this->soplookup->town,
                'city'        => $this->soplookup->city,
                'postcode'    => $this->soplookup->postcode,
                'filename'    => $this->filename
            ],

            ['id' => $ids]
        ) ;
    }


    protected function saveStockItem()
    {
        // RCH 20160229
        if (isset(Yii::$app->user) && Yii::$app->user->id==10) {
            // For demo and testing, hide the stockitem if it's been purchased by Russell
            // but allow Russell to see it.
            // Also pretend that email has been sent
			// YOU SHOULD ALSO SET THE ACCOUNT TO *NOT* Auto-EDI in MDFS
			// You can set it to spare = 1 later
            $this->stockItem->spare = StockItem::KEY_HIDDEN_FROM_ALL_EXCEPT_RUSSELL;
            $this->stockItem->send_email = StockItem::EMAIL_SENT;
            Yii::info(__METHOD__.': Hiding StockItem '.print_r($this->stockItem->attributes,true));
        }
        
        if (!$this->stockItem->saveWithAuditTrail($this->auditlog)) {
            $str       = implode(',', $this->line);
            $this->msg = print_r($this->stockItem->attributes) . ' ' . $str . ',' . print_r($this->stockItem->getErrors(), true) . ',' . FileFeedErrorCodes::toString(FileFeedErrorCodes::STOCKITEM_SAVE_FAILED);
            //Exit we dont have a stockitem id to tie orderdetails together
            Yii::error(__METHOD__.':'.$this->msg);
            $this->msgs[] = $this->msg;
            throw new EDSRException(FileFeedErrorCodes::STOCKITEM_SAVE_FAILED, $this->msg);
        }
    }

    /**
     *
     * @param type $partcode
     *
     * @return type bool if false or ActiveRecord item if true
     */
    protected function findSpareKeyifAvailable($partcode)
    {
        $item = StockItem::find()
            ->where(['productcode' => $partcode, 'spare' => StockItem::KEY_SPARE, 'status' => StockItem::STATUS_PURCHASED])
            ->one();

        return $item;

    }

    /**
     * @TODO get criteria for this
     * @return boolean
     */
    protected function canOrderStateMeansWePurchaseKeys() {
        //if($this->line[$this->linemap['status']] == self::STATUS_STOCK_ALLOCATED){
        // RCH 20160210
        // Oracle now spits out an extra Order Status when the oprder has been CA'd
        // This is our cue to buy the keys
        if($this->line[$this->linemap['status']] == self::STATUS_CA){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates an Account and a Stockroom.
     * RCH 20160113
     * Made public and added option to not send email (when setting up due to SSO)
     * @param string $account_number
     * @param boolean $sendEmail
     * @throws type
     */
    public function createCustomerAccountAndStockRoom($account_number, $sendEmail=true)
    {
        try {
            $transaction   = \Yii::$app->db->beginTransaction();  //start transaction
            $this->account = new Account();
            $this->account->createNewAccount($account_number);

            //create the first stockroom
            $this->stockroom = new Stockroom();
            $this->stockroom->createNewStockRoom($this->account->id);
            $transaction->commit();  //Account is now set up.
            if($sendEmail) {
                //Send Create setupemail to sales
                if (!$this->account->sendAccountCreatedEmail()) {
                    Yii::error('Could not send Account Setup Email for ' . print_r($this->account->attributes, true));
                }
            }
        } catch (EDSRException $e) {
            \Yii::$app->db->rollback();
            $str       = implode(',', $this->line);
            $this->msg = $str . ',' . $e->getMessage();
            Yii::error($e->getMessage());
            throw($e);
        }
    }

    /**
     * Return
     */
    private function isProductDigital($partcode)
    {
        $digital = DigitalProduct::find()
            ->where(['partcode' => $partcode])
            ->exists();

        return $digital;
    }

    private function hasSOPHeaderDetails($sop)
    {
        \Yii::info(__METHOD__."($sop)");

        $item = AccountSopLookup::find($sop)
            ->where(['sop' => $sop])
            ->one();
        if (!isset($item->id)) { //we found no SOP lookup - something went wrong during reading Header
            //We will have to exit. Grab info write to file
            $str       = implode(',', $this->line);
            $this->msg = $str . ',' . FileFeedErrorCodes::toString(FileFeedErrorCodes::HEADER_SOP_LOOKUP_FAILED);
            Yii::info(__METHOD__ . ' SOP does not exist in AccountSOPLookup ' . $sop);
            throw new EDSRException(FileFeedErrorCodes::HEADER_SOP_LOOKUP_FAILED, 'SOP lookup failed');
        }

        return $item;
    }

    private function isNotDuplicateOrderLineID($orderlineid)
    {
        $orderlineexist = Orderdetails::find()
            ->where(['orderlineid' => $orderlineid])
            ->exists();
        if ($orderlineexist) {
            throw new EDSRException(FileFeedErrorCodes::KEY_ALREADY_PURCHASED, 'key already purchased for orderline ' . $orderlineid . ' po ' . $this->soplookup->po . ' file ' . $this->filename);
        }

        return $orderlineexist;
    }

    private function isExertisCustomer($account_number)
    {
        $result = Customer::find()
            ->where(['exertis_account_number' => $account_number])
            ->one();
        if ($result == null) {
            Yii::error(__METHOD__ . 'CUSTOMER NOT ONE KNOWN' . $this->soplookup->account);
            throw new EDSRException(FileFeedErrorCodes::CUSTOMER_NOT_ONE_KNOWN, 'CUSTOMER NOT ONE KNOWN ' . $this->soplookup->account);
        }
    }

    private function purchaseDigitalKey()
    {

//        if (Yii::$app->params['mockKeys'] === true) {
//            return $this->mockedPurchase();
//        }
        $digitalpurchaser = new DigitalPurchaser();
        try {
            $order_id                         = $digitalpurchaser->purchaseProduct($this->stockItem);
            $this->stockItem->eztorm_order_id = $order_id;
            $this->stockItem->status          = StockItem::STATUS_PURCHASED;
            $this->stockItem->send_email      = 1;
            //$stock_item->eztorm_product_id is set in purchase products added in digital purchase ;

            Yii::info('stock item purchased for account ' . $this->account->id . 'stock_item' . print_r($this->stockItem->attributes, true));

        } catch (EDSRException $e) {
            //digital item was not found in the catalogue and therefore not purchased
            //digitalItem could not be purchased from Ztorm
            if ($e->getCode() == fileFeedErrorCodes::ZSTORM_USER_ALREADY_OWNS_PRODUCT) {
                Yii::info('Stock item already owned by  ' . $this->account->id . 'stock_item' . print_r($this->stockItem->attributes, true));

                return false;

            } else {  //we still create a stock item even though purchase did not occurr
                $this->stockItem->status = StockItem::STATUS_NOT_PURCHASED;
                $this->stockItem->reason = FileFeedErrorCodes::toString($e->getCode());
                //need to add dummy product and order id
                $this->stockItem->eztorm_product_id = 0;
                $this->stockItem->eztorm_order_id   = 0;
                $str                                = implode(',', $this->line);
                $this->msg                          = $str . ',' . $e->getMessage();
                $this->msgs[]                       = $this->msg;
                $this->auditlog[]                   = $this->msg;
                //We wont roll back but log that the stockitem was not purchased.
                Yii::info($this->msg);
                Yii::info('ALERT stock item NOT purchased for account ' . $this->account->id . 'stock_item' . print_r($this->stockItem->attributes, true));
            }
        }

        return true;
    }


    private function removeOrderDetails()
    {
        $orderdetails = $this->stockItem->orderdetails;
        if ($orderdetails) {
            $this->stockItem->saveWithAuditTrail('Removing OrderDetails: ' . \yii\helpers\VarDumper::dumpAsString($orderdetails->attributes, 9, true));
            $orderdetails->delete();

            /* $orderdetails->stock_item_id = 'removed';
             $orderdetails->po = 'removed';
             $orderdetails->name = 'removed';
             $orderdetails->contact = 'removed';
             $orderdetails->street = 'removed';
             $orderdetails->town = 'removed';
             $orderdetails->city = 'removed';
             $orderdetails->postcode = 'removed';
             $msg = 'The stockitem ' . $this->stockItem->id . 'has been rehomed to account ' . $this->account->id;
             $orderdetails->saveWithAuditTrail($msg);
             Yii::info($msg);*/
        }
    }

}

?>
