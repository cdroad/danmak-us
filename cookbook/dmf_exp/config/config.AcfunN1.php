<?php if (!defined('PmWiki')) exit();
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
                    ->add('ac20111115', new Player('ac20111115.swf', 'Acfun播放器 (20111116)', 950, 432)) 
                    ->add('ac20120304', new Player('ac20120304.swf', 'Acfun播放器 (20120304)', 950, 445))
                    ->addDefault('ac20120304');
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
                $playerParams->addVar('id', $source->DanmakuId);
                $playerParams->addVar('type', "video");
	        break;
	        
			case "QQ":
			case "TD":
			case "6CN":
			case "URL":
			case "BURL":
			case "LINK":
			case "BLINK":
			case "LOCAL":
				$playerParams->addVar('id', $source->DanmakuId);
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