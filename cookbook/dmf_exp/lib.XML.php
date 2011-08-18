<?php

libxml_use_internal_errors(true);
/**
* Pumps all child elements of second SimpleXML object into first one.
*
* @param    object      $xml1  SimpleXML object
* @param    object      $xml2  SimpleXML object
* @return  void
*/
function simplexml_merge(SimpleXMLElement &$xml1, SimpleXMLElement $xml2)
{
   // convert SimpleXML objects into DOM ones
   $dom1 = new DomDocument();
   $dom2 = new DomDocument();
   $dom1->loadXML($xml1->asXML());
   $dom2->loadXML($xml2->asXML());

   // pull all child elements of second XML
   $xpath = new domXPath($dom2);
   $xpathQuery = $xpath->query('/*/*');
   for ($i = 0; $i < $xpathQuery->length; $i++)
   {
       // and pump them into first one
       $dom1->documentElement->appendChild(
           $dom1->importNode($xpathQuery->item($i), true));
   }
   $xml1 = simplexml_import_dom($dom1);
}

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