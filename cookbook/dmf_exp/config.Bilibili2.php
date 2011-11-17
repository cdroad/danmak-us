<?php
//弹幕权限表
$BilibiliAuthLevel = new DefinedEnum( array
(
    'DefaultLevel' => '10000,1001',
	'Guest'	=> '0',
	'User'	=> '10000,1001',
	'Danmakuer' => '20000,1001'
));

class Bilibili2GroupConfig extends GroupConfig
{
    //是否允许代码弹幕(高级弹幕)
    private $BiliEnableSA = TRUE;
    
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'Bilibili2';
        $this->AllowedXMLFormat = array('d', 'data', 'raw');
        $this->SUID = 'B';
        $this->XMLFolderPath = './uploads/Bilibili2';
        $this->PlayersSet->add('bi20111102', new Player('bi20111102.swf', 'bilibili播放器(20111102)', 950, 482))
                    ->addDefault('bi20111102');
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
            $danmaku->AddAttr($comment->playTime, $comment->message['mode'],
                        $comment->message['fontsize'], $comment->message['color']);
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
			$danmaku = new DanmakuBuilder((string)$comment, $arr[5], 'deadbeef');
            $danmaku->AddAttr($arr[0], $arr[1], $arr[2], $arr[3]);
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