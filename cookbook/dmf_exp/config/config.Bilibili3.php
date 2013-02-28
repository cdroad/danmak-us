<?php if (!defined('PmWiki')) exit();
class Bilibili3GroupConfig extends GroupConfig
{
    //是否允许代码弹幕(高级弹幕)
    private $BiliEnableSA = TRUE;
    
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'Bilibili2';
        $this->AllowedXMLFormat = array('d', 'data', 'raw');
        $this->SUID = '3B';
        $this->XMLFolderPath = './uploads/Bilibili3';
        $this->PlayersSet->add('bi20130124', new Player('bi20130124.swf', 'bilibili播放器(2013-01-24)', 950, 482))
                         ->addDefault('bi20130124');
        $this->VideoSourceSet->add('yk', new YouKuSource());
        
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
        return simplexml_load_string($str);
    }
    
	public function GenerateFlashVarArr(VideoPageData $source)
	{
		$AFVArray = array();
		
	    switch (strtoupper($source->VideoType->getType()))
	    {
	        case "NOR":
	            $AFVArray['vid'] = $source->DanmakuId;
	        break;
	        
			case "QQ":
			case "TD":
			case "6CN":
			case "URL":
			case "BURL":
			case "LINK":
			case "BLINK":
			case "LOCAL":
				$AFVArray['id'] = $source->DanmakuId;
				$AFVArray['file'] = $source->VideoStr;
	        break;
	        
			case "YK":
				$AFVArray['ykid'] = $source->DanmakuId;
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
			case "i":
				return $this->ConvertFromIDForamt($obj);
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

	public function ConvertFromIDForamt(SimpleXMLElement $Obj)
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