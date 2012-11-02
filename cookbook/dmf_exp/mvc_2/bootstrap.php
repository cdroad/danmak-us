<?php
define("MVC_PATH", dirname(__FILE__));
ob_start();
spl_autoload_register(function ($class) {
    $p = "./controller/{$class}.php";
    if (file_exists($p)) 
        include($p);
});
include("./core/class.Controller.php");
include("./core/class.Router.php");
include("./core/class.Input.php");
$input = new K_Input();
$router = Router::getInstance();
$router->init($input);
if (!@class_exists($router->controller, true)) {
    $router->controller = "defaultController";
    $router->action = "page_missing";
}
$controller = create_object($router->controller);
die(call_user_func_array(array($controller, $router->action), $router->params));



function create_object($name) { return new $name(); }