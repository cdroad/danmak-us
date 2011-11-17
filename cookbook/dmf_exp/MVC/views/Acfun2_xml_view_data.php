<?php
echo <<<HEADER
<?xml version="1.0" encoding="utf-8"?>
<information>
HEADER;
/*
<comment id="2070612749" poolid="0" userhash="deadbeef" sendtime="1318518225">
	<text>asdfasdhfjyrqhjwefasdf</text>
	<attr id="0" playtime="271.8" mode="1" fontsize="25" color="16777215" />
</comment>
*/
foreach ($Obj->comment as $node) {
    $attr = $node->attr[0]->attributes();
    $nodeA = $node->attributes();
    
    $attrs = array();
    $pt = $attr['playtime'];
    $mode = $attr['mode'];
    $fontsize = $attr['fontsize'];
    $color = $attr['color'];
    $sendtime = $nodeA['sendtime'];
    
    $usText = $node->text;
    $pString = implode(",", $attrs);
    $text = htmlspecialchars((string)$usText, ENT_NOQUOTES, "UTF-8");
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
