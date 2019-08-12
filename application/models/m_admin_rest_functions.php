<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_admin_rest_functions
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_rest_functions extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_rest_functions';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = true;
        $this->_schema      = Array(
            'id', 'name', 'controller', 'action', 'status', 'description', 'editable',

            'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at'
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
            'controller'    => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
            ),
            'action'        => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
            ),
            'description'   => array(
                'type'          => 'textarea',
                'maxlength'     => 500,
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form  = Array(
            'id'            => 'Function ID',
            'name'          => 'Tên',
            'controller'    => 'Controller',
            'action'        => 'Action',
            'description'   => 'Mô tả',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'          => 'Function ID',
            'm.name'        => 'Tên',
            'm.controller'  => 'Controller',
            'm.action'      => 'Action',
            'm.description' => 'Mô tả',
            'm.status'      => 'Trạng thái',
            'm.created_at'  => 'Thời gian tạo'
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL);
    }

    /**
     * @param $controller
     * @param $method
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_exist_by_controller_action($controller, $method)
    {
        return $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->where('m.controller', $controller) // function chua suspend
            ->where('m.action', $method) // function chua suspend
            ->get()
            ->num_rows();
    }
}