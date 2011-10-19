<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Bilibili Player Interfaces
//Bili播放器接口
class Bpi extends CI_Controller {

	public function index()
	{
        exit;
	}
	
	public function dad()
	{
        global $BilibiliAuthLevel;
        $this->load->helper('dmid');
        $data = array();
        
        if (isset($_REQUEST['id'])) {
            $data['ChatId'] = dmid_to_idhash($_REQUEST['id']);
        } else {
            $data['ChatId'] = 0;
        }
        
        $pn = 'DMR.Bilibili';
        if (CondAuth($pn,'edit')) {
            $data['AuthLevelString'] = $BilibiliAuthLevel->Danmakuer;
        } else {
            $data['AuthLevelString'] = $BilibiliAuthLevel->DefaultLevel;
        }
        
        $this->load->view('bilibili_dad_xml', $data);
	}
	
	public function advanceComment()
	{
        global $BiliEnableSA;
        
        if ($BiliEnableSA)
        {
            die("<confirm>1</confirm><hasBuy>true</hasBuy>");
        } else {
            die("<confirm>0</confirm><hasBuy>false</hasBuy><accept>false</accept>");
        }
	}
	
	public function dmduration()
	{
        exit;
	}
	
	public function rec()
	{
        exit;
	}
	
	public function playtag()
	{
        exit;
	}
	
	public function dmreport()
	{
        exit;
	}

	public function dmerror()
	{
        Utils::WriteLog('bpi::dmerror()', '');
	}
	
	public function dmpost()
	{
        if (	$_POST['date']			== ''		||
                $_POST["playTime"]	    == ''		||
                $_POST["mode"]			== ''		||
                $_POST["fontsize"]	    == ''		||
                $_POST["color"]		    == ''		||
                $_POST["pool"]			== ''		||
                $_POST["message"]		== ''		)
			{ Abort("不允许直接访问"); }
        /*
        vid=DMFWhite
        mode=1
        fontsize=25
        pool=0
        message=xxxxxxx
        rnd=4633
        date=2011-10-13 22:54:35
        color=16777215
        playTime=97.8
        */
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
        $auth = 'edit';
        $new = $page = @RetrieveAuthPage($_pagename, $auth, false, 0);
        if (!$page) die("-55");
    
        $new['text'] .= $xml;
        UpdatePage($_pagename, $page, $new);
        echo mt_rand();
        exit;
	}
}
