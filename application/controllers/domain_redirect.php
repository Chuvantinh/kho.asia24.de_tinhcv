<?php

if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}


class Domain_redirect extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = array('domain' => '');
        $domain = $this->input->get('domain');
        if($domain) {
            $data['domain'] = $domain;
        }
        $this->load->view('domain_redirect/index', $data);
//        $this->load->view('directory_name/file_name');
    }
}