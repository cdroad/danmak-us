<?php if (!defined('PmWiki')) exit();
$EnableNotify = 0;
$EnablePostAuthorRequired = 0;


function hashVid($vid,$head = true)
{
	$numb = $head ? "0x".substr(md5("DMR.B".$vid),0,4) : "0x".substr(md5($vid),0,4);
	return $numb * 1;
}

$HandleActions['Dad'] = "HandleDad";
$HandleAuth['Dad'] = 'read';
function HandleDad($pagename, $auth = 'edit')
{
	global $LoginAuthLevel, $BilibiliAuthLevel;
	
	$pn = 'DMR.Bilibili';
	if (isset($_REQUEST['id']))
	{
		$id = hashVid($_REQUEST['id']);
		$AuthLevelString = $BilibiliAuthLevel->$LoginAuthLevel;
		if (CondAuth($pn,'edit'))
		{
			echo <<<STR
<login>true</login>
<name>KPX</name>
<pwd>05589497</pwd>
<isadmin>true</isadmin>
<permission>$AuthLevelString</permission>
<level>VIP会员</level>
<shot>false</shot>
<chatid>$id</chatid>
<pid>0</pid>
<acceptguest>false</acceptguest>
<server>localhost</server>
<version>1299016295</version>
STR;
		} else {
			echo <<<STR
<login>true</login>
<name>DMF_User</name>
<pwd>05589497</pwd>
<isadmin>true</isadmin>
<permission>10000,1001</permission>
<level>DMF_User</level>
<shot>false</shot>
<chatid>$id</chatid>
<pid>0</pid>
<acceptguest>false</acceptguest>
<server>localhost</server>
<version>1299016295</version>
STR;
		}
		exit();
	}
}

$HandleActions['dmerror'] = "Handledmerror";
$HandleAuth['dmerror'] = 'edit';
//取得数据:
// $_REQUEST['id']
// $_REQUEST['error']
function Handledmerror($pagename, $auth = 'edit')
{
	if (empty($_REQUEST['id']) || empty($_REQUEST['error']))
		exit;
	$pagename = 'Main.BPError';
	$new = $page = ReadPage($pagename, READPAGE_CURRENT);
	$new['text'] .= getErrorString($_REQUEST['error'])."\n视频:\n->(:pagelist group=Acfun2,Bilibili2 fmt=title ".$_REQUEST['id'].":)";
	
	UpdatePage($pagename, $page, $new);
}

function getErrorString($err)
{
	switch($err)
	{
	case "dm error":
		return "弹幕XML错误";
	case "sp error":
		return "视频错误，无法加载";
	default:
		return "未知错误，代码：$err";
	}
}

$HandleActions['Playtag'] = "HandlePlaytag";
$HandleAuth['Playtag'] = 'read';

function HandlePlaytag($pagename,$auth='read')
{
	exit;
}

$HandleActions['dmduration'] = "Handledmduration";
$HandleAuth['dmduration'] = 'read';

function Handledmduration($pagename,$auth='read')
{
	exit;
}

$HandleActions['rec'] = "Handlerec";
$HandleAuth['rec'] = 'read';

function Handlerec($pagename,$auth='read')
{
	exit;
}


$DMM_Modes = array("move","credit","skip","del","update_comment_time");
$HandleActions['dmm'] = "HandleDmm";
$HandleAuth['dmm'] = 'read';


function HandleDmm($pagename,$auth='read')
{
	global $DMM_Modes;
	
	$AuthPage = 'DMR.GroupAttributes';
	//if (!CondAuth($AuthPage,'read'))
	//	die("1");
	
	if (!in_array($_REQUEST['mode'], $DMM_Modes))
		die("1");
	
	$func = "dmm_".$_REQUEST['mode'];
	$func();
	
}

function dmm_move()
{
	die("0");
}

function dmm_credit()
{
	die("0");
}

function findPoolIdByInId($inid)
{
	$pages = ListPages("/DMR\.B/");
	$pn = null;
	foreach ($pages as $p)
	{
		if (hashVid($p, false) == $inid)
		{
			$pn = $p;
			break;
		}
	}
	if (is_null($pn)) return NULL;
	
	$dmid = pathinfo($pn, PATHINFO_EXTENSION);
	$dmid = substr($dmid, 1);
	return $dmid;
}

function dmm_update_comment_time()
{
	$targetTime = intval($_REQUEST['time']);
	$dmId = intval($_REQUEST['dmid']);
	$dm_inid = intval($_REQUEST['dm_inid']);
	
	$poolId = findPoolIdByInId($dm_inid);
	if (is_null($poolId)) die("2");
	
	$DPool = new BiliUniDanmakuPair($poolId, PAIR_DYNAMIC);
	foreach ($DPool->find(array("id" => $dmId)) as $danmaku)
	{
		$oldattr = $newattr = $danmaku->getElementsByTagName("attr")->item(0);
		$newattr->setAttribute('playtime', $targetTime);
		$danmaku->replaceChild($newattr, $oldattr);
		
	}
	$DPool->save(PAIR_DYNAMIC);
}

function dmm_skip()
{
	die("0");
}

function dmm_del()
{
	if (empty($_REQUEST['playerdel']))
		die("1");

	$poolId = findPoolIdByInId($dm_inid);
	if (is_null($poolId)) die("2");
	
	$DPool = new BiliUniDanmakuPair($poolId, PAIR_DYNAMIC);
	foreach (explode(",", $_REQUEST['playerdel']) as $id)
	{
		$DPool->delete(PAIR_DYNAMIC, $id);
	}
	$DPool->save(PAIR_DYNAMIC);

	die("0");
}

$HandleActions['dmreport'] = "HandleDmreport";
$HandleAuth['dmreport'] = 'admin';

function HandleDmreport($pagename, $auth='admin')
{
	die("0");
}


$HandleActions['advanceComment'] = "HandleadvanceComment";
$HandleAuth['advanceComment'] = 'read';
function HandleadvanceComment($pagename, $auth='read')
{
	global $BiliEnableSA;
	
	if ($BiliEnableSA)
	{
		die("<confirm>1</confirm><hasBuy>true</hasBuy>");
	} else {
		die("<confirm>0</confirm><hasBuy>false</hasBuy><accept>false</accept>");
	}
}

