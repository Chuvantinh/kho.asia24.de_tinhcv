<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class M_voxy_users
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_users extends data_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_users';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'user_id', 'user_email', 'first_name', 'phone_number', 'date_joined', 'native_language', 'access_type',

            'date_of_next_vpa', 'tutoring_credits', 'level', 'can_reserve_group_sessions', 'segments', 'feature_group',

            'tutoring_credits_used', 'learning_homework', 'acc_test', 'status',

            'created_by', 'created_at', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array();
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.user_id'         => 'ID Học viên',
            'm.user_email'      => 'Email',
            'm.level'           => 'Level',
            'm.date_joined'     => 'Ngày tham gia',
            'm.first_name'      => 'Tên',
        );
    }

    public function setting_select()
    {
        $this->db->select("m.*");
        $this->db->from($this->_table_name . " AS m");
    }

    public function check_user_id_exists($user_id = 0)
    {
        if(!$user_id || ! intval($user_id)){
            return NULL;
        }

        $this->db->select("m.*");
        $this->db->from($this->_table_name . " AS m");
        $this->db->where('m.user_id', $user_id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->first_row();
        } else {
            return false;
        }
    }

}