<?php if (!defined('PmWiki')) exit();


#BaseVar用于传递最基础的参数：
#视频类型，视频码，期望分段号，和播放器ID， 允许分P(不可手动)
#
#假定外部数据不可信任。进行初步检查。假定videostr可信

class BaseVar
{
	var $_str;
	var $_type;
	var $_partNo;
	var $_playerId;
	var $_playerIdLevel;
	var $_mutiAble;
	var $_pn;
	var $_gp;
	var $_isNull = FALSE ;
	var $_Stats = 'OK';
	
	public function getStat() {
		return $this->_Stats;
	}
	
	public function getStr() {
		return $this->_str;
	}

	public function getVType() {
		return $this->_type;
	}
	
	public function getPartNo() {
		return $this->_partNo;
	}
	
	public function getPlayerId() {
		return $this->_playerId;
	}
	
	public function getPlayerIdLevel() {
		return $this->_playerIdLevel;
	}
	
	public function getIsMutiAble() {
		return $this->_mutiAble;
	}
	
	public function getPagename() {
		return $this->_pn;
	}
	public function getGroup() {
		return $this->_gp;
	}

	public function BaseVar($pn, $str = NULL, $type = NULL, $partNo = 1, $playerId = NULL) {
		
		$auth			 = 'ALWAYS';
		$this->_pn  	 = empty($pn) ? $this->setPN() : $pn ;
		$this->setSTR($str);
		if ($this->_Stats == 'STOP') return;
		$this->_gp		 = PageVar($this->_pn, '$Group');

		try {
			$this->setVTYPE($type);

		} catch (Exception $e) {
			$this->setNULL();
		}
		
		$this->_mutiAble = $this->isVideoMutiAble($pn);
		$this->setUpPartNo($pn, $partNo);
		$this->setUpPlayerId($pn, $playerId);
		
	}

	private function setUpPartNo($pn, $partNo) {
		if ($this->_mutiAble) {
			if ($this->isVaildPartNo($pn, $_GET['Part'])) {
				$this->_partNo = $_GET['Part'];
			} else if ($this->isVaildPartNo($pn, $partNo)) {
				$this->_partNo = $partNo;
			} else {
				$this->_partNo = "1";
			}
		} else {
			$this->_partNo = "1";
		}
	}

	private function isVaildPartNo($pn, $n) {
		if (empty($n))
			return FALSE;
		$value = PageVar($this->_pn,'$:P'.$n);
		return !empty($value);

	}

	private function isVideoMutiAble($pn) {

		$VideoSourceMutiAble = $GLOBALS['DMF_GroupConfig'][$this->_gp]['VideoSourceConfig'][$this->_type]['MutiAble'];
		$pageSettings = strtolower(PageVar($this->_pn,'$:IsMuti'));
		$PageSettingMutiAble = ($pageSettings == "true");

		return ($VideoSourceMutiAble && $PageSettingMutiAble);

	}

	private function setPN() {
		return $GLOBALS['pagename'];
	}

	private function setSTR($str) {
		if (!empty($str)) {
			$this->_str = $str;
			return;
		}
		
		$ss = PageVar($this->_pn, '$:VideoStr');
		if (!empty($ss)) {
			$this->_str = $ss;
			return;
		}
		
		$this->_Stats = 'STOP';
	}
	
	private function setVTYPE($type) {
		
		$type= strtolower($type);
		$pageType= strtolower(PageVar($this->_pn,'$:VideoType'));
		
		if (!empty($type) && $this->isVaildType($type)) {
			$this->_type = $type;
			return;
		}
		if (!empty($pageType) && $this->isVaildType($pageType)) {
			$this->_type = $pageType;
			return;
		}
		//NEVER REACH HERE.
		assert(false);
		throw new Exception();
	}

	private function isVaildType($type) {
		global $DMF_GroupConfig;
		
		return array_key_exists($type, $DMF_GroupConfig[$this->_gp]['VideoSourceConfig']);
	}

	private function setNULL() {
		$this->_isNull		= TRUE;
		$this->_str			= "$ScriptUrl/p/error.mp4";
		$this->_type		= 'LINK';
		$this->_partNo		= NULL;
		$this->_mutiAble	= FALSE;

	}
	
	private function setUpPlayerId($pn, $pid) {
		global $DMF_GroupConfig;
		
		//优先级：
		//  System -> GET -> page -> default;
		$page = ReadPage($this->_pn, READPAGE_CURRENT);
		$this->setPlayerIfExists($DMF_GroupConfig[$this->_gp]['DefaultPlayer'],'Default');
		$this->setPlayerIfExists($page['DP_P0'.$this->_partNo],'PagePre');
		$this->setPlayerIfExists($_GET['Player'],'UserPre');
		$this->setPlayerIfExists($pid,'SystPre');
	
	}

	private function setPlayerIfExists($pid, $Level) {
		
		if (!empty($pid) && $this->isPlayerExists($pid)) {
			$this->_playerId = $pid;
			$this->_playerIdLevel = $Level;
		}
	}
	
	private function isPlayerExists($pid) {
		global $DMF_GroupConfig;
		
		return array_key_exists($pid, $DMF_GroupConfig[$this->_gp]['Players']);
	}

}
