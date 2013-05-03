<?php if (!defined('PmWiki')) exit();
libxml_use_internal_errors(true);
spl_autoload_register(function ($class) {
    $p = DMF_ROOT_PATH."inc/class.{$class}.php";
    
    if (file_exists($p)) {
        $f = $p;
    } else if (stripos($class,  'GroupConfig') !== FALSE) {
        $c = substr($class, 0, stripos($class,  'GroupConfig'));
        $f = DMF_ROOT_PATH."config/config.{$c}.php";
    }
    

    if (file_exists($f)) {
        include $f;
        return;
    } else {
        //echo $class."||||".$f;
        //exit;
    }
    
});


//弹幕权限表
$BilibiliAuthLevel = new DefinedEnum( array
(
    'DefaultLevel' => '10000,1001',
	'Guest'	=> '0',
	'User'	=> '10000,1001',
	'Danmakuer' => '20000,1001'
));


Markup("PlayerPageDisplay", 'directives', "/\\(:PlayerPageDisplay:\\)/e", 'DMF_PlayerPageDisplay()');

function DMF_PlayerPageDisplay() {
    global $pagename, $LOCALVERSION;
    $VDN = new VideoPageData($pagename);
    $xtpl = new XTemplate(DMF_ROOT_PATH.'view/playPage.xpl');
    
    if ($LOCALVERSION) {
        $xtpl->set_null_block('', 'main.source');
    } else {
        $xtpl->assign('SOURCE', $VDN->SourceLink);
        $xtpl->parse("main.source");
    }
    
    $tags = strip_tags(MarkupToHTML($pagename, '(:includeTag:)'), "<a>");
    $xtpl->assign('TAGS', $tags);
    if (CondAuth($pagename, 'edit')) {
        $xtpl->set_null_block('', 'main.tagListNormal');
        $xtpl->parse("main.tagListEditable");
    } else {
        $xtpl->set_null_block('', 'main.tagListEditable');
        $xtpl->parse("main.tagListNormal");
    }
    $GLOBALS['MessagesFmt'] = "{$VDN->Player->desc} -> {$VDN->VideoType->getType()}( \"{$VDN->DanmakuId}\" )";
    $messages = MarkupToHTML($pagename, "(:messages:)");
    $xtpl->assign('MESSAGES', $messages);
    $xtpl->parse("main.messages");
    
    $playerParams = $VDN->GroupConfig->GenerateFlashVarArr($VDN);
    foreach ($playerParams->params as $name => $value) {
        $xtpl->assign("FLASHVARS", array("Name" => $name, "Value" => $value));
        $xtpl->parse("main.FlashVars");
    }
    $xtpl->assign("URL", $playerParams->url);
    $xtpl->assign("HEIGHT", $playerParams->height);
    $xtpl->assign("WIDTH", $playerParams->width);
    $xtpl->parse("main.PlayerLoader");
    $xtpl->parse("main");
    return keep($xtpl->text());
}

function ObjLoadFunc()
{
	global $VDN;//
	$VDN = new VideoData($GLOBALS['pagename']);
	DMF_SetUpPageMarkUp();
}

include_once(DMF_ROOT_PATH."inc/class.DanmakuPoolBase.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuPoolBaseIO.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuBarItem.php");
include_once(DMF_ROOT_PATH."inc/class.VideoSource.php");
include_once(DMF_ROOT_PATH."inc/action.SetDefaultPlayer.php");
include_once(DMF_ROOT_PATH."DMF_Version.php");
Player::$playerBase = $ScriptUrl.'/static/players/';
SafeEnum::Create('PoolMode', 'S', 'D', 'A');
SafeEnum::Create('LoadMode', 'lazy', 'inst');
SafeEnum::Create('XmlAuth', 'read', 'edit', 'admin');
SafeEnum::Create("XmlErrorType", "NoError", "Auth", "Broken");

/*
 * 基础设定
 */
$LOCALVERSION = true;
$DEBUGMODE = true;
$ACFUN = TRUE;
$ACFUN2011 = TRUE;
$BILIBILI = TRUE;
$TWODLAND = TRUE;
$CheckPerfs = FALSE;
$EnableAutoTimeShift = TRUE;
$EnableSysLog = TRUE;
//$TimeShiftDelta = 0.000001;
//$TimeShiftThreshold = 10 * 60; //两次弹幕发送间隔超过阈值后重置漂移。

//权限设定
$HandleAuth['xmlread'] = 'read';
$HandleAuth['xmledit'] = 'edit';
$HandleAuth['xmladmin'] = 'admin';
if ($LOCALVERSION) {
	$HandleAuth['dmpost'] = 'edit';
} else {
	$HandleAuth['dmpost'] = 'admin`';
}
$HTMLHeaderFmt['javascripts'] = "\n".'<script type="text/javascript" src="/pub/min/?b=pub&amp;f=jquery-1.6.1.min.js,qule.js,swfobject.js,jquery-ui-1.8.14.custom.min.js,pdm-bili.js"></script>'."\n";


//处理投稿请求
if ($_POST["xVerify"]=="fca654cb-60ac-4f9c-b751-16ef296227b2")  {
    $_POST["xVideoStr"] = preg_replace('/\s/','',$_POST["xVideoStr"]);
}

if ($LOCALVERSION) {
	include(DMF_ROOT_PATH."DMF_local.php");
} else {
	include(DMF_ROOT_PATH."DMF_main.php");
}