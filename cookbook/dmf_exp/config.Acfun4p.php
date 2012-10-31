<?php
class Acfun4pGroupConfig extends GroupConfig
{
    
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'Acfun4p';
        $this->AllowedXMLFormat = array('json', 'raw', 'data');
        $this->SUID = 'A4P';
        $this->XMLFolderPath = './uploads/Acfun4p';
        $this->PlayersSet
                    ->add('ac201210171424', new Player('ACFlashPlayer.201210171424.swf', 'Acfun播放器 (2012-10-17)', 970, 480)) 
                    ->add('ac201209241900', new Player('ACFlashPlayer.old.201209241900.swf', 'Acfun播放器 (2012-09-24)', 950, 445)) 
                    ->addDefault('ac201210171424');
        
        $this->DanmakuBarSet->add(new DanmakuBarUploadXML());
        $this->DanmakuBarSet->add(new DanmakuBarDownloadXML());
        $this->DanmakuBarSet->add(new DanmakuBarNewLine());
        
        $groupA = new DanmakuBarGroup(DanmakuBarItem::$Auth->Member);
        $groupA->add(new DanmakuBarValPool());
        $groupA->add(new DanmakuBarEditPool());
        $groupA->add(new DanmakuBarEditPart);
        
        $this->DanmakuBarSet->add($groupA);
        $this->DanmakuBarSet->add(new DanmakuBarPoolMove());
        $this->DanmakuBarSet->add(new DanmakuBarPoolClear());
    }

    public function UploadFilePreProcess($str) {
        $test = simplexml_load_string($str);

        if ($test !== FALSE) {return $test;}
        if($str[0] == chr(0xef) && $str[1] == chr(0xbb) && $str[2] == chr(0xbf))
        {	// UTF-8 BOMを取り除く
            $str = substr($str, 3);
        }
        $json = json_decode($str);
        
        if (is_null($json)) return false;
        $xmlstr = '<?xml version="1.0" encoding="UTF-8"?><comments>';
        foreach ($json as $item) {
            $a = explode(",", $item->c);
            $danmaku = new DanmakuBuilder($item->m, 0, $a[4], $a[5]);
            $attrs = array(
                'playtime'  => $a[0],
                'color'     => $a[1],
                'mode'      => $a[2],
                'fontsize'  => $a[3]
            );
            $danmaku->AddAttr($attrs);
            $xmlstr .= (string)$danmaku;
        }
        $xmlstr .= '</comments>';
        
        $xml = simplexml_load_string($xmlstr);
        return $xml;
    }
    
	public function GenerateFlashVarArr(VideoPageData $source)
	{
		$AFVArray = array();
	    switch (strtoupper($source->VideoType->getType()))
	    {
	        case "NOR":
	            $AFVArray['vid'] = $source->DanmakuId;
	            $AFVArray['type'] = "sina";
	        break;
	        
			case "QQ":
			case "TD":
			case "6CN":
			case "URL":
			case "BURL":
			case "LINK":
			case "BLINK":
			case "LOCAL":
			case "YK":
	            //$AFVArray['url'] = $source->VideoStr;
	            $AFVArray['vid'] = PageVar($source->Pagename, '$Name');
	            $AFVArray['system'] = "Artemis";
	            $AFVArray['type'] = "url";
	        break;

			default:
				echo "$source->VideoType->getType(): $source->DanmakuId : $source->VideoStr";
				assert(false);
	        break;
	    }
		return $AFVArray;
	}
	
	public function ConvertToUniXML(SimpleXMLElement $obj)
	{
		switch (strtolower($obj->getName()))
		{
			case "comments":
				return $obj;
			case "information":
				return $this->ConvertFromDataFormat($obj);
            case "c":
                return $this->ConvertFromCLFormat($obj);
			default:
				throw new UnexpectedValueException();
		}
	}
	
	public function ConvertFromDataFormat(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->data as $comment) {
            $pool = 0;
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
	
	public function ConvertFromCLFormat(SimpleXMLElement $Obj)
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
    
    public function __get($name) {
        return $this->$name;
    }
    
    public static function GetInstance()
    {
        if (is_null(self::$Inst)) {
            self::$Inst = new self();
            return self::$Inst;
        } else {
            return self::$Inst;
        }
    }
    
}