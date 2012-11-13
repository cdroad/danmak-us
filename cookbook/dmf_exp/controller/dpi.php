<?php if (!defined('PmWiki')) exit();
class Dpi extends K_Controller {

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

    public function postcmt()
    {
        $builder = new DanmakuBuilder($this->Input->Request->message, 0, 'deadbeef');
        $attrs = array(
                "fontsize" => $this->Input->Request->font_size,
                "playtime" => $this->Input->Request->play_time,
                "mode" => $this->Input->Request->action,
                "showeffect" => $this->Input->Request->show_effect,
                "hideeffect" => $this->Input->Request->hide_effect,
                "fonteffect" => $this->Input->Request->font_effect,
                "color" => $this->Input->Request->color);
        $builder->AddAttr($attrs);
        $xml = (string)$builder;
        $vid = $this->Input->Request->video_id;
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