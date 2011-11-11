<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dpi extends CI_Controller {

    //http://www.2dland.cn/watch/data/player/201002/225.xml?baka=0.945524160284549
    public function getconfigxml($para1, $para2 = null)
    {
        if ($para2 == null) return $this->forbidden();
        $file = './static/page/'.md5(substr($para2,0,-4)).'.xml';
        include_once($file);exit;
    }
    
    public function memberinfo()
    {
        die('{"uid":"1","username":"DMF用户","groupid":"1"}');
    }
    
    public function forbidden()
    {
        die('<?xml version="1.0" encoding="UTF-8"?><keywords/>');
    }
    
    public function test()
    {
        die("test");
    }
    
}