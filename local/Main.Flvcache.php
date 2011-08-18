<?php
include_once("./cookbook/PmDB.php");
$NoHTMLCache = 1;
$EnablePostAuthorRequired = 0;
$HandleActions['GetFlvUrl'] = 'HandleGetFlvUrl';
$HandleAuth['GetFlvUrl'] = 'edit';
$HandleActions['Get6Url'] = 'HandleGet6Url';
$HandleAuth['Get6Url'] = 'edit';
ini_set('display_errors', 0);

function HandleGetFlvUrl($pagename, $auth = 'edit') {
	global $QQJumperURL;
	header("HTTP/1.1 301 Moved Permanently");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	$db = new PmDB($pagename);

	if ($db->read('cache_'.$_GET['vid']) == "") {
		header("X-Cache-Stat: cache miss");
		$trashlist = array('QQVideoOutputJson=',';','QZOutputJson=');
		$data = str_replace($trashlist,'',file_get_contents("http://vv.video.qq.com/geturl?otype=json&platform=0&vid=".$_GET['vid']));
		
		if (json_decode($data,true) == NULL) {
			header("X-Cache-Stat: cache error");
			header("Location: http://danmaku.us/p/jumper_error.mp4");
			return;
		}
		
		$obj = json_decode($data,true);
		$url = $obj['vd']['vi'][0]['url'];

		//·þÎñÆ÷ÅÐ¶Ï
		if ((stripos($url,'http://vhot.qqvideo.tc.qq.com') === true) || (stripos($url,'http://vhotws.video.qq.com') === true) || (stripos($url,'http://vtopws.video.qq.com') === true) || (stripos($url,'http://im.dnion.videocdn.qq.com') === true) || (stripos($url,'http://important.dnion.videocdn.qq.com') === true) || (stripos($url,'http://vhot2.qqvideo.tc.qq.com') === true) || (stripos($url,'http://vtop.qqvideo.tc.qq.com') === true) || (stripos($url,'http://vlive.qqvideo.tc.qq.com') === true) || (stripos($url,'http://web.qqvideo.tc.qq.com') === true) || (stripos($url,'http://im.dnion.videocdn.qq.com') === true)) {
			$db->add('cache_'.$_GET['vid'], $url, 7200);
			$db->save();
			header("Location: ".$url);
		} else {
			$url = getDefaultFlvUrlbak($_GET['vid']);
			$db->add('cache_'.$_GET['vid'], $url, 7200);
			$db->save();
			header("Location: ".$url);	
		}
	} else {
		header("X-Cache-Stat: cache hit");
		header("Location: ".$db->read('cache_'.$_GET['vid']));
	}
}

function getTot($vid , $modder) {
	$i = 0;
	$temp = 0;
	while ($i++ < strlen($vid)) {
		$temp = ($temp * 33) + ord($vid[$i]);
		if ($temp >= $modder) {
			$temp = bcmod($temp,$modder);
		}
	}
	return $temp;
}

function getDefaultFlvUrlbak($vid) {
	$fs = getTot($vid,4294967296) % (10000 * 10000);
	$url = "http://vhot2.qqvideo.tc.qq.com/$fs/$vid.flv?sdtfrom=v2";
	return $url;
	//return 'https://secure.bluehost.com/~twodland/dmf/p/qqvideo_.php?vid='."$vid";
	//return "http://v.video.qq.com/".$temp."/".$vid.".flv";
}

function get6CNURL($url) 
{
	$utc = time() + 123456;
	$key1 = $key = 0;
	$key3 = 1000000000 + mt_rand(0,1000000000);
	$key4 = 1000000000 + mt_rand(0,1000000000);
	
	$km = mt_rand(0,100);
	if ($km > 50) 
	{
		$key1 = abs(floor($utc / 3) ^ $key3);
		$key2 = abs(floor($utc / 3 * 2) ^ $key4);
	} else {
		$key1 = abs(floor($utc / 3 * 2) ^ $key3);
		$key2 = abs(floor($utc / 3) ^ $key4);
	}
	return "$url?key1=$key1&key2=$key2&key3=$key3&key4=$key4";
}

function HandleGet6Url($pagename, $auth = 'edit')
{
	global $EnableNotify;
	header("HTTP/1.1 301 Moved Permanently");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	$page = @RetrieveAuthPage($pagename, $auth, true, READPAGE_CURRENT);
	if (!$page) Abort("?cannot load $pagename");
	$vid = $_GET['vid'];
	if ($page['cache_'.$vid] == '') {
		header("X-Cache-Stat: cache miss");
		$EnableNotify = 0;
		$xml = @file_get_contents("http://6.cn/v72.php?vid=$vid");
		
		if (strpos($xml,'<file>') === FALSE) {
			header("X-Cache-Stat: cache error");
			header("Location: http://danmaku.us/p/jumper_error.mp4");
			return;
		}
		
		$file = substr($xml,strpos($xml,'<file>')+6,strpos($xml,'</file>')-6-strpos($xml,'<file>'));
		
		$new = $page;
		$url = get6CNURL($file);
		$new['cache_'.$_GET['vid']] = $file;
		UpdatePage($pagename, $page,$new);
		header("Location: ".$url);	
	//sth
	} else {
		header("X-Cache-Stat: cache hit");
		header("Location: ".get6CNURL($page['cache_'.$_GET['vid']]));
	}
}