<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bdad extends CI_Controller {

	public function index()
	{
        $data = array();
        
        if (isset($_REQUEST['id'])) {
            $data['ChatId'] = hashVid($_REQUEST['id']);
        } else {
            $data['ChatId'] = 0;
        }
        
        $pn = 'DMR.Bilibili';
        if (CondAuth($pn,'edit')) {
            $data['AuthLevelString'] = $BilibiliAuthLevel->Danmakuer;
        } else {
            $data['AuthLevelString'] = $BilibiliAuthLevel->DefaultLevel;
        }
        
        $this->load->view('bilibili_dad_xml', $data);
	}
}
