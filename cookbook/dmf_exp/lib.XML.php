<?php

libxml_use_internal_errors(true);

function get_xml_error($error, $xml)
{
    #$return  = $xml[$error->line - 1] . "<br />";
    $return .= str_repeat('-', $error->column) . "<br />";

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


    return "$return<br />--------------------------------------------<br />";
}


function display_xml_error($error, $xmlstr = NULL)
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