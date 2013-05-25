<?php if (!defined('PmWiki')) exit();
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
        $this->PlayersSet->add('bi20120427', new Player('bi20120427_DMF.swf', 'bilibili播放器(2012-04-27)', 950, 482))
                         ->add('bi20121024', new Player('bi20121024.swf', 'bilibili播放器(2012-10-24_org)', 950, 482))
                         ->add('bi20121203', new Player('bi20121203.swf', 'bilibili播放器(2012-12-03_org)', 950, 482))
                         ->add('bi20121226', new Player('bi20121226.swf', 'bilibili播放器(2012-12-26_org)', 950, 482))
                         ->addDefault('bi20121226');
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