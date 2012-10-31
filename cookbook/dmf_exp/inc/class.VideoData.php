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
params.wmode = 'direct';
params.allowFullscreenInteractive = true;

STR;

function DMF_RV($x)
{
	global $VDN;

	return $VDN->$x;
}

function DMF_SetUpPageMarkUp()
{
	Markup("PlayerLoader", 'directives',"/\\(:PlayerLoader:\\)/e",
		'keep(DMF_RV("PlayerLoadCode"))');
	Markup("DMF_Messages", 'directives',"/\\(:DMFMessage:\\)/e",
		'DMF_RV("Messages")'); 
	Markup("DMBarLoader", 'directives',"/\\(:DMBarLoader:\\)/e",
		'PRR(DMF_RV("DanmakuBarCode"))'); 
	Markup("PlayerLinkLoader", '<inline',"/\\(:PlayerLinkLoader:\\)/e",
		'DMF_RV("PlayerLinkCode")'); 
	Markup("PartLinkLoader", '<inline',"/\\(:PartLinkLoader:\\)/e",
		'DMF_RV("PartIndexCode")');
}

Markup("ObjInit", '<{$var}', "/\\(:ObjInit:\\)/e", 'ObjLoadFunc()');
function ObjLoadFunc()
{
	global $VDN;
	$VDN = new VideoData($GLOBALS['pagename']);
	DMF_SetUpPageMarkUp();
}

class VideoData
{
    private $vpd;
    
	private $PlayerLoadCode;
	private $DanmakuBarCode;
	private $PlayerLinkCode;
	private $PartIndexCode;
	private $Messages;
	
	private $stat = true;
	
	public function __construct($pn)
	{
		$this->PlayerLoadCode = $GLOBALS['playerCodeHeader'];
		$this->vpd = new VideoPageData($pn);

		if ( empty($this->vpd->VideoStr) && empty($this->vpd->VideoType) )
            $this->stat = false;
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
	
	private function initCodes()
	{
		$this->initDanmakuBarCode();
		$this->initPartIndexCode();
		$this->initPlayerLinkCode();
		$this->initPlayerLoadCode();
		$this->Messages = "{$this->vpd->Player->desc} -> {$this->vpd->VideoType->getType()}( \"{$this->vpd->DanmakuId}\" )";
	}
	
	private function initPageVars()
	{
		$strBool = $this->vpd->IsMuti ? "true" : "false";
		$this->saveFPV('$IsMuti', $strBool);
		$this->saveFPV('$DMID', $this->vpd->DanmakuId);
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
		$AFVArray = $this->vpd->GroupConfig->GenerateFlashVarArr($this->vpd);
		$this->PlayerLoadCode .= $this->AFVArrayToJavascript($AFVArray);
		
		//加载SWF
		$this->PlayerLoadCode .= $this->genSWFObjectCode();
	}
	
	private function genSWFObjectCode()
	{
		return "swfobject.embedSWF(\"".$this->vpd->Player->playerUrl.
			"\", \"flashcontent\", \"".$this->vpd->Player->width.
			"\", \"".$this->vpd->Player->height.
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
		$this->DanmakuBarCode = '||'.$this->vpd->GroupConfig->DanmakuBarSet->getString($this).'||';
	}
	
	private function initPlayerLinkCode()
	{
		$isPreferPlayerAuthed = CondAuth($this->vpd->Pagename, 'admin');
		$URL = PageVar($this->vpd->Pagename, '$PageUrl');
		foreach ($this->vpd->GroupConfig->PlayersSet as $playerId => $playerObj)
		{
			if ($playerId == 'default')
			{
				continue;
			}

			if ($this->vpd->Player->playerUrl == $playerObj->playerUrl)
			{
				$this->PlayerLinkCode .= 
					"&nbsp;&nbsp;'''".$playerObj->desc."'''";
			} else {
				$this->PlayerLinkCode .= "&nbsp;&nbsp;[[$URL".
					"?Player=$playerId | ".$playerObj->desc.' ]]';
			}
			
			if ($isPreferPlayerAuthed)
			{
				$this->PlayerLinkCode .= "[[$URL".
					"?Part=$this->partIndex".
					"?Player=$playerId?action=setdef | "."&nbsp;'^Def^'".' ]]';
			}
		}
	}
	
	private function initPartIndexCode()
	{
		$index = 1;
		$URL = PageVar($this->vpd->Pagename, '$PageUrl');
		while ($Part = PageVar($this->vpd->Pagename, '$:P'.$index))
		{
			assert(!empty($Part));
			
			if ($index == $this->vpd->PartNo)
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