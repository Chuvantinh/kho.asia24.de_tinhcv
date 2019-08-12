<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_used_package
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_used_package extends data_base
{
    var $arr_package_status = Array(
        'ACTIVED'   => 'ACTIVED - Đã kích hoạt',
        'DEACTIVED' => 'DEACTIVED - Chưa kích hoạt',
        'EXPIRED'   => 'EXPIRED - Đã hết hạn',
    );
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_used_package';
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
            'user_id'               => 'Voxy ID',
            'user_email'            => 'Voxy Email',
            'student_id'            => 'LMS ID',
            'contact_id'            => 'Contact ID',
            'sys_weight_name'       => 'Hệ thống',
            // 'vid.cat_code'          => 'Loại Voxy',
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
            , vu.user_id, vu.user_email, vu.first_name, vu.phone_number, vu.date_joined
            , vum.weight_id, vum.student_id, vum.student_email, vum.contact_id
            , swt.name AS sys_weight_name, swt.weight AS sys_weight_weight, swt.connect AS sys_weight_connect
            '
        );
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_invoice AS vi', 'vi.id = m.invoice_id');
        $this->db->join('voxy_invoice_detail AS vid', 'vid.invoice_code = vi.invoice_code');
        $this->db->join('voxy_users AS vu', 'vu.user_id = vi.user_id');
        $this->db->join('voxy_user_mapping AS vum', 'vum.vu_id = vu.id');
        $this->db->join('sys_weight_table AS swt', 'swt.id = vum.weight_id');

        if (isset($this->custom_conds["custom_where"]) && count($this->custom_conds["custom_where"]) > 0) {
            $custom_where = $this->custom_conds["custom_where"];
            $this->db->where($custom_where);
        }
        if (isset($this->custom_conds["custom_like"]) && count($this->custom_conds["custom_like"]) > 0) {
            $custom_like = $this->custom_conds["custom_like"];
            $this->db->like($custom_like);
        }

    }

    public function is_editable($where) {
        return false;
    }

    public function delete_by_id($where) {
        return false;
    }

    /** HoangND2
     * Hàm lấy log voxy invoice
     * @param $time_from => bắt đầu lấy log từ thời điểm nào
     * @param $weight_id => xác định hệ thống nào cần lấy log
     * @return array
     */
    public function select_by_time_range($time_from, $weight_id = 0) {
        if($this->validateDate($time_from)) {
            $this->db->select('
                vup.invoice_id, 
                vup.status, 
                vup.start_time, 
                vup.end_time,
                vup.updated_at
            ');
            $this->db->from('voxy_used_package as vup');
            $this->db->join('voxy_invoice as vi', 'vup.invoice_id = vi.id');
            $this->db->join('voxy_users as vu', 'vi.user_id = vu.user_id');
            $this->db->join('voxy_user_mapping as vum', 'vu.id = vum.vu_id');
            $this->db->where('vup.updated_at >=' , $time_from);
            $this->db->where('vum.weight_id' , $weight_id);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }


    }

    /** HoangND2
     * Hàm check ngày tháng truyền vào có đúng định dạng hay không, chôm trên mạng về chứ k phải tự viết đâu :)
     * @param $date
     * @param string $format
     * @return bool
     */
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

}