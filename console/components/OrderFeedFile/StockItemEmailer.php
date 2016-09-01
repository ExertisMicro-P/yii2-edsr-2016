<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Gets all the stock items which require sending
 *
 * @author helenk
 */
namespace console\components\OrderFeedFile;
use common\models\StockItem;
use console\components\EDSRException;
/**
 * StockItemEmailer
 * Emails the notification to user when a stockitem becomes available
 */
class StockItemEmailer {

    public $account;

    /**
     * getStockItemsforSending()
     * Returns a list of stockitems that require a availibility notification sending to the customer
     * Return active record set of $stockitems
     */
    public function getStockItemsforSending(){
        return StockItem::getStockitemstoemail();
    }

    /**
     * notifyCustomerofNewStockItems
     * Sorts the stockitem into customer/stockroom and PO.
     * Runs through the stockroom and finds users email address - if it can't find one it drops though to the next stockroom.
     * Send the a notification email to each user of new stockitems - seperated into PO
     * Finally updates the stockitems or each customer to show the user has been notified of availibilty.
     * @param type $stockitems
     */
    public function notifyCustomerofNewStockItems($stockitems, $emailSalesToo=true) {

        \Yii::info (__METHOD__.' $stockitems='.print_r($stockitmes,true));
        
        $stockrooms = array();

        foreach ($stockitems as $item) {
            //seperate in stockroom
            $index = (string) $item->stockroom_id;
            $stockrooms[$index][] = $item; //stockitem (an arrayof item organised by stockrooms
        }
        foreach ($stockrooms as $stockroom) {
            $this->account = $stockroom[0]->stockroom->account; //get the account
            $mainUser = $this->account->findMainUser();
            $data = $this->getCustomersofNewStockItems($stockroom); //gets data for a stockroom aka account

            if ($mainUser->email !== NULL && strpos($mainUser->email,'@dummy.com')===FALSE) { //if they have email address we can email them
                //Now email the new stockitems created.
                //Send Create setupemail to sales
                try{
                    if (!$this->account->sendStockItemCreatedEmail($data)) {
                        Yii::warning('Could not send Item Setup Email for ' . print_r($this->account->attributes, true));
                    } else { //went through
                        //all went well finally update stockitems sent
                        foreach ($data['pos'] as $po) {
                            foreach ($po as $dataitem) {
                                //finally set stockitems send_email to 0
                                $stockitem = $dataitem['item'];
                                $stockitem->send_email = StockItem::EMAIL_SENT;
                                $stockitem->saveWithAuditTrail(__METHOD__.': Stock Item Notification email sent to Customer: '.$mainUser->email);
                            }
                        }
                    }//message sent successfully
                }
                catch(EDSRException $e){
                    //This may happen if key cannot be retrieved.
                }
            } else { // if $mainUser->email

                // only do this if we haven't got any email address for this account
                // Send addtional email to Sales
                // Temporary solution until we are fully Self-Serve
                if ($emailSalesToo) {
                    try{
                        if (!$this->account->sendStockItemCreatedEmailToSales($data)) {
                            Yii::warning('Could not send Item Setup Email to Sales for ' . print_r($this->account->attributes, true));
                        } else { //went through
                            //all went well finally update stockitems sent
                            foreach ($data['pos'] as $po) {
                                foreach ($po as $dataitem) {
                                    //finally set stockitems send_email to 0
                                    $stockitem = $dataitem['item'];
                                    $stockitem->send_email = StockItem::EMAIL_SENT;
                                    $stockitem->saveWithAuditTrail(__METHOD__.': Stock Item Notification email sent to Sales Team: '. \yii\helpers\VarDumper::dumpAsString(\Yii::$app->params['account.StockItemCreatedEmailToSalesRecipients']));
                                }
                            }
                        } //email sent
                    }
                    catch(EDSRException $e){
                        \Yii::error('Exception notifying Keys to Sales Team: '.$e->getMessage(), 'app');
                    }
                }
            }// if $mainUser->email
        }

    } //end notifyCustomerofNewStockItems

    /**
     * getCustomersofNewStockItems
     * Prepares the data for the email for each customer/stockroom
     * @param type $stockroom
     * @return type
     */
    public function getCustomersofNewStockItems($stockroom){
         $products = array();
         //seperate items into stockrooms aka account
         $pos = array();
            //seperate items into POs
            foreach($stockroom as $item){
                $orderdetails = $item->orderdetails;
                $po = (string)$orderdetails->po;
                $dataitems = array();

                $dataitems['orderdetails'] = $orderdetails;  //orderdetails
                $dataitems['product'] = $item->digitalProduct; //product;
                $dataitems['item']=$item; //stockitem
                $pos[$po][] = $dataitems;
            }
            //add everything to data  and compose email.
            $data = array();
            $data['account'] = $this->account;
            $data['pos'] = $pos;    //contains
            return $data;
         }


}

?>
