<?php
abstract class Set implements Iterator
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
		return $this->Set[strtoupper($name)];
	}

	public function current()
	{
		return current($this->Set);
	}
	
	public function key()
	{
		return key($this->Set);
	}
	
	public function next()
	{
		next($this->Set);
	}
	
	public function rewind()
	{
		reset($this->Set);
	}
	
	public function valid()
	{
		return $this->isVaildType($this->current());
	}

	abstract protected function isVaildType($Obj);
}