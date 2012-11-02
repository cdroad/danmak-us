<?php

class K_Input {
    private $get;
    private $file;
    private $post;
    private $server;
    private $request;
    
    public function K_Input () {
        $this->server   = $_SERVER;
        $this->get      = $_GET;
        $this->post     = $_POST;
        $this->file     = @$_FILE;
        $this->request  = $_REQUEST;
    }
    
    public function post($id)    {return $this->post[$id];   }
    public function get($id)     {return $this->get[$id];    }
    public function file($id)    {return $this->file[$id];   }
    public function server($id)  {return $this->server[$id]; }
    public function request($id) {return $this->request[$id];}
    
}