<?php
class Bilibili2GroupConfig extends GroupConfigBase
{
	public function __construct()
	{
		parent::GroupConfigBase();
		$this->SUID = 'B';
		$this->XMLFolderPath = './uploads/Bilibili2';
	}
	
	public function GenerateFlashVarArr(VideoData $source)
	{
		$AFVArray = array();
	    switch (strtoupper($source->sourcetype->getType()))
	    {
	        case "NOR":
	            $AFVArray['vid'] = $source->dmid;
	        break;
	        
			case "QQ":
			case "TD":
			case "6CN":
			case "URL":
			case "BURL":
			case "LINK":
			case "BLINK":
			case "LOCAL":
				$AFVArray['id'] = $source->dmid;
				$AFVArray['file'] = $source->sourcetype->source;
	        break;
	        
			case "YK":
				$AFVArray['ykid'] = $source->dmid;
	        break;
	        
			default:
				echo "$source->sourcetype->getType(): $source->dmid : $source->sourcetype->source";
				assert(false);
	        break;
	    }
		return $AFVArray;
	}

	public function HandleXMLPost($group, $dmid, $pair, $Append, $File)
	{
		//TODO:TEST
		/*------>上级搞定
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
		*/
		
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

	public function doPoolConv($id, $PoolString)
	{
		$Pair = new BiliUniDanmakuPair($id, PAIR_ALL);
		$Pair->move($PoolString);
		$Pair->save(PAIR_ALL);
	}
	
	public function doXMLLoad($id)
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
	
	public function doValidate($id,$pair)
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
}