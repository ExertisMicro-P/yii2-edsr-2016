<?php
namespace frontend\controllers;

use common\models\DigitalProduct;
use common\models\EmailedItem;
use common\models\EmailedUser;
use common\models\Orderdetails;
use common\models\SalesRepOrder;
use common\models\SessionOrder;
use yii\data\ActiveDataProvider;

use Yii;
use yii\web\Controller;
use common\models\Stockroom;
use common\models\StockItem;
use common\models\StockItemSearch;

use yii\filters\AccessControl;
use common\components\DigitalPurchaser;

use exertis\savewithaudittrail\models\Audittrail;
use common\components\XmlUtils;
use common\components\CreditLevel;
use common\models\Accounts;
use console\components\OrderFeedFile\OrderFileParser;

class CheckoutController extends OrdersController
{
    private $xmlschemepath;
    private $xmlschemeresponsepath;

    // -----------------------------------------------------------------------
    // the following are directly copied form OrderFileParser. At some point
    // they should be moved to a shared location and imported
    // -----------------------------------------------------------------------
    private $headermap = array('header' => 0, 'account' => 4, 'SOP' => 5, 'PO' => 7, 'type' => 20);
    private $linemap = array('header' => 0, 'orderlineid' => 1, 'SOP' => 2, 'partcode' => 6, 'type' => 3, 'qty' => 8, 'price' => 9, 'status' => 17);
    private $customermap = array('name' => 12, 'contact' => 11, 'street' => 13, 'town' => 14, 'city' => 16, 'postcode' => 17);

    private $sessionOrderId;           // Unique id used to generate the Pouchase Order number
    private $SOP;                      // Unique SOP value, also used for part of orderitemid


    /**
     * BEHAVIOURS
     * ==========
     *
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'purchase', 'getlimit', 'getbasket', 'getitemqty'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * INDEX
     * =====
     * The main entry point
     */
    public function actionIndex()
    {
        return $this->showCheckout();
    }

    /**
     * PURCHASE
     * ========
     * This action handles the 'payment' process and receives the full list of
     * ordered products together with there quantities.
     *
     * Before any real processing takes place, it has to confirm the quantities
     * and the actual prices can be accommodated by the current credit balance. If
     * not, it returns an error to the caller
     *
     * @return string
     */
    public function actionPurchase()
    {
        $this->getUserDetails();
        $result = [];

        if (Yii::$app->request->post('quant', '') == '') {
            $result['status'] = false;
            $result['error']  = 'You have nothing selected for purchase';

        } elseif (($userPo = trim(strtoupper(Yii::$app->request->post('po', '')))) == '') {
            $result['status'] = false;
            $result['error']  = 'You must provide a purchase order';

        } elseif (strlen($userPo) > Orderdetails::MAXUSERPOLENGTH) {
            $result['status'] = false;
            $result['error']  = 'The PO can be at most 27 characters long';

        } elseif (preg_match('/^[a-z0-9\_:\/]{1,27}$/i', $userPo) <> 1) {
            $result['status'] = false;
            $result['error']  = 'The PO must only contain letters, numbers, underscores or colons';

        } else {
            $userSelections = Yii::$app->request->post('quant', 0) ;

            $records = $this->_gatherDetails($userSelections);
            if (!$this->checkOrderAgainstCreditBalance($records)) {
                $result['status'] = false;
                $result['error']  = 'Your credit balance is too low to pay for this order';

            } else {
                $result = $this->completeThePurchase($userPo, $records);
                if ($result['status']) {
                    $this->clearSessionOrder();
                }
            }
        }

        return json_encode($result);
    }

    /**
     * COMPLETE THE PURCHASE
     * =====================
     *
     * Sends order to MDFS (which handles the Oracle SOP creation side of things)
     * Also, uses createStockItems to actually get the keys from eZtorm (by pretend to be an ECO_NEW_ORDER_STATUS file (sort of)
     * 
     * @param $userPo
     * @param $records
     *
     * @return array
     */
    private function completeThePurchase($userPo, $records)
    {
        $this->getSessionOrderId();

        $fullPo = $userPo . 'EDR:' . sprintf('%06d', $this->sessionOrderId);

        $result = $this->sendToMDFS($userPo, $fullPo, $records);

        if ($result['status']) {
            $result = $this->createStockItems($fullPo, $records);
            if ($result['status'] && Yii::$app->session->get('internal_user')) {
                $this->recordSalesRepPurchase($fullPo) ;
            }
        }

        return $result;
    }

    /**
     * RECORD SALES REP PURCHASE
     * =========================
     * Creates a record to identify this order was really added by a sales rep.
     * @param $po
     *
     * @return bool
     */
    private function recordSalesRepPurchase($po) {
        $accountId = $this->user->account->id ;

        $srOrder = new SalesRepOrder() ;
        $srOrder->account_id = $accountId ;
        $srOrder->po = $po ;
        $srOrder->sales_rep_id = Yii::$app->session->get('internal_user') ;
        if (! $srOrder->save() ) {
            print_r($srOrder->errors) ;
        }
    }

    /**
     * CLEAR SESSION ORDER
     * ===================
     * Added a test for the account as well to support sales reps placing
     * orders, which they may do for multiple clients in one session
     */
    private function clearSessionOrder()
    {
        $accountId = $this->user->account->id;

        $sessionOrders = SessionOrder::deleteAll(
            ['session_id' => session_id(), 'account_id' => $accountId]
        );
        //->where(['account_id' => $accountId]) ;
    }

    /**
     * SEND TO MDFS
     * ============
     * Builds an XML string to send the order details to MDFS, returning the
     * status to the caller.
     *
     * @param $fullPo
     * @param $records
     *
     * @return array
     */
    private function sendToMDFS($userPo, $fullPo, $records)
    {
        $this->prepareXML();
        $xml = $this->buildOrderXML($fullPo, $records);
        \Yii::info(__METHOD__.'Created XML: '.$xml);
        
        // RCH 2015111
        // Some accounts can be set so that tehy do not generate an SOP
        // Originally this was planned for SDG, but I also needed to make a purchase
        // to fix an error, but I didn't want to generate the SOP
        if ($this->user->account->dont_raise_sop) {
            \Yii::info(__METHOD__.': SOP not raised - dont_raise_sop is TRUE');
            // skip raising an SOP
            return [
                'status' => true,
                'errors' => []
            ];
        }

        return $this->postXmlBasketToBagging($xml, $userPo);
    }

    /**
     * PREPARE XML
     * ===========
     * XML initialisation
     */
    public function prepareXML()
    {
        $basePath                    = Yii::getAlias('@common') . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'schema' . DIRECTORY_SEPARATOR;
        $this->xmlschemepath         = $basePath . 'IcomMPBasket.xsd';
        $this->xmlschemeresponsepath = $basePath . 'ShopMPBasketResponse.xsd';

    }
    
    public function actionGetbasket(){
        return SessionOrder::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id()])->sum('quantity');
    }
    
    public function actionGetlimit(){
        return \common\models\Account::find()->where(['id' => Yii::$app->user->identity->account_id])->one()->key_limit;
    }
    
    public function actionGetitemqty($item){
        return SessionOrder::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id(), 'partcode'=>$item])->one()->quantity;
    }


    /**
     * SHOW ORDER PAGE
     * ===============
     */
    public function showCheckout()
    {
        $this->getUserDetails();
        $orders = $this->getFlaggedToBuy();

        $this->layout = '@frontend/views/layouts/mainnw';
        $cLevel       = new CreditLevel($this->user);

        $bodyContent = $this->renderPartial('checkout', [
            'title'        => 'Orders',
            'dataProvider' => $this->getProvider(),
            'credit'       => $cLevel->readCurrentCredit()
        ]);

        return $this->render('/site/customerHome', [
            'bodyContent' => $bodyContent
        ]);

    }
    
    private function _getSixDigitUniqId() {
        $r=rand(0,99999);
        $rpad = str_pad($r, 5, '0', STR_PAD_LEFT);
        return '9'.$rpad;
    }

    /**
     * GET SESSION ORDER ID
     * ====================
     * This reads one of the order records saved in the session table for this
     * persons session, simply to give us an unique id for uses elsewhere.
     *
     * Added a test for the account as well to support sales reps placing
     * orders, which they may do for multiple clients in one session
     */
    private function getSessionOrderId()
    {
        $accountId = $this->user->account->id;

        $sorder = SessionOrder::find()
            ->select('id')
            ->where(['session_id' => session_id()])
            ->andWhere(['account_id' => $accountId])
            ->one();

        if ($sorder) {
            $this->sessionOrderId = $sorder->id;
            $this->SOP            = 'sid' . $sorder->id;

        } else {
            Yii::error(__METHOD__.': Issue getting ID. session_id='.session_id().'/ account_id = '.$accountId);
            // ---------------------------------------------------------------
            // Should never get here, but in case we do, create a hopefully
            // unique, 23 character,  value based on the logged in user's id.
            // ---------------------------------------------------------------
            // RCH 20160701
            //$this->sessionOrderId = uniqid($this->user->id, true);
            $this->sessionOrderId = $this->_getSixDigitUniqId();
            $this->SOP = 'sid' . $this->sessionOrderId;
        }

        return $this->SOP;

    }

    /**
     * GET PROVIDER
     * ============
     * Added a test for the account as well to support sales reps placing
     * orders, which they may do for multiple clients in one session
     *
     * @return ActiveDataProvider
     */
    private function getProvider()
    {
        $accountId = $this->user->account->id;

        $query = SessionOrder::find()
            ->where(['session_id' => session_id()])
            ->andWhere(['account_id' => $accountId])
            ->joinWith('product');

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * GATHER DETAILS
     * ==============
     * Reads the details of each ordered item, including the customer specific
     * price, the requested quantity and the total cost
     *
     * @param $selections
     *
     * @return array
     */
    private function _gatherDetails($selections)
    {
        $partcodes = [];
        $account   = $this->user->account->customer_exertis_account_number;

        foreach ($selections as $partcode => $quantity) {
            $partcodes[] = $partcode;
        }

        $items     = DigitalProduct::find()
            ->where(['partcode' => $partcodes])
            ->all();
        $itemArray = [];
        foreach ($items as $item) {

            $itemDetails             = $item->toArray();
            $itemDetails['cost']     = ($cost = $item->getItemPrice($account)); //->one()->item_price);
            $itemDetails['quantity'] = ($quantity = $selections[$item->partcode]);
            $itemDetails['total']    = $quantity * $cost;
            $itemArray[]             = $itemDetails;
        }
        return $itemArray;
    }

    /**
     * CHECK ORDER AGAINST CREDIT BALANCE
     * ==================================
     * Sums the total ordered price and compares it against the current credit
     * balance, returning true if it can be paid for.
     *
     * @param $records
     *
     * @return bool
     */
    private function checkOrderAgainstCreditBalance($records)
    {
        $totalCost = 0;
        foreach ($records as $record) {
            $totalCost = $record['total'];
        }

        $credit = $this->getCurrentCredit();

        //return ($credit['balance'] > $totalCost;
        return ($credit['limit'] + $credit['balance']) > $totalCost; // RCH 20150212
    }


    /**
     * GET CURRENT CREDIT
     * ==================
     * This returns an array of the credit limit and balance, adjusted to allow
     * for all known items not included in the main balance.
     *
     * This is duplicated elsewhere - need to move to shared base controller
     *
     * @return array
     */
    private function getCurrentCredit()
    {
        $account = $this->user->account;
        $credit  = $account->credit;

        $result = [
            'limit'   => $credit ? $credit->overall_credit_limit : 0,
            'balance' => $credit ? $credit->available_credit : 0,
        ];

        return $result;
    }


    /**
     * BUILD ORDER XML
     * ===============
     *
     * @param $po
     * @param $userSelections
     * @param $records
     *
     * @return string XML for an order to be sent to MDFS
     */
    private function buildOrderXML($po, $records)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                    <tns:Basket xmlns:tns="http://www.micro-p.com/mdfs/fulfillment"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xsi:schemaLocation="http://local.htdocs.com/fulfillment/interface%20definitions/IcomToMicro-PBasket/IcomMPBasket.xsd">';

        $xml .= $this->makeBasketHeader($po);

        $xml .= '<tns:basketContent>';
        foreach ($records as $record) {
            $xml .= $this->makeLine($record);
        }
        $xml .= '</tns:basketContent>';
        $xml .= '</tns:Basket>';

        XmlUtils::checkXmlAgainstSchema($xml, $this->xmlschemepath);

        return $xml;
    }

    /**
     * MAKE BASKET HEADER
     * ==================
     *
     * @return string
     */
    private function makeBasketHeader($po, $recordId = 9988)
    {
        $servername = $_SERVER['SERVER_NAME'];
        $userId     = $this->user->account->eztorm_user_id;
        $account    = $this->user->account;
        $email      = $this->user->email;

        $accountCode = $account->customer_exertis_account_number;

        $xml = <<< _EOF
    <tns:basketHeader>
        <tns:account>$accountCode</tns:account>
        <tns:orderId>$po</tns:orderId>
        <tns:userId>$userId</tns:userId>
        <tns:userEmailAddress>$email</tns:userEmailAddress>
        <tns:token></tns:token>
        <tns:notificationURL></tns:notificationURL>
        <tns:baggingType>4</tns:baggingType>
    </tns:basketHeader>
_EOF;

        return $xml;
    }

    /**
     * MAKE LINE
     * =========
     * Creates a basketline xml object
     *
     * @param $record
     *
     * @return string
     */
    private function makeLine($record)
    {

        $record['description'] = htmlentities($record['description']); // RCH 20151102

        // RCH 20160726
        // added passing of the price in the XML Bagging Order to MDFS
        // Initially this will be ignored as I haven't updated MDFS(BG)
        
        $xml = <<< _EOF

        <tns:BasketLine>
            <tns:partcode>{$record['partcode']}</tns:partcode>
            <tns:quantity>{$record['quantity']}</tns:quantity>
            <tns:imageURL>{$record['image_url']}</tns:imageURL>
            <tns:shortDescription>{$record['description']}</tns:shortDescription>
            <tns:productName>productName</tns:productName>
            <tns:price>{$record['cost']}</tns:price>
        </tns:BasketLine>
_EOF;

        return $xml;
    }

    /**
     * POST XML BASKET TO MDFS
     * =======================
     * Simulates an order to mdfs bagging in order to record it in oracle.
     *
     * @returns string XML response from MDFS Bagging
     */
    private function postXmlBasketToBagging($xml, $userPo)
    {
        $url      = Yii::$app->params['baggingURL'];
        $username = Yii::$app->params['baggingUser'];
        $password = Yii::$app->params['baggingPword'];


        //TO TEST use this don't forget to have a debug ntebean session running for posting pickup
        // $url = Yii::app()->createAbsoluteUrl('/bagging/widget?XDEBUG_SESSION_START=netbeans-xdebug');
        //--------------------

        // Initialize cURL
        $ch = curl_init();

        // Set URL on which you want to post the Form and/or data
        curl_setopt($ch, CURLOPT_URL, $url);
        // Data+Files to be posted
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['rawxml' => $xml]);
        // Pass TRUE or 1 if you want to wait for and catch the response against the request made
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // For Debug mode; shows up any error encountered during the operation
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // RCH 20150622 add base64_encode


        // Execute the request
        $response = curl_exec($ch);

        if (!$response) {
            throw new \Exception(__METHOD__.':Failed to cURL to / No Response from '.$url);
        }

        return $this->parseXmlResponse($response, $userPo);
    }

    /**
     * PARSE XML RESPONSE
     * ==================
     * Returns a success status or failed plus an array of error messages
     *
     * @param $xml
     *
     * @return array
     */
    private function parseXmlResponse($xml, $userPo)
    {
        $basketResponse = XmlUtils::readXmlrequest($xml, $this->xmlschemeresponsepath);
        $po             = $basketResponse->orderId;
        $errors         = [];

        if ($basketResponse->status == 'SUCCESS') {
            $status = true;

        } else {
            $status = false;

            foreach ($basketResponse->errors->children() as $xmlError) {
                if ((string)$xmlError->errorCode == 'DUPLICATEORDER') {
                    $errors[] = 'This order, ' . $userPo . ', has already been created';

                } elseif ($xmlError->errorCode <> 'UNABLETOSAVEORDER') {
                    $errors[] = (string)$xmlError->errorDetail;
                }
            }
        }

        return ['status' => $status, 'errors' => $errors];
    }

    /**
     * CREATE STOCK ITEMS
     * ==================
     * Uses the file loader to generate the orderdetails, stock_item and other
     * database entries related to this order, including purchasing the actual
     * keys from eztorm.
     *
     * @param $po
     * @param $records
     *
     * @return array
     */
    private function createStockItems($po, $records)
    {
        $result = [
            'status' => true,
            'errors' => []
        ];

        $customer = $this->user->account->customer;
        $sop      = $this->SOP;

        // -------------------------------------------------------------------
        // Create the csv as a temporary file, but use the //temp stream with
        // a memory threshold of 1 Mb so that, normally, it will be produced
        // totally in memory, avoiding the disc access overhead.
        // -------------------------------------------------------------------
        $fd = fopen('php://temp/maxmemory:1048576', 'w');
        if ($fd === false) {
            die('Failed to open temporary file');
        }

        $header = $this->createCSVHeaderLine($customer, $po, $sop);
        fputcsv($fd, $header);

        foreach ($records as $ind => $record) {
            $line = $this->createCSVLine($record, $sop, $ind);
            fputcsv($fd, $line);
        }

        // -------------------------------------------------------------------
        // Can now read back the contents and process them to create the
        // stockItem and Orderitem records.
        // -------------------------------------------------------------------
        $oParser = new OrderFileParser();

        try {
            rewind($fd);
            while (!feof($fd)) {
                $line = fgetcsv($fd);
                $oParser->processLine($line);
            }
        } catch (Exception $exc) {
            $result['status']   = false;
            $result['errors'][] = 'Unable to process the order ' . $exc->getMessage();
        }
        fclose($fd);

        return $result;
    }


    /**
     * CREATE CSV HEADER
     * =================
     * The layout is defined in OrderFileParser between the HeaderMap and
     * CustomerMap, with only the following columns being defined
     *     column       Item
     *      0           H
     *      4           account code
     *      5           sop
     *      7           po
     *      11          contact
     *      12          name
     *      13          street
     *      14          town
     *      16          city
     *      17          postcode
     *      20          type                    Not actually referenced
     *
     * private $customermap = array('name'=>12,'contact'=>11,'street'=>13,'town'=>14,'city'=>16,'postcode'=>17);
     *
     * "H","MP-SAL-Standard
     * Order",,"24-JUN-2014","MC005030","17083811","36181535","MC016009-36181535-0","XC-CUST-CUSTOMER","Customers Own
     * Carrie","MISS OLIVIA NEWBURN -","07554880287","MISS OLIVIA NEWBURN -","THE LOWRY THEATRE. STAGE DOOR","PIER
     * 8","MC005030 : 36181535","SALFORD QUAYS","M50 3AZ","GB",,
     *
     * @param $customer
     * @param $po
     * @param $sop
     *
     * @return array
     */
    private function createCSVHeaderLine($customer, $po, $sop) //, $record)
    {

        $line = array_pad([], max($this->headermap), '');

        $line[$this->headermap['header']]  = 'H';
        $line[$this->headermap['account']] = $customer->exertis_account_number;
        $line[$this->headermap['SOP']]     = $sop;
        $line[$this->headermap['PO']]      = $po;
        $line[$this->headermap['type']]    = '';

        $line[$this->customermap['name']]     = $customer->name;
        $line[$this->customermap['contact']]  = '';
        $line[$this->customermap['street']]   = $customer->invoicing_address_line1;
        $line[$this->customermap['town']]     = $customer->invoicing_address_line2;
        $line[$this->customermap['city']]     = $customer->invoicing_city;
        $line[$this->customermap['postcode']] = $customer->invoicing_postcode;

        \Yii::info(__METHOD__.': created header: '.implode(',',$line));

        return $line;
    }

    /**
     * CREATE CSV LINE
     * ===============
     *
     *
     * "L","26336227","17145865","MP-INV-Non-Inventory Line","2","CARRIAGE CHARGE","CARRIAGE CHARGE","Carriage
     * Charge","1","0","0","0","0","0",,,,"1","N",,
     *
     * @param $record
     * @param $sop
     * @param $ind
     *
     * @return array
     */
    private function createCSVLine($record, $sop, $ind)
    {

        $line = array_pad([], max($this->linemap), '');

        $line[$this->linemap['header']]      = 'E';
        $line[$this->linemap['orderlineid']] = $sop . '-' . str_pad($ind, 6, 0, STR_PAD_LEFT);
        $line[$this->linemap['SOP']]         = $sop;
        $line[$this->linemap['type']]        = '1';

        $line[$this->linemap['partcode']] = $record['partcode'];

        $line[$this->linemap['qty']]   = $record['quantity'];
        $line[$this->linemap['price']] = $record['cost'];

        $line[$this->linemap['status']] = OrderFileParser::STATUS_CA; // RCH 20160215

        \Yii::info(__METHOD__.': created line: '.implode(',',$line));

        return $line;
    }

}
