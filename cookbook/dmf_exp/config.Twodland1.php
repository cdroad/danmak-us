<?php
class Twodland1GroupConfig extends GroupConfig
{
    protected function __construct()
    {
        parent::__construct();
        $this->GroupString = 'Twodland1';
        $this->AllowedXMLFormat = array('comments', 'raw');
        $this->SUID = 'D';
        $this->XMLFolderPath = './uploads/Twodland1';
        $this->PlayersSet->add('2dl20111024', new Player('2dl20111024.swf', '2dland播放器(20111024)', 950, 512))
                ->addDefault('2dl20111024');
        
        
        
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
		$AFVArray['dir'] = strtoupper($source->sourcetype->getType());
		$AFVArray['vid'] = $source->dmid;
        
        if (strtoupper($source->sourcetype->getType()) == "NOR") {
            $type = 'sina';
            $part = "<vid>{$source->dmid}</vid>";
        } else {
            $type = 'other';
            $url = urldecode($source->sourcetype->source);
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
        $targetFile = './static/page/'.md5($source->dmid).'.xml';
        file_put_contents($targetFile, $contents, LOCK_EX);
		return $AFVArray;
	}
	
	public function ConvertToUniXML(SimpleXMLElement $obj)
	{
        if (strtolower($obj->getName()) == "comments") {
            if (empty($obj->comment[0]->playTime)) {
                //raw
                return $obj;
            } else {
                return $this->ConvertFromCommentsFormat($obj);
            }
        }
	}
	
	public function ConvertFromCommentsFormat(SimpleXMLElement $Obj)
	{
		$XMLString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
		foreach ($Obj->comment as $comment) {
            $pool = 0;
            $time = (string)strtotime((string)$comment->sendTime);
			$danmaku = new DanmakuBuilder((string)$comment->message, $pool, 'deadbeef', $time);
            //var_dump($attrs);
            foreach ($comment->attributes() as $k =>$v) {
                $attrs[strtolower($k)] = $v;
            }
            
            $attrs['playtime'] = (string)$comment->playTime;
            unset($attrs['islocked']);
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
