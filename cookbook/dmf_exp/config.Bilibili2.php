<?php
$PlayerSet = new PlayerSet();
$PlayerSet->add('bi20110712', new Player('bi20110712.swf', 'bilibili播放器(20110712)', 950, 482))
		  ->add('bi20110807', new Player('bi20110807.swf', 'bilibili播放器(20110807)', 950, 482))
		  ->addDefault(bi20110807);
$VideoSourceSet->add('yk', new YouKuSource());
 
$GroupConfigSet->add('bilibili2',new Bilibili2GroupConfig());

function ConvertBilibiliXML_d($XMLObj)
{
	$result = $XMLObj->xpath("//i/d");
	$xml = '<?xml version="1.0" encoding="utf-8"?>'."\r\n".'<comments>'."\r\n";
	foreach ($result as $node)
		{
			$xml .= ConvertBilibiliXML_dNode($node);
		}
		$xml .= "</comments>";
		
	return simplexml_load_string($xml);
}

function ConvertBilibiliXML_dD($XMLObj)
{
	$result = $XMLObj->xpath("//comments/d");
	$xml = '<?xml version="1.0" encoding="utf-8"?>'."\r\n".'<comments>'."\r\n";
	foreach ($result as $node)
		{
			$xml .= ConvertBilibiliXML_dNode($node);
		}
		$xml .= "</comments>";
		
	return simplexml_load_string($xml);
}

function ConvertBilibiliXML_dNode($node)
{
		$attrArr = explode(",", $node->attributes());
		$T = $attrArr[0];
		$M = $attrArr[1];
		$FS = $attrArr[2];
		$CO = $attrArr[3];
		$SENDT = $attrArr[4];
		$Pool = $attrArr[5];
		$UID = $attrArr[6];
		if (is_null($UID))
			$UID = 'DEADBEEF';
		$DMID = $attrArr[7];
		if (is_null($DMID))
			$DMID = intval("0x".strtolower(substr(md5(mt_rand()),0,8)), 16);
		
		
		$TEXT = htmlspecialchars($node,ENT_NOQUOTES,"UTF-8");
		$xml  = "\t<comment id=\"$DMID\">\r\n";
		$xml .= "\t\t<text>$TEXT</text>\r\n";
		$xml .= "\t\t<attrs>\r\n\t\t\t<attr ";
		$xml .= "playtime=\"$T\" mode=\"$M\" fontsize=\"$FS\" color=\"$CO\" sendtime=\"$SENDT\" ";
		$xml .= "poolid=\"$Pool\" userhash=\"$UID\"></attr>\r\n";
		$xml .= "\t\t</attrs>\r\n";
		$xml .= "\t</comment>\r\n";
		return $xml;
}
