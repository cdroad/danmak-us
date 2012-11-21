<?php if (!defined('PmWiki')) exit();

class DanmakuBarSet
{
    private $arr = array();
    private $gc;
    
	public function __construct(GroupConfig $groupConfig)
	{
        $this->gc = $groupConfig;
	}
	
    public function add(DanmakuBarItem $obj)
    {
        $this->arr[] = $obj;
    }
    
	private function getAuthLevel($pagename)
	{
		global $AuthId, $Author;

		$IsAuthed = !empty($AuthId);
		if (!empty($Author))
		{
			$IsPageCreator = (PageVar($pagename,'$CreatedBy') != "") &&
				(PageVar($pagename,'$CreatedBy') == $Author);
			$IsLastEditor = 
				(PageVar($pagename,'$LastModifiedBy') == $Author);
		}
	
		if ( $IsAuthed || $IsPageCreator || $IsLastEditor)
		{
			if (CondAuth($pagename, 'admin'))
			{
				return DanmakuBarItem::$Auth->Admin;
			} else {
				return DanmakuBarItem::$Auth->Member;
			}
		}
		return DanmakuBarItem::$Auth->Guest;
	}
	
	public function getString(VideoData $source)
	{
		$Auth = $this->getAuthLevel($source->pagename);
        $str = "";
        foreach ($this->arr as $item)
        {
            if ($Auth >= $item->auth()) {
                $res = $item->getString($this->gc);
                if ($item->selfStyle()) {
                    $str .= $res;
                } else {
                    $str .= '%danmakubar% '.$res.'%%&nbsp;&nbsp;';
                }
                
            }
        }
		return $str;
	}
}