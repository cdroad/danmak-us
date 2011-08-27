<?php
abstract class BaseFunc
{
	final static public function getGroupConfigObj($str)
	{
		switch (strtoupper($str))
		{
			case "BILIBILI2":
				return new Bilibili2GroupConfig();
			case "ACFUN2":
				return 'Acfun2';
			default:
				echo $str;
				assert(FALSE);
		}
	}
	
}