<?php

/**
 * Description of CustomerFileParser
 * Handles parser activities to do with CustomerFileParser
 * @author helenk
 */
namespace console\components\CustomerFeedFile;
use Yii;
use console\components\IDataLineProcessor;
use common\models\Customer;
use console\components\FileFeedErrorCodes;
use console\components\EDSRException;
use console\components\FileFeedParser;


class CustomerFileParser extends FileFeedParser implements IDataLineProcessor{
    
    private $linemap = array('account'=>1,'status'=>2,'company'=>3);
    private $invoice_address = array('invoice1'=>4,'invoice2'=>5,
        'invoice3'=>6,'invoice4'=>7,'invoice_postcode'=>8,'invoice_city'=>9,'invoice_country'=>10);
    
    /**
     * processLine
     * Reads a line of an Customer File Feed adds a custoer record to the DB
     * @param type $line
     * @return boolean
     */
    public function processLine($line){
          
            $auditlog = array();
            //check is customer exist.
            $account = $line[$this->linemap['account']];
            $customer = Customer::find()
                ->where(['exertis_account_number' => $account])
                        ->one();
            if(isset($customer->id)){ //existing customer this will be an update.
                $customer->status = $line[$this->linemap['status']];
                $customer->name = $line[$this->linemap['company']];
                $auditlog[] = 'Updated Record from file ' .$this->filename;
            }
            else{ //new customer
                $customer = new Customer();
                $customer->exertis_account_number = $line[$this->linemap['account']];
                $customer->status = $line[$this->linemap['status']];
                $customer->name = $line[$this->linemap['company']];
                $auditlog[] = 'New Record from file '. $this->filename;
            }
            //add the invoice address - this may change so applies even if account known already
            $customer->invoicing_address_line1 = $line[$this->invoice_address['invoice1']];
            $customer->invoicing_address_line2 = $line[$this->invoice_address['invoice2']];
            $customer->invoicing_address_line3 = $line[$this->invoice_address['invoice3']];
            $customer->invoicing_address_line4 = $line[$this->invoice_address['invoice4']];
            $customer->invoicing_postcode = $line[$this->invoice_address['invoice_postcode']];
            $customer->invoicing_city = $line[$this->invoice_address['invoice_city']];
            $customer->invoicing_country_code = $line[$this->invoice_address['invoice_country']];
            
            
            //save/update the customer
            if(!$customer->saveWithAuditTrail($auditlog)){
                    //make the $line back into a string
                    $str = implode(',', $line);
                    $msg = $str . ',' . print_r($customer->getErrors(),true);
                    Yii::info($msg,__METHOD__);                         
                    $this->msgs[] = $msg;
                   throw new EDSRException(FileFeedErrorCodes::CUSTOMER_FEED_SAVE_FAILED,
                           print_r($customer->getErrors(), true));
            }
            
    }
    
}

?>
