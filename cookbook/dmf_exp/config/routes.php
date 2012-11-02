<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

/*
 *
		location ~ /static/players/ {
			root   html;
			rewrite ^/static/players/player([^/]*)\.swf$	/static/players/ac/player$1.swf last;
			rewrite ^/static/players/bi([^/]*)\.swf$		/static/players/bi/bi$1.swf last;
		}
 *
 */
#$route['(^/static/players/player([^/]*)\.swf$)'] = "/pub/players/ac/player$2.swf";
#$route['(^/static/players/bi([^/]*)\.swf$)'] = "/pub/players/bi/bi$2.swf";
#$route['(^/static/(.*)$)'] = "/pub/$2";

$route['(^member\/dmm)'] = empty($_REQUEST['mode']) ? "bpi/error" : "bpi/".$_REQUEST['mode'];
$route['(^newflvplayer\/pad\.xml)'] = "bpi/bpad" ;
$route['(^poolop\/loadxml\/twodland1.*)'] = "poolop/loadxml/twodland1/{$_REQUEST['vid']}";
$route['(^dpi\/getconfigxml\/([^\/]+)\/([^\/]*))'] = "dpi/getconfigxml/$2/$3/";

$route['default_controller'] = "defaultController/try_getFile";
$route['404_override'] = 'defaultController/try_getFile';


/* End of file routes.php */
/* Location: ./application/config/routes.php */