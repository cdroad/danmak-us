<?php
class BiliNorDanmakuPair extends DanmakuPairBase
{
	protected $_XMLSpec;
	
	public function BiliNorDanmakuPair($dmid, $Pair = PAIR_ALL)
	{
		$this->_LogPage = 'Main/RecentDanmakuChanges';
		parent::DanmakuPairBase('Bilibili2', $dmid, $Pair);
		$this->_XMLSpec = simplexml_load_string($this->_GC['SPEC']);
	}
	
	public function asXML($Pair, $Format)
	{
		$temp = $this->get($Pair);
		
		$result = $temp->xpath("//i/chatserver | //i/chatid");
		foreach ($result as $node)
		{
			unset($node[0]);
		}
		simplexml_merge($temp, $this->_XMLSpec);
		
		if ($Format == 'd')
		{
			return $temp->asXML();
		}
		
		if ($Format == 'data')
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?><information>'."\r\n";
			$danmakuNodes = $temp->xpath("//i/d");
			foreach ($danmakuNodes as $node)
			{
				$attrArr = explode(",", $node->attributes());
				$T = $attrArr[0];
				$M = $attrArr[1];
				$FS = $attrArr[2];
				$CO = $attrArr[3];
				//2010-09-27 18:36:16
				$SENDT = date("Y-m-d H:i:s", $attrArr[4]);
				$Pool = $attrArr[5];
				$UID = $attrArr[6];
				$DMID = $attrArr[7];
				
				$TEXT = htmlspecialchars($node,ENT_NOQUOTES,"UTF-8");
				$xml .= <<<XMLDATAEND

<data>
	<playTime>$T</playTime>
	<message fontsize="$FS" color="$CO" mode="$M">$TEXT</message>
	<times>$SENDT</times>
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
		foreach ($this->_DPool->d as $danmaku)
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
			$str = "BiliNorDanmakuPair::saveStatic FAIL : COUND NOT WRITE TO STATIC POOL FILE.".
					" DMID = $this->_DMID; FILE = $this->_SFile";
			writeLog('Main/SysLog', $str);
		}
	}
	
	protected function deleteDynamic($info)
	{
		//假定$info一组弹幕ID的数组
		$itemsNumber = count($this->_DPool->d);
		for ($i = $itemsNumber; $i > $itemsNumber; $i--)
		{
			$attr = $danmaku->attributes();
			$attrs = explode(",", $attr['p']);
			$dmid = $attrs[7];
			
			if (in_array($dmid, $info))
			{
				unset($this->_DPool->d[$i - 1][0]);
			}
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