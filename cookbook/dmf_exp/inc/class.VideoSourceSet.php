<?php if (!defined('PmWiki')) exit();
class VideoSourceSet extends Set
{
    protected function get($name)
    {
        if (array_key_exists($name, $this->Set)) {
            return $this->Set[$name];
        } else {
            throw new Exception("找不到那个悲剧啊～");
        }
    }

	protected function isVaildType($Obj)
	{
		return $Obj instanceof VideoSourceBase;
	}
}