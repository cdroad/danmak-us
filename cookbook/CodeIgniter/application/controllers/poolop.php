<?php
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
		
		switch (strtolower($pair))
		{
			case "static":
				$staPool->Clear();
				$staPool->Save();
				break;
			case "dynamic":
				$dynPool->Clear();
				$dynPool->Save();
				break;
			case "all":
				$staPool->Clear();
				$staPool->Save();
				$dynPool->Clear();
				$dynPool->Save();
				break;
		}
		$this->display("和谐弹幕池 $pair 完毕。".self::$GoBack);
		
	}
	
	public function loadxml($group, $dmid) // GET : format attach
	{
		$group = Utils::GetGroup($group);
		$format = is_null($_GET['format']) ? 'd' : $_GET['format'] ;
		
		//header("Content-type: text/xml");
		header("Content-type: text/plain");
		if ($attach == 'true') {
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
		$group = Utils::GetGroup($group);$groupConfigClass = $group."GroupConfig";
		
		//加载文件
		if ($_FILES['uploadfile']['error'] != UPLOAD_ERR_OK)
		{
			$msg = "文件上传失败";
			HandleBrowse('API/XMLTool');
			return;
		}
		
		$xmldata = simplexml_load_file($_FILES['uploadfile']['tmp_name']);
		if ($xmldata === FALSE) 
		{
			$msg = "XML文件非法，拒绝上传请求";
			HandleBrowse('API/XMLTool');
			return;
		}
		
		$pool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $_POST['Pool']));
		$XMLObj = $groupConfigClass::ConvertToUniXML($xmldata);
		$append = strtolower($_GET['append']) == 'true' ;
		if ($append) {
			$pool->MergeFrom($XMLObj);
		} else {
			$pool->SetXML($XMLObj);
		}
        
		$pool->Save()->Dispose();
		$this->display("非常抱歉，上传成功。".self::$GoBack);
	}
	
	public function move($group, $dmid, $from, $to)
	{
		$group = Utils::GetGroup($group);
		$fromPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $from));
		$toPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $to));
		
		$toPool->MoveFrom($fromPool);
		
		$fromPool->Save()->Dispose();
		$toPool->Save()->Dispose();
		$this->display("弹幕池移动： $from -> $to 完毕。".self::$GoBack);
	}
	
	public function validate($group, $dmid, $pair = 'dynamic')
	{
		libxml_clear_errors();
		new DanmakuPoolBase(Utils::GetIOClass($pair, $dmid));
		
		$errors = libxml_get_errors();
		$errorStr = "";
		foreach ($errors as $error) {
			$errorStr .= display_xml_error($errors);
		}
		
		$this->display($errorStr);
	}
	
	private function display($msg)
	{
        $GLOBALS['MessagesFmt'] = $msg;
        $this->load->view('pmwiki_view', array('name' => 'API.XMLTool'));
	}
	
}
