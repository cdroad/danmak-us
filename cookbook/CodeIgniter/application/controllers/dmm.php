<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dmm extends CI_Controller {

	public function error()
	{
        echo $_SERVER['REQUEST_URI'];
		//$this->load->view('welcome_message');
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
	
	public function update_comment_time()
	{
        $targetTime = intval($_REQUEST['time']);
        $dmId = intval($_REQUEST['dmid']);
        $dm_inid = intval($_REQUEST['dm_inid']);
        
        $poolId = findPoolIdByInId($dm_inid);
        if (is_null($poolId)) die("2");
        
        $DPool = new BiliUniDanmakuPair($poolId, PAIR_DYNAMIC);
        foreach ($DPool->find(array("id" => $dmId)) as $danmaku)
        {
            $oldattr = $newattr = $danmaku->getElementsByTagName("attr")->item(0);
            $newattr->setAttribute('playtime', $targetTime);
            $danmaku->replaceChild($newattr, $oldattr);
            
        }
        $DPool->save(PAIR_DYNAMIC);
	}
	
	public function del()
	{
        if (empty($_REQUEST['playerdel']))
            die("1");

        $poolId = findPoolIdByInId($dm_inid);
        if (is_null($poolId)) die("2");
        
        $DPool = new BiliUniDanmakuPair($poolId, PAIR_DYNAMIC);
        foreach (explode(",", $_REQUEST['playerdel']) as $id)
        {
            $DPool->delete(PAIR_DYNAMIC, $id);
        }
        $DPool->save(PAIR_DYNAMIC);

        die("0");
	}
}