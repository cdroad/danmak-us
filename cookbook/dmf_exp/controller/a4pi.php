<?php if (!defined('PmWiki')) exit();

class a4pi extends K_Controller {
    private $GroupConfig;
    
    public function __construct() {
        $this->GroupConfig = Utils::GetGroupConfig("acfun4p");
        parent::__construct();
    }
    
    public function getlogo()
    {
        die(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAA'.
            'AARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAMSURBVBhXY/j/'.
            '/z8ABf4C/qc1gYQAAAAASUVORK5CYII='));
    }
    
    public function dmpost()
    {
        $this->Helper(playerInterface);
        if ($this->requireVars(
                $this->Input->Post,
                array("islock", "color", "text", "size", "mode", "stime", "timestamp", "poolid"))) {
            Abort("不允许直接访问");
        }
        
        if ($this->Input->Post->mode == 7) {
            $text = json_readable_encode(json_decode($this->Input->Post->text), 1);
            $builder = new DanmakuBuilder($text, 0, 'deadbeef');
        } else {
            $builder = new DanmakuBuilder($this->Input->Post->text, 0, 'deadbeef');
        }
        
        $attrs = array(
                'playtime'  => $this->Input->Post->stime,
                'mode'      => $this->Input->Post->mode,
                'fontsize'  => $this->Input->Post->size,
                'color'     => $this->Input->Post->color);
		$builder->AddAttr($attrs);

        if (cmtSave($this->GroupConfig, $this->Input->Post->poolid, $builder)) {
            die('DMF_Local :: a4pi :: dmpost() :: success!');
        } else {
            die('DMF_Local :: a4pi :: dmpost() :: page fail!');
        }
    }
    
    public function dmdelete()
    {
        $this->Helper(playerInterface);
        if ($this->requireVars(
                $this->Input->Post,
                array("islock", "color", "text", "size", "mode", "stime", "timestamp", "poolid"))) {
            Abort("不允许直接访问");
        }

		$key = $this->hashCmt(
            $this->Input->Post->text,
            $this->Input->Post->color,
            $this->Input->Post->size,
            $this->Input->Post->mode,
            $this->Input->Post->stime);
        $vid = basename($this->Input->Post->poolid);
        
		$dynPool = new DanmakuPoolBase(Utils::GetIOClass('Acfun4p', $vid, 'dynamic'));
        foreach ($dynPool->GetXML()->comment as $node)
		{
			$K = $this->hashCmt( $node->text, $node->attr[0]["color"],$node->attr[0]["fontsize"],$node->attr[0]["mode"],$node->attr[0]["playtime"]);
			if ($K == $key) {
				echo 'Found!'.$node->text."\r\n";
				unset($node[0][0]);
				break;
			}
		}
		
        $dynPool->Save()->Dispose();
    }
    
    public function getvideobyid($pageid)
    {
        $pid = basename($pageid);
        $source = new VideoPageData("Acfun4p.{$pid}");
        
        $arr["aid"] = $pid;
        $arr["uid"] = 1;
        $arr["vinfo"] = array("checked" => 2);
        $arr["cid"] = "";
        $arr["vid"] = "";
        $arr["vtype"] = "";
        
        switch (strtoupper($source->VideoType->getType()))
	    {
	        case "NOR":
	            $arr['vid'] = $source->DanmakuId;
	            $arr['cid'] = $source->DanmakuId;
	            $arr['vtype'] = "sina";
	        break;
	        
			case "QQ":
                $arr["vid"]   = $source->DanmakuId;
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "qq";
	        break;
	        
			case "TD":
                $arr["vid"]   = $source->DanmakuId;
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "tudou";
	        break;

			case "YK":
                $arr["vid"]   = $source->DanmakuId;
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "youku";
	        break;
	        
			case "URL":
			case "BURL":
			case "LINK":
			case "BLINK":
			case "LOCAL":
				$arr["vid"]   = rawurldecode($source->VideoStr);
				$arr["file"]   = rawurldecode($source->VideoStr);
				$arr["cid"]   = $source->DanmakuId;
				$arr["vtype"] = "url";
	        break;

			default:
				echo "$source->VideoType->getType(): $source->DanmakuId : $source->VideoStr";
				assert(false);
	        break;
	    }
	    echo json_encode($arr);
	    exit;
    }
    
	private function hashCmt($text, $color, $size, $mode, $stime)
	{
		return md5("$text$color$size$mode$stime");
	}
	
    public function ujson()
    {
        die('[]');
    }
    
    public function badwords()
    {
        die('[]');
    }
    
    public function adsjson()
    {
        die('');
    }
}








/*
    json readable encode
    basically, encode an array (or object) as a json string, but with indentation
    so that i can be easily edited and read by a human

    THIS REQUIRES PHP 5.3+

    Copyleft (C) 2008-2011 BohwaZ <http://bohwaz.net/>

    Licensed under the GNU AGPLv3

    This software is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This software is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this software. If not, see <http://www.gnu.org/licenses/>.
*/

function json_readable_encode($in, $indent = 0, Closure $_escape = null)
{
    if (__CLASS__ && isset($this))
    {
        $_myself = array($this, __FUNCTION__);
    }
    elseif (__CLASS__)
    {
        $_myself = array('self', __FUNCTION__);
    }
    else
    {
        $_myself = __FUNCTION__;
    }

    if (is_null($_escape))
    {
        $_escape = function ($str)
        {
            return str_replace(
                array('\\', '"', "\n", "\r", "\b", "\f", "\t", '/', '\\\\u'),
                array('\\\\', '\\"', "\\n", "\\r", "\\b", "\\f", "\\t", '\\/', '\\u'),
                $str);
        };
    }

    $out = '';

    foreach ($in as $key=>$value)
    {
        $out .= str_repeat("\t", $indent + 1);
        $out .= "\"".$_escape((string)$key)."\": ";

        if (is_object($value) || is_array($value))
        {
            $out .= "\n";
            $out .= call_user_func($_myself, $value, $indent + 1, $_escape);
        }
        elseif (is_bool($value))
        {
            $out .= $value ? 'true' : 'false';
        }
        elseif (is_null($value))
        {
            $out .= 'null';
        }
        elseif (is_string($value))
        {
            $out .= "\"" . $_escape($value) ."\"";
        }
        else
        {
            $out .= $value;
        }

        $out .= ",\n";
    }

    if (!empty($out))
    {
        $out = substr($out, 0, -2);
    }

    $out = str_repeat("\t", $indent) . "{\n" . $out;
    $out .= "\n" . str_repeat("\t", $indent) . "}";

    return $out;
}
