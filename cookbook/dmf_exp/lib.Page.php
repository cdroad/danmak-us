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
	global $BVO, $VDO, $PCO, $CodeObjName;
	
	$BVO = new BaseVar($pagename);
	if ($BVO->getStat() == 'OK') {
		$VDO = new VideoData($BVO);
		$PCO = new PageCodes($VDO);
		$CodeObjName = 'PCO';
		DMF_SetUpPageMarkUp();
	}
}