<?php
abstract class GroupConfigBase
{
	/**
	 * 
	 * @var char
	 */
	protected $SUID;
	
	//XML
	/**
	 * 
	 * @var string
	 */
	protected $XMLHeader = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<comments>";
	/**
	 * 
	 * @var string
	 */
	protected $XMLFooter = '</comments>';
	/**
	 * 
	 * @var string
	 */
	protected $XMLFolderPath;
	/**
	 * 
	 * @var SimpleXMLElement
	 */
	protected $XMLError;

	protected $DanmakuBarSet;
	
	protected $VideoSourceSet;
	
	protected $PlayersSet;
	
	protected function GroupConfigBase()
	{
		$this->XMLError = simplexml_load_string
		(
			'<?xml version="1.0" encoding="UTF-8"?>'.
			'<comments><comment id="DEADBEEF"><text>'.
			'警告 : DanmakuPairBase :: Load() 无法加载弹幕池，请运行XML校验</text><attrs>'.
			'<attr playtime="1" mode="1" fontsize="25" color="16777215" '.
			'sendtime="1311660679" poolid="0" userhash="DEADBEEF"'.
			'/></attrs></comment></comments>'
		);
	}
	
	abstract public function GenerateFlashVarArr(VideoData $source);
	
	abstract public function HandleXMLPost($dmid, $pair, $Append, $File);
	
	abstract public function doPoolConv($id, $from, $to);
	
	abstract public function doXMLLoad($id);
	
	abstract public function doValidate($id,$pair);
	
	public function __get($name)
	{
		return $this->$name;
	}
}

