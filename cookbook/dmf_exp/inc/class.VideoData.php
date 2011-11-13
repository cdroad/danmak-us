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

STR;

function DMF_RV($x)
{
	global $VDN;

	return $VDN->$x;
}

function DMF_SetUpPageMarkUp()
{
	Markup("PlayerLoader", 'split',"/\\(:PlayerLoader:\\)/e",
		'keep(DMF_RV("PlayerLoadCode"))');
	Markup("DMF_Messages", '<split',"/\\(:DMFMessage:\\)/e",
		'DMF_RV("Messages")'); 
	Markup("DMBarLoader", '<split',"/\\(:DMBarLoader:\\)/e",
		'DMF_RV("DanmakuBarCode")'); 
	Markup("PlayerLinkLoader", '<inline',"/\\(:PlayerLinkLoader:\\)/e",
		'DMF_RV("PlayerLinkCode")'); 
	Markup("PartLinkLoader", 'split',"/\\(:PartLinkLoader:\\)/e",
		'DMF_RV("PartIndexCode")');

}

Markup("ObjInit", '_begin', "/\\(:ObjInit:\\)/e", 'ObjLoadFunc()');
function ObjLoadFunc()
{
	global $VDN;
	$VDN = new VideoData($GLOBALS['pagename']);
	DMF_SetUpPageMarkUp();
}

class VideoData
{
	private $source;
	private $sourcetype;
	private $partIndex;

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
		$this->PlayerLoadCode = $GLOBALS['playerCodeHeader'];
		$this->initVars($pn);
		if ($this->stat)
		{
			$this->initCodes();
			$this->initPageVars();
		}
		
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
		$this->groupConfig = Utils::GetGroupConfig($group);
		
		$page = ReadPage($this->pagename);
		
		$this->source = PageVar($this->pagename,'$:VideoStr');
		
		$partIndex = intval($_REQUEST['part']);
		$isRequestPartIndexExist = ($part > 1);
		$PageVarResult = PageVar($this->pagename, '$:P'.$partIndex);
		$isRequestPartIndexVaild = !empty($PageVarResult);
		
		$UserPreferPlayer = $page["PartPlayer_".$this->partIndex];
		$UserPreferPlayer = $_REQUEST['player'];
		if ( !empty($this->groupConfig->PlayersSet->$PartPreferPlayer) )
		{
			$this->player = $this->groupConfig->PlayersSet->$PartPreferPlayer;
		}
		else
		if ( !empty($this->groupConfig->PlayersSet->$PartPreferPlayer) )
		{
			$this->player = $this->groupConfig->PlayersSet->$UserPreferPlayer;
		}
		else
		{
			$this->player = $this->groupConfig->PlayersSet->Default;
		}

		$vt = PageVar($this->pagename,'$:VideoType');
		if (is_null($this->groupConfig->VideoSourceSet->$vt))  {$this->setBroken();return;}
		$this->sourcetype = $this->groupConfig->VideoSourceSet->$vt->init($this);
		$this->muti = $this->sourcetype->MutiAble && PageVar($this->pagename, '$:IsMuti') == 'true';
		if ($this->muti && $isRequestPartIndexExist && $isRequestPartIndexVaild)
		{
			$this->partIndex = $partIndex;
		} 
		$this->dmid = $this->sourcetype->danmakuId;
	}
	
	private function initCodes()
	{
		$this->initDanmakuBarCode();
		$this->initPartIndexCode();
		$this->initPlayerLinkCode();
		$this->initPlayerLoadCode();
	}
	
	private function initPageVars()
	{
		$strBool = $this->muti ? "true" : "false";
		$this->saveFPV('$IsMuti', $strBool);
		$this->saveFPV('$DMID', $this->dmid);
		$this->saveFPV('$Stats', "true");
		$this->saveFPV('$host', $GLOBALS['ScriptUrl']);
	}
	
	private function saveFPV($name, $value, $quote = TRUE) {
		global $FmtPV;

		if ($quote) 
			$value = "\"$value\"";

		$FmtPV[$name] = $value;
	}
	
	private function initPlayerLoadCode()
	{
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
			$str .= "flashvars.$k = \"$v\";\r\n";
		}
		return $str;
	}
	
	private function initDanmakuBarCode()
	{
		$this->DanmakuBarCode = '||'.$this->groupConfig->DanmakuBarSet->getString($this).'||';
	}
	
	private function initPlayerLinkCode()
	{
		$isPreferPlayerAuthed = CondAuth($this->pagename, 'admin');		
		foreach ($this->groupConfig->PlayersSet as $playerId => $playerObj)
		{
			if ($playerId == 'DEFAULT')
			{
				continue;
			}
			
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
		$URL = PageVar($this->pagename, '$PageUrl');
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
					"[[$URL | '''P$index''']]&nbsp;&nbsp;";
				$index++;continue;
			}
			
			$this->PartIndexCode .= '&nbsp;&nbsp;'.
				"[[$URL?Part=$index | '''P$index''']]&nbsp;&nbsp;";
			$index++;
			
		}
	}
	
	private function setBroken()
	{
		$this->stat = FALSE;
	}
}