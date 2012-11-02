<?php
define("MVC_PATH", dirname(__FILE__));

spl_autoload_register(function ($class) {
    $p = MVC_PATH."/controller/{$class}.php";
    if (file_exists($p)) 
        include($p);
});
include(MVC_PATH."/core/class.Controller.php");
include(MVC_PATH."/core/class.Router.php");
include(MVC_PATH."/core/class.Input.php");


$HandleActions['mvc'] = 'HandleMVCURL';
$HandleAuth['mvc'] = 'read';
function HandleMVCURL($pn, $auth = 'read') {
    //die("MVC Function inited");
    ob_start();
    $input = new K_Input();
    $router = Router::getInstance();
    $router->init($input);
    if (!@class_exists($router->controller, true)) {
        $router->controller = "defaultController";
        $router->action = "page_missing";
    }
    //die($router->controller);
    $controller = create_object($router->controller);
    //方法存在判定
    //is_callable
    die(call_user_func_array(array($controller, $router->action), $router->params));
}

function create_object($name) { return new $name(); }