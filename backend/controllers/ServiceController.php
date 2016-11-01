<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Description of ServiceController
 *
 * @author russellh
 */
class ServiceController extends Controller {
    
    private static function _execCurl($strURL, $arrPOST = null, $arrHeader = null){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $strURL);
        curl_setopt($ch,CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch,CURLOPT_TIMEOUT,15); 
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true); 

        if (is_array($arrPOST)){
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $arrPOST);  
        }

        if (is_array($arrHeader)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
        }

        try {
            $strRESPONSE = curl_exec($ch);
            $strHTTPCODE = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            $strURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            
            curl_close($ch);
        } catch (Exception $e) {
            try { curl_close($ch); } catch (Exception $ex) { }
            die ("FATAL ERROR: ".$e->getMessage());
        }

        return array('http_code' => $strHTTPCODE, 'response' => $strRESPONSE, 'realUrl' => $strURL);
    }
    
    
    /**
     * Lists all Role models.
     * @return mixed
     */
    public function actionIndex($url)
    {
        
        if (!empty($url)) {
            $result = $this->_execCurl($url);
            Yii::$app->response->statusCode = $result['http_code'];
            return $result['response'];
        }
         
         
        
    }

}
