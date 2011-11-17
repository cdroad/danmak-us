<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//Acfun (新) 播放器接口
class Anpi extends CI_Controller {
    public function getlogo()
    {
        die(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAA'.
            'AARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAMSURBVBhXY/j/'.
            '/z8ABf4C/qc1gYQAAAAASUVORK5CYII='));
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