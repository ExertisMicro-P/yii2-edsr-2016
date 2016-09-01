<?php

/**
 * Description of GetUserProductsMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;

class GetUserProductsMsgHandler extends APICurlRequest {
 
    private $_memberID;
    
    function __construct($vo) {
        parent::__construct($vo);
        $this->_memberID = $vo->memberID;
    }
    
    protected function _getMsgRequestContent() {
        $msg ='<Method>GetUserProducts</Method>
            <Params>
                <MemberID>'. $this->_memberID  .'</MemberID>
                </Params>
                ';
        return $msg;
    }
    
    
    
    public function getUserProducts(){
        $products = $this->responseValue->children('Value');
        return $products;
    }
}

?>
