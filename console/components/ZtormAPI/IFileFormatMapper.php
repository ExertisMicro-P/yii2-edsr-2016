<?php
/**
 * Determines and maps the formating of a file
 * @author helenk
 * 
 */
interface IFileFormatMapper {
    /**
     * Implements a condition to determine if the file format matches
     * @param AbsFileReader $reader
     * @return boolean
     */
    public function isFileAMatch(UploadFileReader $reader);
    
    /**
     * Updates and returns a FileFormatVo
     * @param AbsFileReader $reader
     * @return a FileFormatVO
     */
    public function configureFormatMappings(); // {return FileFormatVO }
}

?>
