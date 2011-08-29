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
			0 => '%danmakubar% (:input form "{*$host}/dm,{*$DMID}" method=get:)'.
				'下载格式：(:input select name=format value=data label=data :)'.
				'(:input select name=format value=d label=d :)'.
				'(:input select name=format value=raw label=comment :)'.
				'附件：(:input checkbox name=cmd value=download checked:)(:input submit value="下载":)'.
				'(:input end:)%%&nbsp;&nbsp;',	
		);
		
		$this->Authed = array
		(
			0 => '%danmakubar%(:input form enctype="multipart/form-data" "{*$PageUrl}" :)'.
				'(:input hidden action xmlupload:)(:input file uploadfile:)'.
				'(:input hidden dmid value="{$DMID}" :)'.
				'弹幕池:(:input select name=Pool value=S label=静态 :)'.
				'(:input select name=Pool value=D label=动态 :)'.
				'追加:(:input checkbox Append value=true:)'.
				'(:input submit post Upload value="上传":)'.
				'(:input end:)%%&nbsp;&nbsp;',
			
			1 => '%danmakubar%(:input form "{*$host}/API/XMLTool" method=get:)(:input hidden r {(ftime fmt="%s")}:)'.
				'(:input hidden action XMLLoad:)(:input hidden group value="{$Group}" :)'.
				'(:input hidden dmid "{$DMID}" :)'.
				'下载格式：(:input select name=format value=data label=data :)'.
				'(:input select name=format value=d label=d :)'.
				'(:input select name=format value=raw label=comment :)'.
				'附件：(:input checkbox name=cmd value=download checked:)(:input submit value="下载":)&nbsp;&nbsp;'.
				'(:input end:)%%&nbsp;&nbsp;',
				
			2 => '%danmakubar%(:if2 equal "{*$IsMuti}" "true" :)[[{*$FullName}?action=edit | 编辑Part]](:if2end:)%%&nbsp;&nbsp;',
			
			4 => '<br />',
			5 => '%newwin danmakubar% XML:&nbsp;&nbsp;',
			6 => '[[DMR/B{*$DMID}?action=edit|动态池编辑]]&nbsp;&nbsp;',
			7 => '[[{*$host}/API/XMLTool?action=Validate&'.
				'dmid={*$DMID}&group=Bilibili2&r='.mt_rand().'|验证XML]]&nbsp;&nbsp;',
			8 => '%%&nbsp;&nbsp;',
		);
		
		$this->Admin = array
		(
			2 => '%danmakubar%(:input form target="_blank" enctype="multipart/form-data" "{*$host}/API/XMLTool" :)'.
				'弹幕池移动 (:input hidden action PoolConv:)'.
				'(:input hidden dmid value="{$DMID}" :)'.
				'(:input hidden group value="{$Group}" :)'.
				'(:input select name=Pool value=DS label="动态->静态" :)'.
				'(:input select name=Pool value=SD label="静态->动态" :)'.
				'(:input submit value="Go":)'.
				'(:input end:)%%&nbsp;&nbsp;',
			9 => '%danmakubar%(:input form target="_blank" enctype="multipart/form-data" "{*$host}/API/XMLTool" :)'.
				'清空弹幕池 (:input hidden action PoolClear:)'.
				'(:input hidden dmid value="{$DMID}" :)'.
				'(:input hidden group value="{$Group}" :)'.
				'(:input select name=Pool value=S label="静态池" :)'.
				'(:input select name=Pool value=D label="动态池" :)'.
				'(:input select name=Pool value=ALL label="双规" :)'.
				'(:input submit value="Go":)'.
				'(:input end:)%%&nbsp;&nbsp;',
				
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