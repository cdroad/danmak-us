<?php if (!defined('PmWiki')) exit();
class XMLAuth
{
    public static function IsRead($dmid, $group)
    {
        $pn = Utils::GetDMRPageName($dmid, $group);
        return CondAuth($pn, 'xmlread');
    }
    
    public static function IsEdit($dmid, $group)
    {
        $pn = Utils::GetDMRPageName($dmid, $group);
        return CondAuth($pn, 'xmledit');
    }
    
    public static function IsAdmin($dmid, $group)
    {
        $pn = Utils::GetDMRPageName($dmid, $group);
        return CondAuth($pn, 'xmladmin');
    }
    
}