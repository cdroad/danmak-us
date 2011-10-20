<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//弹幕控制接口
//添加到route合并到bpi
class Dmm extends CI_Controller {

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