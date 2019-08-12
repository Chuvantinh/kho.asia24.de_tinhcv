<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/***
 * Class M_admin_rest_access
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_rest_access extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_rest_access';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'key_id', 'controller', 'method',

            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        );

        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'key_id'        => array(
                'type'              => 'select',
                'allow_null'        => false,
                'target_model'      => 'm_admin_rest_keys',
                'target_value'      => 'id',
                'target_display'    => 'key',
                'where_condition'   => array(
                    'm.status'          => 1,
                ),
            ),
            'controller'    => array(
                'type'          => 'text',
                'maxlength'     => 50,
                'required'      => 'required',
            ),
            'method'        => array(
                'type'          => 'textarea',
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            ),

        );
        $this->_field_form  = Array(
            'id'            => 'ID',
            'key_id'        => 'Key',
            'controller'    => 'Controller',
            'method'        => 'List Method',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'rkey_key'           => 'Key',
            'm.controller'      => 'Controller',
            'm.method'          => 'Method',
            'm.status'          => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*,rkey.key rkey_key, rkey.level rkey_level, rkey.domain_name rkey_domain, rkey.ip_addresses rkey_ip');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('admin_rest_keys AS rkey', 'rkey.id = m.key_id', 'left');
    }

}