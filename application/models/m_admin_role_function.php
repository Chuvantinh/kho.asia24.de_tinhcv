<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_admin_role_function
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_role_function extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_role_function';
        $this->_key_name            = 'id';
        $this->_exist_created_field = TRUE;
        $this->_exist_updated_field = TRUE;
        $this->_exist_deleted_field = FALSE;
        $this->_schema      = Array(
            'id', 'name', 'controller', 'action', 'status', 'description', 'editable',

            'created_by', 'created_at', 'updated_by', 'updated_at'
        );
        $this->_rule        = Array();
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'          => 'Role ID',
            'm.name'        => 'Nhóm quyền',
            'm.controller'  => 'Controller',
            'm.action'      => 'Action',
            'm.description' => 'Mô tả',
            'm.status'      => 'Trạng thái'
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*')
            ->from($this->_table_name . ' AS m');
    }

    public function get_list_exist_role_function_id($role_id, $list_function_id = Array())
    {
        return $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->where('role_id',$role_id)
            ->where_in('function_id', $list_function_id)
            ->get()
            ->result();
    }
}