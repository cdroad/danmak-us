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
	global $DMF_GroupConfig;
	
	$XMLPostFunction = $DMF_GroupConfig[PageVar($pagename, '$Group')]['XMLPostFunction'];

	if (empty($XMLPostFunction) || !function_exists($XMLPostFunction))
		Abort("XML post handler not exist");
	
	$XMLPostFunction($pagename);
}

Markup("ObjInit", '_begin', "/\\(:ObjInit:\\)/e", 'ObjLoadFunc()');
function ObjLoadFunc()
{
	global $VDN;
	$VDN = new VideoData($GLOBALS['pagename']);
	DMF_SetUpPageMarkUp();
}