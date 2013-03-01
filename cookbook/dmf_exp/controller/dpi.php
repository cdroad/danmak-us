<?php if (!defined('PmWiki')) exit();
class Dpi extends K_Controller {
    private $GroupConfig;
    
    public function __construct() {
        $this->GroupConfig = Utils::GetGroupConfig("2dland");
        parent::__construct();
    }
    
    public function getconfigxml($para1, $para2 = null)
    {
        if ($para2 == null) return $this->forbidden();
        $file = './static/page/'.md5(substr($para2,0,-4)).'.xml';
        echo file_get_contents($file);exit;
    }
    
    public function memberinfo()
    {
        if (XmlAuth('twodland', "*", XmlAuth::edit)) {
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
        $this->Helper('playerInterface');
        if ($this->requireVars(
                $this->Input->Post,
                array("fontsize", "playtime", "mode", "showeffect",
                    "hideeffect", "fonteffect", "color", "message", "video_id"))) {
            Abort("不允许直接访问");
        }
        
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
        
        if (cmtSave($this->GroupConfig, $this->Input->Request->video_id, $builder)) {
            die('{"ok":-1,"cmnt_id":1265}');
        } else {
            die('{"ok":1,"cmnt_id":1265}');
        }
        
    }
}