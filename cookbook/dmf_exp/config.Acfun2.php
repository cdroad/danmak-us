<?php
SDVA($DMF_GroupConfig['Acfun2'],
array
	(
		'DefaultPlayer'   => 'mukio',
		'AFVFunction'	  => 'Acfun2AFV',
		'SUID'			  => 'A',
		'XMLPostFunction' => 'Acfun2XMLPost',
		'XMLFolderPath'   => './uploads/Acfun2',
		'XMLHeader'		  => '<?xml version="1.0" encoding="UTF-8"?><infomation>',
		'XMLFooter'		  => '</infomation>',
		'XMLError'		  => '<?xml version="1.0" encoding="UTF-8"?><infomation>'.
			'<data><playTime>1.1</playTime><message fontsize="25" color="16777215" mode="1">'.
			'警告 : DanmakuPairBase :: Load() 无法加载弹幕池，请运行XML校验</message>'.
			'<times>2010-06-16 17:03:25</times></data></infomation>',
	)
);

SDVA($DMF_GroupConfig['Acfun2']['DanmakuBarConfig'],
array
	(
		'Guest' => array
		(
			0 => '%newwin% [[{*$host}/newflvplayer/xmldata/{*$DMID}/?format=raw&cmd=download&r='.mt_rand().'|下载XML]] %%'
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
			
			2 => '<br />%newwin danmakubar%XML:&nbsp;',
			
			4 => '[[{*$host}/newflvplayer/xmldata/{*$DMID}/?format=raw&cmd=download&r='.mt_rand().'|下载XML(raw)]]&nbsp;&nbsp;',		
	
			5 => '[[{*$host}/API/XMLTool?action=Validate&'.
				'id={*$DMID}&group=acfun&r='.mt_rand().'|验证]]&nbsp;&nbsp;',
				
			99 => '%%'
	
		),
		
		'Super' => array
		(
			2 => '%danmakubar%(:input form target="_blank" enctype="multipart/form-data" "{*$host}/API/XMLTool" :)'.
				'弹幕池移动 (:input hidden action PoolConv:)'.
				'(:input hidden dmid value="{$DMID}" :)'.
				'(:input hidden group value="{$Group}" :)'.
				'(:input select name=Pool value=DS label="D->S" :)'.
				'(:input select name=Pool value=SD label="S->D" :)'.
				'(:input submit value="Go":)'.
				'(:input end:)%%&nbsp;&nbsp;',
			
			7 => '[[DMR/B{*$DMID}?action=edit|动态池编辑]]&nbsp;&nbsp;'
		)
	)
);

SDVA($DMF_GroupConfig['Acfun2']['Players'],
array
	(
		'mukio'		=> array
		(
			'show'		=> 'true',
			'url'		=> $PlayerBaseUrl.'mukioplayer.swf',
			'desc'		=> 'MukioPlayer (1.36web)',
			'width'		=> '950',
			'height'	=> '432',
		),
	
		'acold09'	=> array
		(
			'show'		=> 'true',
			'url'		=> $PlayerBaseUrl.'player1_09.swf',
			'urlf'		=> $PlayerBaseUrl.'playerf_09.swf',
			'desc'		=> 'Acfun播放器 (20090803)',
			'width'		=> '950',
			'height'	=> '432',
		),
	
		'acold'		=> array
		(
			'show'		=> 'false',
			'url'		=> $PlayerBaseUrl.'player1_old.swf',
			'urlf'		=> $PlayerBaseUrl.'playerf_old.swf',
			'desc'		=> 'Acfun播放器 (2010502)',
			'width'		=> '950',
			'height'	=> '432',
		),
	
		'acnew'		=> array
		(
			'show'		=> 'true',
			'url'		=> $PlayerBaseUrl.'player1_new.swf',
			'desc'		=> 'Acfun播放器 (2010711)',
			'width'		=> '950',
			'height'	=> '432',
		),
	
		'ac20110209'=> array
		(
			'show'		=> 'false',
			'url'		=> $PlayerBaseUrl.'player1_20110209.swf',
			'desc'		=> 'Acfun播放器 (20110209)',
			'width'		=> '950',
			'height'	=> '432',
		)
	)
);

SDVA($DMF_GroupConfig['Acfun2']['VideoSourceConfig'],
array
	(
		'nor' => array
		(
			'MutiAble' => true
		),
		
		#'td' => array
		#(
		#	'MutiAble' => true,
		#	'URLConvert' => true
		#),
		
		'qq' => array
		(
			'MutiAble' => true,
			'URLConvert' => true
		),
		
		#'6cn' => array
		#(
		#	'MutiAble' => true,
		#	'URLConvert' => true
		#),
		
		'url' => array
		(
			'PageNameAsDanmakuId' => true,
			'MutiAble' => true,
			'URLConvert' => true
		),
		
		#'local' => array
		#(
		#	'PageNameAsDanmakuId' => true,
		#	'MutiAble' => true,
		#	'URLConvert' => true
		#),
		
		'link' => array
		(
			'MutiAble' => true,
			'PageNameAsDanmakuId' => true,
			'URLConvert' => true
		),
		
		#'burl' => array
		#(
		#	'MutiAble' => true,
		#	'PageNameAsDanmakuId' => false,
		#	'URLConvert' => true
		#),
		
		#'blink' => array
		#(
		#	'MutiAble' => true,
		#	'PageNameAsDanmakuId' => false,
		#	'URLConvert' => true
		#),
		
		#'yk' => array
		#(
		#	'MutiAble' => false,
		#	'PageNameAsDanmakuId' => false,
		#	'URLConvert' => false
		#),
		
	)
);

function Acfun2AFV($type, $dmid, $url)
{
	$AFVArray = array();
	#Abort("INCOMPLETE_FUNCTION_AcfunAFV");
	switch ($type)
	{
		case "nor":
			$AFVArray['id'] = $dmid;
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

function Acfun2XMLPost($pagename, $auth='upload')
{
	$GC = &$GLOBALS['DMF_GroupConfig']['Acfun2'];
	
	$dmid = basename($_POST['dmid']);
	$DMPair = ($_POST['Pool'] == 'S') ? PAIR_STATIC : PAIR_DYNAMIC ;
	$Append = (strtolower($_POST['Append']) == 'true') ? TRUE : FALSE ;
	
	if ($_FILES['uploadfile']['error'] != UPLOAD_ERR_OK)
	{
		$GLOBALS['MessagesFmt'] = "文件上传失败";
		HandleBrowse('API/XMLTool');
	}
	
	$xmldata = simplexml_load_file($_FILES['uploadfile']['tmp_name']);

	if ($xmldata === FALSE) 
	{
		$GLOBALS['MessagesFmt'] = "XML文件非法，拒绝上传请求";
		HandleBrowse('API/XMLTool');
	}
	
	$Pair = new AcfunUniDanmakuPair_Data($dmid, $DMPair);
	
	if ($Append)
	{
		$Pair->append($DMPair, $xmldata);
	} else {
		$Pair->set($DMPair, $xmldata);
	}
	
	//清理变量以释放空间。
	unset($xmldata);
	$Pair->save($DMPair);
	HandleBrowse($pagename);
}

function doPoolConv_Acfun2($id, $PoolString)
{
	Abort("Not supported function :: doPoolConv()");
	$Pair = new AcfunUniDanmakuPair_Data($id, PAIR_ALL);
	$Pair->move($PoolString);
	$Pair->save(PAIR_ALL);
}

function doXMLLoad_Acfun2($id)
{
	$Pair = new AcfunUniDanmakuPair_Data($id, PAIR_STATIC);
	echo($Pair->asXML(PAIR_STATIC, $_GET['format']));
}

function doValidate_Acfun2($id,$pair)
{
	global $MessagesFmt;

	$MessagesFmt = "<div> 开始对Acfun2 :: DMID:<b>$id</b> ->  <b>PAIR_ALL</b>  进行XML验证 </div>";
	$Pair = new AcfunUniDanmakuPair_Data($id, PAIR_NONE);
	
	$MessagesFmt .= "<hr /><div> 验证静态池 </div>";
	$MessagesFmt .= $Pair->validate(PAIR_STATIC);
	
	#$MessagesFmt .= "<hr /><div> 验证动态池 </div>";
	#$MessagesFmt .= $Pair->validate(PAIR_DYNAMIC);
	
	$MessagesFmt .= "<hr />验证过程结束。";
}

function ConvertAcfunXML_Data($XMLObj)
{
	$result = $XMLObj->xpath("//information/data");
	$xml = '<?xml version="1.0" encoding="utf-8"?>'."\r\n".'<comments>'."\r\n";
	foreach ($result as $node)
		{
			$xml .= ConvertAcfunXML_DataNode($node);
		}
		$xml .= "</comments>";
	return simplexml_load_string($xml);
}

function ConvertAcfunXML_DataNode($danmaku)
{
	$TEXT  = htmlspecialchars($danmaku->message, ENT_COMPAT, "UTF-8");
	$playtime  = $danmaku->playTime;
	$sendtime = time();
	$attr = $danmaku->message->attributes();
	$fontS = $attr["fontsize"];
	$color = $attr["color"];
	$mode  = $attr["mode"];

	$XMLString .= "\t<comment id=\"$DMID\">\r\n";
	$XMLString .= "\t\t<text>$TEXT</text>\r\n";
	$XMLString .= "\t\t<attrs>\r\n\t\t\t<attr ";
	$XMLString .= "playtime=\"$playtime\" mode=\"$mode\" fontsize=\"$fontS\" color=\"$color\" sendtime=\"$sendtime\" ";
	$XMLString .= "></attr>\r\n";
	$XMLString .= "\t\t</attrs>\r\n";
	$XMLString .= "\t</comment>\r\n";
	
	return $XMLString;
}