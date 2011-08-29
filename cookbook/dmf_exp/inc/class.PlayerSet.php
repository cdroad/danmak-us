<?php
class PlayerSet extends Set implements Iterator
{
	public function addDefault($id)
	{
		$this->add('Default', $this->Set[strtoupper($id)]);
	}
	
	protected function isVaildType($Obj)
	{
		return $Obj instanceof Player;
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
}

