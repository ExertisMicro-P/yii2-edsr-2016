<?php



/**
 * Description of ExportProductsMsgHandler
 *
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\ZtormAPI\APICurlRequest;
class GetDownloadUrlMsgHandler extends APICurlRequest {

    /* Avoid hitting eZtorm API and incurring a charge for getting real products
     * Set it to True when you want to avoid charging and also implement stub code
     */
    //protected $_stubAPI = TRUE;


    protected function _getMsgRequestContent() {
        $msg ='<Method>GetFiles</Method>
            <Params>
                <MemberID>'.$this->modelData->getMemberID().'</MemberID>
                <ProductID>'.$this->modelData->getProductID().'</ProductID>
                </Params>
                ';
        return $msg;
    }

    public function getDownloadData(){
        return $this->responseValue->minidl->MemberFile;
    }

    public function getDownloadDataURL(){
        if (!$this->_stubAPI) {
	    //\Yii::error (\yii\helpers\VarDumper::dumpAsString ($this->responseValue,99,true));
		//die(\yii\helpers\VarDumper::dumpAsString ($this->responseValue->misc->MemberFile[1]->DownloadURL->URL,99,true));
            // RCH 20150402
            // handle empty download URLs
            if (isset($this->responseValue->misc->MemberFile[1]->DownloadURL)) {
                return (string)$this->responseValue->misc->MemberFile[1]->DownloadURL->URL;
            } else {
                return '';
            }
        } else
            return 'https://officesetup.getmicrosoftkey.com/'; // dummy fixed stub URL
    }

}

?>
