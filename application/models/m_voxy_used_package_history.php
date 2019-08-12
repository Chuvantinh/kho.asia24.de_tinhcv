<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_used_package_history
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_used_package_history extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_used_package_history';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = false;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'used_package_id', 'status_old', 'status_new', 'description',

            'status_code', 'end_time_old', 'end_time_new', 'end_time_plus',

            'created_at', 'created_by'
        );
        $this->_rule        = Array(
            'id'                => array(
                'type'              => 'hidden'
            ),
            'used_package_id'   => array(
                'type'              => 'number',
                'required'          => 'required',
                'maxlength'         => 11,
                'disabled'          => 'disabled'
            ),
            'status_old'        => array(
                'type'              => 'text',
                'maxlength'         => 20,
                'disabled'          => 'disabled'
            ),
            'status_new'        => array(
                'type'              => 'text',
                'maxlength'         => 20,
                'required'          => 'required',
                'disabled'          => 'disabled'
            ),
            'description'       => array(
                'type'              => 'textarea',
                'disabled'          => 'disabled'
            ),
            'status_code'       => array(
                'type'              => 'text',
                'maxlength'         => 30,
                'required'          => 'required',
                'disabled'          => 'disabled'
            ),
            'end_time_old'      => array(
                'type'              => 'number',
                'maxlength'         => 11,
                'required'          => 'required',
                'disabled'          => 'disabled'
            ),
            'end_time_new'      => array(
                'type'              => 'number',
                'maxlength'         => 11,
                'required'          => 'required',
                'disabled'          => 'disabled'
            ),
            'end_time_plus'     => array(
                'type'              => 'number',
                'maxlength'         => 11,
                'disabled'          => 'disabled'
            )
        );
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.used_package_id' => 'Used Package ID',
            'm.status_old'      => 'Trạng thái cũ',
            'm.status_new'      => 'Trạng thái mới',
            'm.description'     => 'Mô tả',
            'm.status_code'     => 'Mã code trạng thái',
            'm.end_time_old'    => 'ngày hết hạn cũ',
            'm.end_time_new'    => 'Ngày hết hạn mới',
            'm.end_time_plus'   => 'Thời gian cộng thêm',

        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_used_package AS vup', 'vup.id = m.used_package_id');
        $this->db->join('voxy_invoice AS vi', 'vi.id = vup.invoice_id');
    }

    public function is_editable($where) {
        return false;
    }

    public function delete_by_id($where) {
        return false;
    }

}