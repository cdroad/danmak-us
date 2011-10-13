<?php
class VideoSourceSet extends Set
{
	protected function isVaildType($Obj)
	{
		return $Obj instanceof VideoSourceBase;
	}
}

$VideoSourceSet = new VideoSourceSet();
$VideoSourceSet
	->add('nor'		, new XinaSource())
	->add('td'		, new TuDouSource())
	->add('qq'		, new QQSource())
	->add('6cn'		, new sixRoomSource())
	->add('local'	, new LocalSource())
	->add('link'	, new URLSource())
	->add('url'		, new URLSource())
	->add('burl'	, new BURLSource())
	->add('blink'	, new BURLSource());