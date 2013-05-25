<?php if (!defined('PmWiki')) exit();

class XMLConverter
{
    //unused
    public static function FromUniXML(SimpleXMLElement $xml, $format) {
    
    }
    
    // SimpleXMLElement -> SimpleXMLElement
    public static function ToUniXML(SimpleXMLElement $xml) {
        switch (strtolower($xml->getName())) {
            case "comments":
                //因为2dland根节点和目前DMF一样
                //是内部格式
                if (empty($xml->comment[0]->playTime)) {
                    return $obj;
                } else {
                    return self::FromCommentsFormat($xml);
                }
            case "information":
                return self::FromDataFormat($xml);
            case "c":
                return self::FromCLFormat($xml);
            case "i":
                return self::FromIDFormat($xml);
			default:
				throw new UnexpectedValueException("Can't find corresponding format coverter.");
        }
    }
    
    
    //2dland
    private static function FromCommentsFormat(SimpleXMLElement $Obj) {
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->comment as $comment) {
            $pool = 0;
            $time = (string)strtotime((string)$comment->sendTime);
			$danmaku = new DanmakuBuilder((string)$comment->message, $pool, 'deadbeef', $time);
            //var_dump($attrs);
            foreach ($comment->attributes() as $k =>$v) {
                $attrs[strtolower($k)] = $v;
            }
            
            $attrs['playtime'] = (string)$comment->playTime;
            unset($attrs['islocked']);
            $danmaku->AddAttr($attrs);
			$XMLString .= (string)$danmaku;
		}
		$XMLString .= "\r\n</comments>";
		return simplexml_load_string($XMLString);
	}
    
    //老Ac格式，Bilibili上传格式
    private static function FromDataFormat(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->data as $comment) {
            $pool = 1;
            if ($comment->message['mode'] == '8') $pool = 2;
			$danmaku = new DanmakuBuilder((string)$comment->message, $pool, 'deadbeef');
            $attrs = array(
                    'playtime'  => $comment->playTime,
                    'mode'      => $comment->message['mode'],
                    'fontsize'  => $comment->message['fontsize'],
                    'color'     => $comment->message['color']);
            $danmaku->AddAttr($attrs);
			$XMLString .= (string)$danmaku;
		}
		$XMLString .= "\r\n</comments>";
        
		return simplexml_load_string($XMLString);
	}
    
	public static function FromIDForamt(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->d as $comment) {
			$arr = explode(",", $comment['p']);
			
            $attrs = array(
                    'playtime'  => $arr[0],
                    'mode'      => $arr[1],
                    'fontsize'  => $arr[2],
                    'color'     => $arr[3],);
            $danmaku = new DanmakuBuilder((string)$comment, $arr[5], $arr[6]);
            $danmaku->AddAttr($attrs);
			$XMLString .= (string)$danmaku;
		}
		$XMLString .= "\r\n</comments>";
        
		return simplexml_load_string($XMLString);
	}
	
	
	//新ac
	public function FromCLFormat(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->l as $comment) {
            $pool = 0;
			$arrs = explode(",", $comment['i']);
            $attrs = array(
                'playtime'  => $arrs[0],
                'mode'      => $arrs[3],
                'fontsize'  => $arrs[1],
                'color'     => $arrs[2]);
            $text = (string)$comment;
            if ($arrs[3] == "7")
            { 
                $text = stripslashes($text);
            }
            $danmaku = new DanmakuBuilder($text, $pool, 'deadbeef');
            $danmaku->AddAttr($attrs);
			$XMLString .= (string)$danmaku;
		}
		$XMLString .= "\r\n</comments>";
		return simplexml_load_string($XMLString);
	}
}