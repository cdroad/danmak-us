<?php
class AcfunN1GroupConfig extends GroupConfig
{
    
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'AcfunN1';
        $this->AllowedXMLFormat = array('json', 'raw', 'data');
        $this->SUID = 'AN';
        $this->XMLFolderPath = './uploads/AcfunN1';
        $this->PlayersSet
                    ->add('ac20111115', new Player('ac20111115.swf', 'Acfun播放器 (20111116)', 950, 432)) //TODO:大小未知
                    ->addDefault('ac20111115');
        
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
        return simplexml_load_string($xmlstr);
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
	
	public function ConvertToUniXML(SimpleXMLElement $obj)
	{
		switch (strtolower($obj->getName()))
		{
			case "comments":
				return $obj;
			case "information":
				return $this->ConvertFromDataFormat($obj);
			default:
				throw new UnexpectedValueException();
		}
	}
	
	public function ConvertFromDataFormat(SimpleXMLElement $Obj)
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