<?php
echo <<<HEADER
<?xml version="1.0" encoding="UTF-8"?>
<i>
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
    $attrs[] = $attr['playtime'];
    $attrs[] = $attr['mode'];
    $attrs[] = $attr['fontsize'];
    $attrs[] = $attr['color'];
    $attrs[] = $nodeA['sendtime'];
    $attrs[] = $nodeA['poolid'];
    $attrs[] = $nodeA['userhash'];
    $attrs[] = $nodeA['id'];
    
    $usText = $node->text;
    $pString = implode(",", $attrs);
    $text = htmlspecialchars($usText, ENT_NOQUOTES, "UTF-8");
    echo <<<CMT

    <d p="$pString">$text</d>
CMT;
}
echo  <<<FOOTER

</i>
FOOTER;
