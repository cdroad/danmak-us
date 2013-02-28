<?php if (!defined('PmWiki')) exit();


$route['(^member\/dmm)'] = empty($_REQUEST['mode']) ? "bpi/error" : "bpi/".$_REQUEST['mode'];
$route['(^bpi\/dmm)'] = empty($_REQUEST['mode']) ? "bpi/error" : "bpi/".$_REQUEST['mode'];
$route['(^b3pi\/dmm)'] = empty($_REQUEST['mode']) ? "b3pi/error" : "b3pi/".$_REQUEST['mode'];
$route['(^newflvplayer\/pad\.xml)'] = "bpi/bpad" ;
$route['(^poolop\/loadxml\/twodland1.*)'] = "poolop/loadxml/twodland1/{$_REQUEST['vid']}";
$route['(^dpi\/getconfigxml\/([^\/]+)\/([^\/]*))'] = "dpi/getconfigxml/$2/$3/";
$route["{^static/(.*)}i"] = "/pub/$1";
$route["{^pub/players/player([^/]*)\.swf$}i"] = "/pub/players/ac/player$1.swf";
$route["{^pub/players/bi([^/]*)\.swf$}i"] = "/pub/players/bi/player$1.swf";


foreach ($route as $k => $v) {
    Router::addRule($k, $v);
}