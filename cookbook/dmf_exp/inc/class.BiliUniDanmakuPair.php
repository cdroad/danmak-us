<?php
/*
 * <comments>
 * 	TODO -> <infomation type="$TYPE" >
 * 	<comment id="$ID">
 * 		<text> </text>
 * 		<attrs>
 * 			<attr attr1="attr" .... > </attr>
 * 		</attrs>
 * 	</comment>
 * </comments> 
 */
class BiliUniDanmakuPair extends DanmakuPairBase
{
	protected $_XMLSpec;
	
	public function BiliUniDanmakuPair($dmid, $Pair = PAIR_ALL)
	{
		$this->_LogPage = 'Main/SysLog';
		parent::DanmakuPairBase('Bilibili2', $dmid, $Pair);
		$this->_XMLSpec = $this->_GC['SPEC'];
	}
	
	public function asXML($Pair, $Format)
	{
		$temp = $this->get($Pair);
		
		if ($Format == 'raw')
		{
			return $temp->asXML();
		}
		
		if ($Format == 'd')
		{
			$xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n".'<i>';
			$xml .= $this->_XMLSpec;
			//枚举所有弹幕
			$danmakuNodes = $temp->xpath("//comments/comment");
			foreach ($danmakuNodes as $node)
			{
				$text = "";
				$attrs = "";
				$dmid = "";
				$pstring = "";
				$attrs = "";
				
				$dmid = $node->attributes();
				$dmid = $dmid['id'];
				
				$text = htmlspecialchars($node->text,ENT_NOQUOTES,"UTF-8");
				$attrs = $node->attrs->attr[0]->attributes();
				
				$pstring  = "\"";
				$pstring .= $attrs['playtime'];
				$pstring .= ",".$attrs['mode'];
				$pstring .= ",".$attrs['fontsize'];
				$pstring .= ",".$attrs['color'];
				$pstring .= ",".$attrs['sendtime'];
				$pstring .= ",".$attrs['poolid'];
				$pstring .= ",".$attrs['userhash'];
				$pstring .= ",".$dmid;
				$pstring .= "\"";
				
				$xml .= "\t<d p=$pstring>$text</d>\r\n";
			}
			$xml .= "</i>";
			return $xml;
		}
		
		if ($Format == 'data')
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?><information>'."\r\n";
			$danmakuNodes = $temp->xpath("//comments/comment");
			foreach ($danmakuNodes as $node)
			{
				$text = "";
				$attrs = "";
				$dmid = "";
				$pstring = "";
				$attrs = "";
				
				$dmid = $node->attributes();
				$dmid = $dmid['id'];
				
				$text = htmlspecialchars($node->text,ENT_NOQUOTES,"UTF-8");
				$attrs = $node->attrs->attr[0]->attributes();
				
				$playtime = $attrs['playtime'];
				$fontsize = $attrs['fontsize'];
				$color = $attrs['color'];
				$mode = $attrs['mode'];
				
				$sendTime = date("Y-m-d H:i:s", intval($attrs['sendtime']));
				
				$xml .= <<<XMLDATAEND
<data>
	<playTime>$playtime</playTime>
	<message fontsize="$fontsize" color="$color" mode="$mode">$text</message>
	<times>$sendTime</times>
</data>

XMLDATAEND;
			}
			$xml .= '</information>';
			return $xml;
		}
	}
	
	protected function saveDynamic()
	{
		//此过程比较吃内存，必要情况下关闭页面历史功能
		//或者改变储存方式。
		$auth = 'edit';
		$new = $pp = RetrieveAuthPage(
			$this->_DFile,
			$auth, false, 0);
		
		$new['text'] = '';
		foreach ($this->_DPool->comment as $danmaku)
		{
			$new['text'] .= $danmaku->asXML()."\r\n";
		}
		
		UpdatePage($this->_DFile, $pp, $new);
	}
	
	protected function saveStatic()
	{
		if (file_exists($this->_SFile))
		{
			rename($this->_SFile, $this->_SFile.",del-".time());
		}
		$result = file_put_contents($this->_SFile,
			$this->_SPool->asXML(),
			LOCK_EX);
		if ($result == FALSE)
		{
			$str = "BiliUniDanmakuPair::saveStatic FAIL : COUND NOT WRITE TO STATIC POOL FILE.".
					" DMID = $this->_DMID; FILE = $this->_SFile";
			writeLog('Main/SysLog', $str);
		}
	}
	
	protected function deleteDynamic($info)
	{
		//假定$info一组弹幕ID的数组
		
		$targetId = explode(",", $info);
		$orStatement = implode('\' or @id=\'', $targetId);
		$xPathQuery = "//comments/comment[@id='$orStatement']";
		$result = $this->_DPool->xpath($xPathQuery);
		$resultCount = count($result);
		header("X-Debug: $xPathQuery.Matched $resultCount");
		for ($i = $resultCount; $i > $itemsNumber; $i--)
		{
			unset($this->_DPool->comment[$i-1][0]);
		}
		
	}
	
	protected function deleteStatic($info)
	{
		Abort("NOT SUPPORTED METHOD : deleteStatic");
	}

	protected function validateDynamic()
	{
		libxml_clear_errors();
		$this->loadDynamic();
		return libxml_get_errors();
	}
	
	protected function validateStatic()
	{
		libxml_clear_errors();
		$this->loadStatic();
		return libxml_get_errors();
	}

}