<?php if (!defined('PmWiki')) exit();
class PageCodes
{
	var $_Messages;
	var $_PlayerLoadCode;
	var $_DanmakuBarCode;
	var $_PlayerLinkCode;
	var $_PartNoLinkCode;
	var $_DanmakuBarSuperAuthLevel = 'admin';

	public function getMessages() {
		return $this->_Messages;
	}

	public function getPlayerLoadCode() {
		return $this->_PlayerLoadCode;
	}

	public function getDanmakuBarCode() {
		return $this->_DanmakuBarCode;
	}

	public function getPlayerLinkCode() {
		return $this->_PlayerLinkCode;
	}

	public function getPartNoLinkCode() {
		return $this->_PartNoLinkCode;
	}

	public function PageCodes($VideoDataObj)
	{
		$isVaildParams = ( get_class($VideoDataObj) == "VideoData");
		if ($isVaildParams) {
			$this->_videoDataObj = $VideoDataObj;
		} else {
			Abort("SHOULD_GET_VIDEO_DATA_OBJECT");
		}

		$this->_PageBaseURL = PageVar($this->_videoDataObj->getPagename(),
			'$PageUrl');
		$this->_PartQuery = ( $this->_videoDataObj->getPartId() != "1" ) ?
			'?Part='.$this->_videoDataObj->getPartId() : "";

		$this->initPlayerLoadCode();
		$this->initDanmakuBarCode();
		$this->initPlayerLinkCode();
		$this->initPartNoLinkCode();
		
		$this->initMessages();
	}
	
	private function initMessages()
	{
		$this->_Messages = "Player : ".$this->_videoDataObj->getPlayerId().
			"&nbsp;&nbsp;".$this->_videoDataObj->getBaseVarObj()->getVType().
			"(".$this->_videoDataObj->getDanmakuId().")&nbsp;&nbsp;";
		
		if ($GLOBALS['LOCALVERSION']) {
			if (CondAuth('DMR.DMR', 'admin')) {
				$AUTH = 'RDY';
			} else {
				$AUTH = 'OFF';
			}
			$this->_Messages .= "DMR: '''$AUTH''';DML: '''$AUTH''';DEXT: '''OFF''';";
		}
	}
	
	private function initPartNoLinkCode()
	{
		if (!$this->_videoDataObj->isMuti())
			return;
		
		$index = 1;
		$CurrentPart = $this->_videoDataObj->getPartId();
		$pagename = $this->_videoDataObj->getPagename();

		while(PageTextVar($pagename, "P$index"))
		{
			if ($index == $CurrentPart)
			{
				$this->_PartNoLinkCode .= 
					'&nbsp;&nbsp;'."'''P$index'''".'&nbsp;&nbsp;';
				$index++;
				continue;
			}

			if ($index == 1)
			{
				$this->_PartNoLinkCode .= '&nbsp;&nbsp;'.
					"[[".$this->_PageBaseURL.
					" | '''P$index''']]&nbsp;&nbsp;";
				$index++;continue;
			}

			$this->_PartNoLinkCode .= '&nbsp;&nbsp;'.
				"[[".$this->_PageBaseURL.
				"?Part=$index | '''P$index''']]&nbsp;&nbsp;";
			$index++;
		}
		
	}

	private function initPlayerLinkCode()
	{
		global $DMF_GroupConfig;
		
		$PlayerArray = $DMF_GroupConfig[$this->_videoDataObj->getGroup()]
			['Players'];
		$CurrentPlayer = $this->_videoDataObj->getPlayerId();
		$CanSetDefaultPlayer = CondAuth(
			$this->_videoDataObj->getPagename(),'admin');

		foreach ($PlayerArray as $pid => $Config)
		{
			if ($Config['Invisible'] == "true") 
				continue;

			if ($pid == $CurrentPlayer)
			{
				$this->_PlayerLinkCode .= 
					"&nbsp;&nbsp;'''".$Config['desc']."'''";
				continue;
			}
			
			$this->_PlayerLinkCode .= "&nbsp;&nbsp;[[".
				$this->_PageBaseURL.$this->_PartQuery.
				"?Player=$pid | ".$Config['desc'].' ]]';

			if (!$CanSetDefaultPlayer)
				continue;

			$this->_PlayerLinkCode .= "[[".
				$this->_PageBaseURL.$this->_PartQuery.
				"?Player=$pid?action=setdef | "."&nbsp;'^Def^'".' ]]';
		}


	}

	private function initDanmakuBarCode() 
	{
		global $DMF_GroupConfig;

		$DanmakuBarConfig = $DMF_GroupConfig
			[$this->_videoDataObj->getGroup()]
			['DanmakuBarConfig'];
		
		$DanmakuBarAuth = $this->DanmakuBarAuth();
		$DanmakuBarSuperAuth = CondAuth($this->_videoDataObj->getPagename()
			, $this->_DanmakuBarSuperAuthLevel);
		if ($DanmakuBarAuth)
		{
			$ConfigArray = $DanmakuBarConfig['Authed'];
			if ($DanmakuBarSuperAuth)
			{
				array_insert($ConfigArray, 
					$DanmakuBarConfig['Super']);
			}
		} else {
			$ConfigArray = $DanmakuBarConfig['Guest'];
		}

		ksort($ConfigArray);
		foreach ($ConfigArray as $k => $v)
		{
			$this->_DanmakuBarCode .= $v;
		}

		//添加表格标记,左对齐
		$this->_DanmakuBarCode = "||".$this->_DanmakuBarCode." ||";
	}

	private function DanmakuBarAuth()
	{
		global $AuthId, $Author;
		$pagename = $this->_videoDataObj->getPagename();

		$IsAuthed = !empty($AuthId);
		if (!empty($Author))
		{
			$IsPageCreator = (PageVar($pagename,'$CreatedBy') != "") &&
				(PageVar($pagename,'$CreatedBy') == $Author);
			$IsLastEditor = 
				(PageVar($pagename,'$LastModifiedBy') == $Author);
		}
	
		if ( $IsAuthed || $IsPageCreator || $IsLastEditor)
			return true;
		return false;
	}

	private function initPlayerLoadCode() 
	{
		global $playerCodeHeader, $DMF_GroupConfig;

		$PlayerConfig = $DMF_GroupConfig[$this->_videoDataObj->getGroup()]
			['Players'][$this->_videoDataObj->getPlayerId()];


		$this->_PlayerLoadCode = $playerCodeHeader.
			$this->AFVArrayToJS();
		
		$this->_PlayerLoadCode .= "swfobject.embedSWF(\"".$PlayerConfig['url'].
			"\", \"flashcontent\", \"".$PlayerConfig['width'].
			"\", \"".$PlayerConfig['height'].
			"\", \"10.0.0\",\"expressInstall.swf\", flashvars, params);</script>";		
	}

	private function AFVArrayToJS()
	{
		$JS = '';

		foreach ( $this->_videoDataObj->getAFVArray() 
			as $name => $value) {
				$JS .= "flashvars.$name = \"$value\";\r\n";
			}

		return $JS;
	}

	var $_PageBaseUrl;
	var $_PartQuery;
	var $_videoDataObj;

}
