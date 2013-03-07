<?php if (!defined('PmWiki')) exit();

//$this->Helper("danmakuPool");
function GetPool($group, $dmid, $pool) {
    $group = Utils::GetGroup($group);
    if ($group === FALSE) return false;
    return new DanmakuPoolBase($group, $dmid, $pool, LoadMode::lazy);
}

//基本无用
function StrToPool($str) {
    switch (strtolower($str)) {
        case "static"  :
            return PoolMode::S;
        case "dynamic" :
            return PoolMode::D;
        case "all"     :
            return PoolMode::A;
        default        :
            die($str);//Fix me
    }
}

function XmlAuth($group, $dmid, $auth) {
    $pn = Utils::GetDMRPageName($dmid, Utils::GetGroup($group));
    switch ($auth) {
        case XmlAuth::read:
            return CondAuth($pn, 'xmlread');
            break;
        case XmlAuth::edit:
            return CondAuth($pn, 'xmledit');
            break;
        case XmlAuth::admin:
            return CondAuth($pn, 'xmladmin');
            break;
    }
}










