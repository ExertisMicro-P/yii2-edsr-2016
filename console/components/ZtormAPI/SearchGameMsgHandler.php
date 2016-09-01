<?php

/**
 * Description of SearchGameMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;

class SearchGameMsgHandler extends APICurlRequest {
 
    
    
    protected function _getMsgRequestContent() {
        $msg ='<Method>SearchGame</Method>
            <Params>
                <Keyword>halo</Keyword>
                </Params>
                ';
        return $msg;
    }
    
    
    /**
     * 
     * @return array
     */
    public function getProductID(){
        
        return (string)$this->responseValue->StoreProduct->ID;
    }
}

?>
