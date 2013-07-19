<?php if (!defined('PmWiki')) exit();
function cmtSave($gc, $id, DanmakuBuilder $builder) {
    $id = basename($id);
    $_pagename = "DMR.{$gc->SUID}{$id}";
    $auth = 'edit';
    $page = @RetrieveAuthPage($_pagename, $auth, false, 0);
    if (!$page) {
        return false;
    }
    $xml = (string) $builder;
    $page['text'] .= (string) $builder;
    WritePage($_pagename, $page);
    return true;
}