<?php
class VideoSourceSet extends Set
{
	protected function isVaildType($Obj)
	{
		return $Obj instanceof VideoSourceBase;
	}
}