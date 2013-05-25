<?php if (!defined('PmWiki')) exit();
class Bilibili3GroupConfig extends GroupConfig
{
    //是否允许代码弹幕(高级弹幕)
    private $BiliEnableSA = TRUE;
    
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'Bilibili3';
        $this->AllowedXMLFormat = array('d', 'data', 'raw');
        $this->SUID = '3B';
        $this->XMLFolderPath = './uploads/Bilibili3';
        $this->PlayersSet->add('bi20130124', new Player('bi20130124.swf', 'bilibili播放器(2013-01-24)', 950, 482))
                         ->addDefault('bi20130124');
        $this->VideoSourceSet->add('yk', new YouKuSource());
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