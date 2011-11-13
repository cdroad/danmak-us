<?php
class Utils
{
	public static function display_xml_error($error, $xmlstr = NULL)
	{
		if (!is_null($xmlstr))
		{
			$xml = explode("\n",$xml);
			$return  = $xml[$error->line - 1] . "<br />";
		}
		
	    $return .= str_repeat('-', $error->column) . "^<br />";
	
	    switch ($error->level) {
	        case LIBXML_ERR_WARNING:
	            $return .= "Warning $error->code: ";
	            break;
	         case LIBXML_ERR_ERROR:
	            $return .= "Error $error->code: ";
	            break;
	        case LIBXML_ERR_FATAL:
	            $return .= "Fatal Error $error->code: ";
	            break;
	    }
	
	    $return .= trim($error->message) .
	               "<br />  Line: $error->line" .
	               "<br />  Column: $error->column";
	
	    if ($error->file) {
	        $return .= "<br />  File: $error->file";
	    }
	
	    return "$return<br /><br />--------------------------------------------<br /><br />";
	}

	public static function GetXMLFilePath($dmid, $group)
	{
		$gc = self::GetGroupConfig($group);
		return $gc->XMLFolderPath."/$dmid.xml";
	}
	
	public static function GetDMRPageName($dmid, $group)
	{
        $gc = self::GetGroupConfig($group);
		return "DMR.{$gc->SUID}{$dmid}";
	}
	
	public static function GetIOClass($group, $dmid, $typeStr)
	{
		$group = self::GetGroup($group);
		
		if (stripos(strtolower($typeStr),'static') !== FALSE)
			return new StaticPoolIO($dmid, $group);
		if (stripos(strtolower($typeStr),'dynamic')!== FALSE)
			return new DynamicPoolIO($dmid, $group);
		
		throw new Exception("Unexcepted IOClass Type");
	}
	
	public static function GetGroup($str)
	{
		switch (strtolower($str))
		{
			case "bilibili2":
			case "bilibili":
				return "Bilibili2";
			case "acfun2":
			case "acfun":
				return "Acfun2";
            case "twodland1":
            case "twodland":
            case "2dland":
                return "Twodland1";
		}
	}
    
	public static function GetGroupConfig($str)
    {
        $str = self::GetGroup($str);
        $class = "{$str}GroupConfig";
        if (!class_exists($class)) {
            throw new Exception("Group Config Not Found : {$class}");
        }
        return call_user_func("{$str}GroupConfig::GetInstance");
    }
    
	public static function WriteLog($action, $message)
	{
        if (!$GLOBALS['EnableSysLog']) return;
        
		$str = sprintf("\r\n%s  ... %s ... %s", strftime($GLOBALS['TimeFmt']), $action, $message);
		$pagename = "Main/SysLog";
		$page = ReadPage($pagename);
		$page['text'] .= $str;
		
		WritePage($pagename, $page);
	}
    
	public static function createCommentText($text,$pool,$userhash,$attrs)
	{
        throw new Exception("已废弃");
	}
}
