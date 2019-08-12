<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_admin_rest_keys
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_rest_keys extends data_base
{
    var $arr_is_private = array(
        '0' => 'NO',
        '1' => 'YES'
    );
    var $arr_ignore_limits = array(
        '0' => 'NO',
        '1' => 'YES'
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_rest_keys';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = true;
        $this->_schema      = Array(
            'id', 'key', 'level', 'ignore_limits', 'is_private_key', 'domain_name', 'ip_addresses',

            'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'key'     => array(
                'type'          => 'text',
                'maxlength'     => 41,
                'required'      => 'required',
                'unique'        => true
            ),
            'level'         => array(
                'type'          => 'number',
                'maxlength'     => 2,
            ),
            'ignore_limits'          => array(
                'type'          => 'select',
                'array_list'    => $this->arr_ignore_limits,
                'allow_null'    => "true",
            ),
            'is_private_key'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_is_private,
                'allow_null'    => "true",
            ),
            'domain_name'     => array(
                'type'          => 'textarea',
            ),
            'ip_addresses'     => array(
                'type'          => 'textarea',
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            ),

        );
        $this->_field_form  = Array(
            'id'                => 'ID',
            'key'               => 'Key',
            'level'             => 'Level',
            'ignore_limits'     => 'Ignore Limits',
            'is_private_key'    => 'Is Private Key',
            'domain_name'       => 'Domain Access',
            'ip_addresses'      => 'IP Access',
            'status'            => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.key'             => 'Key',
            'm.level'           => 'Level',
            'm.ignore_limits'   => 'Ignore Limits',
            'm.is_private_key'  => 'Is Private Key',
            'm.domain_name'     => 'Domain Access',
            'm.ip_addresses'    => 'IP Access',
            'm.status'          => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL);
    }

}