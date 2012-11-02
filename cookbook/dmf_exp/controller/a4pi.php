<?php
//Acfun (新) 播放器接口
//include_once(DMF_ROOT_PATH."config.Acfun4p.php");
//include_once(DMF_ROOT_PATH."/inc/class.VideoPageData.php");

class a4pi extends K_Controller {
    public function getlogo()
    {
        die(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAA'.
            'AARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAMSURBVBhXY/j/'.
            '/z8ABf4C/qc1gYQAAAAASUVORK5CYII='));
    }
    
    public function dmpost()
    {
        if (	$_POST['islock']	== ''		||
                $_POST["color"]	    == ''		||
                $_POST["text"]		== ''		||
                $_POST["size"]	    == ''		||
                $_POST["mode"]		== ''		||
                $_POST["stime"]		== ''		||
                $_POST["timestamp"]	== ''		||
				$_POST["poolid"]	== ''		)
			{ Abort("不允许直接访问"); }
		$text = stripmagic($_POST["text"]);
        $pt = $_POST["stime"];
		$vid = basename($_POST['poolid']);
        
        $builder = new DanmakuBuilder($text, 0, 'deadbeef');
        $attrs = array(
                'playtime'  => $pt,
                'mode'      => $_POST["mode"],
                'fontsize'  => $_POST["size"],
                'color'     => $_POST["color"]);
		$builder->AddAttr($attrs);
		$xml = (string)$builder;
		
        //准备写入PmWiki
        $_pagename = 'DMR.A4P'.$vid;
		$auth = 'edit';
        $page = @RetrieveAuthPage($_pagename, $auth, false, 0);
		
        if (!$page) die("-55");
        
        $page['text'] .= $xml;
        WritePage($_pagename, $page);
        die('DMF_Local :: a4pi :: dmpost() :: success!');
    }
    
    public function dmdelete()
    {
        if (	$_POST['islock']	== ''		||
                $_POST["color"]	    == ''		||
                $_POST["text"]		== ''		||
                $_POST["size"]	    == ''		||
                $_POST["mode"]		== ''		||
                $_POST["stime"]		== ''		||
                $_POST["timestamp"]	== ''		||
				$_POST["poolid"]	== ''		)
			{ Abort("不允许直接访问"); }
		$key = $this->hashCmt($_POST["text"],$_POST["color"],$_POST["size"],$_POST["mode"],$_POST["stime"]);
		$dynPool = new DanmakuPoolBase(Utils::GetIOClass('Acfun4p', $_POST["poolid"], 'dynamic'));
        foreach ($dynPool->GetXML()->comment as $node)
		{
			$K = $this->hashCmt( $node->text, $node->attr[0]["color"],$node->attr[0]["fontsize"],$node->attr[0]["mode"],$node->attr[0]["playtime"]);
			if ($K == $key) {
				echo 'Found!'.$node->text."\r\n";
				unset($node[0][0]);
				break;
			}
		}
		
        $dynPool->Save()->Dispose();
    }
    
    public function getvideobyid($pageid)
    {
        $source = new VideoPageData("Acfun4p.{$pageid}");
        
        $arr["aid"] = $pageid;
        $arr["uid"] = 1;
        $arr["vinfo"] = array("checked" => 2);
        $arr["cid"] = "";
        $arr["vid"] = "";
        $arr["vtype"] = "";
        
        switch (strtoupper($source->VideoType->getType()))
	    {
	        case "NOR":
	            $arr['vid'] = $source->DanmakuId;
	            $arr['cid'] = $source->DanmakuId;
	            $arr['vtype'] = "sina";
	        break;
	        
			case "QQ":
                $arr["vid"]   = $source->DanmakuId;
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "qq";
	        break;
	        
			case "TD":
                $arr["vid"]   = $source->DanmakuId;
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "tudou";
	        break;

			case "YK":
                $arr["vid"]   = $source->DanmakuId;
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "youku";
	        break;
	        
			case "URL":
			case "BURL":
			case "LINK":
			case "BLINK":
			case "LOCAL":
				$arr["vid"]   = rawurldecode($source->VideoStr);
				$arr["file"]   = rawurldecode($source->VideoStr);
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "url";
	        break;

			default:
				echo "$source->VideoType->getType(): $source->DanmakuId : $source->VideoStr";
				assert(false);
	        break;
	    }
	    echo json_encode($arr);
	    exit;
    }
    
	private function hashCmt($text, $color, $size, $mode, $stime)
	{
		return md5("$text$color$size$mode$stime");
	}
	
    public function ujson()
    {
        die('[]');
    }
    
    public function badwords()
    {
        die('[]');
    }
    
    public function adsjson()
    {
        die('');
    }
}