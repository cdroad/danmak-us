<?php if (!defined('PmWiki')) exit();

/*
 * 基础设定
 */
$LOCALVERSION = true;
$DEBUGMODE = true;
$ACFUN = FALSE;
$BILIBILI = TRUE;
$FmtPV['$DMFVersion'] = '"DMF-5.0.0 pre-alpha"';
$LoginAuthLevel = 'Danmakuer';
$CheckPerfs = FALSE;
$EnableAutoTimeShift = TRUE;
$TimeShiftDelta = 0.000001;

// Bilibili设定
//是否允许代码弹幕(高级弹幕)
$BiliEnableSA = TRUE;

$VideoSourceSet = new VideoSourceSet();
$VideoSourceSet
	->add('nor'		, new XinaSource())
	->add('td'		, new TuDouSource())
	->add('qq'		, new QQSource())
	->add('6cn'		, new sixRoomSource())
	->add('local'	, new LocalSource())
	->add('link'	, new URLSource())
	->add('url'		, new URLSource())
	->add('burl'	, new BURLSource())
	->add('blink'	, new BURLSource());

//弹幕权限表
$BilibiliAuthLevel = new DefinedEnum( array
(
	'Guest'	=> '0',
	'User'	=> '10000,1001',
	'Danmakuer' => '20000,1001'
));

//调试
if (CondAuth($pagename, 'admin')) $EnableDiag = 1;
$HTMLHeaderFmt['playerScripts'] = "\n".'<script type="text/javascript" src="/static/min/b=static&amp;f=jquery-1.6.1.min.js,qule.js,swfobject.js,jquery-ui-1.8.14.custom.min.js,pdm-bili.js"></script>'."\n";

//添加属性
include_once("$FarmD/scripts/forms.php");
$InputAttrs[] = 'onclick';
$InputAttrs[] = 'onsubmit';
$InputAttrs[] = 'onchange';
$InputAttrs[] = 'target';
$InputAttrs[] = 'onkeyup';
$InputAttrs[] = 'maxlength';