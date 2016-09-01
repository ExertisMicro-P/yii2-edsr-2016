<?php



/**
 * Description of ExportProductsMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;
use common\models\PersistantDataLookup;

class ExportProductsMsgHandler extends APICurlRequest {
 
    public $products = null;
    private $pd=null;
    public $page;
    protected function _getMsgRequestContent() {        
        $msg ='<Method>ExportProducts</Method>
            <Params>
               
                <PageNumber>'.$this->page.'</PageNumber>
                <PageSize>200</PageSize>
                <ShowInactive>false</ShowInactive>
                <ShowDiscontinued>false</ShowDiscontinued>
                <UpdatedSinceTimestamp>'. $this->getTimestamp() .'</UpdatedSinceTimestamp>
                </Params>
                ';
        $this->saveTimestamp();
        return $msg;
    }
    // <Category>software</Category>
    private function getTimestamp(){
        $this->pd = PersistantDataLookup::getZtormTimeStamp();
        $timestamp =$this->pd->value;
        return $timestamp;
    }
    
    private function saveTimestamp(){
        // $this->pd->saveZtormCatalogueLookupdate();
    }
    
    public function getStoreProducts($nodes){
         
        return $nodes->StoreProduct;
    }
    
    public function getPriceForProduct($id){
        //search for product of id an return
    }
}

?>
