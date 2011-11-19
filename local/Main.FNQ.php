<?php if (!defined('PmWiki')) exit();
include_once("./cookbook/dmf_exp/inc/package.FNQ.php");
include_once("./cookbook/dmf_exp/config.Twodland1.php");
include_once("./cookbook/dmf_exp/config.Bilibili2.php");
include_once("./cookbook/dmf_exp/config.Acfun2.php");
include_once("./cookbook/dmf_exp/config.AcfunN1.php");

$HandleActions['FastPost'] = 'HandleFastPost';
$HandleAuth['FastPost'] = 'edit';

function HandleFastPost($pn, $auth)
{
    $GLOBALS['EnablePostAuthorRequired'] = 0;
    
    $url = $_POST['xURL'];
    try {
        $class = GetFNQClass($url);
    } catch (Exception $e) {
        //error here
        var_dump($e);
        die("doom!");
    }
    
    WriteFNQPage((string)$class);
}

function WriteFNQPage($str)
{
    $new['text'] = '[@'.$str."\n\n@]";
	$gn = "Queue.".time();
    WritePage($gn,$new);
}
//http://www.bilibili.tv/video/av63814/