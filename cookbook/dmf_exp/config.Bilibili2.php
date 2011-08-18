<?php
SDVA($DMF_GroupConfig['Bilibili2'],
array
	(
		'DefaultPlayer' => 'bi20110807',
		'AFVFunction'	=> 'BilibiliAFV',
		'SUID'			=> 'B',
		
		'XMLPostFunction' => 'BiliXMLPost',
		'XMLFolderPath'	  => './uploads/Bilibili2',
		'XMLHeader' => '<?xml version="1.0" encoding="UTF-8"?>'."\r\n".'<comments>',
		'XMLFooter' => '</comments>',
		'SPEC'		=> "\r\n\t".'<chatserver>localhost</chatserver>'."\r\n\t".
			'<chatid>11111</chatid>'."\r\n",
		'XMLError'	=> '<?xml version="1.0" encoding="UTF-8"?>'.
			'<comments><comment id="DEADBEEF"><text>'.
			'警告 : DanmakuPairBase :: Load() 无法加载弹幕池，请运行XML校验</text><attrs>'.
			'<attr playtime="1" mode="1" fontsize="25" color="16777215" '.
			'sendtime="1311660679" poolid="0" userhash="DEADBEEF"'.
			'/></attrs></comment></comments>',
	)
);

SDVA($DMF_GroupConfig['Bilibili2']['DanmakuBarConfig'],
array
	(
		'Guest' => array
		(
			0 => '%newwin% [[{*$host}/dm,{*$DMID}?format=data&cmd=download&r='.mt_rand().'|下载XML]] %%'
		),

		'Authed' => array
		(
			0 => '%danmakubar%(:input form enctype="multipart/form-data" "{*$PageUrl}" :)'.
				'(:input hidden action xmlupload:)(:input file uploadfile:)'.
				'(:input hidden dmid value="{$DMID}" :)'.
				'弹幕池:(:input select name=Pool value=S label=静态 :)'.
				'(:input select name=Pool value=D label=动态 :)'.
				'追加:(:input checkbox Append value=true:)'.
				'(:input submit post Upload value="上传":)'.
				'(:input end:)%%&nbsp;&nbsp;',
			
			1 => '(:if2 equal "{*$IsMuti}" "true" :)[[{*$FullName}?action=edit | 编辑Part]]&nbsp;&nbsp;(:if2end:)',
			
			4 => '<br />',
			
			5 => '%newwin danmakubar%XML:&nbsp;&nbsp;',
				
	
			6 => '[[{*$host}/API/XMLTool?action=Validate&'.
				'dmid={*$DMID}&group=Bilibili2&r='.mt_rand().'|验证XML]]&nbsp;&nbsp;',
			
			7 => '(:input form "{*$host}/dm,{*$DMID}" method=get:)(:input hidden r {(ftime fmt="%s")}:)'.
				'下载格式：(:input select name=format value=data label=data :)'.
				'(:input select name=format value=d label=d :)'.
				'(:input select name=format value=raw label=comment :)'.
				'附件：(:input checkbox name=cmd value=download checked:)(:input submit value="下载":)'.
				'(:input end:)&nbsp;&nbsp;',	
			
			99 => '%%'
	
		),
		
		'Super' => array
		(
			2 => '%danmakubar%(:input form target="_blank" enctype="multipart/form-data" "{*$host}/API/XMLTool" :)'.
				'弹幕池移动 (:input hidden action PoolConv:)'.
				'(:input hidden dmid value="{$DMID}" :)'.
				'(:input hidden group value="{$Group}" :)'.
				'(:input select name=Pool value=DS label="动态->静态" :)'.
				'(:input select name=Pool value=SD label="静态->动态" :)'.
				'(:input submit value="Go":)'.
				'(:input end:)%%&nbsp;&nbsp;',
				
			5 => '%danmakubar%(:input form target="_blank" enctype="multipart/form-data" "{*$host}/API/XMLTool" :)'.
				'清空弹幕池 (:input hidden action PoolClear:)'.
				'(:input hidden dmid value="{$DMID}" :)'.
				'(:input hidden group value="{$Group}" :)'.
				'(:input select name=Pool value=S label="静态池" :)'.
				'(:input select name=Pool value=D label="动态池" :)'.
				'(:input select name=Pool value=ALL label="双规" :)'.
				'(:input submit value="Go":)'.
				'(:input end:)%%&nbsp;&nbsp;',
				
			7 => '[[DMR/B{*$DMID}?action=edit|动态池编辑]]&nbsp;&nbsp;'
		)
	)
);

SDVA($DMF_GroupConfig['Bilibili2']['Players'],
array
	(
		'bi20110712' =>	 array
		(
			'url'	    => $PlayerBaseUrl."bi20110712.swf",
			'desc'	    => 'bilibili播放器(20110712)_DEV',
			'width'     => '950',
			'height'    => '482'
		),
		
		'bi20110807' =>	 array
		(
			'url'	    => $PlayerBaseUrl."bi20110807.swf",
			'desc'	    => 'bilibili播放器(20110807)_DEV',
			'width'     => '950',
			'height'    => '482'
		),
		
	)
);

SDVA($DMF_GroupConfig['Bilibili2']['VideoSourceConfig'],
array
	(
		'nor' => array
		(
			'MutiAble' => true
		),
		
		'td' => array
		(
			'MutiAble' => true,
			'URLConvert' => true
		),
		
		'qq' => array
		(
			'MutiAble' => true,
			'URLConvert' => true
		),
		
		'6cn' => array
		(
			'MutiAble' => true,
			'URLConvert' => true
		),
		
		'url' => array
		(
			'PageNameAsDanmakuId' => true,
			'MutiAble' => true,
			'URLConvert' => true
		),
		
		'local' => array
		(
			'PageNameAsDanmakuId' => true,
			'MutiAble' => true,
			'URLConvert' => true
		),
		
		'link' => array
		(
			'MutiAble' => true,
			'PageNameAsDanmakuId' => true,
			'URLConvert' => true
		),
		
		'burl' => array
		(
			'MutiAble' => true,
			'PageNameAsDanmakuId' => false,
			'URLConvert' => true
		),
		
		'blink' => array
		(
			'MutiAble' => true,
			'PageNameAsDanmakuId' => false,
			'URLConvert' => true
		),
		
		'yk' => array
		(
			'MutiAble' => false,
			'PageNameAsDanmakuId' => false,
			'URLConvert' => false
		),
		
	)
);

function BilibiliAFV($type, $dmid, $url)
{
	$AFVArray = array();
	#Abort("INCOMPLETE_FUNCTION_BilibiliAFV");
    switch ($type)
    {
        case "nor":
            $AFVArray['vid'] = $dmid;
        break;
        
		case "qq":
		case "td":
		case "6cn":
		case "url":
		case "burl":
		case "link":
		case "blink":
		case "local":
			$AFVArray['id'] = $dmid;
			$AFVArray['file'] = $url;
        break;
        
		case "yk":
			$AFVArray['ykid'] = $dmid;
        break;
        
		default:
			echo "$type : $dmid : $url";
			assert(false);
        break;
    }
	return $AFVArray;
}

function BiliXMLPost($pagename, $auth='upload')
{
	$GC = &$GLOBALS['DMF_GroupConfig']['Bilibili2'];
	
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
	
	$XMLString = $GC['XMLHeader']."\r\n";

	//统一转换为UniXML(<comment>)格式
	foreach ($xmldata->comment as $danmaku)
	{
		$XMLString .= $danmaku->asXML();
	}
	
	//转换d->UniXML
	$dResult = $xmldata->xpath("//i/d");
	foreach ($dResult as $node)
		{
			$XMLString .= ConvertBilibiliXML_dNode($node);
		}
	
	
	//转换data->UniXML
	$sendtime = time();
	if ($DMPair == POOL_STATIC) {
		$TargetPool = "1";
	} else {
		$TargetPool = "0";
	}
	
	foreach ($xmldata->data as $danmaku)
	{
		$TEXT  = htmlspecialchars($danmaku->message, ENT_COMPAT, "UTF-8");
		$playtime  = $danmaku->playTime;
		$sendtime++;
		$attr = $danmaku->message->attributes();
		$fontS = $attr["fontsize"];
		$color = $attr["color"];
		$mode  = $attr["mode"];
		$DMID = intval("0x".strtolower(substr(md5(mt_rand()),0,8)), 16);
		$UID = 'DEADBEEF';

		$XMLString .= "\t<comment id=\"$DMID\">\r\n";
		$XMLString .= "\t\t<text>$TEXT</text>\r\n";
		$XMLString .= "\t\t<attrs>\r\n\t\t\t<attr ";
		$XMLString .= "playtime=\"$playtime\" mode=\"$mode\" fontsize=\"$fontS\" color=\"$color\" sendtime=\"$sendtime\" ";
		$XMLString .= "poolid=\"$TargetPool\" userhash=\"$UID\"></attr>\r\n";
		$XMLString .= "\t\t</attrs>\r\n";
		$XMLString .= "\t</comment>\r\n";
	}
	$XMLString .= $GC['XMLFooter'];
	
	unset($xmldata);

	$XMLObj = simplexml_load_string($XMLString);
	if ($XMLObj === FALSE) {
		$Error = libxml_get_errors();
		var_dump($Error);
		Abort("内部错误 :: XML格式统一失败.");
	}
	
	$Pair = new BiliUniDanmakuPair($dmid, $DMPair);
	
	if ($Append)
	{
		$Pair->append($DMPair, $XMLObj);
	} else {
		$Pair->set($DMPair, $XMLObj);
	}
	$Pair->save($DMPair);
	
	HandleBrowse($pagename);
}

function doPoolConv_Bilibili2($id, $PoolString)
{
	$Pair = new BiliUniDanmakuPair($id, PAIR_ALL);
	$Pair->move($PoolString);
	$Pair->save(PAIR_ALL);
}

function doXMLLoad_Bilibili2($id)
{
	$Pair = new BiliUniDanmakuPair($id, PAIR_ALL);

	if ($_GET['format'] == "data")
	{
		$Format = 'data';
	} else if ($_GET['format'] == "raw")
	{
		$Format = 'raw';
	} else {
		$Format = 'd';
	}
	echo($Pair->asXML(PAIR_ALL, $Format));
}

function doValidate_Bilibili2($id,$pair)
{
	global $MessagesFmt;

	$MessagesFmt = "<div> 开始对Bilibili2 :: DMID:<b>$id</b> ->  <b>PAIR_ALL</b>  进行XML验证 </div>";
	$Pair = new BiliUniDanmakuPair($id, PAIR_NONE);
	
	$MessagesFmt .= "<hr /><div> 验证静态池 </div>";
	$MessagesFmt .= $Pair->validate(PAIR_STATIC);
	
	$MessagesFmt .= "<hr /><div> 验证动态池 </div>";
	$MessagesFmt .= $Pair->validate(PAIR_DYNAMIC);
	
	$MessagesFmt .= "<hr />验证过程结束。";
}

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
