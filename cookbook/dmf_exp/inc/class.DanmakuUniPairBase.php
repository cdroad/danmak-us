<?php if (!defined('PmWiki')) exit();

define("PAIR_NONE", "PAIR_NONE");
define("PAIR_STATIC", "PAIR_STATIC");
define("PAIR_DYNAMIC", "PAIR_DYNAMIC");
define("PAIR_ALL", "PAIR_ALL");

abstract class DanmakuUniPairBase
{

	/**
	 * @var SimpleXMLElement
	 */
	private $SPoolObj;
	/**
	 * @var SimpleXMLElement
	 */
	private $DPoolObj;
	/**
	 * @var SimpleXMLElement
	 */
	private $ErrorXMLObj;
	/**
	 * @var SimpleXMLElement
	 */
	private $NullXMLObj;
	/**
	 * @var array
	 */
	private $GroupConfig;
	/**
	 * @var array
	 */
	private $Group;
	/**
	 * @var string
	 */
	private $SPoolFile;
	/**
	 * @var string
	 */
	private $DPoolFile;
	/**
	 * @var string
	 */
	private $dmid;
	/**
	 * @var string
	 */
	private $LogPage = "Main/SysLog";
	
	
	public function __construct($group, $dmid, $pair = "PAIR_ALL")
	{
		$this->dmid = $dmid;
		
		$this->Group = $group;
		$this->GroupConfig = $GLOBALS['GroupConfigSet']->$group;
		
		$this->ErrorXMLObj = simplexml_load_string($this->GroupConfig->XMLError);
		
		$this->NullXMLObj = simplexml_load_string($this->GroupConfig->XMLHeader.
			$this->GroupConfig->XMLFooter);
		
		$this->SPoolFile = "./uploads/$this->Group/$dmid.xml";
		$this->DPoolFile = 'DMR.'.$this->GroupConfig->SUID.$this->dmid;
		
		$this->load($pair);
		
	}
	
	final public function load($pair)
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
		return $this->$pair->xpath($this->convertQuery($query));
	}
	
	final public function insert($pair, SimpleXMLElement $xml)
	{
		$this->merge($this->$pair, $xml);
	}
	
	final public function update($pair, $query, SimpleXMLElement $xml)
	{
		$result = $this->find($pair, $query);
		foreach (array_reverse($result) as $node)
		{
			$node[0] = $xml;
		}
	}
	
	final public function delete($pair, $query)
	{
		if ($pair != PAIR_DYNAMIC)
			return;

		$result = $this->find($pair, $query);
		foreach (array_reverse($result) as $node)
		{
			unset($node[0]);
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
	
	final public function import($pair, SimpleXMLElement $from)
	{
		$tempPair = $this->$pair;
		$this->merge($tempPair, $from);
		$this->$pair = $tempPair;
	}
	
	final public function validate($pair)
	{
		libxml_clear_errors();
		$this->load($pair);
		$errors = libxml_get_errors();
		return display_xml_error($errors, $this);
	}
	
	final public function save($pair = "PAIR_ALL")
	{
		switch (strtoupper($pair))
		{
			case "PAIR_NONE":
				break;
			case "PAIR_STATIC":
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
				echo strtoupper($pair);
				assert(FALSE);
		}
	}
	
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			return $this->$name;
		}
		
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
				$this->merge($tempPool, $this->DPoolObj);
				return $tempPool;
			default:
				var_dump($name);
				assert(FALSE);
				return NULL;
		}
	}
	
	public function __set($name, SimpleXMLElement $Obj)
	{
		
		switch (strtoupper($name))
		{
			case "OBJE_ERROR":
				$this->ErrorXMLObj = $Obj;
				break;
			case "OBJE_NULL":
			case "PAIR_NULL":
			case "PAIR_NONE":
				$this->NullXMLObj = $Obj;
				break;
			case "PAIR_STATIC":
				$this->SPoolObj = $Obj;
				break;
			case "PAIR_DYNAMIC":
				$this->DPoolObj = $Obj;
				break;
			case "PAIR_ALL":
				$this->DPoolObj = $this->SPoolObj = $Obj;
				break;
			default:
				echo $name;
				assert(FALSE);
		}
		return;
	}
	
	protected function loadStaticPool()
	{
		$p = 'PAIR_STATIC';
		
		if (!file_exists($this->SPoolFile))
		{
			$this->$p =  $this->NullXMLObj;
			return;
		}
		
		$Obj = simplexml_load_file($this->SPoolFile);
		
		if ($Obj === FALSE)
		{
			$this->WriteLog($p, "STATIC XML->FALSE");
			$this->$p = $this->ErrorXMLObj;
		} else {
			$this->$p = $Obj;
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
		
		$XML = $this->GroupConfig->XMLHeader.$page['text'].
			$this->GroupConfig->XMLFooter;
		$Obj = simplexml_load_string($XML);

		if ($Obj === FALSE)
		{
			$this->WriteLog($p, "DYNAMIC XML->FALSE");
			$this->$p = $this->ErrorXMLObj;
		} else {
			$this->$p = $Obj;
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
		
		$Str = "\n$this->Group :: $this->dmid :: $pair $msg . . . . ".strftime($GLOBALS['TimeFmt'])." . . . $auth";
		writeLog($this->LogPage, $Str);
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
		
		$tempObj = $this->XMLFilter($this->SPoolObj);
		
		$result = file_put_contents($this->SPoolFile,
			$tempObj->saveXML(),
			LOCK_EX);
		if ($result == FALSE)
		{
			$className = get_class();
			$str = "$className::saveStatic FAIL : COUND NOT WRITE TO STATIC POOL FILE.".
					" DMID = $this->dmid; FILE = $this->SPoolFile";
			$this->WriteLog("PAIR_STATIC", $str);
		}
	}
	
	/*******************************XML Functions*******************************/

	protected function convertQuery($query)
	{
		$queryString = "//comments/comment";
		
		if (key_exists('and', $query))
		{
			foreach ($query['and'] as $k => $v)
			{
				$queryString .= 
					"[".
					call_user_func(array($this, "KVQueryToXPath"), $k, $v).
					"]";
			}
		} else if (key_exists('or', $query)) {
			$queryString .= "[";
			foreach ($query['or'] as $k => $v)
			{
				$queryString .= call_user_func(array($this, "KVQueryToXPath"), $k, $v).
					" or ";
			}
			$queryString = substr($queryString, 0, -4)."]";
		}
		return $queryString;
	}
	
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
			default:
				assert(FALSE);
				return "";
		}
	}

	protected function merge(SimpleXMLElement $xml1, SimpleXMLElement $xml2)
	{
	   // convert SimpleXML objects into DOM ones
	   $dom1 = dom_import_simplexml($xml1)->ownerDocument;
	   $dom2 = dom_import_simplexml($xml2)->ownerDocument;
	
	   // pull all child elements of second XML
	   $xpath = new domXPath($dom2);
	   $xpathQuery = $xpath->query('/*/*');
	   for ($i = 0; $i < $xpathQuery->length; $i++)
	   {
	       // and pump them into first one
	       $dom1->documentElement->appendChild(
	           $dom1->importNode($xpathQuery->item($i), true));
	   }
	}
	
	/**
	 * 
	 * XML过滤
	 * @param DOMDocument $node
	 * @return DOMDocument
	 */
	abstract protected function XMLFilter(SimpleXMLElement $node);

	abstract protected function asXML($pair = "PAIR_ALL", $format);
	
}

/*`
<comment id="2147483647">
	<text>var b="bbbbbbbbb";</text>
	<attrs>
		<attr playtime="2" mode="8" fontsize="25" color="16777215" sendtime="1313039597" poolid="2" userhash="DEADBEEF"></attr>
	</attrs>
</comment>
 */