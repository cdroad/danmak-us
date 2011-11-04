<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pmwiki extends CI_Controller {

	public function page_missing()
	{
		$this->view('Site/PageNotFound');
	}
	
	public function view($name = 'Main/HomePage')
	{
        $data = array('name' => $name);
        $this->load->view('pmwiki_view', $data);
	}
}
