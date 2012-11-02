<?php if (!defined('PmWiki')) exit();
echo <<<HEADER
<?xml version="1.0" encoding="utf-8"?>
<comments>
HEADER;

foreach ($Obj->comment as $node) {
    $attr = $node->attr[0]->attributes();
    $nodeA = $node->attributes();
    
    $attrs = array();
    $pt = $attr['playtime'];
    $mode = $attr['mode'];
    $fontsize = $attr['fontsize'];
    $color = $attr['color'];
    $SE = $attr['showeffect'];
    $HE = $attr['hideeffect'];
    $FE = $attr['fonteffect'];
    $sendtime = $nodeA['sendtime'];
    
    $usText = $node->text;
    $text = htmlspecialchars((string)$usText, ENT_NOQUOTES, "UTF-8");
    echo <<<CMT

    <comment mode="$mode" showEffect="$SE" hideEffect="$HE" fontEffect="$FE" isLocked="-1" fontSize="$fontsize" color="$color">
        <playTime>$pt</playTime>
        <message>$text</message>
        <sendTime>$sendtime</sendTime>
    </comment>
CMT;
}
echo <<<FOOTER

</comments>
FOOTER;
