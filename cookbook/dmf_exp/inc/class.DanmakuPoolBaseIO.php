<?php if (!defined('PmWiki')) exit();
abstract class DanmakuPoolBaseIO
{
	protected $file;
	protected $NullXMLObj;
	
	public function __construct($file)
	{
		$this->file = $file;
        $this->NullXMLObj = simplexml_load_string("<comments></comments>");
	}

	abstract public function Load();

	abstract public function Save(SimpleXMLElement $obj);
}

class ErrorPoolIO extends DanmakuPoolBaseIO {
    
    private $e = XmlErrorType::NoError;
    
    public function __construct(XmlOperationException $e) {
        parent::__construct("/dev/null");
        $this->e = $e;
    }
    
    private function GetErrorXMLObj($msg) {
        $str = <<<EOF
        <comments><comment id="0" poolid="0" userhash="deadbeef" sendtime="1320983341">
            <text>$msg</text>
            <attr id="0" playtime="-1.0" color="16711680" mode="7" fontsize="30"/>
        </comment></comments>
EOF;
        return simplexml_load_string($str);
    }

    public function Load() {
        switch ($this->e->getType()) {
            case XmlErrorType::Auth:
                return $this->NullXMLObj; 
            default:
                return $this->GetErrorXMLObj($this->e->getMessage());
        }
    }
    
    public function Save(SimpleXMLElement $obj) { }
    
}

class StaticPoolIO extends DanmakuPoolBaseIO
{
    private $id;
    private $group;
    
	public function __construct($dmid, $group)
	{
		parent::__construct(utils::GetXMLFilePath($dmid, $group));
		$this->id    = $dmid;
		$this->group = $group;
	}
	
	public function Load()
	{
        if ( !XmlAuth($this->group, $this->id, XmlAuth::read) ) {
            Utils::WriteLog('StaticPoolIO::Load()', "{$this->group} :: {$this->id}  :: 请求read权限失败");
            throw new XmlOperationException("拒绝访问静态池 {$this->group} :: {$this->id}", XmlErrorType::Auth);
        }
        
		if (!file_exists($this->file))
		{
			return $this->NullXMLObj;
		}
		
		$Obj = simplexml_load_file($this->file);
		
		if ($Obj === FALSE)
		{
            Utils::WriteLog('StaticPoolIO::Load()', "{$this->group} :: {$this->id}  :: XML格式非法");
			throw new XmlOperationException("XML格式非法，对静态池{$this->group} :: {$this->id}的访问已拒绝", XmlErrorType::Broken);
		} else {
			return $Obj;
		}
	}
	
	public function Save(SimpleXMLElement $Obj)
	{
        if ( !XmlAuth($this->group, $this->id, XmlAuth::edit) ) {
            Utils::WriteLog('StaticPoolIO::Save()', "{$this->group} :: {$this->id}  :: 请求write权限失败");
            return;
        }
        
		if (file_exists($this->file))
		{
			rename($this->file, $this->file.",del-".time());
		}
		
		$folder = pathinfo($this->file, PATHINFO_DIRNAME);
		if (!file_exists($folder))
		{
            mkdir($folder, 0777, true);
		}
		$result = file_put_contents($this->file, $Obj->saveXML(), LOCK_EX);
		if ($result == FALSE)
		{
            Utils::WriteLog('StaticPoolIO::Save()', "{$this->group} :: {$this->id}  :: file_put_contents() :: 文件写入失败");
		} else {
            Utils::WriteLog('StaticPoolIO::Save()', "{$this->group} :: {$this->id}  :: file_put_contents() :: 文件写入成功");
        }
	}
	
}

class DynamicPoolIO extends DanmakuPoolBaseIO
{
    private $id;
    private $group;
    
	public function __construct($dmid, $group)
	{
		parent::__construct(Utils::GetDMRPageName($dmid, $group));
		$this->id    = $dmid;
		$this->group = $group;
	}
	
	public function Load()
	{
        if ( !XmlAuth($this->group, $this->id, XmlAuth::read) ) {
            Utils::WriteLog('DynamicPoolIO::Load()', "{$this->group} :: {$this->id}  :: 请求read权限失败");
            throw new XmlOperationException("拒绝访问动态池 {$this->group} :: {$this->id}", XmlErrorType::Auth);
        } else {        
            $auth = 'read';
            $page = RetrieveAuthPage($this->file, $auth, FALSE, READPAGE_CURRENT);
        }
        
		if (empty($page['text']))
		{
			return $this->NullXMLObj;
		}
		
		$XML  = '<?xml version="1.0" encoding="utf-8"?>'."\r\n".'<comments>'."\r\n";
		$XML .= $page['text'];
		$XML .= "\r\n</comments>";
		
		$Obj = simplexml_load_string($XML);

		if ($Obj === FALSE)
		{
            Utils::WriteLog('DynamicPoolIO::Load()', "{$this->group} :: {$this->id}  :: XML格式非法");
			throw new XmlOperationException("XML格式非法，对动态池{$this->group} :: {$this->id}的访问已拒绝", XmlErrorType::Broken);
		} else {
			return $Obj;
		}
	}
	
	public function Save(SimpleXMLElement $Obj)
	{
        if ( !XmlAuth($this->group, $this->id, XmlAuth::read) ) {
            Utils::WriteLog('DynamicPoolIO::Save()', "{$this->group} :: {$this->id}  :: 请求write权限失败");
            return;
        }
        
        $auth = 'edit';
		$new = $old = RetrieveAuthPage($this->file, $auth, FALSE, 0);
		$danmakuS = $Obj->comment;
		$new['text'] = '';
		foreach ($danmakuS as $danmaku)
		{
			$new['text'] .= PHP_EOL.$danmaku->asXML(); 
		}
		if (UpdatePage($this->file, $old, $new)) {
            Utils::WriteLog('DynamicPoolIO::Save()', "{$this->group} :: {$this->id}  :: UpdatePage() :: PmWiki页面更新成功");
        } else {
            Utils::WriteLog('DynamicPoolIO::Save()', "{$this->group} :: {$this->id}  :: UpdatePage() :: PmWiki页面更新失败");
        }
	}
	
}
