<?php
abstract class Set
{
	protected $Set = array();
	
	public function add($id, $Obj)
	{
		if (!$this->isVaildType($Obj))
			return;
		$this->Set[strtoupper($id)] = $Obj;
		return $this;
	}
	
	public function __get($name)
	{
		return $this->Set[strtoupper($id)];
	}
	
	abstract protected function isVaildType($Obj);
}