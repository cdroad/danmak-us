<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(DMF_ROOT_PATH."config.Twodland1.php");
include_once(DMF_ROOT_PATH."config.Bilibili2.php");
include_once(DMF_ROOT_PATH."config.Acfun2.php");
include_once(DMF_ROOT_PATH."config.AcfunN1.php");
//PoolOp / command / group / dmid / params
//post move clear valid download

//弹幕操作接口
//返回HTML
class PoolOp extends CI_Controller {
    private static $GoBack = 
        "<script language='javascript'> setTimeout('history.go(-1)', 2000);</script>两秒后传送回家";
    
	public function index($group, $dmid)
	{
		die("group : $group; dmid : $dmid;");
	}
	
	public function clear($group, $dmid, $pair)
	{
		$group = Utils::GetGroup($group);
		
		$staPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, 'static'), false);
		$dynPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, 'dynamic'), false);
		if (!XMLAuth::IsAdmin($dmid, $group)) {
            Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: Unauthorized access!");
            $this->display("越权访问。");
            return;
        }
        
		switch (strtolower($pair))
		{
			case "static":
				$staPool->Clear();
                Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: Done!");
				$staPool->Save()->Dispose();
				break;
			case "dynamic":
				$dynPool->Clear();
                Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: Done!");
				$dynPool->Save()->Dispose();
				break;
			case "all":
				$staPool->Clear();
                $dynPool->Clear();
                Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: Done!");
                $staPool->Save()->Dispose();
				$dynPool->Save()->Dispose();
				break;
		}
        
		$this->display("和谐弹幕池 $pair 完毕。".self::$GoBack);
		
	}
	
	public function loadxml($group, $dmid) // GET : format attach
	{
		$group = Utils::GetGroup($group);
        $gc = Utils::GetGroupConfig($group);
		$format = is_null($_GET['format']) ? $gc->AllowedXMLFormat[0] : $_GET['format'] ;
		
		//header("Content-type: text/xml");
		header("Content-type: text/plain");
		if ($_GET['attach'] == 'true') {
			header("Content-disposition: ".
				"attachment; filename=\"".$group."_$dmid".".xml\"");
		}

		$staPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, 'static'));
		$dynPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, 'dynamic'));
        
        $staPool->MoveFrom($dynPool);
		
		$view = sprintf( "%s_xml_view_%s", $group, strtolower($format));
		// 不做保存，纯粹合并
        $this->load->view($view, array('Obj' => $staPool->GetXML()) );
	}
	
	public function post($group, $dmid) // GET : pool append
	{
		$group = Utils::GetGroup($group);
        $gc = Utils::GetGroupConfig($group);
		
		//加载文件
		if ($_FILES['uploadfile']['error'] != UPLOAD_ERR_OK)
		{
            Utils::WriteLog('PoolOp::post()', "{$group} :: {$dmid} :: File Upload Fail!");
			$this->display("文件上传失败");
			return;
		}
		
		$xmldata = $gc->UploadFilePreProcess(file_get_contents($_FILES['uploadfile']['tmp_name']));
		if ($xmldata === FALSE) 
		{
            Utils::WriteLog('PoolOp::post()', "{$group} :: {$dmid} :: XMLInvalid!");
            $this->display("XML文件非法，拒绝上传请求");
			return;
		}
		
		$pool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $_POST['Pool']));
		$XMLObj = $gc->ConvertToUniXML($xmldata);
		$append = strtolower($_GET['append']) == 'true' ;
		if ($append) {
			$pool->MergeFrom($XMLObj);
		} else {
			$pool->SetXML($XMLObj);
		}
        
        Utils::WriteLog('PoolOp::post()', "{$group} :: {$dmid} :: Success!");
        $pool->Save()->Dispose();
		$this->display("非常抱歉，上传成功。".self::$GoBack);
	}
	
	public function move($group, $dmid, $from, $to)
	{
		$group = Utils::GetGroup($group);
        if (!XMLAuth::IsAdmin($dmid, $group)) {
            Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: Unauthorized access!");
            $this->display("越权访问。");
            return;
        }
		$fromPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $from));
		$toPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $to));
		
		$toPool->MoveFrom($fromPool);
		
        Utils::WriteLog('PoolOp::move()', "{$group} :: {$dmid} :: from {$from} to {$to} Done!");
		$fromPool->Save()->Dispose();
		$toPool->Save()->Dispose();
		$this->display("弹幕池移动： $from -> $to 完毕。".self::$GoBack);
	}
	
	public function validate($group, $dmid, $pair = 'dynamic')
	{
        $group = Utils::GetGroup($group);
		libxml_clear_errors();
		new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $pair));
		
		$errors = libxml_get_errors();
        if (empty($errors)) {
            $this->display("居然没有错误，不愧是冷酷的TD。".self::$GoBack);
            return;
        }
		$errorStr = "";
		foreach ($errors as $error) {
			$errorStr .= display_xml_error($errors);
		}
		
		$this->display("糟糕！居然有错？<br />".$errorStr);
	}
	
	private function display($msg)
	{
        $GLOBALS['MessagesFmt'] = $msg;
        $this->load->view('pmwiki_view', array('name' => 'API.XMLTool'));
	}
	
}
