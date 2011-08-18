<?php if (!defined('PmWiki')) exit();

// 继承并扩展从BaseVar传递的数据
class VideoData 
{
	var $_AFVArray;
	var $_danmakuId;

	public function getAFVArray() {
		return $this->_AFVArray;
	}

	public function getBaseVarObj() {
		return $this->_baseVarObj;
	}
	
	public function getDanmakuId() {
		return $this->_danmakuId;
	}
	
	public function getPartNo() {
		return $this->_baseVarObj->getPartNo();
	}

	public function getPlayerId() {
		return $this->_baseVarObj->getPlayerId();
	}
	
	public function getPartId() {
		return $this->_baseVarObj->getPartNo();
	}
	
	public function getGroup() {
		return $this->_baseVarObj->getGroup();
	}
	
	public function getPagename() {
		return $this->_baseVarObj->getPagename();
	}
	
	public function isMuti() {
		return $this->_baseVarObj->getIsMutiAble();
	}
	
	public function VideoData(&$BaseVarObj) {

		$isVaildParams = ( get_class($BaseVarObj) == "BaseVar");
		if ($isVaildParams) {
			$this->_baseVarObj = $BaseVarObj;
		} else {
			$this->_baseVarObj = Abort("Wrong object given!");
		}
		
		$this->setDanmakuId();
		$this->setFileURLIfNeeded();
		$this->setAFVArray();
		
		$this->setPmwikiVars();
	}

	private function setPmwikiVars() {
		$PV_Muti = ($this->_baseVarObj->getIsMutiAble()) ? "true" : "false";
		$this->saveFPV('$IsMuti', $PV_Muti);
		$this->saveFPV('$DMID', $this->_danmakuId);
		$this->saveFPV('$Stats', "true");
		$this->saveFPV('$host',$GLOBALS['ScriptUrl']);
	}

	private function saveFPV($name, $value, $quote = TRUE) {
		global $FmtPV;

		if ($quote) 
			$value = "\"$value\"";

		$FmtPV[$name] = $value;
	}

	private function setAFVArray() {
		global $DMF_GroupConfig;

		$AFVFunc = $DMF_GroupConfig[$this->_baseVarObj->getGroup()]
			['AFVFunction'];
		
		$IsAFVFuncExist = function_exists($AFVFunc);

		if (!$IsAFVFuncExist)
		{
			writeLog('Main/SysLog', "AFV function not exist. id = $this->_danmakuId");
			Abort("AFV_ARRAY_CONV_FUNCTION_NOT_EXIST");
		}
		$this->_AFVArray = $AFVFunc(
							$this->_baseVarObj->getVType(), 
							$this->_danmakuId, 
							$this->_fileURL);
	}
	
	private function setDanmakuId() {
		global $DMF_GroupConfig;

		$isUsePageNameAsDanmakuId = 
			$DMF_GroupConfig[$this->_baseVarObj->getGroup()]
			['VideoSourceConfig'][$this->_baseVarObj->getVType()]
			['PageNameAsDanmakuId'];

		if ($isUsePageNameAsDanmakuId) {
			$this->_danmakuId = $this->appendPartId(
				PageVar($this->_baseVarObj->getPagename(), '$Name')
			);
		} else {
				$this->_danmakuId = $this->appendPartId(
					$this->_baseVarObj->getStr()
				);
		}
	}

	private function appendPartId($ID) {

		$MutiAble = $this->_baseVarObj->getIsMutiAble();
		if ($MutiAble) {
			$Part = $this->_baseVarObj->getPartNo();
			
			if ($Part != "1") {
				return $ID."P$Part";
			}
		}
		return $ID;
	}
	private function setFileURLIfNeeded() {
		global $DMF_GroupConfig;

		$isFileURLNeeded = 
			$DMF_GroupConfig[$this->_baseVarObj->getGroup()]
			['VideoSourceConfig'][$this->_baseVarObj->getVType()]
			['URLConvert'];

		if ($isFileURLNeeded) {
			$ConvFunc = 'DMF_URL_CONV_'.$this->_baseVarObj->getVType();
			$IsConvFuncExists = function_exists($ConvFunc);

			if ($IsConvFuncExists) {
				$this->_fileURL = $ConvFunc($this->_baseVarObj->getStr());
			} else {
				writeLog('Main/SysLog', "URL convert function not exist. id = $this->_danmakuId;".
							"VType = ".$this->_baseVarObj->getVType().";");
				Abort("URL CONV FUNCTION NOT EXISTS!");
			}

		} else {
			$this->_fileURL = NULL;
		}
	}
	
	var $_fileURL;
	var $_baseVarObj;
}
