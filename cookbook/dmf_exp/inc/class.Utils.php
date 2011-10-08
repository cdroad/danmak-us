<?php
class Utils
{
	public function display_xml_error($error, $xmlstr = NULL)
	{
		if (!is_null($xmlstr))
		{
			$xml = explode("\n",$xml);
			$return  = $xml[$error->line - 1] . "\n";
		}
		
	    $return .= str_repeat('-', $error->column) . "^\n";
	
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
	               "\n  Line: $error->line" .
	               "\n  Column: $error->column";
	
	    if ($error->file) {
	        $return .= "\n  File: $error->file";
	    }
	
	    return "$return\n\n--------------------------------------------\n\n";
	}

	public function GetIOClass($str, $file)
	{
		if (stripos($str,'static'))
			return new StaticPoolIO($file);
		if (stripos($str,'dynamic'))
			return new DynamicPoolIO($file);
		
		throw new Exception("Unexcepted IOClass Type");
	}
	
	public function GetGroup($str)
	{
		switch (strtolower($str))
		{
			case "bilibili2":
				return "Bilibili2";
			case "acfun2":
				return "Acfun2";
		}
	}
	
	public static function WriteLog($action, $message)
	{
		$str = sprintf("\r\n%s  ... %s ... %s", strftime($GLOBALS['TimeFmt']), $action, $message);
		
		$pagename = "Main/SysLog";
		$page = ReadPage($pagename);
		$page['text'] .= $str;
		
		WritePage($pagename, $page);
	}
	
	public static function createCommentText($text,$pool,$userhash,$attrs)
	{
		$dmid = mt_rand(0,2147483647);
		$sendTime = time();
		$attr = "";
		foreach ($attrs as $attr) {
			$playtime = $attr['playtime'];
			$mode = $attr['mode'];
			$fontsize = $attr['fontsize'];
			$color = $attr['color'];
			$attr = "<attr playtime=\"$playtime\" mode=\"$mode\" fontsize=\"$fontsize\" color=\"$color\" />\r\n";
		}
		
		$str = <<<CMT

<comment id="$dmid" poolid="$pool" userhash="$userhash" sendtime="$sendtime">
	<text>$text</text>
    <attrs>
        $attr
    </attrs>
</comment>
CMT;
		return $str;
	}
}
