<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_chamsockhachhang extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'dongxuan_users';
        $this->_key_name = 'id';
        //$this->_exist_created_field = true;
        //$this->_exist_updated_field = true;
        //$this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'first_name', 'last_name', 'email', 'password','phone','address',
        );
        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),
            'first_name' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'last_name' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
        );
        $this->_field_form = Array(
            'id' => 'Role ID',
            'first_name' => 'Tên',
            'last_name' => 'Họ',
        );
        $this->_field_table = Array(
            'm.id' => 'ID',
            'm.first_name' => 'Tên',
            'm.phone' => 'Phone',
            'm.email' => 'Email',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        //$this->db->order_by('m.id', 'DESC');
        $this->db->where('m.is_admin', 0);
    }

    public function check_title($name)
    {
        $this->db->select('name');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('name', $name);
        $query = $this->db->get();
        if ($query->result_array()) {
            return true;
        } else {
            return false;
        }

    }

    public function get_all_location()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->order_by('m.id', 'ASC');
        $query = $this->db->get();
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

}