<?php if (!defined('PmWiki')) exit();
## Page generation timer recipe
$GenerationBegin = explode(' ', microtime());
$GenerationBegin = $GenerationBegin[0] + $GenerationBegin[1];
function GenerationTime(){
	global $GenerationBegin;
	$GenerationEnd = explode(' ', microtime());
	$GenerationEnd = $GenerationEnd[0] + $GenerationEnd[1];
	$GenerationTotal = $GenerationEnd - $GenerationBegin;
	$GenerationTotal = round($GenerationTotal, 5);
	print "$GenerationTotal";
}