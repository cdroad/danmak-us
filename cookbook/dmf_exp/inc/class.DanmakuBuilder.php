<?php
class DanmakuBuilder 
{
	private $text;
	private $attrIndex = 0;
	
	public function __construct($text, $pool, $userhash, $time = null)
	{
        if ($time == null) {
            $sendtime = time();
        } else {
            $sendtime = $time;
        }
        $text = htmlspecialchars($text, ENT_NOQUOTES);
		$dmid = mt_rand(0,2147483647); 
		$this->text = <<<CMT

<comment id="$dmid" poolid="$pool" userhash="$userhash" sendtime="$sendtime">
	<text>$text</text>

CMT;
	}
	
	public function AddAttr(array $fields)
	{
		$this->text .= "\t<attr id=\"$this->attrIndex\"";
        foreach ($fields as $key => $value) {
            $this->text .= " {$key}=\"{$value}\"";
        }
        $this->text .= " />\r\n";
		$this->attrIndex += 1;
	}
	
	public function __toString()
	{
		$End = "</comment>";
		return $this->text.$End;
	}
}
