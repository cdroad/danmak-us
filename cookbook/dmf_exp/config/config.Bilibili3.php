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
                         ->add('bi20130628', new Player('bi20130628_.swf', 'bilibili播放器(2013-06-28)', 950, 482))
                         ->addDefault('bi20130628');
        $this->VideoSourceSet->add('yk', new YouKuSource());
    }
    
    public function UploadFilePreProcess($str) {
        return simplexml_load_string($str);
    }
    
	public function GenerateFlashVarArr(VideoPageData $source)
	{
        $p = $source->Player;
		$playerParams = new FlashParams($p->playerUrl, $p->width, $p->height);
	    switch (strtoupper($source->VideoType->getType()))
	    {
	        case "NOR":
	            $playerParams->addVar('vid', $source->DanmakuId);
	        break;
	        
			case "QQ":
			case "TD":
			case "6CN":
			case "URL":
			case "BURL":
			case "LINK":
			case "BLINK":
			case "LOCAL":
                $playerParams->addVar('id'  , $source->DanmakuId);
                $playerParams->addVar('file', $source->VideoStr);
	        break;
	        
			case "YK":
				$playerParams->addVar('ykid', $source->DanmakuId);
	        break;
	        
			default:
				echo "$source->VideoType->getType(): $source->DanmakuId : $source->VideoStr";
				assert(false);
	        break;
	    }
		return $playerParams;
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