<?php

/**
 *  Processes the SDG Customer file which is dropped into /var/www/html/mdfs/uploads every 5 minutes
 */
namespace console\components;
use yii;
use console\components\AccountSetupException;
use console\components\EDSRException;

/**
 * Main engine for reading a CSV file.
 * Handler methods for reading CSV/DAT files and moving them once processed
 * Author H Kappes
 *  public function setFilePaths($filename)
 * public function run($directorypath, $filename, $output=TRUE)
 * public function processFile($filepath)
 * public function readCSVFile($filename)
 * public function readCSVRowsFile($filename)
 * public function processData($data)
 * public function processRowsFile($filepath)
 */
class CSVProcessor
{
     //public $movetopath = '/../uploads/dev/archive/';
     //public $filepath = '/../uploads/dev/in/';
     public $filepath; // set in __construct // = '/../uploads/in/';
     public $seperator = ',';
     public $movetopath; // set in __construct // = '/var/interfaces/mdfs/archive/in/';
     public $errorfilepath; // set in __construct // = '/../uploads/error/' ;
	 
     public $_filenamePattern = '';
     protected $_files = array();
     public $messages = array();
     protected $_count_new = 0;
     protected $_count_duplicate = 0;
     protected $_count_errors = 0;
     protected $_filename = '';
     protected $_processed_directory = '';
     protected $_filedirectory='';
     public $configurator;  /* type CSVConfigurator */
     public $error_in_file = false;
     

     /**
      * __construct
      * Sets up the configurator/parser to be used for file reading
      * Sets up what field seperator to look for.
      * @param \console\components\IDataLineProcessor $configurator
      * @param type $seperator
      */
     function __construct(IDataLineProcessor $configurator, $seperator) {
        $this->configurator = $configurator;
        $this->seperator = $seperator;
        $this->filepath = \Yii::$app->params['CSVProcesser.filepath'];
        $this->movetopath = \Yii::$app->params['CSVProcesser.movetopath'];
        $this->errorfilepath = \Yii::$app->params['CSVProcesser.errorfilepath'];
        if (\Yii::$app->params['CSVProcesser.isRelative']){
            $this->filepath = Yii::getAlias('@console') . $this->filepath; //relative path find the base path
            $this->movetopath = Yii::getAlias('@console') . $this->movetopath;
            $this->errorfilepath = Yii::getAlias('@console') . $this->errorfilepath;
        }
        
    }
    /**
     * setFilePaths
     * Sets the path where the files are kept
     * @param type $filename
     */
     public function setFilePaths($filename){
        $this->_filename = $filename;
        $this->_filedirectory = $this->filepath . $this->_filename;
        $this->_processed_directory = $this->movetopath;
     }


     /**
      * Run
      * Runs the engine for seting the path and processing the filename passed in.
      * @param type $directorypath
      * @param type $filename
      * @param type $output
      */
     public function run($directorypath, $filename, $output=TRUE)
    {
       $this->setFilePaths($filename);      
       try{
        $this->processFile($this->_filedirectory);
        
        //Grab the messages and output file and email if msgs cnt > 0;
        $this->messages = $this->configurator->msgs;
        $this->messages[] = "{$this->_count_new} records added for " .  $filename ;
        $this->messages[] = "There were {$this->_count_duplicate} duplicates, these have been skipped";
        $this->messages[] = "There were {$this->_count_errors} errors with the import";  
        return true;
       }
       catch(EDSRException $e){
           Yii::error(__METHOD__ . ' something went wrong');
           return false;
       }
    }

    /**
     * processFile
     * Calls generic methods
     * Reads the CSV file gets the data from it and then processes the data
     * @param type $filepath
     */
     public function processFile($filepath){ 
        $result = $this->readCSVRowsFile($filepath);
        //Reads each line of file and adds it to the DB if it does not exist.
        if($result){ //no errors
            $this->messages[] = $filepath . ' processed successfully with NO errors'; 
        }
        else{ //errors during processing
            $this->messages[] = $filepath . ' processed successfully with errors check logs, emails and messages.';
        }
        return $result;
     } 
     
     public function processFilesSuccess(){
         
     }
     
     /**
     * readCSVRowsFile
     * Reads each row of data from file and processes it. 
     * Keeps track of errors for reporting at the end.
     * @param type $filename
     * @return boolean     */
    public function readCSVRowsFile($filename){
            // CSV format.
            $data = array();
            $this->error_in_file = true;
            if (($handle = fopen($filename, "r")) !== FALSE) {
                $this->configurator->filename = $filename; // we need this peice of information for logging
                while (($row = fgetcsv($handle, 0, $this->seperator)) !== FALSE) {
                    try{
                        $this->configurator->processLine($row);   //will raise an exception if it fails
                        $this->_count_new++;
                        //all OK
                    }
                    catch(EDSRException $e){
                      //process line threw exception somthing wrong with line
                      //log issue but carry on.
                      Yii::error($e->getMessage());
                      $this->messages[] = $e->getMessage();
                      $this->_count_errors++;
                      $this->error_in_file = $this->error_in_file & false;
                    }
                    
                }
                }
                fclose($handle);
            return $this->error_in_file;
                
    }
     
     
   /****
    * *************************************METHOD FOR READING FILE INTO ARRAY AND THEN PROCESSING IT *******************/
     
    /**
     * readCSVFile
     * Reads a CSV file into an array and returns array
     * @param type $filename
     * @return type array
     */
    public function readCSVFile($filename){
            // CSV format.
            $data = array();
            if (($handle = fopen($filename, "r")) !== FALSE) {
                while (($row = fgetcsv($handle, 0, $this->seperator)) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            }
            return $data;
    }
    
    
    /**
     * processData
     * Takes an array of an array of data and processes it
     * Keeps track of the result of each line parsed
     * @param type $data
     */
    public function processData($data) {
            //loop through the csv file and insert into database
           foreach($data as $dataline){   //each data line is an array of one line of the uploaded file
                if(count($dataline)>0){
                    try{
                        $this->configurator->processLine($dataline);
                        $this->_count_new++;
                    }
                    catch(EDSRException $e){
                        //@TODO handle this
                    }
                    
                }
            }   
    }
      
    
    } // CSVProcessor

