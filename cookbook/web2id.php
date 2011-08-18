<?php if (!defined('PmWiki')) exit();
function biliweb2id($url) {
	global $FarmD;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "$FarmD/temp/cookies.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "$FarmD/temp/cookies.txt");
	$str = curl_exec($ch);
	curl_close($ch);

	if (stripos($str, "PADplayer.swf") === false) {echo "PF";return "PAGE FAIL";}
	
	preg_match('/(\<embed.*PADplayer.*embed>)/',$str,$matches);
	#var_dump($matches);
	preg_match('/id=([0-9a-zA-Z]*)(\"|&| |\?)/',$matches[0],$matches2);
	#var_dump($matches2);
	return $matches2[1];
}
function acfweb2id($url) {
	$str = file_get_contents($url);
	preg_match('/(.*shockwave.*embed>)/',$str,$matches);
	preg_match('/id=([0-9a-zA-Z]*)(\"|&| |\?)/',$matches[0],$matches2);
	return $matches2[1];
}

function bilibiliLogin()
{
	global $FarmD;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://bilibili.us/member/ajax_loginsta.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "$FarmD/temp/cookies.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "$FarmD/temp/cookies.txt");
	$test = curl_exec($ch);
	curl_close($ch);
	if (strpos($test, "welcome") !== false) {return "testOK";}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://bilibili.us/member/index_do.php");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'fmdo=login&dopost=login&refurl=http%3A%2F%2Fbilibili.us%2F&keeptime=604800&userid=SHK&pwd=A98532E21655&keeptime=2592000');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "$FarmD/temp/cookies.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "$FarmD/temp/cookies.txt");
	$test2 = curl_exec($ch);
	if (strpos($test2, 'javascript:history.go(-1)') === false) {return "LoginOK";}
	return "FAIL";
}