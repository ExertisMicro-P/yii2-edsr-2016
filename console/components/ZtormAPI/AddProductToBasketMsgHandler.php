<?php


/**
 * Description of AddProductToBasketMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;

class AddProductToBasketMsgHandler extends APICurlRequest {

    /* Avoid hitting eZtorm API and incurring a charge for getting real products
     * Set it to True when you want to avoid charging and also implement stub code
     */
    //protected $_stubAPI = TRUE;

    protected $_basketID;
    protected $_productID;
    protected $_price;

    function __construct($vo){
        parent::__construct($vo);
        $this->_basketID = $vo->basketID;
        $this->_productID = $vo->productID;
        $this->_price = $vo->getProductPrice();
    }

    protected function _getMsgRequestContent() {
        $msg ='<Method>AddProductToBasket</Method>
            <Params>
                <BasketID>' .$this->_basketID . '</BasketID>
                <ProductID>'. $this->_productID  .'</ProductID>
                <Price>' .$this->_price . ' GBP</Price>
                </Params>
                ';
        return $msg;
    }



    public function getOrderID(){
        if (!$this->_stubAPI)
            return (string)$this->responseValue;
        else
           return rand (9000000, 9999999); // Stub return a random OrderID
    }
}

?>
