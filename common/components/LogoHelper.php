<?php

namespace common\components;

class LogoHelper {
   
    public static function cURLImage($img){
    
        $curl = curl_init();
        curl_setopt_array($curl, 
        [
            CURLOPT_URL => $img,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_CUSTOMREQUEST => 'HEAD',
            CURLOPT_RETURNTRANSFER => true
        ]);
        
        curl_exec($curl); 
        $result = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);
        
        if($result == 200){
            return true;
        } else {
            return false;
        }
        
    }
    
}
