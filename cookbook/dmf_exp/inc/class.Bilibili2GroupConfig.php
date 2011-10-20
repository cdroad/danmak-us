<?php

class Bilibili2GroupConfig
{
	public static $SUID = 'B';
	public static $XMLFolderPath = './uploads/Bilibili2';
	public static $DanmakuBarSet;
	public static $VideoSourceSet;
	public static $PlayersSet;

	public static function GenerateFlashVarArr(VideoData $source)
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
	
	public static function ConvertToUniXML(SimpleXMLElement $obj)
	{
		switch (strtolower($obj->getName()))
		{
			case "comments":
				return $obj;
			case "information":
				return self::ConvertFromDataFormat($obj);
			case "i":
				return self::ConvertFromIDForamt($obj);
			default:
				throw new UnexpectedValueException();
		}
	}
	
	public static function ConvertFromDataFormat(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->data as $comment) {
            $pool = 1;
            if ($comment->message['mode'] == '8') $pool = 2;
			$danmaku = new DanmakuBuilder((string)$comment->message, $pool, 'deadbeef');
            $danmaku->AddAttr($comment->playTime, $comment->message['mode'],
                        $comment->message['fontsize'], $comment->message['color']);
			$XMLString .= (string)$danmaku;
		}
		$XMLString .= "\r\n</comments>";
        
		return simplexml_load_string($XMLString);
	}

	public static function ConvertFromIDForamt(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->d as $comment) {
			$arr = explode(",", $comment['p']);
			$danmaku = new DanmakuBuilder((string)$comment, $arr[5], 'deadbeef');
            $danmaku->AddAttr($arr[0], $arr[1], $arr[2], $arr[3]);
			$XMLString .= (string)$danmaku;
		}
		$XMLString .= "\r\n</comments>";
        
		return simplexml_load_string($XMLString);
	}
}

Bilibili2GroupConfig::$DanmakuBarSet = new DanmakuBarSet();
Bilibili2GroupConfig::$VideoSourceSet = $GLOBALS['VideoSourceSet'];
Bilibili2GroupConfig::$PlayersSet = $GLOBALS['PlayerSet'];



























