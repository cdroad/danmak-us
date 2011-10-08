<?php
//PoolOp / command / group / dmid / params
//post move clear valid download

class PoolOp extends CI_Controller {
	public function index($group, $dmid)
	{
		die("group : $group; dmid : $dmid;");
	}
	
	public function loadXML($group, $dmid, $attach = 'false') // GET : format
	{
		$group = Utils::GetGroup($group);
		$format = $_GET['format'];
		
		header("Content-type: text/xml");
		if ($attach = 'true') {
			header("Content-disposition: ".
				"attachment; filename=\"".$group."_$dmid".".xml\"");
		}

		$staPool = new DanmakuPoolBase(Utils::GetIOClass('static', $dmid));
		$dynPool = new DanmakuPoolBase(Utils::GetIOClass('dynamic', $dmid));
		
		$view = sprintf( "%s_xml_view_%s", $Group, strtolower($format));
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
		
		$pool = new DanmakuPoolBase(Utils::GetIOClass($_GET['pool'], $dmid));
		$XMLObj = $groupConfigClass::ConvertToUniXML($XMLObj);
		$append = strtolower($_GET['append']) == 'true' ;
		if ($append) {
			$pool->MergeFrom($XMLObj);
		} else {
			$pool->SetXML($XMLObj);
		}
		$pool->Save()->Dispose();
	}
	
	public function move($dmid, $from, $to)
	{
		$fromPool = new DanmakuPoolBase(Utils::GetIOClass($from, $dmid));
		$toPool = new DanmakuPoolBase(Utils::GetIOClass($to, $dmid));
		
		$toPool->MoveFrom($fromPool);
		
		$fromPool->Save()->Dispose();
		$toPool->Save()->Dispose();
	}
	
	public function validate($dmid, $pair)
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
