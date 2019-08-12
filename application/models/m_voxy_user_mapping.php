<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model mapping giua he thong va user
 * Class M_voxy_user_mapping
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_user_mapping extends data_base
{
    var $_diff_weight = 10000000; // 10 trieu

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_user_mapping';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'weight_id', 'vu_id', 'student_id', 'student_email', 'contact_id',

            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array();
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.contact_id'      => 'Contact ID',
            'm.student_id'      => 'Native User ID',
            'vu_user_id'        => 'Voxy User ID',
            'm.student_email'   => 'Native User Email',
            'vu_user_email'     => 'Voxy User Email',
            'vu_first_name'     => 'Voxy User Name',
            'vu_date_joined'    => 'Voxy ngày tham gia',
            'swt_name'          => 'Weight Name',
            'swt_weight'        => 'Cân nặng',
            'swt_connect'       => 'Chuỗi nối',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*
            , swt.name AS swt_name, swt.weight AS swt_weight, swt.connect AS swt_connect
            , vu.user_id AS vu_user_id, vu.user_email AS vu_user_email, vu.first_name AS vu_first_name, vu.date_joined AS vu_date_joined
        ');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('sys_weight_table AS swt', 'swt.id = m.weight_id');
        $this->db->join('voxy_users AS vu', 'vu.id = m.vu_id');
        $this->db->order_by('m.id', 'DESC');
    }
}