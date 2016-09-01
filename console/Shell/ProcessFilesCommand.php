<?php
/** Invoke using
*  php /var/www/html/mdfs/protected/yiic processFiles
*  from the command line (for testing/debug)
*  If using local environment for testing,  the use the -dev flag as
* a command line argument this will
* example cmd /k "C:/xampp/php/php protected/yiic processfiles -dev"
* You will need to create an the uploads/dev/in and the uploads/dev/archive folder.
* Add the files to the uploads/dev/in
*
*/
namespace console\Shell;
use console\components\CSVProcessor;
use yii;
// Not safe to rely on servers timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Europe/London');
}

ini_set("memory_limit","14096M"); // High due to Processimeis.php

/**
* Check the incoming directory for files, scan to determine the type and then
* load the relevant processor
*
* @use Call the command as follows, from within the protected direcrory call
* [path to php.exe if not in path otther wise php] yiic processFiles
*/
class ProcessFilesCommand
{
        /**
         * Patterns for matching filenames so we know how to process them
         */

        const HELP_FLAG = '-help'; //prints out the help
        const UNLOCK_PROCESS = '-unlock'; //command flag to unlock process
        const MUTEX_ID = 'processFiles'; //id used to lock the process
        const DEV_FLAG = '-dev'; // command flag to indicate this is a dev process -> need to change path
        const SKIPMUTEX_FLAG = '-skipmutex'; // command flag to indicate this is a dev process -> need to change path
        const LOCKTM = 600; //time for the mutex in seconds lock to stop parallel processing
	const PATHSTUB_PROD = '/var/interfaces/mdfs';
	const PATHSTUB_DEV = 'uploads/dev';
	const COPYTO_PROD = '/archive/in';
        const COPYTO_DEV =  '/archive/in';
	const UNRECOGNISED = '/unrecognised';
        public $_processor;  /* type CSVProcessor */
        public $filenamepattern;  /* must add this to construct */

        /**
         * @var string Path to the input files, supports both dev & production
         */
        private $pathstub = '';

        /**
         * @var string Path to store processed files (archive), supports both dev & production
         */
        private $copyto = '';
        private $unrecognised = '';

        /**
         * @var Full path for where the processed files are stored and archived
         */
        private $directory = '';
    	private $processed_directory = '' ;
        private $unrecognised_directory = '';


        /*
         * Set this using the -dev command line argument
         */
        private $isdevelopment = false;

    function __construct(CSVProcessor $processor, $filepattern) {
       $this->_processor = $processor;
       $this->filenamepattern = $filepattern;
       $this->_processor->_filenamePattern = $filepattern; /* not sure why we need this */

   }
    private function setup_paths($args) {

            //check for dev flag. remove the flag for $args
            if(!$this->isdevelopment){
                $this->pathstub = self::PATHSTUB_PROD;
                $this->copyto = self::COPYTO_PROD;
                $this->unrecognised = self::UNRECOGNISED;

            }
            else{
                // development paths for a local XAMPP environment
                $this->pathstub = Yii::app()->getBasePath().'/../'.self::PATHSTUB_DEV;
                $this->copyto = self::COPYTO_DEV;
                $this->unrecognised = self::UNRECOGNISED;

            }


        if (count($args)==0) {
  		$this->directory =  $this->pathstub.'/in';
    		$this->processed_directory =  $this->pathstub .  $this->copyto;
                $this->unrecognised_directory =  $this->unrecognised;

    	} else {
                // Running on the live server
                $this->processed_directory =  $this->pathstub .  $this->copyto;
                $this->unrecognised_directory =  $this->unrecognised;
    	}

        return $args;
    } // setup_paths

    /**
     * @desc Check for the presence of the command line developement flag.
     * If present removes the flag from the passed in array
     * @param  ref $command_args -> the arguments to be searched and processed
     * @returns bool
     */
    private function isDevelopment(&$command_args){
        //remove -dev flag from args - no longer required.
        if(($key = array_search(self::DEV_FLAG,$command_args)) !== false) {
            unset($command_args[$key]);
            $this->isdevelopment = true;
            return true;
        }
        else{
           $this->isdevelopment = false;
           return false;
        }
    } // isDevelopement


    /**
     * categorizeFile
     * Inspect directory to detect file matching the filenamepattern
     * @param type $directory
     * @param type $file
     * @return boolean
     */
	private function categorizeFile($directory='', $file='') {
		echo __METHOD__.': Considering '.$directory.'/'.$file."\n";
		$res = array();
		if (file_exists($directory.'/'.$file) && $file!='.' && $file!='..') {
			if (is_dir($directory.'/'.$file)!==TRUE ) {
                            Yii::info(__METHOD__.':'.__LINE__.': '.$directory.'/'.$file);
				echo __METHOD__.': '.$directory.'/'.$file."\n";
				if (preg_match($this->filenamepattern,$file)>0){ //MC005030-51267123.xml
					return $file;
                                }
                                else {
                                   // Yii::trace(__METHOD__.':'.__LINE__.': Couldn\'t Categorize '.$directory.'/'.$file);
                                        $res= array('type'=>'uncategorised', 'file'=>$file);
				}
			} else {
                                   // Yii::trace(__METHOD__.':'.__LINE__.': '.$directory.'/'.$file);
				echo __METHOD__.': Skipping '.$directory.'/'.$file."\n";
			}
		} else {
			echo __METHOD__.': Not found: '.$directory.'/'.$file."\n";
		}

		echo __METHOD__.': '.print_r($res,true);
		return false;
	} // categorizeFile


    /**
     * getFilesForProcessing
     * Searches folder for files to process.
     * @param array $args
     * @return array for filenames and types
     */
    private function getFilesForProcessing($args=array()){

        $this->directory = $this->_processor->filepath;
       // $this->directory = realpath(Yii::app()->getBasePath()). self::FILEPATH .'/' .$filename;$this->_processor->filepath;
        $assocfiles = array();
        $files = array();
                // Without arguments.. Check directory exists
                if (file_exists($this->directory)){
                    $dh = opendir($this->directory);
                    // Open directory and check for files
                    while (($file = readdir($dh)) !== false)  {
                        //echo "filename:" . $file ;

                        $result = $this->categorizeFile($this->directory, $file);
                        if($result){ //used to sort file in date order later on
                            $time = (string)filemtime($this->directory.$file);
                            $assocfiles[$time] = $result;
                        }

                    } // while
                    //sort list by time key in standard array - use krsort for Descending
                    ksort($assocfiles,SORT_NUMERIC);
                    foreach($assocfiles as $file){
                        $files[] = $file;
                    }
                    return $files;
                }else{
                    echo "Folder '". $this->directory ."' not found check command line statements are correct \n";

                }
            return $files;
    } //getFilesForProcessing

    /**
     * beforeProcessingFiles
     * Files found do any activities before file processing
     */
    protected function beforeProcessingFiles(){
        return true;
    }

    protected function afterProcessingFiles(){
        return true;
    }
    /**
    * Run the processor on any matching files in the set directory, once run
    * the process should move the file to an archive
    * @param array $args
    */
    protected function processFiles($files){

            if(count($files) > 0) {
                $this->beforeProcessingFiles();
	    	foreach($files as $file) {
                                echo __LINE__.': Processing '.$file. "\n";

                                if($this->_processor->run($this->directory, $file, FALSE)){
                                    $this->_movefiletoarchive($file);
                                }
                                else{
                                    //something went wrong during processing move file to error folder.
                                    $this->_movefiletoerrorarchive($file);
                                }

                    } //end foreach file
                    $this->afterProcessingFiles();
                    //$mailer = new ProcessMailer();
                    //$mailer->run('sdg customer files', $this->_processor->messages, 'SDG File');
               }  //end while count > 0
               else {
                   echo "No matching files found. Nothing processed.\n";
               }


    }	//processFiles





    // creates a list of files in a given directory and processes the files.
    private function getAndProcessFiles($args=array()){
        try{
            $files = $this->getFilesForProcessing($args);
            $this->processFiles($files);
        }catch( Exception $e){

         //   Yii::trace('exception happens  in method'.__METHOD__);

            throw $e; //we  just  rethrow the exception instance
        }

    } //getAndProcessFiles

    /**
     * Setup directory to search. Searches directory for files and processes files
     * matching a criteria type.
     * Once processed file is moved to archive directory.
     * @param type $args
     */
    public function run($args=array())
    {
        //$this->isDevelopment($args);
                try{
                    $args = $this->setup_paths($args);
                    $this->getAndProcessFiles($args);

                    foreach($this->_processor->messages as $msg){
                        echo $msg . "\n";
                    }
                    echo "Process completed.\n";
                }
                catch( Exception $e){
          //          Yii::trace('exception happens  in method'.__METHOD__);
                    throw $e; //we  just  rethrow the exception instance
                }

    } // run


     /**
     *  MOVE FILE TO ARCHIVE
     * =====================
     * Moves the file from the it's current location to a new location after processing
     */
    private function _movefiletoarchive($filename) {
        $this->_processed_directory = $this->_processor->movetopath;
        echo __LINE__ . ': Processed  ' . $filename . ' - moving to ' . $this->_processed_directory . $filename . "\n";
        if (file_exists($this->directory)) {
            try {
                if (rename($this->directory . $filename, $this->_processed_directory . $filename))
                    Yii::trace(__METHOD__ . ': ' . __LINE__ . ' - ' . $this->directory . ' Moved.');
            } catch (Exception $exc) {
                Yii::info(__METHOD__ . ': ' . __LINE__ . ' - Problem moving ' . $this->_filedirectory . ' : ' . $exc->getMessage());
            }
        } else {
            Yii::info(__METHOD__ . ': ' . __LINE__ . ' - ' . $this->directory . ' Moved before it could be renamed');
        }
    }

    private function _movefiletoerrorarchive($filename) {
        $this->_processed_directory = $this->_processor->errorfilepath;
        echo __LINE__ . ': Processed  ' . $filename . ' - moving to ' . $this->_processed_directory . $filename . "\n";
        if (file_exists($this->directory)) {
            try {
                if (rename($this->directory, $this->_processed_directory . $filename))
                    Yii::trace(__METHOD__ . ': ' . __LINE__ . ' - ' . $this->directory . ' Moved.');
            } catch (Exception $exc) {
                Yii::info(__METHOD__ . ': ' . __LINE__ . ' - Problem moving ' . $this->directory . ' : ' . $exc->getMessage());
            }
        } else {
            Yii::info(__METHOD__ . ': ' . __LINE__ . ' - ' . $this->directory . ' Moved before it could be renamed');
        }
    }

    private function _movefileforprocessing($filename) {
        $this->_processed_directory = $this->_processor->processingfilepath;
        echo __LINE__ . ': Processed  ' . $filename . ' - moving to ' . $this->_processed_directory . $filename . "\n";
        if (file_exists($this->directory)) {
            try {
                if (rename($this->directory, $this->_processed_directory  . $filename))
                    Yii::trace(__METHOD__ . ': ' . __LINE__ . ' - ' . $this->directory . ' Moved.');
            } catch (Exception $exc) {
                Yii::info(__METHOD__ . ': ' . __LINE__ . ' - Problem moving ' . $this->directory . ' : ' . $exc->getMessage());
            }
        } else {
            Yii::info(__METHOD__ . ': ' . __LINE__ . ' - ' . $this->directory . ' Moved before it could be renamed');
        }
    }

}
