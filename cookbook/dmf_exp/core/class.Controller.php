<?php if (!defined('PmWiki')) exit();
class K_Controller {
    public function K_Controller() {}
    
    protected function DisplayView($viewName, $vars = array()) {
        //localize vars
        foreach ($vars as $k => $v) {
            $$k = $v;
        }
        
        $p = MVC_PATH."/view/{$viewName}.php";
        if (file_exists($p)) {
            include($p);
        } else {
            die("view not found {$p}.");
        }
        
    }
    
    protected function GetView($viewName, $vars = array()) {
        ob_flush();
        $this->DisplayView($viewName, $vars);
        return ob_get_clean();
    }
    
}