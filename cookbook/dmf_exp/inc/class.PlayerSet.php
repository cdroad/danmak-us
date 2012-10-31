<?php if (!defined('PmWiki')) exit();
class PlayerSet extends Set
{
    protected function get($name)
    {
        if (array_key_exists($name, $this->Set)) {
            return $this->Set[$name];
        } else {
            return false;
        }
    }
    
	public function addDefault($id)
	{
		$this->add('Default', $this->Set[strtolower($id)]);
	}
	
	protected function isVaildType($Obj)
	{
		return $Obj instanceof Player;
	}
}

