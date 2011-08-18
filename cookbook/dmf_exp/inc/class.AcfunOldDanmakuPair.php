<?php
class AcfunOldDanmakuPair extends DanmakuPairBase
{
	public function AcfunOldDanmakuPair($dmid, $pair = PAIR_ALL)
	{
		$this->_LogPage = 'Main/RecentDanmakuChanges';
		parent::DanmakuPairBase('Acfun2', $dmid, $pair);
		
	}
	
	public function asXML($pair, $format)
	{
		$temp = $this->get($pair);

		echo($temp->asXML());
	}
	
	protected function saveDynamic()
	{
		return;
	}
	
	protected function saveStatic()
	{
		if (file_exists($this->_SFile))
		{
			rename($this->_SFile, $this->_SFile.",del-".time());
		}
		$result = file_put_contents($this->_SFile,
			$this->_SPool->asXML(),
			LOCK_EX);
		if ($result == FALSE)
		{
			$str = "AcfunOldDanmakuPair::saveStatic FAIL : COUND NOT WRITE TO STATIC POOL FILE.".
					" DMID = $this->_DMID; FILE = $this->_SFile";
			writeLog('Main/SysLog', $str);
		}
	}
	
	protected function deleteDynamic($info)
	{
		Abort("deleteDynamic Not Supported");
	}
	
	protected function deleteStatic($info)
	{
		Abort("deleteStatic Not Supported");
	}
	
	protected function validateDynamic()
	{
		return array();
	}
	
	protected function validateStatic()
	{
		libxml_clear_errors();
		$this->loadStatic();
		return libxml_get_errors();
	}
}