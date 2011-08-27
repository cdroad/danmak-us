<?php if (!defined('PmWiki')) exit();

//https://secure.bluehost.com/~twodland/dmf/p/qqvideo_.php?vid=
function __autoload($classname)
{
  require_once("./cookbook/dmf_exp/inc/class.$classname.php");
}

$acips = array('220.170.79.48', '220.170.79.105', '220.170.79.109', '124.228.254.234', '124.228.254.229');
$FarmPubDirUrl = 'http://'.$_SERVER['HTTP_HOST'].'/shared/pub';

if ($_SERVER['HTTP_HOST'] == '69.89.31.95')
{
	$FarmPubDirUrl = 'https://secure.bluehost.com/~twodland/dmf/shared/pub';
	$EnablePathInfo = 0;
	$ScriptUrl = "https://secure.bluehost.com/~twodland/dmf";
	Player::$playerBase = 'http://danmaku.us/static/players/';
}


if (strpos($_SERVER['HTTP_HOST'],'danmaku.us') !== FALSE )
{
	$FarmPubDirUrl = 'http://'.$_SERVER['HTTP_HOST'].'/shared/pub';
	$EnablePathInfo = 1;
	$ScriptUrl = "http://".$_SERVER['HTTP_HOST'];
	Player::$playerBase = 'http://'.$_SERVER['HTTP_HOST'].'/static/players/';
}


if ($_SERVER['HTTP_HOST'] == 'localhost')
{
	$FarmPubDirUrl = 'http://localhost/shared/pub';
	$EnablePathInfo = 1;
	$ScriptUrl = "http://localhost";
	Player::$playerBase = 'http://localhost/static/players/';
}