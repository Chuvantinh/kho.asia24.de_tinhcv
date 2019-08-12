<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_shippers extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'dongxuan_shippers';
        $this->_key_name = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
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
            'm.email' => 'email',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
       // $this->db->order_by('m.id', 'DESC');
    }

    public function check_title($name)
    {
        $this->db->select('name');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('name', $name);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            return true;
        } else {
            return false;
        }

    }

    public function get_id($name)
    {
        $this->db->select('id');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('first_name', $name);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result_array() as $row){
                return $row['id'];
            }
        } else {
            return false;
        }

    }

    public function get_name($id)
    {
        $this->db->select('first_name');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result_array() as $row){
                return $row['first_name'];
            }
        } else {
            return false;
        }

    }

    public function get_order_profit($order_number){
        $this->db->select('order_profit');
        $this->db->from('dongxuan_orders AS m');
        $this->db->where('local_order_id', $order_number);
        $this->db->where('order_status', 'completed');
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result_array() as $row){
                return $row['order_profit'];
            }
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