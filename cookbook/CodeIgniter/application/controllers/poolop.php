<?php
//PoolOp / command / group / dmid / params
//post move clear valid download

class PoolOp extends CI_Controller {
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
        //var_dump($dynPool);exit;
        $staPool->MoveFrom($dynPool);
		
		$view = sprintf( "%s_xml_view_%s", $group, strtolower($format));
		// 不做保存，纯粹合并
        $this->load->view($view, array('Obj' => $staPool->GetXML()) );
	}
	
	public function post($group, $dmid) // GET : pool append
	{
		$group = Utils::GetGroup($group);$groupConfigClass = $Group."Config";
		
		//加载文件
		if ($_FILES['uploadfile']['error'] != UPLOAD_ERR_OK)
		{
			$GLOBALS['MessagesFmt'] = "文件上传失败";
			HandleBrowse('API/XMLTool');
			return;
		}
		
		$xmldata = simplexml_load_file($_FILES['uploadfile']['tmp_name']);
		if ($xmldata === FALSE) 
		{
			$GLOBALS['MessagesFmt'] = "XML文件非法，拒绝上传请求";
			HandleBrowse('API/XMLTool');
			return;
		}
		
		$pool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $_GET['pool']));
		$XMLObj = $groupConfigClass::ConvertToUniXML($XMLObj);
		$append = strtolower($_GET['append']) == 'true' ;
		if ($append) {
			$pool->MergeFrom($XMLObj);
		} else {
			$pool->SetXML($XMLObj);
		}
		$pool->Save()->Dispose();
	}
	
	public function move($group, $dmid, $from, $to)
	{
		$group = Utils::GetGroup($group);
		$fromPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $from));
		$toPool = new DanmakuPoolBase(Utils::GetIOClass($group, $dmid, $to));
		
		$toPool->MoveFrom($fromPool);
		
		$fromPool->Save()->Dispose();
		$toPool->Save()->Dispose();
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
		
		return $errorStr;
	}
	
}
