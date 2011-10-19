<?php

class DanmakuBarSet
{
	private $Guest = array();
	private $Authed = array();
	private $Admin = array();
	
	public function __construct()
	{
		$this->Guest = array
		(
			0 => '%danmakubar% (:input form "{*$host}/poolop/loadxml/{$Group}/{$DMID}" method=get:)'.
				 '下载格式：(:input select name=format value=data label=data :)'.
				 '(:input select name=format value=d label=d :)'.
				 '(:input select name=format value=raw label=comment :)'.
				 '附件：(:input checkbox name=attach value=true checked:)(:input submit value="下载":)'.
				 '(:input end:)%%&nbsp;&nbsp;',	
		);
		
		$this->Authed = array
		(
			0 => '%danmakubar%(:input form enctype="multipart/form-data" "{*$host}/poolop/post/{$Group}/{$DMID}" :)'.
				 '(:input file uploadfile:)'.
				 '弹幕池:(:input select name=Pool value=Static label=静态 :)'.
				 '(:input select name=Pool value=Dynamic label=动态 :)'.
				 '追加:(:input checkbox Append value=true:)'.
				 '(:input submit post Upload value="上传":)'.
				 '(:input end:)%%&nbsp;&nbsp;',
			
			1 => '%danmakubar% (:input form "{*$host}/poolop/loadxml/{$Group}/{$DMID}" method=get:)'.
				 '下载格式：(:input select name=format value=data label=data :)'.
				 '(:input select name=format value=d label=d :)'.
				 '(:input select name=format value=raw label=comment :)'.
				 '附件：(:input checkbox name=attach value=true checked:)(:input submit value="下载":)'.
				 '(:input end:)%%&nbsp;&nbsp;',	
				
			2 => '%danmakubar%(:if2 equal "{*$IsMuti}" "true" :)[[{*$FullName}?action=edit | 编辑Part]](:if2end:)%%&nbsp;&nbsp;',
			
			4 => '<br />',
			5 => '%newwin danmakubar% XML:&nbsp;&nbsp;',
			6 => '[[DMR/B{*$DMID}?action=edit|动态池编辑]]&nbsp;&nbsp;',
			7 => '[[{*$host}/poolop/validate/{$Group}/{$DMID}/dynamic |验证动态池]]&nbsp;&nbsp;',
			8 => '%%&nbsp;&nbsp;',
		);
		
		$this->Admin = array
		(
			2 => '%danmakubar% 弹幕池移动： '.
				'[[{*$host}/poolop/move/{$Group}/{$DMID}/dynamic/static | 动静]]&nbsp'.
				'[[{*$host}/poolop/move/{$Group}/{$DMID}/static/dynamic | 静动]]&nbsp'.
				'%%&nbsp;&nbsp;',
			
			9 => '%danmakubar%'.
				 '清空弹幕池 ： '.
				 '[[{*$host}/poolop/clear/{$Group}/{$DMID}/staitc | 静态]]&nbsp'.
				 '[[{*$host}/poolop/clear/{$Group}/{$DMID}/dynamic | 动态]]&nbsp'.
				 '[[{*$host}/poolop/clear/{$Group}/{$DMID}/all | 双杀]]&nbsp'.
				 '%%&nbsp;&nbsp;',
				
		);
	}
	
	private function getAuthLevel($pagename)
	{
		global $AuthId, $Author;

		$IsAuthed = !empty($AuthId);
		if (!empty($Author))
		{
			$IsPageCreator = (PageVar($pagename,'$CreatedBy') != "") &&
				(PageVar($pagename,'$CreatedBy') == $Author);
			$IsLastEditor = 
				(PageVar($pagename,'$LastModifiedBy') == $Author);
		}
	
		if ( $IsAuthed || $IsPageCreator || $IsLastEditor)
		{
			if (CondAuth($pagename, 'admin'))
			{
				return 'ADMIN';
			} else {
				return 'AUTHED';
			}
		}
		return 'GUEST';
	}
	
	public function getString(VideoData $source)
	{
		$Arr = $this->Guest;
		$Auth = $this->getAuthLevel($source->pagename);
		if ($Auth == 'AUTHED' || $Auth == 'ADMIN')
		{
			$Arr = $this->Authed;
			
			if ($Auth == 'ADMIN')
			{
				$Arr = array_merge($Arr, $this->Admin);
			}
			
		}
		
		//ksort($Arr, SORT_REGULAR);
		$str = implode($Arr);
		//var_dump($str);exit;
		return $str;
	}
}