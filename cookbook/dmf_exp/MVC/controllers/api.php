<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//Acfun (老) 播放器接口
class Api extends CI_Controller {
    public function filtrate()
    {
        die('<?xml version="1.0" encoding="utf-8"?><information>
<filtrate name="普通关键字"><data>费大人</data></filtrate><filtrate name="超级关键字"><data>费大人</data></filtrate></information>');
    }
    
    public function filtrate2()
    {
        die('<?xml version="1.0" encoding="utf-8"?><information><data>taobao</data></information>');
    }    

    public function ujson()
    {
        die('[]');
    }
    
    public function badwords()
    {
        die('[]');
    }
}