<?php
$PlayerSet->add('2dl20111024', new Player('2dl20111024.swf', '2dland播放器(20111024)', 950, 512))
		  ->addDefault('2dl20111024');


class Twodland1GroupConfig
{
    public static $GroupString = 'Twodland1';
    public static $AllowedXMLFormat = array('raw', 'comments');
	public static $SUID = 'D';
	public static $XMLFolderPath = './uploads/Twodland1';
	public static $DanmakuBarSet;
	public static $VideoSourceSet;
	public static $PlayersSet;

    public static function GetDanmakuBarSet()
    {
        $set = new DanmakuBarSet(get_class());
        $set->add(new DanmakuBarUploadXML());
        $set->add(new DanmakuBarDownloadXML());
        $set->add(new DanmakuBarNewLine());
        
        $groupA = new DanmakuBarGroup(DanmakuBarItem::$Auth->Member);
        $groupA->add(new DanmakuBarValPool());
        $groupA->add(new DanmakuBarEditPool());
        $groupA->add(new DanmakuBarEditPart);
        
        $set->add($groupA);
        $set->add(new DanmakuBarPoolMove());
        $set->add(new DanmakuBarPoolClear());
        
        return $set;
    }
    
	public static function GenerateFlashVarArr(VideoData $source)
	{
		$AFVArray = array();
		$AFVArray['dir'] = strtoupper($source->sourcetype->getType());
		$AFVArray['vid'] = $source->dmid;
        
        if (strtoupper($source->sourcetype->getType()) == "nor") {
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
	
	public static function ConvertToUniXML(SimpleXMLElement $obj)
	{
        if (strtolower($obj->getName()) == "comments") {
            if (empty($obj->comment[0]->playTime)) {
                //raw
                return $obj;
            } else {
                return self::ConvertFromCommentsFormat($obj);
            }
        }
	}
	
	public static function ConvertFromCommentsFormat(SimpleXMLElement $Obj)
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
}

Twodland1GroupConfig::$DanmakuBarSet = Twodland1GroupConfig::GetDanmakuBarSet();
Twodland1GroupConfig::$VideoSourceSet = $GLOBALS['VideoSourceSet'];
Twodland1GroupConfig::$PlayersSet = $GLOBALS['PlayerSet'];
