<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_admin_rest_users
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_rest_users extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_rest_users';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'user_name', 'password', 'password_show', 'salt', 'check_sum',

            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'user_name'     => array(
                'type'          => 'text',
                'maxlength'     => 50,
                'required'      => 'required',
                'unique'        => true
            ),
            'password_show'     => array(
                'type'          => 'text',
                'maxlength'     => 50,
                'required'      => 'required',
            ),
            'salt'          => array(
                'type'          => 'text',
                'maxlength'     => 255,
            ),
            'check_sum'     => array(
                'type'          => 'text',
                'maxlength'     => 255,
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form  = Array(
            'id'            => 'ID',
            'user_name'     => 'Username',
            'password_show' => 'Password Show',
            'salt'          => 'Salt',
            'check_sum'     => 'Check Sum',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.user_name'       => 'User name',
            'm.password'        => 'Password',
            'm.password_show'   => 'Password show',
            'm.salt'            => 'Salt',
            'm.check_sum'       => 'Check Sum',
            'm.status'          => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
    }

}