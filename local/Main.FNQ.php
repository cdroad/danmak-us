<?php if (!defined('PmWiki')) exit();
//TODO:重构提交器
//处理投稿请求

if ($_POST["xVerify"]=="5259797e-df81-4ee9-b00b-e961dc133e6e")  {
	$HandleActions['FastPost'] = 'HandleFastPost';
	$HandleAuth['FastPost'] = 'edit';
	$EnablePostAuthorRequired = 0; 
}


function HandleFastPost($pagename, $auth = 'read') {
	if ( $_COOKIE['author'] != '') {
		$_POST['author'] == $_COOKIE['author'];
	}
if ( $_POST["xVerify"] == '5259797e-df81-4ee9-b00b-e961dc133e6e' ){
	global $HTTPHeaders, $FmtPV ,$page , $MessagesFmt;
		if (stripos($_POST['xURL'],'bilibili.us') == true) {
			//if bilibili
			$D = biliwebdata($_POST["xURL"]);
			if ($D['id'] != "") {$xml = @gzinflate(file_get_contents('http://bilibili.us/dm,'.$D['id']));}
			
			foreach ($D as $key => $value) 
			{
				$data .= $key."\t=>\t".$value."\n";
			}
			
			$new['text'] = '[@'.$data.$xml."\n\n@]";
			$gn = "Queue.".time();UpdatePage($gn, $old,$new);
			$MessagesFmt = <<<STR
取得视频ID => OK; <br />
取得标题 => OK; <br />
取得分类 => OK; <br />
暂存弹幕XML => OK; <br />
STR;
			HandleBrowse('Main.FNQ');
		} else {
			//if acfun
			str_replace($acips,'www.acfun.cn',$_POST['xURL']);
			$id = acfweb2id($_POST["xURL"]);
			$xmld = file_get_contents('http://124.228.254.234/newflvplayer/xmldata/'.$id.'/comment_on.xml');
			$new['text'] = '[@'.$_POST['xURL']."\n$id\n".$xmld."\n\n@]";
			$gn = "Queue.".time();UpdatePage($gn, $old,$new);
			$MessagesFmt = "提交完成，请等待管理员处理";
			HandleBrowse('Main.FNQ');
		}
	} else {
		header('HTTP/1.1 403 Forbidden');
		exit;
	}
}


function biliwebdata($url) {
	global $FarmD;
	include_once("./local/Bilibili2.php");
	
	$LS = bilibiliLogin();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "$FarmD/temp/cookies.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "$FarmD/temp/cookies.txt");
	$str = curl_exec($ch);
	curl_close($ch);

	if (stripos($str, "play.swf") === false) {return array('url' => $url, 'LS' => $LS, 'MS' => 'PF');}
	
	preg_match('/(\<embed.*play\.swf.*embed>)/',$str,$matches);
	preg_match('/id=([0-9a-zA-Z]*)(\"|&| |\?)/',$matches[0],$matches2);
	$id = $matches2[1];
	preg_match('/\<meta.*keywords.*\"(.*)\".*>/',$str,$matches);
	$tags = $matches[1];
	preg_match('/\<meta.*description.*\"(.*)\".*>/',$str,$matches);
	$des = $matches[1];
	preg_match('/\<title>(.*)\<\/title>/',$str,$matches);
	$title = $matches[1];
	
	return array(
		'url' => $url,
		'id' => $id,
		'tags' => $tags,
		'des' => $des,
		'title' => $title,
		'LS' => $LS
		);
}
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
