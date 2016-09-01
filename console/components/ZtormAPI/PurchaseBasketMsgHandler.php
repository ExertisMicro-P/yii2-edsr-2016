<?php

/**
 * Description of PurchaseBasketMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;

class PurchaseBasketMsgHandler extends APICurlRequest {

    /* Avoid hitting eZtorm API and incurring a charge for getting real products
     * Set it to True when you want to avoid charging and also implement stub code
     */
    //protected $_stubAPI = FALSE;

    private $_basketID;
    private $_storeOrderID;
    private $_ip;

    function __construct($vo) {
        parent::__construct($vo);
        $this->_basketID = $vo->basketID;
        $this->_ip = $vo->ip;
        $this->_storeOrderID = $vo->getstoreOrderID();
    }

    protected function _getMsgRequestContent() {
        $msg ='<Method>PurchaseBasket</Method>
            <Params>
                <BasketID>' .$this->_basketID . '</BasketID>
                <StoreOrderID>'. $this->_storeOrderID .'</StoreOrderID>
                <IP>' .$this->_ip.'</IP>
                </Params>
                ';
        return $msg;
        //<StoreOrderID>'. $this->_memberID  .'</StoreOrderID>
    }



    public function getOrderID(){
        if (!$this->_stubAPI)
            return (string)$this->responseValue;
        else
            return rand (9000000, 9999999); // Stub return a random OrderID
    }
    
    
}

?>
