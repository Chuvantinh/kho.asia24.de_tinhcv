<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_sys_not_controller_config
 *
 * @author chuvantinh1991@gmail.com
 */
class M_sys_not_controller_config extends data_base
{
    var $arr_status = array(
        '1' => 'ACTIVE',
        '0' => 'DEACTIVE'
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'sys_not_controller_config';
        $this->_key_name            = 'id';
        $this->_exist_created_field = TRUE;
        $this->_exist_updated_field = TRUE;
        $this->_exist_deleted_field = FALSE;
        $this->_schema = Array(
            'id', 'controller', 'description', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        );

        $this->_rule = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'controller'    => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
                'unique'        => true
            ),
            'description'   => array(
                'type'          => 'textarea',
                'maxlength'     => 255,
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form = Array(
            'id'            => 'ID',
            'controller'    => 'Controller',
            'description'   => 'Mô tả',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'          => 'ID',
            'm.controller'  => 'Controller',
            'm.description' => 'Mô tả',
            'm.status'      => 'Trạng thái',
            'm.created_at'  => 'Thời gian tạo',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');

        if (isset($this->custom_conds["custom_where"]) && count($this->custom_conds["custom_where"])) {
            $custom_where = $this->custom_conds["custom_where"];

            $this->db->where($custom_where);
        }
        if (isset($this->custom_conds["custom_like"]) && count($this->custom_conds["custom_like"])) {
            $custom_like = $this->custom_conds["custom_like"];

            $this->db->like($custom_like);
        }
    }

}