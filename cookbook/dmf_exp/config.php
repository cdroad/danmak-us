<?php if (!defined('PmWiki')) exit();

/*
 * 基础设定
 */
$LOCALVERSION = true;
$DEBUGMODE = true;
$ACFUN = FALSE;
$BILIBILI = TRUE;
$FmtPV['$DMFVersion'] = '"DMF-5.1.0 pre-alpha"';
$CheckPerfs = FALSE;
$EnableAutoTimeShift = TRUE;
$EnableSysLog = TRUE;
$TimeShiftDelta = 0.000001;
$TimeShiftThreshold = 10 * 60; //两次弹幕发送间隔超过阈值后重置漂移。

$HTMLHeaderFmt['javascripts'] = "\n".'<script type="text/javascript" src="/static/min/b=static&amp;f=jquery-1.6.1.min.js,qule.js,swfobject.js,jquery-ui-1.8.14.custom.min.js,pdm-bili.js"></script>'."\n";