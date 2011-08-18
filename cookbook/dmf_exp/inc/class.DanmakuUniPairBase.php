<?php if (!defined('PmWiki')) exit();

define("PAIR_NONE", "PAIR_NONE");
define("PAIR_STATIC", "PAIR_STATIC");
define("PAIR_DYNAMIC", "PAIR_DYNAMIC");
define("PAIR_ALL", "PAIR_ALL");

abstract class DanmakuUniPairBase
{
	/**
	 * 
	 * 静态池XML对象.
	 * @var DOMDocument
	 */
	private $SPoolObj;
	/**
	 * 
	 * 动态池XML对象.
	 * @var DOMDocument
	 */
	private $DPoolObj;
	/**
	 * 
	 * 用于提示错误的XML对象
	 * @var DOMDocument
	 */
	private $ErrorXMLObj;
	/**
	 * 
	 * 空XML对象
	 * @var DOMDocument
	 */
	private $NullXMLObj;
	/**
	 * 
	 * 组配置
	 * @var array
	 */
	private $GroupConfig;
	/**
	 * 
	 * 组名
	 * @var string
	 */
	private $Group;
	/**
	 * 
	 * S池储存文件
	 * @var string
	 */
	private $SPoolFile;
	/**
	 * 
	 * D池储存文件
	 * @var DPool
	 */
	private $DPoolFile;
	private $dmid;
	private $LogPage = "Main/SysLog";
	
	
	public function __construct($group, $dmid, $pair = "PAIR_ALL")
	{
		$this->dmid = $dmid;
		
		$this->Group = $group;
		$this->GroupConfig = $GLOBALS['DMF_GroupConfig'][$group];
		
		$this->ErrorXMLObj = new DOMDocument("1.0", "UTF-8");
		$this->ErrorXMLObj->loadXML($this->GroupConfig['XMLError']);
		
		$this->NullXMLObj = new DOMDocument("1.0", "UTF-8");
		$this->NullXMLObj->loadXML($this->GroupConfig['XMLHeader'].
			$this->GroupConfig['XMLFooter']);
		
		$this->SPoolFile = realpath("./uploads/$this->Group/$dmid.xml");
		$this->DPoolFile = 'DMR.'.$this->_SUID.$this->_DMID;
		
		$this->load($pair);
	}
	
	public function load($pair)
	{
		$this->DPoolObj = $this->SPoolObj = $this->NullXMLObj;
		switch (strtoupper($pair))
		{
			case "PAIR_NONE":
				break;
			case "PAIR_STATIC":
				$this->loadStaticPool();
				break;
			case "PAIR_DYNAMIC":
				$this->loadDynamicPool();
			case "PAIR_ALL":
				$this->loadStaticPool();
				$this->loadDynamicPool();
				break;
		}
	}
	
	// Find Insert Update Delete
	
	final public function find($pair, $query)
	{
		$xpath_obj = new DOMXPath($this->$pair);
		return $xpath_obj->query($this->convertQuery($query));
	}
	
	final public function insert($pair, DOMDocument $DOM)
	{
		$this->$pair->importNode($DOM);
	}
	
	final public function update($pair, $id, DOMDocument $new)
	{
		foreach ($this->find($pair, array("id" => "$id")) as $node)
		{
			$node = $new;
		}
	}
	
	final public function delete($pair, $query)
	{
		$result = $this->find($pair, $query);
		foreach ($result as $node)
		{
			$node->parentNode->removeChild($node);
		}
	}
	
	final public function clearPool($pair)
	{
		$this->$pair = $this->PAIR_NONE;
	}
	
	final public function movePool($from, $to)
	{
		$this->import($to, $this->$from);
		$this->$from = $this->PAIR_NONE;
	}
	
	final public function import($pair, $from)
	{
		$tempPair = $this->$pair;
		$tempPair->importNode($from, TRUE);
		$this->$pair = $tempPair;
	}
	
	final public function validate($pair)
	{
		libxml_clear_errors();
		$this->load($pair);
		$errors = libxml_get_errors();
		return "";
	}
	
	public function __get($name)
	{
		switch (strtoupper($name))
		{
			case "OBJE_ERROR":
				return $this->ErrorXMLObj;
			case "OBJE_NULL":
			case "PAIR_NULL":
			case "PAIR_NONE":
				return $this->NullXMLObj;
			case "PAIR_STATIC":
				return $this->SPoolObj;
			case "PAIR_DYNAMIC":
				return $this->DPoolObj;
			case "PAIR_ALL":
				$tempPool = $this->SPoolObj;
				$tempPool->importNode($this->DPoolObj, true);
				return $tempPool;
			default:
				asert(FALSE);
				return NULL;
		}
	}
	
	public function __set($name, DOMDocument $Obj)
	{
		switch (strtoupper($name))
		{
			case "OBJE_ERROR":
				$this->ErrorXMLObj = $Obj;
			case "OBJE_NULL":
			case "PAIR_NULL":
			case "PAIR_NONE":
				$this->NullXMLObj = $Obj;
			case "PAIR_STATIC":
				$this->SPoolObj = $Obj;
			case "PAIR_DYNAMIC":
				$this->DPoolObj = $Obj;
			case "PAIR_ALL":
				$this->DPoolObj = $this->SPoolObj = $Obj;
			default:
				assert(FALSE);
		}
		return;
	}
	
	private function convertQuery($query)
	{
		$queryString = "//comments/comment";
		foreach ($query as $key => $value)
		{
			$queryString .= $this->KVQueryToXPath($key, $value);
		}
		return $queryString;
	}
	
	protected function loadStaticPool()
	{
		$p = 'PAIR_STATIC';
		
		if (!file_exists($this->SPoolFile))
		{
			$this->$p =  $this->NullXMLObj;
			return;
		}
		
		$tempDOM = new DOMDocument("1.0", "UTF-8");
		$tempDOM->formatOutput = true;
		$bResult = $tempDOM->load($this->SPoolFile);
		if ($bResult === FALSE)
		{
			$this->WriteLog($p, "XML->FALSE");
			$this->$p = $this->ErrorXMLObj;
		} else {
			$this->$p = $tempDOM;
		}
	}
	
	protected function loadDynamicPool()
	{
		$p = 'PAIR_DYNAMIC';
		$auth = 'read';
		
		$page = RetrieveAuthPage($this->DPoolFile, $auth, FALSE, READPAGE_CURRENT);
		if (empty($page))
		{
			$this->$p = $this->NullXMLObj;
		}
		
		$XML = $this->GroupConfig['XMLHeader'].$page['text'].
			$this->GroupConfig['XMLFooter'];
		$tempDOM = new DOMDocument("1.0", "UTF-8");
		$tempDOM->formatOutput = true;
		$bResult = $tempDOM->loadXML($XML);

		if ($bResult === FALSE)
		{
			$this->WriteLog($p, "XML->FALSE");
			$this->$p = $this->ErrorXMLObj;
		} else {
			$this->$p = $tempDOM;
		}
		
	} 
	
	protected function WriteLog($pair, $msg)
	{	
		if (($pair == 'PAIR_DYNAMIC') && ($GLOBALS['DMF_DynamicPairLogging'] == FALSE))
		{
			return;
		}
		
		if (empty($GLOBALS['AuthId']))
		{
			$auth = $_SERVER['REMOTE_ADDR'];
		} else {
			$auth = $GLOBALS['AuthId'];
		}
		
		$Str = "\r\n$this->Group :: $this->dmid :: $pair $msg . . . . ".strftime($GLOBALS['TimeFmt'])." . . . $auth";
		writeLog($this->LogPage, $Str);
	}
	
	protected function save($pair = "PAIR_ALL")
	{
		switch (strtoupper($pair))
		{
			case "PAIR_NONE":
				break;
			case "PAIR_STAITC":
				$this->saveStaticPool();
				break;
			case "PAIR_DYNAMIC":
				$this->saveDynamicPool();
				break;
			case "PAIR_ALL":
				$this->saveStaticPool();
				$this->saveDynamicPool();
				break;
			default:
				assert(FALSE);
		}
	}
	
	protected function saveDynamicPool()
	{
		$auth = 'edit';
		$new = $old = RetrieveAuthPage($this->DPoolFile, $auth, FALSE, 0);
		
		$tempObj = $this->XMLFilter($this->DPoolObj);
		$new['text'] = $tempObj->saveXML();
		
		UpdatePage($this->DPoolFile, $old, $new);
	}
	
	protected function saveStaticPool()
	{
		if (file_exists($this->SPoolFile))
		{
			rename($this->SPoolFile, $this->SPoolFile.",del-".time());
		}
		
		$tempObj = $this->XMLFilter($this->DPoolObj);
		
		$result = file_put_contents($this->SPoolFile,
			$tempObj->saveXML(),
			LOCK_EX);
		if ($result == FALSE)
		{
			$className = get_class();
			$str = "$className::saveStatic FAIL : COUND NOT WRITE TO STATIC POOL FILE.".
					" DMID = $this->_DMID; FILE = $this->_SFile";
			$this->WriteLog("PAIR_STATIC", $str);
		}
	}
	
	/**
	 * 
	 * XML过滤
	 * @param DOMDocument $node
	 * @return DOMDocument
	 */
	abstract protected function XMLFilter(DOMDocument $node);

	abstract protected function asXML($pair = "PAIR_ALL");
	
	protected function KVQueryToXPath($k, $v)
	{
		//TODO
		switch (strtoupper($k))
		{
			case "ID":
				return "[@id='$v']";
			case "TEXT":
			case "MESSAGE":
			case "CMT":
				return "[contains(string(text),'$v']";
			case "PLAYTIME":
				return "[//attrs/attr@playtime='$v']"; 
			case "mode":
				return "[//attrs/attr@mode='$v']"; 
			case "fontsize":
				return "[//attrs/attr@fontsize='$v']"; 
			case "color":
				return "[//attrs/attr@color='$v']"; 
			case "sendtime":
				return "[//attrs/attr@sendtime='$v']"; 
			case "poolid":
				return "[//attrs/attr@poolid='$v']"; 
			case "userhash":
				return "[//attrs/attr@puserhash='$v']"; 
			default:
				assert(FALSE);
				return "";
		}
	}
}

/*
<comment id="2147483647">
	<text>var b="bbbbbbbbb";</text>
	<attrs>
		<attr playtime="2" mode="8" fontsize="25" color="16777215" sendtime="1313039597" poolid="2" userhash="DEADBEEF"></attr>
	</attrs>
</comment>
 */