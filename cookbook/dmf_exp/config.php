<?php if (!defined('PmWiki')) exit();

/*
 * 基础设定
 */
$LOCALVERSION = true;
$DEBUGMODE = true;
$ACFUN = FALSE;
$BILIBILI = TRUE;
$FmtPV['$DMFVersion'] = '"DMF-5.0.0 pre-alpha"';
$CheckPerfs = FALSE;
$EnableAutoTimeShift = TRUE;
$TimeShiftDelta = 0.000001;

$HTMLHeaderFmt['playerScripts'] = "\n".'<script type="text/javascript" src="/static/min/b=static&amp;f=jquery-1.6.1.min.js,qule.js,swfobject.js,jquery-ui-1.8.14.custom.min.js,pdm-bili.js"></script>'."\n";