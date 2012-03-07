<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//Acfun (新) 播放器接口
include_once(DMF_ROOT_PATH."config.AcfunN1.php");
class Anpi extends CI_Controller {
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
        $_pagename = 'DMR.AN'.$vid;
		$auth = 'edit';
        $page = @RetrieveAuthPage($_pagename, $auth, false, 0);
		
        if (!$page) die("-55");
        
        $page['text'] .= $xml;
        WritePage($_pagename, $page);
        echo 'DMF_Local :: anpi :: dmpost() :: success!';
        exit;
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
		$dynPool = new DanmakuPoolBase(Utils::GetIOClass('acfunn1', $_POST["poolid"], 'dynamic'));
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
}