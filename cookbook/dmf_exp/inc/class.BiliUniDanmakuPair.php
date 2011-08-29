<?php

class BiliUniDanmakuPair extends DanmakuUniPairBase
{
	private $BilibiliChatString;
	
	public function BiliUniDanmakuPair($dmid, $pair = PAIR_ALL)
	{
		parent::__construct('Bilibili2', $dmid, $pair);
		$this->BilibiliChatString = $this->GroupConfig->XMLSPEC;
		
	}
	
	public function asXML($pair, $format)
	{
		$tempPool = $this->$pair;
		
		if ($format == 'raw')
		{
			return $tempPool->saveXML();
		}
		
		$danmakus = $this->find($pair, array());
		
		if ($format == 'd')
		{
			$xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<i>'."\n";
			$xml .= $this->BilibiliChatString;
			foreach ($danmakus as $node)
			{
				$attr = $node->attrs->attr[0]->attributes();
				
				$attrs = array();
				$attrs[] = $attr['playtime'];
				$attrs[] = $attr['mode'];
				$attrs[] = $attr['fontsize'];
				$attrs[] = $attr['color'];
				$attrs[] = $attr['sendtime'];
				$attrs[] = $attr['poolid'];
				$attrs[] = $attr['userhash'];
				$attrs[] = $node['id'];
				
				$usText = $node->text;
				$pString = implode(",", $attrs);
				$text = htmlspecialchars($usText, ENT_NOQUOTES, "UTF-8");
				$xml .= "\t<d p=\"$pString\">$text</d>\n";
			}
			$xml .= "</i>";
			return $xml;
		}
		
		if ($format == 'data')
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?><information>'."\n";
			
			foreach ($danmakus as $node)
			{
				$attr = $node->attrs->attr[0]->attributes();
				
				$dmid = $node['id'];
				
				$usText = $node->text;
				$text = htmlspecialchars($usText, ENT_NOQUOTES, "UTF-8");
				
				$playtime = $attr['playtime'];
				$fontsize = $attr['fontsize'];
				$color = $attr['color'];
				$mode = $attr['mode'];
				
				$sendTime = date("Y-m-d H:i:s", 
					intval($attr['sendtime']));
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
	
	protected function XMLFilter(SimpleXMLElement $node)
	{
		return $node;
	}
	
	protected function KVQueryToXPath($k, $v)
	{
		switch (strtoupper($k))
		{
			case "poolid":
				return "[//attrs/attr@poolid='$v']"; 
			case "userhash":
				return "[//attrs/attr@puserhash='$v']"; 
			default:
				return parent::KVQueryToXPath($k, $v);
		}
	}
}
