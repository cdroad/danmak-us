<?php
$playerCodeHeader = <<<STR
<script type="text/javascript">
var flashvars = {};
var params = {};
params.menu = "true";
params.allowscriptaccess = "always";
params.allowfullscreen = "true";
params.bgcolor = "#FFFFFF";
params.autostart = "false";
params.play = "false";
//params.scale = "noscale";
//params.wmode = "opaque";

STR;

function DMF_RV($x)
{
	global $VDN;

	return $VDN->$x;
}

function DMF_SetUpPageMarkUp()
{
	Markup("PlayerLoader", 'split',"/\\(:PlayerLoader:\\)/e",
		'keep(DMF_RV("PlayerLoadCode"))');
	Markup("DMF_Messages", '<split',"/\\(:DMFMessage:\\)/e",
		'DMF_RV("Messages")'); 
	Markup("DMBarLoader", '<split',"/\\(:DMBarLoader:\\)/e",
		'DMF_RV("DanmakuBarCode")'); 
	Markup("PlayerLinkLoader", '<inline',"/\\(:PlayerLinkLoader:\\)/e",
		'DMF_RV("PlayerLinkCode")'); 
	Markup("PartLinkLoader", 'split',"/\\(:PartLinkLoader:\\)/e",
		'DMF_RV("PartIndexCode")');

}

$HandleActions['xmlupload'] = "HandleXMLPost";
$HandleAuth['xmlupload'] = 'upload';

function HandleXMLPost($pagename, $auth = 'upload')
{
	global $GroupConfigSet;
	
	$G = PageVar($pagename, '$Group');
	$GC = $GroupConfigSet->$G;

	$dmid = basename($_POST['dmid']);
	$DMPair = ($_POST['Pool'] == 'S') ? PAIR_STATIC : PAIR_DYNAMIC ;
	$Append = (strtolower($_POST['Append']) == 'true') ? TRUE : FALSE ;
	
	if ($_FILES['uploadfile']['error'] != UPLOAD_ERR_OK)
	{
		$GLOBALS['MessagesFmt'] = "文件上传失败";
		HandleBrowse('API/XMLTool');
		return;
	}
	
	$xmldata = simplexml_load_file($_FILES['uploadfile']['tmp_name']);
	if ($xmldata === FALSE) 
	{
		$GLOBALS['MessagesFmt'] = "XML文件非法，拒绝上传请求";
		HandleBrowse('API/XMLTool');
		return;
	}
	
	$GC->HandleXMLPost($dmid, $DMPair, $Append, $xmldata);
	
	HandleBrowse($pagename);
}

Markup("ObjInit", '_begin', "/\\(:ObjInit:\\)/e", 'ObjLoadFunc()');
function ObjLoadFunc()
{
	global $VDN;
	$VDN = new VideoData($GLOBALS['pagename']);
	DMF_SetUpPageMarkUp();
}