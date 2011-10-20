<?php

class DanmakuXPathBuilder
{
	private $str = "";
	
	public function DanmakuXPathBuilder($base = '//comments/comment')
	{
		$this->str = $base;
	}

	public function CommentId($id)
	{
		$this->append("@id='$id'");
		return $this;
	}
	
	public function CommentAttr($name, $value = "")
	{
		$this->append("//attrs/attr@$name='$value']");
		return $this;
	}
	
	public function CommentBaseAttr($name, $value = "")
	{
		$this->append("@$name='$value'");
		return $this;
	}
	
	public function Text($str)
	{
		$this->append("contains(string(text),'$str'");
		return $this;
	}
	
	public function ToString()
	{
		return $this->str;
	}
	
	private function append($str)
	{
		$this->str .= "[$str]";
	}
}
