<?php if (!defined('PmWiki')) exit();
class PmDB
{
	public $page;
	public $newp;
	public $pn;
	
	function __construct($pagename,$auth = 'edit')
	{
		$this->pn = $pagename;
		$this->page = @RetrieveAuthPage($pagename, $auth, true, READPAGE_CURRENT);
		$this->newp = $this->page;
		if (!$this->page) Abort("?cannot load $pagename");
	}
	
	public  function add($vn,$vv,$exp = 0)
	{
		$this->newp[$vn] = rawurlencode($vv);
		if ($exp) $this->newp[$vn."exp"] = time() + $exp;
	}
	
	public  function read($vn)
	{
		if ($this->page[$vn] == "") return "";
		$d = rawurldecode($this->page[$vn]);
		if ($this->page[$vn."exp"] && (time() >= $this->page[$vn."exp"])) {$this->delete($vn);}
		return $d;
	}
	
	public  function delete($vn)
	{
		unset($this->newp[$vn."exp"]);
		unset($this->newp[$vn]);
	}
	
	public  function save()
	{
		global $EnableNotify;
		$EnableNotify = 0;
		
		UpdatePage($this->pn, $this->page,$this->newp);
	}
}