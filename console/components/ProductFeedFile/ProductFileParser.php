<?php


/**
 * Description of ProductFileParser
 * Handles parser activities to do with ProductFileParser
 * @author helenk
 */
namespace console\components\ProductFeedFile;
use Yii;
use console\components\IDataLineProcessor;
use console\components\FileFeedErrorCodes;
use console\components\EDSRException;
use console\components\FileFeedParser;
use common\models\DigitalProduct;

class ProductFileParser extends FileFeedParser implements IDataLineProcessor{

    const STOCKGROUP = '2053';
    private $fieldmap = array('code'=>0,'description'=>1,'type'=>19, 'stockgroup'=>35);
    //private $digitalpattern = "/SOFTWARE/";

    /**
     * processLine
     * Reads a line of an Product File Feed adds a digital_product record to the DB
     * @param type $line
     * @return boolean
     */
    public function processLine($line){
          $auditlog = array();
          if($line[$this->fieldmap['stockgroup']] == self::STOCKGROUP){
                $partcode = $line[$this->fieldmap['code']];
                $product = DigitalProduct::find()
                ->where(['partcode' => $partcode])
                        ->one();
            if(isset($product->id)){ //existing product this will be an update.
               $product->description = $line[$this->fieldmap['description']];
               $auditlog[] = 'Updated record';
            }
            else{ //new Product
                $product = new DigitalProduct();
                $product->partcode = $line[$this->fieldmap['code']];
                $product->description = $line[$this->fieldmap['description']];
                $auditlog[] = 'New record';
            }

            // RCH 20141214
            // Lookup a suitable product image
            $url = 'http://apps2.exertismicro-p.co.uk/product_api/lookupmediaurl.php?src=mp&nopg=1&p='.$product->partcode;
            $response = \console\components\ZtormAPI\CurlHandler::sendXml(null,$url,true);
            if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {
                $product->image_url = $response;
            }




            // RCH 20141214

            //save/update product
            if(!$product->saveWithAuditTrail($auditlog)){
                $str = implode(',', $line);
                $msg = $str . ',' . print_r($product->getErrors(),true);
                Yii::info($msg,__METHOD__);
                $this->msgs[] = $msg;
                throw new EDSRException(FileFeedErrorCodes::PRODUCT_FEED_SAVE_FAILED,
                       print_r($product->getErrors(), true));
            }
        }//is digital
    }//end processLine

}

?>
