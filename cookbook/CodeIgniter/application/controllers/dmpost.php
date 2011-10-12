<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dmduration extends CI_Controller {

	public function index()
	{
        if (	$_POST['date']			= ''		||
                $_POST["playTime"]	    = ''		||
                $_POST["mode"]			= ''		||
                $_POST["fontsize"]	    = ''		||
                $_POST["color"]		    = ''		||
                $_POST["pool"]			= ''		||
                $_POST["message"]		= ''		)
			{ exit; }
		
		$text = htmlspecialchars(stripmagic($_POST["message"]), ENT_NOQUOTES, "UTF-8");
		$pool = ($_POST["mode"] == '8') ? 2 : $_POST["pool"]; //mode = 8 时 pool 必须 = 2		
		$builder = new DanmakuBuilder($text, $pool, 'deadbeef');
		$builder->AddAttr($_POST["playTime"], $_POST["mode"], $_POST["fontsize"], $_POST["color"]);
		$xml = (string)$builder;
		
		$vid = basename($_POST['vid']);
		
        global $EnableAutoTimeShift;
        if ($EnableAutoTimeShift)
        {
            $Shift = 0.0;
            $pp = 'Site.LastDanmakuCommit';
            $n = $p = ReadPage($pp);
            $LastCommit = @unserialize($p[$vid]);
            if ( ($LastCommit !== FALSE) &&
                 (floatval($LastCommit['playTime']) == floatval($pt)) )
                {
                    $Shift = floatval($LastCommit['lastShift']) + $GLOBALS['TimeShiftDelta'];
                }
            
            $dataArray = array
            (
                'playTime' => $attrs['playtime'],
                'lastShift' => $Shift,
            );
            $attrs['playtime'] += $Shift;
            
            $n[$vid] = serialize($dataArray);
            UpdatePage($pp, $p, $n);
        }

        //准备写入PmWiki
        $_pagename = 'DMR.B'.$vid;
        
        $new = $page = @RetrieveAuthPage($_pagename, $auth, false, 0);
        if (!$page) Abort("?cannot load $_pagename");
    
        $new['text'] .= $xml;
        UpdatePage($_pagename, $page, $new);
        echo $DMID;
        exit;
	}
}
