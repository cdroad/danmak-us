<?php
echo <<<HEADER
<?xml version="1.0" encoding="utf-8"?>
<information>
HEADER;
foreach ($Obj->comment as $node) {
    $attr = $node->attrs->attr[0]->attributes();
    $nodeA = $node->attributes();
    
    $attrs = array();
    $pt = $attr['playtime'];
    $mode = $attr['mode'];
    $fontsize = $attr['fontsize'];
    $color = $attr['color'];
    $sendtime = $nodeA['sendtime'];
    
    $usText = $node->text;
    $pString = implode(",", $attrs);
    $text = htmlspecialchars($usText, ENT_NOQUOTES, "UTF-8");
    echo <<<CMT
  <data>
    <playTime>$pt</playTime>
    <message fontsize="$fontsize" color="$color" mode="$mode">$text</message>
    <times>$sendtime</times>
  </data>
CMT;
}
echo <<<FOOTER
</information>
FOOTER;
