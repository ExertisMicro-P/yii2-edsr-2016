<?php
/**
 * Description of GetInstallKeysMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;

class GetInstallKeysMsgHandler extends APICurlRequest {

    /* Avoid hitting eZtorm API and incurring a charge for getting real products
     * Set it to True when you want to avoid charging and also implement stub code
     *
     * The above comment appears to be incorrect, and setting this _stubAPI to true
     * isn't sufficient to prevent calls to the server. Instead, the main one in
     * APICurlRequest has to be set.
     *
     */
    //protected $_stubAPI = FALSE;

    private $_keys=null;

    protected function _getMsgRequestContent() {
        $msg ='<Method>GetInstallKeys</Method>
            <Params>
                <MemberID>' .$this->modelData->getMemberID() . '</MemberID>
                <ProductID>'. $this->modelData->getProductID()  .'</ProductID>
                <OrderID>' .$this->modelData->getOrderID() . '</OrderID>
                </Params>
                ';
        return $msg;
    }


    private function getRandomCharacters($num=5) {
        $str = uniqid();
        $len = strlen($str);
        $start = rand(0,$len-$num-1);
	//echo "st:$start  le:$len  ---- ";
        return substr($str, $start, $num);
    } // getRandomCharacters

    /**
     *
     * @return array
     */
    public function setInstallKeys(){
        if (!$this->_stubAPI) {
            $this->_keys = $this->responseValue->InstallKey;
            \Yii::info('Key Obtained: '.substr($this->_keys,-5),__METHOD__);
        } else {
            // stubbed fake install key
            $fakeInstallKey = new \stdClass();
            $fakeInstallKey->Value = $this->getRandomCharacters().'-'.$this->getRandomCharacters().'-'.$this->getRandomCharacters().'-'.$this->getRandomCharacters().'-TEST';

            $this->_keys = $fakeInstallKey;
            \Yii::info('Fake Key Generated: '.$this->_keys->Value,__METHOD__);
        }
        return $this->_keys;
    }
    public function getInstallKeyValue(){
        //$pos not currently in use
        if(isset($this->_keys)){
            if(isset($this->_keys->Value)){
                //in this instant we are only expecting the one key.
                return (string)$this->_keys->Value;
            }
        }
        else {
           //throw new CampaignException(CampaignErrorCodes::NORESPONSE, 'NO KEY');
             \Yii::info('No Key!', __METHOD__);
            return null;
        }

    }

}

?>
