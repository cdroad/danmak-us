<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//弹幕控制接口
//添加到route合并到bpi
class Dmm extends CI_Controller {

	public function update_comment_time()
	{   
        die("0");
        //TODO:
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
        Utils::WriteLog('Dmm::update_comment_time()', "{$poolId} :: Pool->Save() :: Done!");
	}
	
	public function del()
	{
        die("0");
        //TODO:
        $this->load->helper('dmid');
        
        if (empty($_REQUEST['playerdel']))
            die("1");

        $poolId = idhash_to_dmid($dm_inid);
        if (is_null($poolId)) die("2");
        
        $DPool = new BiliUniDanmakuPair($poolId, PAIR_DYNAMIC);
        foreach (explode(",", $_REQUEST['playerdel']) as $id)
        {
            $DPool->delete(PAIR_DYNAMIC, $id);
        }
        $DPool->save(PAIR_DYNAMIC);
        
        Utils::WriteLog('Dmm::del()', ' :: '.$_REQUEST['playerdel'].':: Done!');
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