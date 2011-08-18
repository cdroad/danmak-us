<?php if (!defined('PmWiki')) exit();

/*
 * 基础设定
 */
$LOCALVERSION = true;
$DEBUGMODE = true;
$ACFUN = FALSE;
$BILIBILI = TRUE;
$FmtPV['$DMFVersion'] = '"DMF-4.1.2"';
$LoginAuthLevel = 'Danmakuer';
$CheckPerfs = FALSE;
$EnableAutoTimeShift = TRUE;
$TimeShiftDelta = 0.000001;


// Bilibili设定
//是否允许代码弹幕(高级弹幕)
$BiliEnableSA = TRUE;

//弹幕权限表
$BilibiliAuthLevel = new DefinedEnum( array
(
	'Guest'	=> '0',
	'User'	=> '10000,1001',
	'Danmakuer' => '20000,1001'
));

//调试
if (CondAuth($pagename, 'admin')) $EnableDiag = 1;
//必须的javascript加载
//$HTMLHeaderFmt['swfobject'] = '<script type="text/javascript" src="/static/swfobject.js"></script>';
//$HTMLHeaderFmt['jquery'] = '<script type="text/javascript" src="/static/jquery-1.6.1.min.js"></script>';
//$HTMLHeaderFmt['bilibqule'] = '<script type="text/javascript" src="/static/qule.js"></script>';
$HTMLHeaderFmt['playerScripts'] = "\n".'<script type="text/javascript" src="/static/min/b=static&amp;f=jquery-1.6.1.min.js,qule.js,swfobject.js,jquery-ui-1.8.14.custom.min.js,pdm-bili.js"></script>'."\n";
#$HTMLFooterFmt['gPlus1'] = "\n".'<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: \'zh-CN\', parsetags: \'explicit\'}</script>'
#	."\n".'<script type="text/javascript">gapi.plusone.go();</script>'."\n";
#Markup("googlePlusOne", 'directives',"/\\(:googlePlusOne:\\)/e",
#	'keep("<g:plusone></g:plusone>")');


//添加属性
include_once("$FarmD/scripts/forms.php");
$InputAttrs[] = 'onclick';
$InputAttrs[] = 'onsubmit';
$InputAttrs[] = 'onchange';
$InputAttrs[] = 'target';
$InputAttrs[] = 'onkeyup';
$InputAttrs[] = 'maxlength';