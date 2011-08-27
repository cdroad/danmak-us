<?php
$playerCodeHeader = <<<STR
<script type="text/javascript">
var flashvars = {};
var params = {};
params.menu = "true";
params.allowscriptaccess = "always";
params.allowfullscreen = "true";
params.bgcolor = "#FFFFFF";
params.autostart = "false";
params.play = "false";
//params.scale = "noscale";
//params.wmode = "opaque";

STR;

class VideoData
{
	private $source;
	private $sourcetype;
	private $partIndex;
	/**
	 * 
	 * @var Player
	 */
	private $player;
	private $muti;
	private $pagename;
	private $groupConfig;
	
	private $dmid;
	
	private $PlayerLoadCode;
	private $DanmakuBarCode;
	private $PlayerLinkCode;
	private $PartIndexCode;
	
	private $stat = true;
	
	public function __construct($pn)
	{
		$this->initVars($pn);
		$this->initCodes();
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
	private function initVars($pn)
	{
		if (!PageExists($pn))  {assert (FALSE);$this->setBroken();return;}
		$this->pagename = $pn;
		$group = PageVar($this->pagename, '$Group');
		
		$this->groupConfig = BaseFunc::getGroupConfigObj($group);
		
		$page = ReadPage($this->pagename);
		
		$this->source = PageVar($this->pagename,'$:VideoStr');
		$this->muti = $this->source instanceof IMutiAble;
		
		$partIndex = intval($_REQUEST['part']);
		$isRequestPartIndexExist = ($part > 1);
		$isRequestPartIndexVaild = !empty(PageVar($this->pagename, '$:P'.$partIndex));
		
		if ($this->muti && $isRequestPartIndexExist && $isRequestPartIndexVaild)
		{
			$this->partIndex = $partIndex;
		} 
		
		
		$UserPreferPlayer = $page["PartPlayer_".$this->partIndex];
		$UserPreferPlayer = $_REQUEST['player'];
		if ( !empty($GLOBALS['PlayerSet']->$PartPreferPlayer) )
		{
			$this->player = $GLOBALS['PlayerSet']->$PartPreferPlayer;
		}
		else
		if ( !empty($GLOBALS['PlayerSet']->$PartPreferPlayer) )
		{
			$this->player = $GLOBALS['PlayerSet']->$UserPreferPlayer;
		}
		else
		{
			$this->player = $GLOBALS['PlayerSet']->Default;
		}

		$vt = PageVar($this->pagename,'$:VideoType');
		if (is_null($GLOBALS['VideoSourceSet']->$vt))  {assert (FALSE);$this->setBroken();return;}
		$this->sourcetype = $GLOBALS['VideoSourceSet']->$vt->init($this);
		$this->dmid = $this->sourcetype->danmakuId;
	
	}
	
	private function initCodes()
	{
		$this->initDanmakuBarCode();
		$this->initPartIndexCode();
		$this->initPlayerLinkCode();
		$this->initPlayerLoadCode();
	}
	
	private function initPlayerLoadCode()
	{
		$this->PlayerLoadCode = $GLOBALS['playerCodeHeader'];
		$AFVArray = $this->groupConfig->GenerateFlashVarArr($this);
		$this->PlayerLoadCode .= $this->AFVArrayToJavascript($AFVArray);
		
		//加载SWF
		$this->PlayerLoadCode .= $this->genSWFObjectCode();
	}
	
	private function genSWFObjectCode()
	{
		return "swfobject.embedSWF(\"".$this->player->playerUrl.
			"\", \"flashcontent\", \"".$this->player->width.
			"\", \"".$this->player->height.
			"\", \"10.0.0\",\"expressInstall.swf\", flashvars, params);</script>";	
	}
	
	private function AFVArrayToJavascript($arr)
	{
		$str = '';
		foreach ($arr as $k => $v) {
			$str .= "flashvars.$k = \"$\";\r\n";
		}
		return $str;
	}
	
	private function initDanmakuBarCode()
	{
		$Obj = new DanmakuBarSet();
		$this->DanmakuBarCode = $Obj->getArray($this);
	}
	
	private function initPlayerLinkCode()
	{
		$Obj = $GLOBALS['PlayerSet'];
		$isPreferPlayerAuthed = CondAuth($this->pagename, 'admin');
		
		foreach ($Obj as $playerId => $playerObj)
		{
			if ($this->player == $playerObj)
			{
				$this->PlayerLinkCode .= 
					"&nbsp;&nbsp;'''".$playerObj->desc."'''";
			} else {
				$this->PlayerLinkCode .= "&nbsp;&nbsp;[[".
					$playerObj->playerUrl.
					"?Player=$playerId | ".$playerObj->desc.' ]]';
			}
			
			if ($isPreferPlayerAuthed)
			{
				$this->PlayerLinkCode .= '[[{*$host}'.
					"?Part=$this->partIndex".
					"?Player=$playerId?action=setdef | "."&nbsp;'^Def^'".' ]]';
			}
		}
	}
	
	private function initPartIndexCode()
	{
		$index = 1;
		while ($Part = PageVar($this->pagename, '$:P'.$index))
		{
			assert(!empty($Part));
			
			if ($index == $this->partIndex)
			{
				$this->PartIndexCode .= 
					'&nbsp;&nbsp;'."'''P$index'''".'&nbsp;&nbsp;';
				$index++;
				continue;
			}

			if ($index == 1)
			{
				$this->PartIndexCode .= '&nbsp;&nbsp;'.
					'[[{$host}'.
					" | '''P$index''']]&nbsp;&nbsp;";
				$index++;continue;
			}
			
			$this->PartIndexCode .= '&nbsp;&nbsp;'.
				'[[{$host}'.
				"?Part=$index | '''P$index''']]&nbsp;&nbsp;";
			$index++;
			
		}
	}
	
	private function setBroken()
	{
		$this->stat = FALSE;
	}
}