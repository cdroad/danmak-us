<?php if (!defined('PmWiki')) exit();
$HandleActions['getLocalUploads'] = 'HandleGetLocalUploads';
$HandleAuth['getLocalUploads'] = 'read';

function HandleGetLocalUploads($pn, $auth='read') {
	global $LOCALVERSION;
	
	$AllowedFileType = array("flv", "mp4", "m4a", "m4r", "m4v");
	$BaseUrl = 'http://localhost/uploads/LocalVideo/';
	if (!$LOCALVERSION) {
		exit;
	}
	
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
	