<?php
namespace common\components;
use Yii;
use yii\console\Exception;
use DOMDocument;

class MpXmlException extends Exception {} ;


class XmlUtils {
    const INVALID_XML_FILE = 'Unable to process the XML file ' ;

    static private $_errors ;

    /**
     * GET ERRORS
     * ==========
     * Returns an array of all errors detected while processing the XML. These
     * are technical messages and should not be shown to the end user.
     *
     * @return type
     */
    static public function getErrors() {
        return self::$_errors ;
    }

    /**
     * STRIP BOM
     * =========
     * Removes the Byte Order Marker from UTF-8 text
     *
     * @param type $text
     * @return type
     */
    static function stripBOM($text) {

        $bom = pack("CCC", 0xef, 0xbb, 0xbf);
        if (0 == strncmp($text, $bom, 3)) {
//                echo "BOM detected - file is UTF-8\n";
                $text = substr($text, 3);
        }
        return $text ;
    }

    /**
     * READ ORDER XML
     * ==============
     * Validates and loads the received xml data, then identifies and returns
     * the main body for subsequent processing.
     *
     * NOTE: The xml schema checker throws an exception if it detect errors.
     *
     * @return SimpleXMLElement Response
     */
    static function readXMLrequest($rawxml=null ,$xmlschemepath=null) {
        if(!isset($rawxml)){
        //    $rawxml = $_POST['rawxml'] ;
            // When CmsInput extension is enabled the POST will have been cleaned
            // and so all the XML Tags will be missing.
            // This line grabs the original, uncleaned POST
       //     $uncleanedpost = Yii::app()->input->getOriginalPost();
       //     $rawxml = isset($uncleanedpost['rawxml'] )?$uncleanedpost['rawxml']:$rawxml;
        }

        if (!empty($rawxml)) {
            $rawxml = XmlUtils::stripBOM($rawxml) ;
        }


        $rawxml = trim($rawxml);
        if (!empty($rawxml)) {
            if(isset($xmlschemepath)){
                XmlUtils::checkXmlAgainstSchema ($rawxml, $xmlschemepath) ;
            }
            //die ($rawxml);
            $order = simplexml_load_string($rawxml) ;
            $ns = $order->getDocNamespaces(true) ;
            $nodes = $order->children() ;

            return $nodes ;
        } else {
            throw new Exception('No XML returned from eZtorm API');
        }
    }


    /**
     * CHECK XML AGAINST SCHEMA
     * ========================
     * vaidates a passed XML string against either a passed schema or a default
     * micro-p one.
     *
     * NOTE: If there are any errors it raises an exectpion rather than returning
     *
     * @param type $doc
     * @param string $xmlschema
     * @return type
     */
    static function checkXmlAgainstSchema($doc, $xmlschema) {
    //    Yii::log(htmlentities($doc), 'info') ;
        Yii::info(__METHOD__.":AAA($doc, $xmlschema)", 'info') ;
    //    $basePath = Yii::app()->controller->module->basePath;
    //    if (!$xmlschema) {
   //         $xmlschema = $fileName = $basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'IcomMPBasket.xsd' ;
   //     }

        $xdoc = new DomDocument ;
        $xdoc->loadXML( self::stripBOM($doc) ) ;

        // Enable user error handling
        libxml_use_internal_errors(true);

        // Validate the XML file against the schema
        if (file_exists($xmlschema)) {
            // ---------------------------------------------------------------
            // if there are any errors, schemaValidate just dumps the text to
            // stdout, so need to capture the output
            // ---------------------------------------------------------------
            ob_start() ;
//            echo htmlspecialchars($doc );exit;
            $is_valid_against_schema = $xdoc->schemaValidate($xmlschema);
            $errors = ob_get_clean() ;

            // ---------------------------------------------------------------
            // If any errors, note them, in case we want to read/log them, then
            // throw an excpetion.
            // ---------------------------------------------------------------
            if (!$is_valid_against_schema) {
                $errors = print_r( libxml_get_errors(), true) ;
                Yii::info(': '. $errors,  __METHOD__);
                throw new MpXmlException ( self::INVALID_XML_FILE . $errors ) ;

            }
            return $is_valid_against_schema ;
        }
    }


    static function debugsxml($sxml, $what=null) {
        if($what) {
            echo '<h3>XML: ' . $what . '</h3>' ;
        }
        $dom = dom_import_simplexml($sxml);
        pre_dump($dom);
    }

     static function _getSampleData($filename) {
         $basePath = Yii::app()->controller->module->basePath;
         $filePath = $basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $filename ;
         $data = file_get_contents($filePath) ;

         $data = self::stripBOM($data);

         return $data ;

    }
}
