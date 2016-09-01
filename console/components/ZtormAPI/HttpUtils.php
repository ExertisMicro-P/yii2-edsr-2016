<?php
namespace console\components\ZtormAPI;

class HttpUtils {
    /**
     * CHECK AUTH
     * Checks if the header contains a correct user and password for the retailer
     * 
     */
    
      
     static function sendResponse($status = 404, $body = '', $content_type = 'text/html')
      {
        // set the status
       // $status_header = 'HTTP/1.1 ' . $status . ' ' . 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found';
          //$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
          $status_header = 'HTTP/1.0 404 Not Found';
          header($status_header);
        // and the content type
          header('Content-type: ' . $content_type);
        /* FUTURE PROOF MAYBE OPEN THIS OUT TO SEND OTHER RESPONSES */
        // pages with body are easy
        if($body != '')
        {
            // send the body
            echo $body;
        }
        // we need to create the body if none is passed
        else
        {
        // create some body messages
        $message = '';
 
        // this is purely optional, but makes the pages a little nicer to read
        // for your users.  Since you won't likely send a lot of different status codes,
        // this also shouldn't be too ponderous to maintain
        switch($status)
        {
            case 401:
                $message = 'You must be authorized to view this page.';
                break;
            case 404:
                $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                break;
            case 500:
                $message = 'The server encountered an error processing your request.';
                break;
            case 501:
                $message = 'The requested method is not implemented.';
                break;
        }
 
        // servers don't always have a signature turned on 
        // (this is an apache directive "ServerSignature On")
        //$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
 
        // this should be templated in a real-world solution
        $body = $message;
        echo $body;
    }
    Yii::app()->end();
}
static function _getStatusCodeMessage($status)
{
    // these could be stored in a .ini file and loaded
    // via parse_ini_file()... however, this will suffice
    // for an example
    $codes = Array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
    );
    return (isset($codes[$status])) ? $codes[$status] : '';
}
    
}