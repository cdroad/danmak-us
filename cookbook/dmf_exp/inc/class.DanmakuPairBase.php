<?php if (!defined('PmWiki')) exit();

/*
 * 声明基础弹幕对
 * 
 * 使用D,S对，其他对由子类扩充。
 * delete, save函数由子类扩充。
 */

abstract class DanmakuPairBase
{
	const PAIR_NONE		= 0;
	const PAIR_STATIC	= 1;
	const PAIR_DYNAMIC	= 2;
	const PAIR_STATIC_CACHE  = 3; //性能瓶颈时开启缓冲器.
	const PAIR_DYNAMIC_CACHE = 4;
	const PAIR_ALL		= 0xff;
	/*
	 * 预留给扩展
	 */
	
	protected $_SPool = NULL;
	protected $_DPool = NULL;
	protected $_SFile;
	protected $_DFile;

	protected $_DMID;
	protected $_GP;
	protected $_SUID;
	protected $_GC;
	
	protected $_XMLError;
	protected $_XMLNull;
	
	protected $_LogPage;
	
	public function DanmakuPairBase($group, $dmid, $pair = PAIR_ALL)
	{
	
		$isGroupVaild = array_key_exists($group, $GLOBALS['DMF_GroupConfig']);
		if (!$isGroupVaild)
		{
			$errorStr = "CONFIG_GROUP_NOT_EXISTS__$group";
			writeLog('Main/SysLog', $errorStr);
			Abort($errorStr);
		}
		$this->_DMID = basename($dmid);
		$this->_GP = $group;
		$this->_GC = &$GLOBALS['DMF_GroupConfig'][$group];
		$this->_SUID = $this->_GC['SUID'];

		$this->_DFile = 'DMR.'.$this->_SUID.$this->_DMID;
		$this->_SFile = "./uploads/$this->_GP/$this->_DMID.xml";
		
		$this->_XMLError = simplexml_load_string($this->_GC['XMLError']);
		$this->_XMLNull = simplexml_load_string($this->_GC['XMLHeader'].$this->_GC['XMLFooter']);
		
		$this->_SPool = $this->_XMLNull;
		$this->_DPool = $this->_XMLNull;
		
		switch ($pair)
		{
			case PAIR_STATIC:
				$this->loadStatic();
				break;
			case PAIR_DYNAMIC:
				$this->loadDynamic();
				break;
			case PAIR_ALL:
				$this->loadDynamic();
				$this->loadStatic();
				break;
			case PAIR_NONE:
				break;
			default:
				writeLog('Main/SysLog', "UNEXPECTED_PAIR_TYPE : $pair".__FILE__.":".__LINE__);
				break;
		}
		if ($this->_SPool === FALSE) writeLog('Main/SysLog', "$this->_GP :: $this->_DMID :: PAIR_STATIC XML->FALSE");
		if ($this->_DPool === FALSE) writeLog('Main/SysLog', "$this->_GP :: $this->_DMID :: PAIR_DYNAMIC XML->FALSE");
	}
	
	public function append($pair, &$Obj)
	{
		if ($Obj === FALSE)
		{
			return FALSE;
		}
		foreach ($this->getOpArray($pair) as $k)
		{
			simplexml_merge($this->$k, $Obj);
		}
	}
	
	public function delete($pair, $info)
	{
		switch ($pair)
		{
			case PAIR_STATIC:
				Abort("PAIR_STATIC_COULD_NOT_BE_OPERATED");
				break;
			case PAIR_DYNAMIC:
				$this->deleteDynamic($info);
				break;
			case PAIR_ALL:
				Abort("PAIR_ALL_COULD_NOT_BE_OPERATED");
				break;
			case PAIR_NONE:
				break;
			default:
				writeLog('Main/SysLog', "UNEXPECTED_PAIR_TYPE : $pair".__FILE__.":".__LINE__);
				break;
		}
	}

	public function save($pair)
	{
		switch ($pair)
		{
			case PAIR_STATIC:
				$this->saveStatic();
				break;
			case PAIR_DYNAMIC:
				$this->saveDynamic();
				break;
			case PAIR_ALL:
				$this->saveDynamic();
				$this->saveStatic();
				break;
			case PAIR_NONE:
				return;
				break;
			default:
				writeLog('Main/SysLog', "UNEXPECTED_PAIR_TYPE : $pair".__FILE__.":".__LINE__);
				return;
				break;
		}
		$this->postLog($pair);
	}
	
	public function get($pair)
	{
		switch ($pair)
		{
			case PAIR_STATIC:
				return $this->_SPool;
				break;
			case PAIR_DYNAMIC:
				return $this->_DPool;
				break;
			case PAIR_ALL:
				$temp = $this->_SPool;
				simplexml_merge($temp, $this->_DPool);
				return $temp;
				break;
			case PAIR_NONE:
				return $this->_XMLNull;
				break;
			default:
				writeLog('Main/SysLog', "UNEXPECTED_PAIR_TYPE : $pair".__FILE__.":".__LINE__);
				return $this->_XMLNull;
				break;
		}
	}
	
	public function set($pair, $Obj)
	{
		switch ($pair)
		{
			case PAIR_STATIC:
				$this->_SPool = $Obj;
				break;
			case PAIR_DYNAMIC:
				$this->_DPool = $Obj;
				break;
			case PAIR_ALL:
				$this->_SPool = $this->_DPool = $Obj;
				break;
			case PAIR_NONE:
				break;
			default:
				writeLog('Main/SysLog', "UNEXPECTED_PAIR_TYPE : $pair".__FILE__.":".__LINE__);
				break;
		}
	}
	
	public function move($PairString)
	{
		switch ($PairString)
		{
			case "DS":
				$this->append(PAIR_STATIC, $this->get(PAIR_DYNAMIC));
				$this->set(PAIR_DYNAMIC, $this->_XMLNull);
				break;
			case "SD":
				$this->append(PAIR_DYNAMIC, $this->get(PAIR_STATIC));
				$this->set(PAIR_STATIC, $this->_XMLNull);
				break;
			default:
				writeLog('Main/SysLog', "Unknown Danmaku Pair : $PairString.".
						 " In function DanmakuPairBase::move()");
				break;
		}
	}
	
	public function clear($pair)
	{
		switch ($pair)
		{
			case PAIR_STATIC:
				$this->_SPool = $this->_XMLNull;
				break;
			case PAIR_DYNAMIC:
				$this->_DPool = $this->_XMLNull;
				break;
			case PAIR_ALL:
				$this->_SPool = $this->_DPool = $this->_XMLNull;
				break;
			case PAIR_NONE:
				break;
			default:
				writeLog('Main/SysLog', "UNEXPECTED_PAIR_TYPE : $pair".__FILE__.":".__LINE__);
				break;
		}
	}
	
	public function validate($pair)
	{
		switch ($pair)
		{
			case PAIR_STATIC:
				$errors = $this->validateStatic();
				break;
			case PAIR_DYNAMIC:
				$errors = $this->validateDynamic();
				break;
			case PAIR_ALL:
				$temp = $this->validateStatic();
				$errors = array_merge($temp, $this->validateDynamic());
				break;
			case PAIR_NONE:
				$errors = array();
				break;
			default:
				$errors = array();
				writeLog('Main/SysLog', "UNEXPECTED_PAIR_TYPE : $pair".__FILE__.":".__LINE__);
				break;
		}
		if (empty($errors))
		{
			return "$pair 状态良好";
		}
		
		$text = "";
		foreach ($errors as $error)
		{
			$text .= get_xml_error($error,$XML);
		}
		return $text;
		
	}
	
	public abstract function asXML($Pair, $Format); 
	
	abstract protected function deleteStatic($info);
	
	abstract protected function deleteDynamic($info);

	abstract protected function saveStatic();

	abstract protected function saveDynamic();

	abstract protected function validateStatic();
	 
	abstract protected function validateDynamic();
		
	protected function loadDynamic()
	{
		$auth = 'read';
		$page = RetrieveAuthPage($this->_DFile, $auth, FALSE, READPAGE_CURRENT);
		if (empty($page))
		{
			$this->_DPool = $this->_XMLNull;
			return;
		}
		
		$XMLObj = simplexml_load_string($this->_GC['XMLHeader'].$page['text'].$this->_GC['XMLFooter']);
		if ($XMLObj === FALSE)
		{
			writeLog('Main/SysLog', "$this->_GP :: $this->_DMID :: PAIR_DYNAMIC XML->FALSE");
			$this->_DPool = $this->_XMLError;
		} else {
			$this->_DPool = $XMLObj;
		}

	}
	
	protected function loadStatic()
	{
		if (!file_exists($this->_SFile))
		{
			$this->_SPool =  $this->_XMLNull;
			return;
		}
		
		$XMLObj = simplexml_load_file($this->_SFile);
		if ($XMLObj === FALSE)
		{
			writeLog('Main/SysLog', "$this->_GP :: $this->_DMID :: PAIR_STATIC XML->FALSE");
			$this->_SPool = $this->_XMLError;
		} else {
			$this->_SPool = $XMLObj;
		}
	}
	
	protected function getOpArray($pair)
	{
		switch ($pair)
		{
		case PAIR_NONE:
			return array();
		case PAIR_STATIC:
			return array('_SPool');
		case PAIR_DYNAMIC:
			return array('_DPool');
		case PAIR_ALL:
			return array('_SPool', '_DPool');
		}
	}
	
	protected function postLog($pair)
	{
		myAssert(!empty($this->_LogPage), "Danmaku Change Logging Page Not Defined.");
		
		if (($pair == PAIR_DYNAMIC) && ($GLOBALS['DMF_DynamicPairLogging'] == FALSE))
		{
			return;
		}
		
		if (empty($GLOBALS['AuthId']))
		{
			$auth = "?";
		} else {
			$auth = $GLOBALS['AuthId'];
		}
		
		$Str = "\r\n$this->_GP :: $this->_DMID :: $pair  . . . . ".strftime($GLOBALS['TimeFmt'])." . . . $auth";
		$page = ReadPage($this->_LogPage);
		$page['text'] .= $Str;
		WritePage($this->_LogPage, $page);
	}
}