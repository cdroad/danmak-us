<?php
//abstract class GroupConfig
abstract class GroupConfig
{
    protected $GroupString = 'Twodland1';
    protected $AllowedXMLFormat = array('raw', 'comments');
	protected $SUID = 'D';
	protected $XMLFolderPath = './uploads/Twodland1';
    
    //×Ô¶¯ÉèÖÃ
	protected $DanmakuBarSet;
	protected $VideoSourceSet;
	protected $PlayersSet;
    protected static $Inst;
    
    protected function __construct() {
        $this->VideoSourceSet = new VideoSourceSet();
        $this->VideoSourceSet
                ->add('nor'		, new XinaSource())
                ->add('td'		, new TuDouSource())
                ->add('qq'		, new QQSource())
                ->add('6cn'		, new sixRoomSource())
                ->add('local'	, new LocalSource())
                ->add('link'	, new URLSource())
                ->add('url'		, new URLSource())
                ->add('burl'	, new BURLSource())
                ->add('blink'	, new BURLSource());
                
        $this->PlayersSet = new PlayerSet();
        $this->DanmakuBarSet = new DanmakuBarSet($this);
    }
    
    public abstract function GenerateFlashVarArr(VideoPageData $vdp);
    
    public abstract function ConvertToUniXML(SimpleXMLElement $obj);
}
