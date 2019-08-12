<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package extends data_base
{
    var $arr_package_type = array(
        '0'     => '- Lựa chọn giá trị -',
        'TT'    => 'TT - Thỏa thích',
        'TC'    => 'TC - Tùy chọn vô hạn',
        'TCL'   => 'TCL - Tùy chọn có hạn',
    );
    var $arr_native_package_parent = array(
        '0'         => '- Lựa chọn giá trị -',
        'NATIVE'    => 'TOPICA NATIVE',
    );
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_package';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'cat_id', 'pack_code', 'pack_name', 'pack_use_time', 'pack_cost', 'native_parent', 'pack_type', 'description', 'status',

            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'cat_id'        => array(
                'type'              => 'select',
                'target_model'      => 'm_voxy_category',
                'target_value'      => 'id',
                'target_display'    => 'title',
                'where_condition'   => array(
                    'm.status'          => 1,
                ),
            ),
            'native_parent'          => array(
                'type'          => 'select',
                'array_list'    => $this->arr_native_package_parent,
                'required'      => 'required',
                'allow_null'    => "false",
            ),
            'pack_code'     => array(
                'type'          => 'text',
                'maxlength'     => 50,
                'required'      => 'required',
                'unique'        => true
            ),
            'pack_type'     => array(
                'type'          => 'select',
                'array_list'    => $this->arr_package_type,
                'required'      => 'required',
                'allow_null'    => "false",
            ),
            'pack_name'     => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
            ),
            'pack_use_time' => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'required'      => 'required',
            ),
            'pack_cost'     => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'required'      => 'required',
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
        $this->_field_form  = Array(
            'id'            => 'Package ID',
            'cat_id'        => 'Category',
            'native_parent' => 'Native Parent Code',
            'pack_type'     => 'Loại gói',
            'pack_code'     => 'Package Code',
            'pack_name'     => 'Tên gói',
            'pack_use_time' => 'Thời gian học',
            'pack_cost'     => 'Giá gói',
            'description'   => 'Mô tả',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'              => 'Package ID',
            'cat_code'          => 'Parent',
            'cat_status'        => 'Parent Status',
            'm.pack_code'       => 'Package Code',
            'm.pack_name'       => 'Tên gói',
            'm.pack_use_time'   => 'Thời gian học',
            'm.pack_cost'       => 'Giá gói',
            'm.native_parent'   => 'Native Parent Code',
            'm.pack_type'       => 'Loại gói',
            'm.description'     => 'Mô tả',
            'm.status'          => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*, cat.status AS cat_status, cat.cat_id AS cat_id, cat.title AS cat_title');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_category AS cat', 'cat.id = m.cat_id');

        if (isset($this->custom_conds["custom_where"]) && count($this->custom_conds["custom_where"]) > 0) {
            $custom_where = $this->custom_conds["custom_where"];
            $this->db->where($custom_where);
        }
        if (isset($this->custom_conds["custom_like"]) && count($this->custom_conds["custom_like"]) > 0) {
            $custom_like = $this->custom_conds["custom_like"];
            $this->db->like($custom_like);
        }
    }

    /**
     * Ham kiem tra su ton tai cua 1 package code
     * @param string $pack_code
     * @param int $id
     * @return bool|null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_exist_pack_code($pack_code = '', $id = 0){
        if(!((is_string($pack_code) && trim($pack_code) != '') || (intval($pack_code) && $pack_code))){
            return NULL;
        }

        $pack_code = trim($pack_code);
        $this->setting_select();
        $this->db->where('m.pack_code', $pack_code);
        if($id){
            $this->db->where('m.id !=', $id);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->first_row();
        } else {
            return false;
        }
    }
}