<?php
class GroupConfigSet extends Set
{
	protected function isVaildType($Obj)
	{
		return $Obj instanceof GroupConfigBase;
	}
}