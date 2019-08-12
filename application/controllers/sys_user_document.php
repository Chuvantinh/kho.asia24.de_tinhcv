<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_user_document extends manager_base {

    public function __construct()
    {

    }

    public function setting_class()
    {
        // TODO
    }

    public function index()
    {
        // TODO
    }

    /**
     * ghi de ham require_login
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function require_login()
    {
        return false;
    }

    /**
     * Ghi de ham check_permission, mac dinh tra ve true de co quyen truy cap chuc nang
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function check_permission()
    {
        return true;
    }

	public function faq()
	{
		return $this->load->view('sys_user_document/faq');
	}

    public function terms_conditions()
	{
        return $this->load->view('sys_user_document/terms_conditions');
	}
}
