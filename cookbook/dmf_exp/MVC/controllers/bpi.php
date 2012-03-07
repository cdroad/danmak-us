<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(DMF_ROOT_PATH."config.Bilibili2.php");
//Bilibili Player Interfaces
//Bili播放器接口
class Bpi extends CI_Controller {

	public function index()
	{
        die("unknown action");
	}
    
    public function bpad()
    {
        $this->load->view('bilibili_pad');
    }
    
    public function error()
	{
        $GLOBALS['MessagesFmt'] = '你知道的太多了，小心大表哥。';
        $this->load->view('pmwiki_view', array('name' => 'API.XMLTool'));
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
        
        if (XMLAuth::IsEdit($_REQUEST['id'], 'Bilibili2')) {
            $data['AuthLevelString'] = $BilibiliAuthLevel->Danmakuer;
        } else {
            $data['AuthLevelString'] = $BilibiliAuthLevel->DefaultLevel;
        }
        
        $this->load->view('bilibili_dad_xml', $data);
	}
	
	public function advanceComment()
	{
        $gc = Utils::GetGroupConfig('Bilibili2');
        if ($gc->BiliEnableSA)
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
	//关联视频
	public function playtag()
	{
        exit;
	}
	//弹幕举报
	public function dmreport()
	{
        exit;
	}
    //播放器接口 。弹幕错误汇报
	public function dmerror()
	{
        if (empty($_REQUEST['id']) || empty($_REQUEST['error']))
            exit;
        $str = "播放器汇报错误{$_REQUEST['error']}, 返回视频vid : {$_REQUEST['id']}";
        Utils::WriteLog('bpi::dmerror()', $str);
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
        $auth = 'edit';
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
        
		$text = stripmagic($_POST["message"]);
		$pool = ($_POST["mode"] == '8') ? 2 : 1; //mode = 8 时 pool 必须 = 2
        $pt = $_POST["playTime"];
		$vid = basename($_POST['vid']);
        
        global $EnableAutoTimeShift;
        if ($EnableAutoTimeShift)
        {
            $Shift = 0.0;
            $pp = 'Site.LastDanmakuCommit';
            $n = @RetrieveAuthPage($pp, $auth, false, 0);;
            if (!$n) die("-55");
            $LastCommit = @unserialize($n[$vid]);
            
            if ( ($LastCommit !== FALSE) &&
                 (floatval($LastCommit['playTime']) == floatval($pt)) )
                {
                    if ((time() - $LastCommit['lastTime']) > $GLOBALS['TimeShiftThreshold']) {
                        $Shift = 0.0;
                    } else {
                        $Shift = floatval($LastCommit['lastShift']) + $GLOBALS['TimeShiftDelta'];
                    }
                }
            
            $dataArray = array
            (
                'playTime' => $LastCommit['playTime'],
                'lastShift' => $Shift,
                'lastTime' => time()
            );
            
            $pt += $Shift;
            
            $n[$vid] = serialize($dataArray);
            WritePage($pp, $n);
        }
        
        
        $builder = new DanmakuBuilder($text, $pool, 'deadbeef');
        $attrs = array(
                'playtime'  => $pt,
                'mode'      => $_POST["mode"],
                'fontsize'  => $_POST["fontsize"],
                'color'     => $_POST["color"]);
		$builder->AddAttr($attrs);
		$xml = (string)$builder;
        
        //准备写入PmWiki
        $_pagename = 'DMR.B'.$vid;
        $page = @RetrieveAuthPage($_pagename, $auth, false, 0);
        if (!$page) die("-55");
        
        $page['text'] .= $xml;
        WritePage($_pagename, $page);
        echo mt_rand();
        exit;
	}

    
    // ************************* dmm ********************//
    
    
	public function update_comment_time()
	{   
        $this->load->helper('dmid');
        
        $targetTime = intval($_REQUEST['time']);
        $dmid = intval($_REQUEST['dmid']);
        $poolId = idhash_to_dmid(intval($_REQUEST['dm_inid']));
        if (is_null($poolId)) die("2");
        
        $dynPool = new DanmakuPoolBase(Utils::GetIOClass('bilibili2', $poolId, 'dynamic'));
        $query = new DanmakuXPathBuilder();
        $result = $dynPool->Find($query->CommentId($dmid));
        
        if (empty($result)) die("3");
        
        foreach ( $result as $danmaku ) {
            $danmaku->attr[0]["playtime"] = $targetTime;
        }
        $dynPool->Save()->Dispose();
        Utils::WriteLog('Dmm::update_comment_time()', "{$poolId} :: Pool->Save() :: Done!");
        die("0");
	}
	
	public function del()
	{
        $this->load->helper('dmid');

        if (empty($_REQUEST['playerdel']))
            die("1");
        $poolId = idhash_to_dmid($_REQUEST['dm_inid']);
        if (is_null($poolId)) die("2");
        
        $dynPool = new DanmakuPoolBase(Utils::GetIOClass('bilibili2', $poolId, 'dynamic'));

        $deleted = "";
        
        foreach (explode(",", $_REQUEST['playerdel']) as $id)
        {
            $query = new DanmakuXPathBuilder();
            $result = $dynPool->Find($query->CommentId($id));
            $matched = count($result);
            
            if ($matched == 1) {
                unset($result[0][0]);
                $deleted .= ", '{$id}'";
            } else {
                Utils::WriteLog('Dmm::del()', "Bilibili2 :: {$poolId} :: Unexcepted dmid {$id}, matched {$matched}");
                die("3");
            }
        }
        $dynPool->Save()->Dispose();
        
        Utils::WriteLog('Dmm::del()', "Bilibili2 :: {$poolId} :: Done!  \r\n{$deleted}");
        die("0");
	}

	public function move()
	{
        die("0");
	}
	
	public function credit()
	{
        die("0");
	}
	
	public function skip()
	{
        die("0");
	}
}
