<?php
//Utils::WriteLog('PoolOp::clear()', "{$group} :: {$dmid} :: Unauthorized access!");
abstract class DanmakuPoolBaseIO
{
	protected $file;
	protected $NullXMLObj;
	
	public function __construct($file)
	{
		$this->file = $file;
        $this->NullXMLObj = simplexml_load_string("<comments></comments>");
	}
	
	protected function GenErrorXMLObj($msg)
	{
		$str = <<<EOF
		<comment id="140247779">
			<text>$msg</text>
			<poolid>0</poolid>
			<userhash>deafbeef</userhash>
			<sendtime>1285334695</sendtime>
		    <attrs>
		        <attr playtime="1.4" mode="1" fontsize="25" color="16777215" />
		    </attrs>
		</comment>
EOF;
		return simplexml_load_string($str);
	}
	
	abstract public function Load();

	abstract public function Save(SimpleXMLElement $obj);
}



class StaticPoolIO extends DanmakuPoolBaseIO
{
    private $id;
    private $group;
    
	public function __construct($dmid, $group)
	{
        if (empty($group)) throw new Exception("No group spec!");
		parent::__construct(utils::GetXMLFilePath($dmid, $group));
		$this->id    = $dmid;
		$this->group = $group;
	}
	
	public function Load()
	{
        //如果没有 读取 权限
        if ( !XMLAuth::IsRead($this->id, $this->group) ) {
            Utils::WriteLog('StaticPoolIO::Load()', "{$this->group} :: {$this->id}  :: Unauthorized access!");
            return $this->NullXMLObj;
        }
        
        //文件不存在的情况
		if (!file_exists($this->file))
		{
			return $this->NullXMLObj;
		}
		
		$Obj = simplexml_load_file($this->file);
		
		if ($Obj === FALSE)
		{
            Utils::WriteLog('StaticPoolIO::Load()', "{$this->group} :: {$this->id}  :: simplexml_load_file :: FALSE! XML Broken!");
			return $this->GenErrorXMLObj('静态池加载失败，请尝试XML校验。');
		} else {
			return $Obj;
		}
	}
	
	public function Save(SimpleXMLElement $Obj)
	{
        if ( !XMLAuth::IsEdit($this->id, $this->group) ) {
            Utils::WriteLog('StaticPoolIO::Save()', "{$this->group} :: {$this->id}  :: Unauthorized access!");
            return;
        }
        
		if (file_exists($this->file))
		{
			rename($this->file, $this->file.",del-".time());
		}
		
		$result = file_put_contents($this->file, $Obj->saveXML(), LOCK_EX);
		if ($result == FALSE)
		{
            Utils::WriteLog('StaticPoolIO::Save()', "{$this->group} :: {$this->id}  :: file_put_contents() :: FALSE !");
		} else {
            Utils::WriteLog('StaticPoolIO::Save()', "{$this->group} :: {$this->id}  :: file_put_contents() :: Success.");
        }
	}
	
}

class DynamicPoolIO extends DanmakuPoolBaseIO
{
    private $id;
    private $group;
    
	public function __construct($dmid, $group)
	{
        if (empty($group)) throw new Exception("No group spec!");
		parent::__construct(Utils::GetDMRPageName($dmid, $group));
		$this->id    = $dmid;
		$this->group = $group;
	}
	
	public function Load()
	{
        if ( !XMLAuth::IsRead($this->id, $this->group) ) {
            Utils::WriteLog('DynamicPoolIO::Load()', "{$this->group} :: {$this->id}  :: Unauthorized access!");
            return $this->NullXMLObj;
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
            Utils::WriteLog('DynamicPoolIO::Load()', "{$this->group} :: {$this->id}  :: simplexml_load_string :: FALSE!");
			return $this->GenErrorXMLObj('动态池加载失败，请尝试XML校验。');
		} else {
			return $Obj;
		}
	}
	
	public function Save(SimpleXMLElement $Obj)
	{
        if ( !XMLAuth::IsEdit($this->id, $this->group) ) {
            Utils::WriteLog('DynamicPoolIO::Save()', "{$this->group} :: {$this->id}  :: Unauthorized access!");
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
            Utils::WriteLog('DynamicPoolIO::Save()', "{$this->group} :: {$this->id}  :: UpdatePage() :: Done!");
        } else {
            Utils::WriteLog('DynamicPoolIO::Save()', "{$this->group} :: {$this->id}  :: UpdatePage() :: False!");
        }
	}
	
}
