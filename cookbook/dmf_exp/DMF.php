<?php
libxml_use_internal_errors(true);
include_once(DMF_ROOT_PATH."inc/class.Enum.php");
include_once(DMF_ROOT_PATH."inc/class.DefinedEnum.php");
include_once(DMF_ROOT_PATH."inc/class.FlagsEnum.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuPoolBase.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuPoolBaseIO.php");
include_once(DMF_ROOT_PATH."inc/class.Set.php");
include_once(DMF_ROOT_PATH."inc/class.VideoSourceSet.php");
include_once(DMF_ROOT_PATH."inc/class.PlayerSet.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuBarItem.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuBarSet.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuBuilder.php");
include_once(DMF_ROOT_PATH."inc/class.DanmakuXPathBuilder.php");
include_once(DMF_ROOT_PATH."inc/class.Player.php");
include_once(DMF_ROOT_PATH."inc/class.Utils.php");
include_once(DMF_ROOT_PATH."inc/class.VideoData.php");
include_once(DMF_ROOT_PATH."inc/class.VideoSource.php");
include_once(DMF_ROOT_PATH."inc/class.GroupConfig.php");
include_once(DMF_ROOT_PATH."inc/action.SetDefaultPlayer.php");







/*
 * 基础设定
 */
$LOCALVERSION = true;
$DEBUGMODE = true;
$ACFUN = TRUE;
$ACFUN2011 = TRUE;
$BILIBILI = TRUE;
$TWODLAND = TRUE;
$FmtPV['$DMFVersion'] = '"DMF-5.2.0 alpha"';
$CheckPerfs = FALSE;
$EnableAutoTimeShift = TRUE;
$EnableSysLog = TRUE;
$TimeShiftDelta = 0.000001;
$TimeShiftThreshold = 10 * 60; //两次弹幕发送间隔超过阈值后重置漂移。

//权限设定
$HandleAuth['xmlread'] = 'read';
$HandleAuth['xmledit'] = 'edit';
$HandleAuth['xmladmin'] = 'admin';
if ($LOCALVERSION) {
	$HandleAuth['dmpost'] = 'edit';
} else {
	$HandleAuth['dmpost'] = 'admin`';
}
$HTMLHeaderFmt['javascripts'] = "\n".'<script type="text/javascript" src="/static/min/?b=static&amp;f=jquery-1.6.1.min.js,qule.js,swfobject.js,jquery-ui-1.8.14.custom.min.js,pdm-bili.js"></script>'."\n";


//处理投稿请求
if ($_POST["xVerify"]=="fca654cb-60ac-4f9c-b751-16ef296227b2")  {
    $_POST["xVideoStr"] = preg_replace('/\s/','',$_POST["xVideoStr"]);
}

if ($LOCALVERSION) {
	include(DMF_ROOT_PATH."DMF_local.php");
} else {
	include(DMF_ROOT_PATH."DMF_main.php");
}
