<?php if (!defined('PmWiki')) exit();
class Twodland1GroupConfig extends GroupConfig
{
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'Twodland1';
        $this->AllowedXMLFormat = array('comments', 'raw');
        $this->SUID = 'D';
        $this->XMLFolderPath = './uploads/Twodland1';
        $this->PlayersSet->add('2dl20111204', new Player('2dl20111204.swf', '2dland播放器(20111024)', 950, 512))
                ->addDefault('2dl20111204');
    }
    
    public function UploadFilePreProcess($str) {
        return simplexml_load_string($str);
    }
    
	public function GenerateFlashVarArr(VideoPageData $vdp)
	{
		$AFVArray = array();
		$AFVArray['dir'] = strtoupper($vdp->VideoType->getType());
		$AFVArray['vid'] = $vdp->DanmakuId;
        
        if (strtoupper($vdp->VideoType->getType()) == "NOR") {
            $type = 'sina';
            $part = "<vid>{$vdp->DanmakuId}</vid>";
        } else {
            $type = 'other';
            $url = urldecode($vdp->VideoStr);
            $part = "<url>$url</url>";
        }
        $contents = <<<CONT
<?xml version="1.0" encoding="UTF-8"?>
<parts>
  <part name="DMF本地版" smooth="1" type="$type">
    $part
  </part>
</parts>
CONT;
        $targetFile = './static/page/'.md5($vdp->DanmakuId).'.xml';
        file_put_contents($targetFile, $contents, LOCK_EX);
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
