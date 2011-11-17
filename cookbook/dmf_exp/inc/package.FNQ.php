<?php
function GetFNQClass($url)
{
    $class = Utils::GetGroup($url)."FNQClass";
    return new $class($url);
}

Header("Content-Type: text/plain");
abstract class FNQClass
{
    protected $html;
    protected $xml;
    protected $cookieFile;
    protected $username;
    protected $password;
    protected $gc
    
    public function Init($url)
    {
        $this->Login();
        $this->html = $this->DownloadWebPage($url);
        $this->gc = Utils::GetGroupConfig($url);
        $this->xml = $this->gc->UploadFilePreProcess($this->GetXMLdata());
    }
    
    abstract protected function Login();
    abstract protected function DownloadWebPage($url);
    abstract protected function GetXMLData();
    abstract protected function GetDanmakuId();
    abstract protected function GetTitle();
    abstract protected function GetDesc();
    
    public function __toString()
    {
        $str = "";
        $str .= $this->GetTitle();
        $str .= "\r\n".$this->GetDesc();
        $id = $this->GetDanmakuId();
        if (is_array($id))
            {$str .= "\r\n{$id[1]} :: {$id[2]}";} else {$str .= "\r\n{$id}";}
        $str .= "\r\n".$this->GetXMLData();
        return $str;
    }
}

class Bilibili2FNQClass extends FNQClass
{
    public function __construct($url)
    {
        $this->cookieFile = 'test.txt';
        $this->Init($url);
    }
    
    protected function GetDesc()
    {
        preg_match('/\<meta.*description.*\"(.*)\".*>/',$this->html,$matches);
        $des = $matches[1];
    }
    
    protected function GetTitle()
    {
        preg_match('/\<title>(.*)\<\/title>/',$this->html,$matches);
        return $matches[1];
    }
    
    protected function GetDanmakuId()
    {
        preg_match('/(\<embed.*play\.swf.*embed>)/',$this->html,$matches);
        preg_match('/id=([0-9a-zA-Z]*)(\"|&| |\?)/',$matches[0],$matches2);
        $id = $matches2[1];
        return $id;
    }
    
    protected function GetXMLData()
    {
        $id = $this->GetDanmakuId();
        return @gzinflate(file_get_contents("http://comment.bilibili.tv/dm,{$id}"));
    }
    
    protected function DownloadWebPage($url)
    {
        global $FarmD;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }
    
    protected function Login()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.bilibili.tv/member/ajax_loginsta.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        $test = curl_exec($ch);
        curl_close($ch);
        if (strpos($test, "welcome") !== false) {return true;}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.bilibili.tv/member/index_do.php");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'fmdo=login&dopost=login&refurl=http%3A%2F%2Fwww.bilibili.tv%2F&keeptime=604800&userid=SHK&pwd=A98532E21655&keeptime=2592000');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        $test2 = curl_exec($ch);
        if (strpos($test2, 'javascript:history.go(-1)') === false) {return true;}
        return false;;
    }
}

class AcfunN1FNQClass extends FNQClass
{
    public function __construct($url)
    {
        $this->cookieFile = 'Acfun.txt';
        $this->Init($url);
    }
    
    protected function GetDesc()
    {
        preg_match('/\<meta.*description.*\"(.*)\".*>/',$this->html,$matches);
        $des = $matches[1];
    }
    
    protected function GetTitle()
    {
        preg_match('/\<title>(.*)\<\/title>/',$this->html,$matches);
        return $matches[1];
    }
    
    protected function GetDanmakuId()
    {
        preg_match('/(.*shockwave.*embed>)/',$this->html,$matches);
        preg_match('/id=([0-9a-zA-Z]*)(\"|&| |\?)/',$matches[0],$matches2);
        return $matches2[1];
    }
    
    protected function GetXMLData()
    {
        $id = $this->GetDanmakuId();
        $str = @file_get_contents("http://comment.acfun.tv/{$id}.json");
        $str .= @file_get_contents("http://comment.acfun.tv/{$id}_lock.json");
        return $str;
    }
    
    protected function DownloadWebPage($url)
    {
        global $FarmD;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }
    
    protected function Login()
    {
        return;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.bilibili.tv/member/ajax_loginsta.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        $test = curl_exec($ch);
        curl_close($ch);
        if (strpos($test, "welcome") !== false) {return true;}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.bilibili.tv/member/index_do.php");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'fmdo=login&dopost=login&refurl=http%3A%2F%2Fwww.bilibili.tv%2F&keeptime=604800&userid=SHK&pwd=A98532E21655&keeptime=2592000');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        $test2 = curl_exec($ch);
        if (strpos($test2, 'javascript:history.go(-1)') === false) {return true;}
        return false;;
    }
}

class Twodland1FNQClass extends FNQClass
{
    public function __construct($url)
    {
        $this->cookieFile = '2dland.txt';
        $this->Init($url);
    }
    
    protected function GetDesc()
    {
        if (preg_match('/\<meta.*description.*\"(.*)\".*>/',$this->html,$matches))
            return $matches[1];
    }
    
    protected function GetTitle()
    {
        if (preg_match('/\<title>(.*)\<\/title>/',$this->html,$matches))
            return $matches[1];
    }
    
    protected function GetDanmakuId()
    {
        preg_match('/{dir:\'([^\']*)\', vid:\'([^\']*)\'}/',$this->html , $matches);
        return $matches;
    }
    
    protected function GetXMLData()
    {
        $id = $this->GetDanmakuId();
        $str = @file_get_contents("http://www.2dland.cn/watch/api.php?mod=comment&act=load&static=0&dir={$id[1]}&vid={$id[2]}");
        return $str;
    }
    
    protected function DownloadWebPage($url)
    {
        global $FarmD;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }
    
    protected function Login()
    {
        return;
    }
}