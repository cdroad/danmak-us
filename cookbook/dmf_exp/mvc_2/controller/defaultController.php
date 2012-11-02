<?php 
class DefaultController extends K_Controller {

    private function _urlReplace($pattern, $to, &$subject) {
        $subject = preg_replace($pattern, $to, $subject);
    }
    
    public function try_getFile() {
        $bak = $p = $this->input->server('REQUEST_URI');
        $this->_urlReplace("{^/static/(.*)}i", "/pub/$1", $p);
        $this->_urlReplace("{^/pub/players/player([^/]*)\.swf$}i", "/pub/players/ac/player$1.swf", $p);
        //$this->_urlReplace("{^/pub/players/bi([^/]*)\.swf$}i", "/pub/players/bi/player$1.swf", $p);
        
        $p = substr($p, 1);
        if (file_exists($p)) {
            Header("Url_Router_Stats : {$bak} => {$p}");
            include($p);
            exit;
        } else {
            $this->page_missing();
        }
        
    }
    
	public function page_missing()
	{
        //return "BBBBBBBBBBB";
		echo $this->GetView('Site/PageNotFound');
	}
	
	public function view($name = 'Main/HomePage')
	{
        $data = array('name' => $name);
        $this->DisplayView('pmwiki_view', $data);
	}
}
