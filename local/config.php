<?php if (!defined('PmWiki')) exit();

$WikiTitle = "弹幕塚-本地版 ~少壮不努力，一生在内地~";
$PageLogoUrl = "$ScriptUrl/static/logo.jpg";

$AuthUser['Admin'] = crypt('/n/n/n/n');
$AuthUser['@admins'] = array('KPX', 'Admin');
$DefaultPasswords['admin'] = array('@admins');
$DefaultPasswords['upload'] = array('@admins');
$HandleAuth['delete'] = 'admin';
include_once("$FarmD/scripts/authuser.php");


$EnablePostAttrClearSession = 0;
$Skin = 'pmwikiGPT';
## PmWiki未公开功能 等稳定后启用
/*
$PageCacheDir = './static/page';
$PageListCacheDir = './static/pagelist';
$EnableHTMLCache = 1;
*/
$MarkupCss = true;
$EnableIMSCaching = 0;
$EnableRelativePageVars = 1;
$EnableUndefinedTemplateVars = 0;
$EnablePostAuthorRequired = 1;
$EnableDiffInline = 1;
$HTMLPNewline = '<br />'; 
$SearchPatterns['default'][] = '!^PmWiki\\.!';
//调试
if (CondAuth($pagename, 'admin')) $EnableDiag = 1;
//添加属性
include_once("$FarmD/scripts/forms.php");
$InputAttrs[] = 'onclick';
$InputAttrs[] = 'onsubmit';
$InputAttrs[] = 'onchange';
$InputAttrs[] = 'target';
$InputAttrs[] = 'onkeyup';
$InputAttrs[] = 'maxlength';

# 广告屏蔽
$BlocklistDownload["$SiteAdminGroup.Blocklist-MoinMaster"] = array(
'url' => 'http://master.moinmo.in/BadContent?action=raw',
'format' => 'regex',
'refresh' => 8640000000);
# END

# 页面储存
$WikiDir = new PageStore('./wiki.d/{$Group}/{$FullName}');
$WikiLibDirs = array( &$WikiDir,
	new PageStore('$FarmD/dmflib.d/{$Group}/$FullName'),
	new PageStore('$FarmD/wikilib.d/$FullName')
);
# END

# 附件
$EnableUpload = 1;
$UploadMaxSize = 1000000;
$EnableUploadVersions=1;
$UploadExts['xml'] = 'text/xml';
# END

# i18n
include_once($FarmD.'/scripts/xlpage-utf-8.php');
XLPage('ZhCn','PmWikiZhCn.XLPage');
if(date_default_timezone_get() != "Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
# END

include_once($FarmD.'/cookbook/expirediff.php');
include_once($FarmD.'/scripts/guiedit.php');
include_once($FarmD.'/cookbook/bbcode.php');
include_once($FarmD.'/cookbook/newpageboxplus.php');
include_once($FarmD.'/cookbook/pagetoc.php');
include_once($FarmD.'/cookbook/mkexpext.php');
include_once($FarmD.'/cookbook/fplcount.php');
include_once($FarmD.'/cookbook/deletepage.php');
include_once($FarmD.'/cookbook/adddeleteline2.php');
include_once($FarmD.'/cookbook/quickreplace.php');
include_once("$FarmD/cookbook/uploadform.php");
include_once("$FarmD/cookbook/PageGenerationTime.php");
include_once("$FarmD/cookbook/HtmlMarkup.php");
include_once("$FarmD/cookbook/CreatedBy.php");

$XESTagAuth = 'edit';
include_once("$FarmD/cookbook/tagpages.php");
$WikiStyleCSS[] = 'line-height';

# 插件设定
## 外链审核
//include_once("$FarmD/scripts/urlapprove.php");
$UrlLinkFmt = "<a class='urllink' href='\$LinkUrl' >\$LinkText</a>";
$GroupHeaderFmt =
  '(:include {$SiteGroup}.AllGroupHeader:)(:nl:)'
  .'(:include {$Group}.GroupHeader:)(:nl:)';

## 编辑通告
/*
if (($_GET['action'] != 'setdef') && ($_GET['action'] != 'GetFlvUrl')) {$EnableNotify = 1;}
$NotifySquelch = 60;
$NotifyDelay = 300;
$NotifyItemFmt = " * \$FullName . . . \$PostTime by \$Author : \n \$PageUrl"; 
$EnableNotifySubjectEncode = 1;
$NotifyHeaders = "Content-type: text/plain; charset=$Charset"; 
*/

## RSS & 网站地图
/*
if ( $action=="sitemap" ) {
	$RssMaxItems=50000;
	$RssSourceSize=0;
	$RssDescSize=0;
	$action="rss";
	SDVA($FeedTrailOpt, array('trail' => $pagename, 'count' => 50000));
}
include_once("$FarmD/scripts/feeds.php");
*/

## 游客记录
/*
$VisitorsLoggingDirectory = "./uploads/VisitorsLog";
$VisitorsLoggingFileName = "%Y-%m-%d.txt";
$VisitorsLoggingPurgeAfterDays = 0;
$VisitorsLoggingFormat = '%Date %Time %RemoteAddr:pad %Action:pad '
    . '%HttpHost %WikiGroup.%WikiPage '
    . '"%HttpReferer" "%HttpUserAgent"' . "\n";
$VisitorsLoggingDateFormat = '%Y-%m-%d';
$VisitorsLoggingTimeFormat = '%H:%M:%S';
$VisitorsLoggingIgnoreList = array('127.0.0.1');
include_once("$FarmD/cookbook/visitorslogging1337.php");
*/

# END

include("./cookbook/dmf_exp/DMF.php");

if ( !(bool)preg_match("/^\/([A-Z0-9\xa0-\xff\?].*)/", $_SERVER['REQUEST_URI'])
      && !($_SERVER['REQUEST_URI'] == "/") ) {
    $pagename = $_REQUEST['n'] = $_REQUEST['pagename'] = 'Main/HomePage';
    $EnableCodeIgniter = TRUE;
}