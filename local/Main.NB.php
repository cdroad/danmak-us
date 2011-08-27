<?php
define("BRIDGE_UN",'suphycsy%40gmail.com');
define("BRIDGE_PW",'SephirothC');
define("BRIDGE_COOKIE_FILE","$FarmD/temp/cookies.txt");
define("NICOVIDEO_LOGIN_URL", 'https://secure.nicovideo.jp/secure/login?site=niconico');
define("BRIDGE_USER_AGENT", 'Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-CN; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729; .NET4.0E)');


$NoHTMLCache = 1;
$HandleActions['nekomimimode'] = 'HandleNicoBridge';
$HandleAuth['nekomimimode'] = 'read';
include_once('./cookbook/attachdel.php');

function HandleNicoBridge($pagename, $auth= 'read')
{
	header("Cache-Control: no-cache");
	header("Content-type: text/plain; charset=UTF-8");
	ob_end_clean();
	
	echo("DMF N1co Bridge V2.1.0 .\n");
	
	$matched = 
		preg_match("{(?:http://www.nicovideo.jp/watch/)?(?P<vid>(?P<type>sm|nm|so|ca|ax|yo|nl|ig|na|cw|z[a-e]|om|sk|yk)?\d{1,14})}i",
		trim($_POST['vid']),
		$matches);
	
	if ($matched == 0 )
		die("解析视频ID失败。");

	$type = $matches['type'];
	$vid  = $matches['vid'];

	if (isLoggedIn()) 
	{
		if (!doLogin())
			die("登录nicovideo失败。");
	}

	viewPage($vid);
	$url = getFlvUrl($vid, $type);
	if (empty($url))
		die("解析下载地址失败。");
	echo("Downloading $vid ($url) \n");
	doDownloadVideo($url, $vid);
}

function doDownloadVideo($url,$vid)
{
	global $rFile,$fileext;
	
	$downloadTemp = "./uploads/Main/"."IMCOMPLETE_$vid.mp4";
	
	$fileExists = file_exists($downloadTemp);
	$rFile = fopen($downloadTemp, "a+");
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIEFILE, BRIDGE_COOKIE_FILE);
	curl_setopt($ch, CURLOPT_USERAGENT, BRIDGE_USER_AGENT);
	curl_setopt($ch, CURLOPT_TIMEOUT, 90);
	curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'read_header');
	curl_setopt($ch, CURLOPT_WRITEFUNCTION, "myPoorProgressFunc"); 
	
	if ($fileExists)
	{
		if (flock($rFile,LOCK_EX | LOCK_NB))
		{
			echo "尝试启用断点续传\n";
			curl_setopt($ch, CURLOPT_RANGE, filesize($downloadTemp)."-");
		} else {
			fclose($rFile);
			die("共享违例 :: 文件已被其他任务占用。");
		}
	}
	
	curl_exec($ch);
	curl_close($ch);
	
	flock($rFile, LOCK_UN);
	fclose($rFile);
	
	$downloadTarget = "./uploads/Main/"."$vid.$fileext";
	
	if (file_exists($downloadTemp))
	{
		rename($downloadTemp, $downloadTarget);
		echo "下载地址: http://danmaku.us/uploads/Main/".$vid.".".$fileext."\n";
		echo "完毕。\n";
	} else {
		echo "下载失败。\n";
	}	

}


function read_header($ch, $string) 
{
	global $totalsize, $fileext;
	if(!strncasecmp($string, "Content-Length:",15)) {$totalsize = round(trim(substr($string,16)) / 1024 / 1024,2);echo "Total size:$totalsize MB\n";}
	if(!strncasecmp($string, "Content-Disposition:",20)) {$fileext = trim(substr($string,45,3));echo "File extension:.$fileext \n";}
	return strlen($string);
}


function myPoorProgressFunc($ch,$str)
{
	global $rFile , $downloaded , $totalsize , $t2 ;
	$len = fwrite($rFile,$str);;
	$downloaded += $len;$t2 += $len;
	if ($t2 >= 5*1024*1024) {
		$t = round($downloaded / 1024 / 1024,2);
		$downper = round ($t / $totalsize * 100 , 2);
		echo "已完成: $t MB / $totalsize MB   $downper % \n";
		$t2 = 0 ;
	}
	return $len;
} 



function getFlvUrl($vid, $type)
{
	if (strtolower($type) == 'nm')
	{
		echo("尝试启用nm下载模式\n");
		$query = "v=".$vid.'&as3=1';
	} else {
		echo("尝试普通视频模式\n");
		$query = "v=$vid";
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://flapi.nicovideo.jp/api/getflv");
	curl_setopt($ch, CURLOPT_COOKIEFILE, BRIDGE_COOKIE_FILE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$str=curl_exec($ch);
	parse_str($str);
	curl_close($ch);
	return $url;
}



function viewPage($vid)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.nicovideo.jp/watch/$vid");
	curl_setopt($ch, CURLOPT_COOKIEFILE, BRIDGE_COOKIE_FILE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, BRIDGE_COOKIE_FILE);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_exec($ch);
	curl_close($ch);
}

function doLogin()
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://www.nicovideo.jp/');
	curl_setopt($ch, CURLOPT_COOKIEFILE, BRIDGE_COOKIE_FILE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'next_url=&mail='.
	BRIDGE_UN.'&password='.BRIDGE_PW);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);	
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, BRIDGE_COOKIE_FILE);
	$str=curl_exec($ch);
	curl_close($ch);
	if (strpos($str, 'ログイン') === true)
		return false;

	return true;
}

function isLoggedIn()
{
	if (!file_exists(NICO_BRIDGE_COOKIE_FILE))
		return false;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, NICOVIDEO_LOGIN_UR);
	curl_setopt($ch, CURLOPT_COOKIEFILE, BRIDGE_COOKIE_FILE);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$str=curl_exec($ch);
	curl_close($ch);
	if (strpos($str, 'ログイン') === true)
		return false;

	return true;
}


