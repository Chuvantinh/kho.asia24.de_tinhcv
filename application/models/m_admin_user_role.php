<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class M_admin_user_role extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = "admin_user_role";
        $this->_key_name            = "id";
        $this->_exist_created_field = true;
        $this->_exist_updated_field = false;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'user_id', 'role_id', 'editable',

            'created_at', 'created_by'
        );
        $this->_rule        = Array();
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'          => 'ID',
            'm.user_id'     => 'User ID',
            'm.role_id'     => 'Role ID',
            'm.created_at'  => 'Thời gian tạo',
            'm.created_by'  => 'ID người tạo',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
    }
}