<?php

abstract class DanmakuBarItem
{
    public static $Auth;
    abstract public function auth();
    abstract public function getString(GroupConfig $gc);
    abstract public function selfStyle();
}
DanmakuBarItem::$Auth = new Enum("Guest", "Member", "Admin");

class DanmakuBarGroup extends DanmakuBarItem
{
    private $arr = array();
    private $auth;
    
    public function __construct($auth)
    {
        $this->auth = $auth;
    }
    
    public function add(DanmakuBarItem $item)
    {
        $this->arr[] = $item;
    }
    
    public function auth() {return $this->auth;}
    
    public function getString(GroupConfig $gc)
    {
        foreach ($this->arr as $item) {
            $str .= $item->getString($gc).'&nbsp;&nbsp;';
        }
        return $str;
    }
    public function selfStyle() {return false;}
}

class DanmakuBarNewLine extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Guest;}
    
    public function getString(GroupConfig $gc)
    {
        return '<br />';
    }
    public function selfStyle() {return true;}
}

class DanmakuBarDownloadXML extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Guest;}
    public function getString(GroupConfig $gc)
    {
        $group = $gc->GroupString;
        $xmlformat = $gc->AllowedXMLFormat;
        
        foreach ($xmlformat as $format) {
            $formats .= "(:input select name=format value={$format} label={$format} :)";
        }
        
         return "(:input form class=\"DanmakuBar\" \"{*\$host}/poolop/loadxml/{$group}/{\$DMID}\" method=get:)".
         "下载格式：$formats".
         '附件：(:input checkbox name=attach value=true checked:)(:input submit value="下载":)'.
         '(:input end:)&nbsp;&nbsp;';	
    }
    public function selfStyle() {return true;}
}

class DanmakuBarUploadXML extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Member;}
    public function getString(GroupConfig $gc)
    {
        $group = $gc->GroupString;
        return   '(:input form class="DanmakuBar" enctype="multipart/form-data" '.
                 "\"{\$host}/poolop/post/{$group}/{\$DMID}\" :)".
                 '(:input file uploadfile:)'.
                 '弹幕池:(:input select name=Pool value=Static label=静态 :)'.
                 '(:input select name=Pool value=Dynamic label=动态 :)'.
                 '追加:(:input checkbox Append value=true:)'.
                 '(:input submit post Upload value="上传":)'.
                 '(:input end:)&nbsp;&nbsp;';
    }
    public function selfStyle() {return true;}
}

class DanmakuBarEditPart extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Member;}
    public function getString(GroupConfig $gc)
    {
        return '(:if2 equal "{*$IsMuti}" "true" :)[[{*$FullName}?action=edit | 编辑Part]](:else2:){-编辑Part-}(:if2end:)';
    }
    public function selfStyle() {return false;}
}

class DanmakuBarValPool extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Member;}
    public function getString(GroupConfig $gc)
    {
        $group = $gc->GroupString;
        return "[[{*\$host}/poolop/validate/{$group}/{\$DMID}/dynamic |验证动态池]]";
    }
    public function selfStyle() {return false;}
}

class DanmakuBarEditPool extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Member;}
    public function getString(GroupConfig $gc)
    {
        $suid = $gc->SUID;
        return "[[DMR/{$suid}{*\$DMID}?action=edit|动态池编辑]]";
    }
    public function selfStyle() {return false;}
}

class DanmakuBarPoolClear extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Admin;}
    public function getString(GroupConfig $gc)
    {
        $group = $gc->GroupString;
        return '移动弹幕池： '.
               "[[{*\$host}/poolop/move/{$group}/{\$DMID}/static/dynamic | S-D]]&nbsp".
               "[[{*\$host}/poolop/move/{$group}/{\$DMID}/dynamic/static | D-S]]&nbsp";
    }
    public function selfStyle() {return false;}
}

class DanmakuBarPoolMove extends DanmakuBarItem
{
    public function auth() {return self::$Auth->Admin;}
    
    public function getString(GroupConfig $gc)
    {
        $group = $gc->GroupString;
        return '清空弹幕池： '.
               "[[{*\$host}/poolop/clear/{$group}/{\$DMID}/static | 静态]]&nbsp".
               "[[{*\$host}/poolop/clear/{$group}/{\$DMID}/dynamic | 动态]]&nbsp".
               "[[{*\$host}/poolop/clear/{$group}/{\$DMID}/all | 双杀]]&nbsp";
    }
    public function selfStyle() {return false;}
}















