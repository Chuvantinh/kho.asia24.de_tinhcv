<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class M_location extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'location';
        $this->_key_name = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'name', 'status', 'products', 'description',
            'created_at', 'created_by', 'updated_at', 'updated_by',
        );
        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),
            'name' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'status' => array(
                'type' => 'select',
                'array_list' => $this->arr_status,
                'allow_null' => "true",
            ),
            'created_at' => array(
                'type' => 'number',
                'maxlength' => 11,
            ),
            'created_by' => array(
                'type' => 'number',
                'maxlength' => 11,
            ),
            'updated_at' => array(
                'type' => 'datetime',
            ),
            'updated_by' => array(
                'type' => 'number',
                'maxlength' => 11,
            )
        );
        $this->_field_form = Array(
            'id' => 'Role ID',
            'name' => 'Tên',
            'status' => 'Trạng thái',
            'description' => 'Mô tả',
        );
        $this->_field_table = Array(
            'm.id' => 'ID',
            'm.name' => 'Tên vị trí',
            'm.gruppe' => 'Nhóm',
            'm.description' => 'Mô tả',
            //'m.products' => 'Sản Phẩm',
            'm.status' => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        //$this->db->order_by('m.id', 'DESC');
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

//    public function update_location($id,$gruppe){
//        $data = array(
//            'gruppe' => $gruppe,
//        );
//
//        $this->db->where('id', $id);
//        $this->db->update($this->_table_name, $data);
//    }

}