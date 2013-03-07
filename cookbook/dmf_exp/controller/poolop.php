<?php if (!defined('PmWiki')) exit();
//PoolOp / command / group / dmid / params
//post move clear valid download

//弹幕操作接口
//返回HTML
class PoolOp extends K_Controller {
    const GoBack = "<script language='javascript'> setTimeout('history.go(-1)', 2000);</script>两秒后传送回家";
        
    public function PoolOp() {
        $this->Helper("danmakuPool");
    }
    
	public function clear($group, $dmid, $pool)
	{
		$staPool = GetPool($group, $dmid, PoolMode::S);
		$dynPool = GetPool($group, $dmid, PoolMode::D);
		if (!XmlAuth($group, $dmid, XmlAuth::admin)) {
            Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: 权限不足");
            $this->display("越权访问。");
            return;
        }
        
		switch (strtolower($pair))
		{
			case "static":
				$staPool->Clear();
				$staPool->SaveAndDispose();
				Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: {$pool} :: Done!");
				break;
			case "dynamic":
				$dynPool->Clear();
				$dynPool->SaveAndDispose();
				Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: {$pool} :: Done!");
				break;
			case "all":
				$staPool->Clear();
                $dynPool->Clear();
                $staPool->SaveAndDispose();
				$dynPool->SaveAndDispose();
				Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: {$pool} ::Done!");
				break;
		}
        
		$this->display("和谐弹幕池 $pair 完毕。".self::GoBack);
	}
	
	
	public function loadxml($group, $dmid) // GET : format attach
	{
        $gc = Utils::GetGroupConfig($group);
		//header("Content-type: text/xml");
		header("Content-type: text/plain");
		if ($_GET['attach'] == 'true') {
			header("Content-disposition: ".
				"attachment; filename=\"".$group."_$dmid".".xml\"");
		}

		$staPool = GetPool($group, $dmid, PoolMode::S);
		$dynPool = GetPool($group, $dmid, PoolMode::D);
        
        $staPool->MoveFrom($dynPool);
		
		$format = is_null($_GET['format']) ? $gc->AllowedXMLFormat[0] : $_GET['format'] ;
		$view = sprintf( "xml_view_%s", strtolower($format));
		// 不做保存，纯粹合并
        $this->DisplayView($view, array('Obj' => $staPool->GetXML()) );
	}
	
	public function post($group, $dmid) // GET : pool append
	{
        if (!XmlAuth($group, $dmid, XmlAuth::edit)) {
            Utils::WriteLog('PoolOp::post()', "{$group} :: {$dmid} :: 权限不足");
            return;
        }
        
		//加载文件
		if ($this->Input->File->uploadfile['error'] != UPLOAD_ERR_OK)
		{
            Utils::WriteLog('PoolOp::post()', "{$group} :: {$dmid} :: 文件上传失败");
			$this->display("文件上传失败");
			return;
		}
	
		if ($xmldata === FALSE) 
		{
            Utils::WriteLog('PoolOp::post()', "{$group} :: {$dmid} :: XML非法");
            $this->display("XML文件非法，拒绝上传请求");
			return;
		}
		
        $pool = GetPool($group, $dmid, StrToPool($this->Input->Post->Pool));
        
		$gc = Utils::GetGroupConfig($group);
		$xmldata = $gc->UploadFilePreProcess(file_get_contents($this->Input->File->uploadfile['tmp_name']));
		$XMLObj = $gc->ConvertToUniXML($xmldata);unset($xmldata);
		
		$append = strtolower($this->Input->Get->append) == 'true' ;
		if ($append) {
			$pool->MergeFrom($XMLObj);
		} else {
			$pool->SetXML($XMLObj);
		}
        
        $pool->SaveAndDispose();
        Utils::WriteLog('PoolOp::post()', "{$group} :: {$dmid} :: Success!");
		$this->display("非常抱歉，上传成功。".self::GoBack);
	}
	
	public function move($group, $dmid, $from, $to)
	{
        if (!XmlAuth($group, $dmid, XmlAuth::admin)) {
            Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: Unauthorized access!");
            $this->display("越权访问。");
            return;
        }
        
		$fromPool =  GetPool($group, $dmid, StrToPool($from));
		$toPool   =  GetPool($group, $dmid, StrToPool($to  ));
		
		$toPool->MoveFrom($fromPool);
		
		$fromPool->SaveAndDispose();
		$toPool->SaveAndDispose();
		Utils::WriteLog('PoolOp::move()', "{$group} :: {$dmid} :: 从 {$from} 移动到 {$to} 成功");
		$this->display("弹幕池移动： $from -> $to 完毕。".self::GoBack);
	}
	
	public function validate($group, $dmid, $pool = 'dynamic')
	{
		libxml_clear_errors();
		GetPool($group, $dmid, $pool, LoadMode::inst);
		
		$errors = libxml_get_errors();
        if (empty($errors)) {
            $this->display("弹幕池{$pool}校验正常".self::GoBack);
            return;
        }
		$errorStr = "";
		foreach ($errors as $error) {
			$errorStr .= display_xml_error($errors);
		}
		
		$this->display($errorStr);
	}
	
	private function display($msg)
	{
        $GLOBALS['MessagesFmt'] = $msg;
        $this->DisplayView('pmwiki_view', array('name' => 'API.XMLTool'));
	}
	
}
