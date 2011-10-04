<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dmerror extends CI_Controller {

	public function index()
	{
        if (empty($_REQUEST['id']) || empty($_REQUEST['error']))
            exit;
        $pagename = 'Main.BPError';
        $new = $page = ReadPage($pagename, READPAGE_CURRENT);
        $new['text'] .= getErrorString($_REQUEST['error'])."\n视频:\n->(:pagelist group=Acfun2,Bilibili2 fmt=title ".$_REQUEST['id'].":)";
        
        UpdatePage($pagename, $page, $new);
	}
}
