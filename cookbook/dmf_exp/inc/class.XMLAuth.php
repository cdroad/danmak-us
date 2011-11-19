<?php
class XMLAuth
{
    public static function IsRead($dmid, $group)
    {
        $pn = Utils::GetDMRPageName($dmid, $group);
        return CondAuth('xmlread', $pn);
    }
    
    public static function IsEdit($dmid, $group)
    {
        $pn = Utils::GetDMRPageName($dmid, $group);
        return CondAuth('xmledit', $pn);
    }
    
    public static function IsAdmin($dmid, $group)
    {
        $pn = Utils::GetDMRPageName($dmid, $group);
        return CondAuth('xmladmin', $pn);
    }
    
}