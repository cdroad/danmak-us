<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dmf extends CI_Controller {

    public function getLocalUploads() {
        if (!$GLOBALS['LOCALVERSION']) {
            exit;
        }
        
        $AllowedFileType = array("flv", "mp4", "m4a", "m4r", "m4v");
        $BaseUrl = 'http://localhost/uploads/LocalVideo/';
        $D = "./uploads/LocalVideo";
        
        $localD = opendir($D);
        while ( ($file = readdir($localD)) !== false ) {
            $ext = pathinfo("$D$file", PATHINFO_EXTENSION);
            if ( in_array(strtolower($ext), $AllowedFileType) ){
                $files[basename($file)] = $BaseUrl.$file;
            }
        }
        echo "var files=".json_encode($files).";";
    }
}
