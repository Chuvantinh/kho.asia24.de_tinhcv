<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_nha_cung_cap
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_nha_cung_cap extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'voxy_nha_cung_cap';
        $this->_key_name = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'sku', 'name', 'phone', 'email','no','tong_mua','description', 'status',
            'created_at', 'created_by', 'updated_at', 'updated_by',
        );
        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),
            'sku' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'name' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'phone' => array(
                'type' => 'text',
                'maxlength' => 50,
            ),
            'email' => array(
                'type' => 'text',
                'maxlength' => 50,
            ),
            'no' => array(
                'type' => 'text',
                'maxlength' => 50,
            ),
            'tong_mua' => array(
                'type' => 'text',
                'maxlength' => 50,
            ),
            'description' => array(
                'type' => 'textarena',
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
            'id' => 'ID',
            'sku' => 'Mã nhà cung cấp',
            'name' => 'Tên',
            'phone' => 'Số ĐT',
            'email' => 'Email',
            'no' => 'Nợ',
            'tong_mua' => 'Tổng mua',
            'status' => 'Trạng thái',
            'description' => 'Mô tả',
        );
        $this->_field_table = Array(
            'm.id' => 'ID',
            'm.name' => 'Tên vị trí',
            'm.phone' => 'Số ĐT',
            'm.email' => 'E-mail',
            //'m.no' => 'Nợ cần trả',
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
    public function get_title($id)
    {
        $this->db->select('name');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row)
            {
                return $row->name;
            }
        } else {
            return false;
        }

    }

    public function get_all_title()
    {
        $this->db->select('name');
        $this->db->select('id');
        $this->db->from($this->_table_name . ' AS m');
        $query = $this->db->get();
        if ($query->result_array()) {
                return $query->result_array();
        } else {
            return false;
        }

    }




}