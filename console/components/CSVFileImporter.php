<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CSVFileImporter
 * Finds File. Checks for File of fileID. Reads file. Uploads file to DB table.
 * @author helenk
 */
class CSVFileImporter {

    protected $fileID = '';
    protected $seperator;
    protected $filename;
    protected $dataline;
    protected $configurator;
    protected $archivePath;
    protected $filePath;

    /**
     * __construct
     * Instatiates $fileID to correct Identifier
     * @param type $fileID
     */
    function __construct(FileIDTypesDefiner $fileID, CSVConfigurator $configurator, SeperatorTypesDefiner $seperator,
            $archivePath,$filePath,$directory) {
        $this->fileID = $fileID;
        $this->configurator = $configurator;
        $this->seperator = $seperator;
        $this->archivePath = $archivePath;
        $this->filePath = $filePath;
        $this->directory = $directory;
    }

    /**
     * uploadDatatoDatabase()
     * Handlers initiation of file import and adding data to the database
     */
    public function uploadDatatoDatabase() {
        //get the file
        ini_set('auto_detect_line_endings', true);
        if ($_FILES[csv][size] > 0) {
            $this->setFileForImport();
            $this->addDatatoTable();
        }
    }

    /**
     * setFileForImport()
     * sets the CSV matching the fileIdentify to $file
     * 
     */
    public function setFileForImport() {
        //search for file with correct ID
       // search directory for correct file type
        $this->filename = $_FILES[csv][tmp_name];
        $this->data = self::readCSVFile($this->filename);
    }
    
    //while ($this->dataline = fgetcsv($handle, 1000, $this->seperator, "'"));
    public function readCSVFile($filename){
            // CSV format.
            $data = array();
            if (($handle = fopen($filename, "r")) !== FALSE) {
                while (($row = fgetcsv($handle, 1000, $this->seperator)) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            }
            return $data;
    }

    /**
     * addFileRecordtoDB()
     * Adds each line of the file as a record to the Database based on configurator type
     */
    protected function addRecordtoTable() {
        
        $this->configurator->addData($this->dataline);
       /* mysql_query("INSERT INTO contacts (contact_first, contact_last, contact_email) VALUES 
                ( 
                    '" . addslashes($data[0]) . "', 
                    '" . addslashes($data[1]) . "', 
                    '" . addslashes($data[2]) . "' 
                ) 
            ");# 
        
        */
    }
/**
 * addDatatoDatabase()
 * Overall method for reading each dataline into DB.
 */
    public function addDatatoTable() {            
            //loop through the csv file and insert into database 
           foreach($this->data as $dataline){
                if(count($dataline)>0){
                    $this->addRecordtoTable();
                }
            } 
    }

public function moveFileToArchive(){
    //Once processing done move file to Archive
    
}
/**
     * Searches folder for files to process.
     * @param array $args
     * @return array for filenames and types
     */
    public function getFilesForProcessing($args=array()){
                if (file_exists($this->directory)){
                    $dh = opendir($this->directory);
                    // Open directory and check for files
                    while (($file = readdir($dh)) !== false)  {           
                         $this->processMatchedFile($this->directory, $file);                  
                    } // while
                   
                }else{
                    //echo "Folder". $this->directory ." not found check command line statements are correct \n";
                }
              
            
    } //getFilesForProcessing
    
    /**
	 * Inspect filename to detect the type of file to be imported.
	 */
	private function processMatchedFile($directory='', $file='') {
		
				if (preg_match($this->_filenamePattern,$file)>0){
                                        //extract data and populate table0
                                        processFile();      
                                }
                                else{
                                    return false;
                                }
		
	} // categorizeFile

        
        /**
     * PROCESS XML
     * Read the xml extract the data for the table and populate the table 
     */
    public function processFile(){
        $this->data = readCSVFile($this->_filepath);
        $this->addDatatoTable();            
            
        } 
}
//end class
?>
