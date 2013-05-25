<?php if (!defined('PmWiki')) exit();
class Acfun2GroupConfig extends GroupConfig
{
    
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'Acfun2';
        $this->AllowedXMLFormat = array('data', 'raw');
        $this->SUID = 'A';
        $this->XMLFolderPath = './uploads/Acfun2';
        $this->PlayersSet
                    ->add('mukio', new Player('mukioplayer.swf', 'MukioPlayer (1.36web)', 950, 432))
                    ->add('acold09', new Player('player1_09.swf', 'Acfun播放器 (20090803)', 950, 432))
                    ->add('acold', new Player('player1_old.swf', 'Acfun播放器 (2010502)', 950, 432))
                    ->add('acnew', new Player('player1_new.swf', 'Acfun播放器 (2010711)', 950, 432))
                    ->add('ac20110209', new Player('player1_20110209.swf', 'Acfun播放器 (20110209)', 950, 432))
                    ->addDefault('mukio');
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
	            $AFVArray['id'] = $source->DanmakuId;
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