<?php
function dmid_to_idhash($dmid, $prefix = true)
{
    $numb = $head ? substr(md5("DMR.B".$vid),0,4) : substr(md5($vid),0,4);
    return intval($numb, 16);
}

function idhash_to_dmid($hash)
{
    $pn = null;
    foreach ( ListPages("/DMR\.B/") as $page) {
        if ( dmid_to_idhash($page, false) == $hash ) {
            $pn = $page;
        }
    }
    
    if (is_null($pn)) return "";
    
	$dmid = pathinfo($pn, PATHINFO_EXTENSION);
	$dmid = substr($dmid, 1);
    return $dmid;
}