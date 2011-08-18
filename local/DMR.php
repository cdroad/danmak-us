<?php if (!defined('PmWiki')) exit();
$EnableNotify = 0;
$EnablePostAuthorRequired = 0;

$HandleActions['Format'] = "HandleFormat";
$HandleAuth['Format'] = 'read';


function HandleFormat($pagename, $auth = 'edit')
{
	libxml_use_internal_errors(true);
	$page = @RetrieveAuthPage($pagename, 'ALWAYS', true, READPAGE_CURRENT);
	$xml = "<i>".$page['text']."</i>";
	$xmlobj = simplexml_load_string($xml);
	
	if (!$xmlobj)
	{
		foreach (libxml_get_errors() as $error)
		{
			$MessagesFmt .= display_xml_error($error,$XML);
		}
	} else {
		foreach ($xmlobj->d as $danmaku)
		{
			$attributes = $danmaku->attributes();
			$danmaku = str_replace("/n","|<br />",$danmaku);
			echo "<br />ATTR:<br />".$attributes['p'].'<br />TEXT:<br /><div style="font-family:simhei;font-size:18px;">'.$danmaku."</div>";
		}
	}
}

array_unshift($EditFunctions,'VXML');
include_once("./cookbook/dmf_exp/lib.XML.php");
$PageEditForm = 'DMR.EditForm';
function VXML($pagename,&$page,&$new)
{
	global $Now, $EnablePost, $MessagesFmt, $WorkDir;

	$SimXMLHeader = '<?xml version="1.0" encoding="UTF-8"?><comments>';
	$SimXMLFooter = '</comments>';
	
	if ($new['text'] == '') return;
	
	$test = simplexml_load_string($SimXMLHeader.$new['text'].$SimXMLFooter);
	if ($test === FALSE)
	{
		$ec = '文档已被保存，但检测到错误：<br />';
		foreach (libxml_get_errors() as $e)
		{
			$ec .= get_xml_error($e, $test);
		}
		$MessagesFmt = "<p class='editconflict'>$ec
			</p>\n";
		//$new['text'] = $page['text'];
	}
	return;
}