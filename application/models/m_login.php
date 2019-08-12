<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class M_login extends data_base
{
    public function setting_table()
    {
        $this->_table_name  = "";
        $this->_key_name    = "id";
        $this->_schema      = Array();
        $this->_rule        = Array(
            'user_name'         => array(
                'type'              => 'text',
                'maxlength'         => 255,
                'required'          => 'required',
                'unique'            => true
            ),
            'password'          => array(
                'type'              => 'password',
                'maxlength'         => 255,
                'minlength'         => 6,
                'required'          => 'required'
            )
        );
        $this->_field_form  = Array(
            'user_name' => 'Tên đăng nhập',
            'password'  => 'Mật khẩu'
        );
        $this->_field_table = Array();
    }

}