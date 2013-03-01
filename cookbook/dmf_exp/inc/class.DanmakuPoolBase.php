<?php if (!defined('PmWiki')) exit();
class DanmakuPoolBase
{
	private $IOClass;
	private $XMLObj;
	private $loaded = false;
	
	public function __construct($group, $dmid, $pool, $load = LoadMode::lazy) {
		$this->IOClass = Utils::GetIOClass($group, $dmid, $pool);
		if ($load == LoadMode::inst) $this->Load();
	}
	
	private function Load() {
        try {
            $this->XMLObj = $this->IOClass->Load();
        } catch (XmlOperationException $e) {
            $this->IOClass = new ErrorPoolIO($e);
            $this->XMLObj  = $this->IOClass->Load();
        }
        $this->loaded = true;
	}
	
	public function Find(DanmakuXPathBuilder $query)
	{
        if (!$this->loaded) $this->load();
        
		$result = $this->XMLObj->xpath($query->ToString());
        if ($result === FALSE) {
            Utils::WriteLog('DanmakuPoolBase::Find::SimpleXML::xpath()', "Synatx Error".$query->ToString());
            throw new Exception("Bad Synatx");
            die("2");
        } else {
            return $result;
        }
	}
	
	public function Delete(DanmakuXPathBuilder $query)
	{
        if (!$this->loaded) $this->load();
        
		$result = $this->Find($query);
		foreach (array_reverse($result) as $node)
		{
			unset($node[0]);
		}
	}
	
	public function Modify($obj)
	{
        if (!$this->loaded) $this->load();
        
		$query = new DanmakuXPathBuilder();
		$query->CommentId( $obj->{"@id"} );
		$result = $this->Find($query);
		foreach ($result as $node) {
			$node = $obj;
		}
	}
	
	public function Clear()
	{
		$this->XMLObj = DanmakuPoolBase::GetEmpty();
		$this->loaded = true;
	}
	
	public function MoveFrom(DanmakuPoolBase $pool)
	{
        if (!$this->loaded) $this->load();
		$this->Merge($pool->GetXML());
		$pool->Clear();
	}
	
	public function Dispose()
	{
		unset($this->XMLObj);
	}
	
	public function Save()
	{
        if (!$this->loaded) throw new Exception("Save() on unloaded pool");
		$this->IOClass->Save($this->XMLObj);
	}
	
	public function SaveAndDispose()
	{
        $this->Save();
        $this->Dispose();
	}
	
	public function GetXML()
	{
        if (!$this->loaded) $this->load();
		return $this->XMLObj;
	}
	
	public function SetXML($obj)
	{
		$this->XMLObj = $obj;
		$this->loaded = true;
	}
	
	protected function Merge(SimpleXMLElement $xml2)
	{
        if (!$this->loaded) $this->load();
	   // convert SimpleXML objects into DOM ones
	   $dom1 = dom_import_simplexml($this->XMLObj)->ownerDocument;
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
	
	public static function GetEmpty()
	{
		return simplexml_load_string('<comments></comments>');
	}
}

