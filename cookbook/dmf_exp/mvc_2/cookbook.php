<?php
class Cookbook extends K_Controller  {
    public function Cookbook() {
        parent::__construct();
    }
    
    public function dmf_exp($para1, $para2) {
        die("HIT!  {$para1}, {$para2}");
    }
}