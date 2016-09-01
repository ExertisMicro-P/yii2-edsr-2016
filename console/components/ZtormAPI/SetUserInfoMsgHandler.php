<?php

/**
 * Description of SetUserInfoMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;

class SetUserInfoMsgHandler extends APICurlRequest {
 
    
    protected function _getMsgRequestContent() {
        $msg ='<Method>SetUserInfo</Method>
            <Params>
                <MemberID>'. $this->modelData->getMemberID() . '</MemberID>
                <Name>'. $this->modelData->getCompanyName() . '</Name>
                <Password></Password>
                <Email>webteam@exertis.co.uk</Email>
                <StoreMemberID>'.$this->modelData->getStoreMemberID().'</StoreMemberID>
                </Params>
                ';
        return $msg;
    }
    
    
    
    public function getMemberID(){
        return (string)$this->responseValue;
    }
}

?>
