<?php

function DMF_RV($x)
{
	global $CodeObjName;

	$obj = $GLOBALS[$CodeObjName];
	$target = $x;
	return $obj->{"get$x"}();
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
		'DMF_RV("PartNoLinkCode")');

}

function DMF_URL_CONV_url($str)
{
	return rawurlencode($str);
}

function DMF_URL_CONV_burl($str)
{
	return rawurlencode('http://pl.bilibili.us/uploads/'.
		str_replace(array("level"),"",$str).
		'.flv');
}

function DMF_URL_CONV_link($str)
{
	return rawurlencode($str);
}

function DMF_URL_CONV_blink($str)
{
	return rawurlencode('http://pl.bilibili.us/'.
		str_replace(array("levelup"),"/",$str).
		'.flv');
}

function DMF_URL_CONV_qq($str)
{
	return rawurlencode('https://secure.bluehost.com/~twodland/dmf/index.php?n=Main.Flvcache&action=GetFlvUrl&vid='.$str);
}

function DMF_URL_CONV_6cn($str)
{
	Abort("UNCOMPLETE_DMF_JUMPER_FUNCTION");
}

function DMF_URL_CONV_td($str)
{
	Abort("UNCOMPLETE_DMF_JUMPER_FUNCTION");
}

function DMF_URL_CONV_local($str)
{
	return rawurlencode($str);
}

$HandleActions['xmlupload'] = "HandleXMLPost";
$HandleAuth['xmlupload'] = 'upload';

function HandleXMLPost($pagename, $auth = 'upload')
{
	global $DMF_GroupConfig;
	
	$XMLPostFunction = $DMF_GroupConfig[PageVar($pagename, '$Group')]['XMLPostFunction'];

	if (empty($XMLPostFunction) || !function_exists($XMLPostFunction))
		Abort("XML post handler not exist");
	
	$XMLPostFunction($pagename);
}
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

Markup("ObjInit", '_begin', "/\\(:ObjInit:\\)/e", 'ObjLoadFunc()');
function ObjLoadFunc()
{
	global $BVO, $VDO, $PCO, $CodeObjName;
	
	$BVO = new BaseVar($pagename);
	if ($BVO->getStat() == 'OK') {
		$VDO = new VideoData($BVO);
		$PCO = new PageCodes($VDO);
		$CodeObjName = 'PCO';
		DMF_SetUpPageMarkUp();
	}
}