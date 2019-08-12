<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_unit_study_history
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_unit_study_history extends data_base
{
    var $arr_status = array(
        'COMPLETE'      => 'COMPLETE',
        'INCOMPLETE'    => 'INCOMPLETE',
        'NOCOMPLETE'    => 'NOCOMPLETE',
    );
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_unit_study_history';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'user_id', 'unit_id', 'status', 'update_time',

            'created_at', 'created_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'user_id'       => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'required'      => 'required',
                'disabled'      => 'disabled'
            ),
            'unit_id'       => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'required'      => 'required',
                'disabled'      => 'disabled'
            ),
            'status'        => array(
                'type'          => 'select', // text - 50
                'array_list'    => $this->arr_status,
                'required'      => 'required',
                'disabled'      => 'disabled'
            ),
            'update_time'   => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'required'      => 'required',
                'disabled'      => 'disabled'
            ),
            'created_at'    => array(
                'type'          => 'number',
                'maxlength'     => 11
            ),
            'created_by'    => array(
                'type'          => 'number',
                'maxlength'     => 11
            )
        );
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.user_id'         => 'Voxy ID',
            'vuser.user_email'  => 'User Email',
            'vuser.first_name'  => 'Tên học viên',
            'vuser.level'       => 'Level',
            'unit_name'         => 'Tên Unit',
            'm.status'          => 'Trạng thái',
            'm.update_time'     => 'Thời gian cập nhật',
            'm.created_at'      => 'Ngày lấy lịch sử'
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*
            , vunit.name AS unit_name
            , vuser.user_email, vuser.first_name, vuser.level, vuser.phone_number, vuser.acc_test, vuser.date_joined
        ');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_units AS vunit', 'vunit.id = m.unit_id');
        $this->db->join('voxy_users AS vuser', 'vuser.user_id = m.user_id');
    }
}