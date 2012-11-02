<?php if (!defined('PmWiki')) exit();
#$route['(^/static/players/player([^/]*)\.swf$)'] = "/pub/players/ac/player$2.swf";
#$route['(^/static/players/bi([^/]*)\.swf$)'] = "/pub/players/bi/bi$2.swf";
#$route['(^/static/(.*)$)'] = "/pub/$2";

$route['(^member\/dmm)'] = empty($_REQUEST['mode']) ? "bpi/error" : "bpi/".$_REQUEST['mode'];
$route['(^newflvplayer\/pad\.xml)'] = "bpi/bpad" ;
$route['(^poolop\/loadxml\/twodland1.*)'] = "poolop/loadxml/twodland1/{$_REQUEST['vid']}";
$route['(^dpi\/getconfigxml\/([^\/]+)\/([^\/]*))'] = "dpi/getconfigxml/$2/$3/";

$route['default_controller'] = "defaultController/try_getFile";
$route['404_override'] = 'defaultController/try_getFile';