<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Sys_not_method_config
 *
 * @author chuvantinh1991@gmail.com
 */
class Sys_not_method_config extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "sys_not_method_config",
            "view"      => "sys_not_method_config",
            "model"     => "m_sys_not_method_config",
            "object"    => "Method không đưa vào Danh sách Function"
        );
    }
}