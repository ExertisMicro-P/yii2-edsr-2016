<?php

/*
 * Use api
 */

/**
 * APICurlRequest
 * Abstract call.
 * Handles the creation, sending of an xml. The transaltion of the xml response into a 'DOM object'
 * Methods :
 * generateRequest()
 * _addXmlOpen()
 * _addIdentifier()
 * _getMsgRequestContent() ::Abstract Method
 * _addContent()
 * _convertxmltoobj($xml)
 * _checkForResponseError
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use \Yii;
use console\components\ItemPurchaser;
use common\components\XmlUtils;

abstract class APICurlRequest
{

    /* Avoid hitting eZtorm API and incurring a charge for getting real products
     * Keep this FALSE here, but override it in any subclasses that want to avoid charging and also implement their own stub code
     */
    protected $_stubAPI = false;

    public $xmlRequest = ''; //used to build the xml request
    public $url = '';
    public $responseValue = '';
    public $xmlResponse = '';
    public $modelData = null; //this is the value object containing data to be used
    public $store = null;
    public $nodes = null;

    function __construct(ItemPurchaser $basecurl)
    {
        $this->modelData = $basecurl;
        $this->store = $basecurl->getStore();
        $this->url       = $this->store->geturl();

        $this->_stubAPI = array_key_exists('mockKeys', Yii::$app->params) && Yii::$app->params['mockKeys'];
    }

    /**
     * generateRequest
     * Builds the xml request using specifi headers and footers.
     */
    protected function generateRequest()
    {
        $this->_addXmlOpen();
        $this->_addIdentifier($this->store);
        $this->_addContent();
        $this->_addXmlClose();
    }

    /**
     * _addXmlOpen()
     * Builds xml string for the xml header
     */
    protected function _addXmlOpen()
    {
        $this->xmlRequest = ''; //always start with empty xml
        $this->xmlRequest .= '<?xml version="1.0" encoding="utf-8"?>';
        $this->xmlRequest .= '<Request>'; //'<tns:Basket xmlns:tns="http://www.micro-p.com/mdfs/fulfillment" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://local.htdocs.com/fulfillment/interface%20definitions/IcomToMicro-PBasket/IcomMPBasket.xsd">';
    }

    /**
     * _addIdentifier()
     * Builds xml string for the xml Identify tags
     */
    protected function _addIdentifier($store)
    {
        $this->xmlRequest .= '<Identify>
            <StoreID>' . $store->getstoreID() . '</StoreID>
               <Password>' . $store->getstorekey() . '</Password>
            </Identify>';

    }


    /**
     * Abstract method. Each class has a specific xml message to build
     */
    abstract protected function _getMsgRequestContent();

    /**
     * _addContent()
     * Calls the abstract method
     */
    protected function _addContent()
    {

        $this->xmlRequest .= $this->_getMsgRequestContent();
    }


    /**
     * _getResponseData
     * Convert the raw xml response into an obj to make the data more accessible.
     *
     * @param type $xml
     *
     * @example
     *
     * @return stdclassobject.
     */
    //abstract protected function _getResponseData ($xml);

    protected function _convertxmltoobj($xml)
    {
        //check schema ? will require a path to do this
        \Yii::info(__METHOD__ . ': $xml=' . $xml);
        $this->xmlResponse = XmlUtils::readXMLrequest($xml);

        return $this->xmlResponse;

    }

    protected function _addXmlClose()
    {
        $this->xmlRequest .= '</Request>';
    }

    /**
     * Attempts to contact server with CURL Request
     * Raises exception if request fails
     *
     * @return responseValue
     */
    public function sendRequest()
    {
        \Yii::beginProfile('sendRequest');
        try {
            $this->generateRequest();
            if (!$this->_stubAPI) {
                $xmlcontent        = CurlHandler::sendXml($this->xmlRequest, $this->url, 1);
                $this->xmlResponse = $this->_convertxmltoobj($xmlcontent);
                $this->_checkForResponseError(); //will throw exception an error exists
                $this->responseValue = $this->xmlResponse->Value;

                \Yii::endProfile('sendRequest');
                return $this->responseValue;

            } else {

                // Stub the API
                if (method_exists($this, 'returnStub')) {
                    $this->responseValue = $this->returnStub();

                } else {
                    $this->responseValue = '<stub>The eZtorm API has been Stubbed</stub>';
                }

                \Yii::endProfile('sendRequest');
                return $this->responseValue;
            }
        } catch (CurlException $e) { //raised if curl fails
            //@TODO
			\Yii::endProfile('sendRequest');
            throw($e);
        } catch (ZtormAPIException $e) { //raised if Ztorm did not the request for some reason.
            //@TODO
			\Yii::endProfile('sendRequest');
            throw($e);
        }

    }

    /**
     * checkForResponseError()
     * Check if API response caused an error.
     *
     * @return boolean
     * @throws ZtormAPIException
     */
    protected function _checkForResponseError()
    {
        Yii::info(__METHOD__.': '.$this->xmlResponse->asXML());
        $errorcode = (int)$this->xmlResponse->ErrorCode;
        if ($errorcode > 0) {
            //we have an error. log it.
            $errormessage = '-' . __CLASS__ . ' ' . __METHOD__ . print_r($this->xmlResponse, true) . ' request' . print_r($this->xmlRequest,true) . ' Error ' . $errorcode;
            Yii::error(print_r($this->xmlRequest, true) . 'for member' . $this->modelData->memberID . ' ' . $errormessage);
            throw new ZtormAPIException($errorcode, $errormessage);
        } else {
            return true;
        }
    }

}

?>
