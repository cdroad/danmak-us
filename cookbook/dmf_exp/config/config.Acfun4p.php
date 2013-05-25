<?php if (!defined('PmWiki')) exit();
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
                    ->add('ac20130118debug', new Player('ac20130118debug.swf', 'Acfun播放器 测试版 (2013-01-18)', 950, 445)) 
                    ->add('ac201210171424', new Player('ACFlashPlayer.201210171424.swf', 'Acfun播放器 (2012-10-17)', 970, 480)) 
                    ->add('ac201209241900', new Player('ACFlashPlayer.old.201209241900.swf', 'Acfun播放器 (2012-09-24)', 950, 445)) 
                    ->addDefault('ac20130118debug');
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
        $p = $source->Player;
        $playerParams = new FlashParams($p->playerUrl, $p->width, $p->height);
	    switch (strtoupper($source->VideoType->getType()))
	    {
	        case "NOR":
                $playerParams->addVar('vid', $source->DanmakuId);
                $playerParams->addVar('type', "sina");
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
	            $playerParams->addVar('vid', $source->DanmakuId);
	            $playerParams->addVar('system', "Artemis" );
	            $playerParams->addVar('type', "url" );
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