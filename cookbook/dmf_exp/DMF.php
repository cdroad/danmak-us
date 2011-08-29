<?php
$GroupConfigSet = new GroupConfigSet();

include_once("./cookbook/dmf_exp/config.php");
//include_once("./cookbook/dmf_exp/config.Acfun2.php");
include_once("./cookbook/dmf_exp/config.Bilibili2.php");

//处理投稿请求
if ($_POST["xVerify"]=="fca654cb-60ac-4f9c-b751-16ef296227b2")  {
    $_POST["xVideoStr"] = preg_replace('/\s/','',$_POST["xVideoStr"]);
}

if ($DEBUGMODE) {
	assert_options(ASSERT_ACTIVE, 1);
} else {
	assert_options(ASSERT_ACTIVE, 0);
}
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_BAIL, 1);
assert_options(ASSERT_QUIET_EVAL, 1);

if ( $CheckPerfs && ($_GET['skipPerfs'] != 'TRUE') )
{
	$Perfs = ReadPage('Site.DMFPerfs');
	//var_dump($Perfs);exit;
	if ($Perfs['isXMLConverted'] != 'YES')
	{
		header("HTTP/1.1 301 Moved Permanently");
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Location: /API/XMLTool?action=PairConv&skipPerfs=TRUE");
		exit();
	}
}

if ($LOCALVERSION) {
	include("./cookbook/dmf_exp/DMF_local.php");
} else {
	include("./cookbook/dmf_exp/DMF_main.php");
}

function myAssert($b, $str)
{
	global $myAssertInteraEnable;
	//扩展Assert
	if ($b === TRUE)
		return;
	if ($myAssertInteraEnable)
	{
		echo $str;
		assert(FALSE);
	} else {
		Abort($str);
	}
	return;
}

function writeLog($pagename, $errorStr)
{
	$Str = "\r\n".strftime($GLOBALS['TimeFmt'])."$errorStr";
	$page = ReadPage($pagename);
	$page['text'] .= $Str;
	WritePage($pagename, $page);
}

$HandleActions['setdef'] = 'Handlesetdef';
$HandleAuth['setdef'] = 'admin';

function Handlesetdef($pn,$auth = 'admin')
{
	global $EnableNotify,$FmtPV;
	$EnableNotify = 0;
	$page = RetrieveAuthPage($pn, $auth, true, READPAGE_CURRENT);
	if (!$page) Abort("?cannot source $pn");
	$new = $page;
	if ($_GET['Player']) {$new['DP_P0'.$_GET['PartEX']] = $_GET['Player'];}
	updatepage($pn,$page,$new);
	header("HTTP/1.1 301 Moved Permanently");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Location: ".PageVar($pn,'$PageUrl'));
	//HandleBrowse($pn);
}