<?php
//FILE : dmf_api/imps/{Action}/{Group}.php {Group} = Default Acfun2 Bilibili2




class ServiceProvider
{
    private static $impInst = array();  
    
    public static function Add($pattern, $name)
    {
        self::$impInst[] = new ServiceProviderImp($pattern, $name);
    }
    
    public static function MatchAndInvolve($uri)
    {
        foreach (self::$impInst as $imp) {
            if ($imp->Match($uri)) {
                $name = $imp->name;
            }
        }
    }
    
}

interface IService
{
    public function involve();
}

class ServiceProviderImp
{
    private $pattern;
    private $name;
    
    public function __construct($pat, $name)
    {
        $this->pattern = $pat;
        $this->name    = $name;
    }
    
    public function Match($uri)
    {
        return (bool)preg_match($this->pattern, $uri);
    }
    
    public function __get($k)
    {
        return $this->$k;
    }
}