<?php

abstract class VideoSourceBase
{

	protected $PageNameAsDanmakuId;
	protected $MutiAble;
	protected $UrlConvert;
	
	protected $danmakuId;
	protected $source;
	
	abstract public function getType();
	
	public function init(VideoData $dataSource)
	{
		
		if ($this->PageNameAsDanmakuId)
		{
			$this->danmakuId = PageVar($dataSource->pagename, '$:Name');
		} else {
			$this->danmakuId = $dataSource->source;
		}
		
		if ($this->MutiAble)
		{
			$this->warpDanmakuId($dataSource);
		}
		
		if ($this->UrlConvert)
		{
			$this->source = $this->convertVideoUrl($dataSource);
		} else {
			$this->source = $this->danmakuId;
		}
	}
	
	public function warpDanmakuId($dataSource)
	{
		$this->danmakuId .= "P".$dataSource->partIndex; 
	}
	
	public function convertVideoUrl(VideoData $dataSource)
	{
		
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
}

class XinaSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = true;
		$this->PageNameAsDanmakuId = false;
		$this->UrlConvert = false;
	}
	
	public function getType()
	{
		return "nor";
	}
}

class TuDouSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = false;
		$this->PageNameAsDanmakuId = false;
		$this->UrlConvert = false;
	}
	public function getType()
	{
		return "td";
	}	
}

class QQSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = true;
		$this->PageNameAsDanmakuId = false;
		$this->UrlConvert = true;
	}
	public function getType()
	{
		return "qq";
	}
	
	public function convertVideoUrl($dataSource)
	{
		return rawurlencode(
			'https://secure.bluehost.com/~twodland/dmf/index.php?n=Main.Flvcache&action=GetFlvUrl&vid='.
			$dataSource->source);
	}
}

class sixRoomSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = true;
		$this->PageNameAsDanmakuId = false;
		$this->UrlConvert = true;
	}
	public function getType()
	{
		return "6cn";
	}
}

class URLSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = true;
		$this->PageNameAsDanmakuId = true;
		$this->UrlConvert = true;
	}
	public function getType()
	{
		return "url";
	}
	
	public function convertVideoUrl($dataSource)
	{
		return rawurlencode($dataSource->source);
	}
}

class BURLSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = true;
		$this->PageNameAsDanmakuId = false;
		$this->UrlConvert = true;
	}
	public function getType()
	{
		return "burl";
	}
	
	public function convertVideoUrl($dataSource)
	{
		return rawurlencode('http://pl.bilibili.us/'.
			str_replace(array("levelup"),"/",$dataSource->source).
			'.flv');
	}
}

class LocalSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = true;
		$this->PageNameAsDanmakuId = true;
		$this->UrlConvert = true;
	}
	public function getType()
	{
		return "local";
	}
	
	public function convertVideoUrl($dataSource)
	{
		return rawurlencode($dataSource->source);
	}
}

class YouKuSource extends VideoSourceBase
{
	public function __construct()
	{
		$this->MutiAble = true;
		$this->PageNameAsDanmakuId = false;
		$this->UrlConvert = false;
	}
	public function getType()
	{
		return "yk";
	}
}

