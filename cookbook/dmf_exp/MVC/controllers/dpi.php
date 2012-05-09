<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(DMF_ROOT_PATH."config.Twodland1.php");
class Dpi extends CI_Controller {

    //http://www.2dland.cn/watch/data/player/201002/225.xml?baka=0.945524160284549
    public function getconfigxml($para1, $para2 = null)
    {
        if ($para2 == null) return $this->forbidden();
        $file = './static/page/'.md5(substr($para2,0,-4)).'.xml';
        echo file_get_contents($file);exit;
    }
    
    public function memberinfo()
    {
        if (XMLAuth::IsEdit("*",'twodland')) {
            die('{"uid":"1","username":"DMF用户","groupid":"1"}');
        } else {
            die('{"uid":0,"username":"","groupid":7}');
        }
    }
    
    public function forbidden()
    {
        die('<?xml version="1.0" encoding="UTF-8"?><keywords/>');
    }
    
    public function test()
    {
        die("test");
    }
    /*
        http://www.2dland.cn/watch/ajax.php?mod=comment&act=post
        font%5Fsize=24&video%5Fid=12898&play%5Ftime=0%2E4&action=1&show%5Feffect=1&message=%E6%9C%89%E5%A5%B6%E3%80%82%E3%80%82%E3%80%82&hide%5Feffect=1&font%5Feffect=1&color=16777215
        rep:{"ok":1,"cmnt_id":1265}
    */
    public function postcmt()
    {
        $builder = new DanmakuBuilder($_REQUEST['message'], 0, 'deadbeef');
        $attrs = array(
                "fontsize" => $_REQUEST['font_size'],
                "playtime" => $_REQUEST['play_time'],
                "mode" => $_REQUEST['action'],
                "showeffect" => $_REQUEST['show_effect'],
                "hideeffect" => $_REQUEST['hide_effect'],
                "fonteffect" => $_REQUEST['font_effect'],
                "color" => $_REQUEST['color']);
        $builder->AddAttr($attrs);
        $xml = (string)$builder;
        $vid = $_REQUEST['video_id'];
        //准备写入PmWiki
        $_pagename = 'DMR.D'.$vid;
        $auth = 'xmledit';
        $page = @RetrieveAuthPage($_pagename, $auth, false, 0);
        if (!$page) die('{"ok":-1,"cmnt_id":1265}');
        
        $page['text'] .= $xml;
        WritePage($_pagename, $page);
        die('{"ok":1,"cmnt_id":1265}');
    }
}