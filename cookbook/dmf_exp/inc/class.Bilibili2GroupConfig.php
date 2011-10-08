<?php

class Bilibili2Config
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
	
	public static function ConvertToUniXML(SimpleXMLElemen $obj)
	{
		switch (strtolower($obj->getName()))
		{
			case "comments":
				return $obj;
			case "infomation":
				return self::ConvertFromDataFormat($obj);
			case "i":
				return self::ConvertFromIDForamt($obj);
			default:
				throw UnexpectedValueException();
		}
	}
	
	public static function ConvertFromDataFormat(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->data as $comment) {
			$attrs = array();
			$attrs['playtime']=$comment->playTime;
			$attrs['mode']=$comment->message->{"@mode"};
			$attrs['fontsize']=$comment->message->{"@fontsize"};
			$attrs['color']=$comment->message->{"@color"};
			$XMLString .= Utils::createCommentText($comment->message,0,'deafbeef',$attrs);
		}
		$XMLString .= "\r\n<comments>";
		return simplexml_load_file($XMLString);
	}

	public static function ConvertFromIDForamt(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->d as $comment) {
			$arr = explode(",", $comment->{"@p"});
			$attrs = array();
			
			$attrs['playtime'] = $arr[0];
			$attrs['mode'] = $arr[1];
			$attrs['fontsize'] = $arr[2];
			$attrs['color'] = $arr[3];
			
			$XMLString .= Utils::createCommentText($comment->d,0,'deafbeef',$attrs);
		}
		$XMLString .= "\r\n<comments>";
		return simplexml_load_file($XMLString);
	}
}

Bilibili2Config::$DanmakuBarSet = new DanmakuBarSet();
Bilibili2Config::$VideoSourceSet = $GLOBALS['VideoSourceSet'];
Bilibili2Config::$PlayersSet = $GLOBALS['PlayerSet'];



























