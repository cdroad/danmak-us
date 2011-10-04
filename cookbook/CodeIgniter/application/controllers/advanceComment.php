<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdvanceComment extends CI_Controller {

	public function index()
	{
        global $BiliEnableSA;
        
        if ($BiliEnableSA)
        {
            die("<confirm>1</confirm><hasBuy>true</hasBuy>");
        } else {
            die("<confirm>0</confirm><hasBuy>false</hasBuy><accept>false</accept>");
        }
	}
}
