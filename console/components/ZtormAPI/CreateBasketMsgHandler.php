<?php

/**
 * Description of CreateBasketMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;
class CreateBasketMsgHandler extends APICurlRequest {

    /* Avoid hitting eZtorm API and incurring a charge for getting real products
     * Set it to True when you want to avoid charging and also implement stub code
     */
    //protected $_stubAPI = FALSE;

    private $_memberID;

    public function __construct($vo) {
        parent::__construct($vo);
        $this->_memberID = $vo->memberID;
    }

    protected function _getMsgRequestContent() {
        $msg ='<Method>CreateBasket</Method>
            <Params>
                <MemberID>'. $this->_memberID  .'</MemberID>
                </Params>
                ';
        return $msg;
    }



    public function getBasketID(){
        if (!$this->_stubAPI)
            return (string)$this->responseValue;
        else
            return rand (9000000, 9999999); // Stub return a random BasketID
    }
}

?>
