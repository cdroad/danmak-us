<?php
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
	public function __construct($dmid, $group)
	{
        if (empty($group)) throw new Exception("No group spec!");
		parent::__construct(utils::GetXMLFilePath($dmid, $group));
	}
	
	public function Load()
	{
		if (!file_exists($this->file))
		{
			return $this->NullXMLObj;
		}
		
		$Obj = simplexml_load_file($this->file);
		
		if ($Obj === FALSE)
		{
			Utils::WriteLog('StaticPoolIO::Load()', 'simplexml_load_file :: FALSE!');
			return $this->GenErrorXMLObj('静态池加载失败，请尝试XML校验。');
		} else {
			return $Obj;
		}
	}
	
	public function Save(SimpleXMLElement $Obj)
	{
		if (file_exists($this->file))
		{
			rename($this->file, $this->file.",del-".time());
		}
		
		$result = file_put_contents($this->file, $Obj->saveXML(), LOCK_EX);
		if ($result == FALSE)
		{
            Utils::WriteLog('StaticPoolIO::Save()', 'file_put_contents :: FALSE!');
		} else {
            Utils::WriteLog('StaticPoolIO::Save()', 'file_put_contents :: Success!');
        }
	}
	
}

class DynamicPoolIO extends DanmakuPoolBaseIO
{
	public function __construct($dmid, $group)
	{
        if (empty($group)) throw new Exception("No group spec!");
		parent::__construct(Utils::GetDMRPageName($dmid, $group));
	}
	
	public function Load()
	{
		$auth = 'read';
		$page = RetrieveAuthPage($this->file, $auth, FALSE, READPAGE_CURRENT);
        
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
			Utils::WriteLog('DynamicPoolIO::Load()', 'simplexml_load_string :: FALSE!');
			return $this->GenErrorXMLObj('动态池加载失败，请尝试XML校验。');
		} else {
			return $Obj;
		}
	}
	
	public function Save(SimpleXMLElement $Obj)
	{
		$auth = 'edit';
		$new = $old = RetrieveAuthPage($this->file, $auth, FALSE, 0);
		$danmakuS = $Obj->comment;
		$new['text'] = '';
		foreach ($danmakuS as $danmaku)
		{
			$new['text'] .= PHP_EOL.$danmaku->asXML(); 
		}
		UpdatePage($this->file, $old, $new);
        Utils::WriteLog('DynamicPoolIO::Save()', 'file_put_contents :: Success!');
	}
	
}
