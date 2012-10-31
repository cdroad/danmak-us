<?php
class K_Controller {
    public function K_Controller() {}
    
    protected function DisplayView($viewName, $vars) {
        echo "Display View -{$viewName}- with \r\n";
        var_dump($vars);
    }
    
    protected function GetView($viewName, $vars) {
        echo "Return View -{$viewName}- with \r\n";
        var_dump($vars);
    }
    
    
}