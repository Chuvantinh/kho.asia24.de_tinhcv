<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 06/05/2018
 * Time: 09:35
 *
 * @author chuvantinh1991@gmail.com
 */

class m_admin_custom_tools extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = "admin_users";
        $this->_key_name            = "id";
        $this->_exist_created_field = FALSE;
        $this->_exist_updated_field = FALSE;
        $this->_exist_deleted_field = FALSE;
        $this->_schema      = Array();
        $this->_rule        = Array();
        $this->_field_form  = Array();
        $this->_field_table = Array();
    }

    public function setting_select()
    {
        parent::setting_select(); // TODO: Change the autogenerated stub
    }

}