<?php
class DanmakuBuilder 
{
	private $text;
	private $attrIndex = 0;
	
	public function __construct($text, $pool, $userhash)
	{
		$sendtime = time();
		$dmid = mt_rand(0,2147483647); 
		$this->text = <<<CMT

<comment id="$dmid" poolid="$pool" userhash="$userhash" sendtime="$sendtime">
	<text>$text</text>

CMT;
	}
	
	public function AddAttr($playtime, $mode, $fontsize, $color)
	{
		$this->text .= 
			"\t<attr id=\"$this->attrIndex\" playtime=\"$playtime\" mode=\"$mode\" fontsize=\"$fontsize\" color=\"$color\" />\r\n";
		$this->attrIndex += 1;
	}
	
	public function __toString()
	{
		$End = "</comment>";
		return $this->text.$End;
	}
}
