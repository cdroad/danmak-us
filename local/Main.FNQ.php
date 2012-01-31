<?php if (!defined('PmWiki')) exit();
include_once(DMF_ROOT_PATH."inc/package.FNQ.php");
include_once(DMF_ROOT_PATH."config.Twodland1.php");
include_once(DMF_ROOT_PATH."config.Bilibili2.php");
include_once(DMF_ROOT_PATH."config.Acfun2.php");
include_once(DMF_ROOT_PATH."config.AcfunN1.php");

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
    $GLOBALS['MessagesFmt'] = "提交完成，请等待管理员处理";
    HandleBrowse('Main.FNQ');
}

function WriteFNQPage($str)
{
    $new['text'] = '[@'.$str."\n\n@]";
	$gn = "Queue.".time();
    WritePage($gn,$new);
}
//http://www.bilibili.tv/video/av63814/