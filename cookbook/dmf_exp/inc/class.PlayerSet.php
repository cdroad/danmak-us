<?php
class PlayerSet extends Set
{
	public function addDefault($id)
	{
		$this->add('Default', $this->Set[strtoupper($id)]);
	}
	
	protected function isVaildType($Obj)
	{
		return $Obj instanceof Player;
	}
}
$PlayerSet = new PlayerSet();
