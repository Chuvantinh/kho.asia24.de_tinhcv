<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class m_admin_roles
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_roles extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_roles';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'name', 'description', 'status', 'editable',

            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'name'          => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
                'unique'        => true
            ),
            'description'   => array(
                'type'          => 'textarea',
                'required'      => 'required',
                'allow_null'        => false
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form  = Array(
            'id'            => 'Role ID',
            'name'          => 'Tên nhóm quyền',
            'description'   => 'Mô tả',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'          => 'Role ID',
            'm.name'        => 'Nhóm quyền',
            'm.description' => 'Mô tả',
            'm.status'      => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
    }

}