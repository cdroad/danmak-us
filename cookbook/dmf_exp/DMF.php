<?php
libxml_use_internal_errors(true);
include_once("cookbook/dmf_exp/inc/class.Enum.php");
include_once("cookbook/dmf_exp/inc/class.DefinedEnum.php");
include_once("cookbook/dmf_exp/inc/class.FlagsEnum.php");
include_once("cookbook/dmf_exp/inc/class.DanmakuPoolBase.php");
include_once("cookbook/dmf_exp/inc/class.DanmakuPoolBaseIO.php");
include_once("cookbook/dmf_exp/inc/class.Set.php");
include_once("cookbook/dmf_exp/inc/class.VideoSourceSet.php");
include_once("cookbook/dmf_exp/inc/class.PlayerSet.php");
include_once("cookbook/dmf_exp/inc/class.DanmakuBarItem.php");
include_once("cookbook/dmf_exp/inc/class.DanmakuBarSet.php");
include_once("cookbook/dmf_exp/inc/class.DanmakuBuilder.php");
include_once("cookbook/dmf_exp/inc/class.DanmakuXPathBuilder.php");
include_once("cookbook/dmf_exp/inc/class.Player.php");
include_once("cookbook/dmf_exp/inc/class.Utils.php");
include_once("cookbook/dmf_exp/inc/class.VideoData.php");
include_once("cookbook/dmf_exp/inc/class.VideoSource.php");
include_once("cookbook/dmf_exp/inc/class.GroupConfig.php");








/*
 * 基础设定
 */
$LOCALVERSION = true;
$DEBUGMODE = true;
$ACFUN = TRUE;
$ACFUN2011 = TRUE;
$BILIBILI = TRUE;
$TWODLAND = TRUE;
$FmtPV['$DMFVersion'] = '"DMF-5.1.0 pre-alpha"';
$CheckPerfs = FALSE;
$EnableAutoTimeShift = TRUE;
$EnableSysLog = TRUE;
$TimeShiftDelta = 0.000001;
$TimeShiftThreshold = 10 * 60; //两次弹幕发送间隔超过阈值后重置漂移。

$HTMLHeaderFmt['javascripts'] = "\n".'<script type="text/javascript" src="/static/min/?b=static&amp;f=jquery-1.6.1.min.js,qule.js,swfobject.js,jquery-ui-1.8.14.custom.min.js,pdm-bili.js"></script>'."\n";


//处理投稿请求
if ($_POST["xVerify"]=="fca654cb-60ac-4f9c-b751-16ef296227b2")  {
    $_POST["xVideoStr"] = preg_replace('/\s/','',$_POST["xVideoStr"]);
}

$HandleActions['setdef'] = 'Handlesetdef';
$HandleAuth['setdef'] = 'admin';
function Handlesetdef($pn,$auth = 'admin')
{
	global $FmtPV;
	
	$page = RetrieveAuthPage($pn, $auth, true, READPAGE_CURRENT);
	if (!$page) Abort("?cannot source $pn");
	if ($_GET['Player']) {$page['DP_P0'.$_GET['PartEX']] = $_GET['Player'];}
	WritePage($pn,$page);
	
	header("HTTP/1.1 301 Moved Permanently");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Location: ".PageVar($pn,'$PageUrl'));
}

if ($LOCALVERSION) {
	include("./cookbook/dmf_exp/DMF_local.php");
} else {
	include("./cookbook/dmf_exp/DMF_main.php");
}
