<?php
class DanmakuPoolBase
{
	private $IOClass;
	/**
	 * @var SimpleXMLElement 
	 */
	private $XMLObj;
	
	
	public function __construct($IOClass, $load = true)
	{
		$this->IOClass = $IOClass;
		if ($load) {
			$this->XMLObj = $IOClass->Load();
		}
	}
	
	public function Find(DanmakuXPathBuilder $query)
	{
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
		$result = $this->Find($query);
		foreach (array_reverse($result) as $node)
		{
			unset($node[0]);
		}
	}
	
	public function Modify($obj)
	{
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
	}
	
	public function MoveFrom(DanmakuPoolBase $pool)
	{
		$this->merge($pool->GetXML());
		$pool->Clear();
	}
	
	public function Dispose()
	{
		unset($this->XMLObj);
	}
	
	public function Save()
	{
		$this->IOClass->Save($this->XMLObj);
		return $this;
	}
	
	public function GetXML()
	{
		return $this->XMLObj;
	}
	
	public function SetXML($obj)
	{
		$this->XMLObj = $obj;
	}
	
	protected function merge(SimpleXMLElement $xml2)
	{
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

