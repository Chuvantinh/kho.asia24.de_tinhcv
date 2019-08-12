<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_admin_menus
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_menus extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = "admin_menus";
        $this->_key_name            = "id";
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'parent_id', 'display', 'icon', 'url', 'controller', 'method',

            'class', 'sort', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'parent_id'     => array(
                'type'              => 'select',
                'target_model'      => 'm_admin_menus',
                'target_value'      => 'id',
                'target_display'    => 'display',
                'where_condition'   => array(
                    "(m.parent_id IS NULL OR m.parent_id = 0)"   => NULL
                ),
                "allow_null"     => "true",
            ),
            'display'       => array(
                'type'          => 'text',
                'maxlength'     => 100,
                'required'      => 'required'
            ),
            'icon'          => array(
                'type'          => 'text',
                'maxlength'     => 50,
                'required'      => 'required'
            ),
            'url'           => array(
                'type'          => 'text',
                'maxlength'     => 100,
                'required'      => 'required'
            ),
            'controller'    => array(
                'type'          => 'text',
                'maxlength'     => 100,
            ),
            'method'        => array(
                'type'          => 'text',
                'maxlength'     => 100,
            ),
            'class'         => array(
                'type'          => 'text',
                'maxlength'     => 100,
            ),
            'sort'          => array(
                'type'          => 'number',
                'maxlength'     => 4,
                'minlength'     => 1,
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form  = Array(
            'id'            => 'Admin Menu',
            'parent_id'     => 'Menu cha',
            'display'       => 'Tên hiển thị',
            'icon'          => 'Menu Icon',
            'url'           => 'Menu Url',
            'controller'    => 'Controller',
            'method'        => 'Method',
            'class'         => 'Class',
            'status'        => 'Trạng thái',
            'sort'          => 'Ưu tiên',
        );
        $this->_field_table = Array(
            'm.id'          => 'Menu ID',
            'm.display'     => 'Tên hiển thị',
            'par_display'   => 'Menu Cha',
            'm.url'         => 'Menu URL',
            'm.controller'  => 'Controller',
            'm.method'      => 'Mothod',
            'm.status'      => 'Trạng thái',
            'm.sort'        => 'Ưu tiên',
        );
    }

    public function setting_select()
    {
        $this->db->select("m.*, par.display AS par_display"); // , par.icon AS par_icon, par.url AS par_url, par.controller AS par_controller, par.method AS par_method, par.class AS par_class, par.sort AS par_sort
        $this->db->from($this->_table_name . " AS m");
        $this->db->join('admin_menus AS par', 'par.id = m.parent_id', 'LEFT');
        $this->db->order_by('m.sort', 'ASC');
    }

}