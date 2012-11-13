<?php if (!defined('PmWiki')) exit();
function CmtPostArgChk() {
    global $MVC_Input;
    
    if ($MVC_Input->Post->islock    == ''		||
        $MVC_Input->Post->color     == ''		||
        $MVC_Input->Post->text      == ''		||
        $MVC_Input->Post->size      == ''		||
        $MVC_Input->Post->mode      == ''		||
        $MVC_Input->Post->stime     == ''		||
        $MVC_Input->Post->timestamp == ''		||
        $MVC_Input->Post->poolid    == ''		) {
        return true;
    } else {
        return false;
    }
}