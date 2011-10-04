<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dmduration extends CI_Controller {

	public function index()
	{
        if (	$_POST['date']			!= ''		||
                $_POST["playTime"]	    != ''			||
                $_POST["mode"]			!= ''		||
                $_POST["fontsize"]	    != ''			||
                $_POST["color"]		    != ''			||
                $_POST["pool"]			!= ''		||
                $_POST["message"]		!= ''		)
        {
            $d = new DateTime($_POST['date']);
            $date = $d->format('U');
            $pt =$_POST["playTime"];
            $mode = $_POST["mode"];
            $fs = $_POST["fontsize"];
            $co = $_POST["color"];
            $pool = $_POST["pool"];
            $msg = htmlspecialchars(stripmagic($_POST["message"]), ENT_NOQUOTES, "UTF-8");
            $DMID = mt_rand();
            $vid = basename($_POST['vid']);

            global $EnableAutoTimeShift;
            if ($EnableAutoTimeShift)
            {
                $Shift = 0.0;
                $pp = 'Site.LastDanmakuCommit';
                $n = $p = ReadPage($pp);
                $LastCommit = @unserialize($p[$vid]);
                //dmid playTime相同，则对时间进行偏移
                if ( ($LastCommit !== FALSE) &&
                     (floatval($LastCommit['playTime']) == floatval($pt))
                    )
                    {
                        $Shift = floatval($LastCommit['lastShift']) + $GLOBALS['TimeShiftDelta'];
                    }
                
                $dataArray = array
                (
                    'playTime' => $pt,
                    'lastShift' => $Shift,
                );
                $pt += $Shift;
                
                $n[$vid] = serialize($dataArray);
                UpdatePage($pp, $p, $n);
            }
            
            $xml = <<<XML

    <comment id="$DMID">
        <text>$msg</text>
        <attrs>
            <attr playtime="$pt" mode="$mode" fontsize="$fs" color="$co" sendtime="$date" poolid="$pool" userhash="DEADBEEF"></attr>
        </attrs>
    </comment>
    XML;
        
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
}
