<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_unit_study_history_last_update
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_unit_study_history_last_update extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_unit_study_history_last_update';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'invoice_id', 'use_time', 'start_time', 'end_time', 'status_code', 'status',

            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'invoice_id'    => array(
                'type'          => 'number',
                'required'      => 'required',
                'maxlength'     => 11,
                'disabled'      => 'disabled'
            ),
            'use_time'      => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'required'      => 'required',
                'disabled'      => 'disabled'
            ),
            'start_time'    => array(
                'type'              => 'number',
                'maxlength'         => 11,
                'disabled'          => 'disabled'
            ),
            'end_time'      => array(
                'type'              => 'number',
                'maxlength'         => 11,
                'disabled'          => 'disabled'
            ),
            'status_code'   => array(
                'type'              => 'text',
                'maxlength'         => 30,
                'required'          => 'required',
                'disabled'          => 'disabled'
            ),
            'status'        => array(
                'type'              => 'text',
                'maxlength'         => 20,
                'required'          => 'required',
                'disabled'          => 'disabled'
            ),
        );
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'                  => 'ID',
            'vid.cat_code'          => 'Loại Voxy',
            'vid.package_code'      => 'Mã gói',
            'vid.native_parent'     => 'Gói cha Native',
            'vid.package_type'      => 'Loại gói Native',
            'm.start_time'          => 'Ngày kích hoạt',
            'm.end_time'            => 'Ngày hết hạn',
            'm.use_time'            => 'Số ngày sử dụng',
            'm.status'              => 'Trạng thái gói',
            'm.created_at'          => 'Ngày mua gói',
        );
    }

    public function setting_select()
    {
        $this->db->select('
            m.*
            , vi.invoice_code, vi.invoice_price, vi.invoice_package_price, vi.invoice_description, vi.invoice_status
            , vid.cat_code, vid.package_code, vid.package_type, vid.native_parent
            , vu.user_id, vu.user_email, vu.first_name, vu.phone_number, vu.date_joined'
        );
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_invoice AS vi', 'vi.id = m.invoice_id');
        $this->db->join('voxy_invoice_detail AS vid', 'vid.invoice_code = vi.invoice_code');
        $this->db->join('voxy_users AS vu', 'vu.user_id = vi.user_id');

    }

    public function is_user_unit_last_updated($user_id = 0, $unit_id = 0, $unit_last_update_time = 0)
    {
        return false;
    }

}