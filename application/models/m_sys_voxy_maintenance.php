<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_sys_voxy_maintenance
 *
 * @author chuvantinh1991@gmail.com
 */
class M_sys_voxy_maintenance extends data_base
{
    var $arr_status = array(
        1 => 'MAINTENANCE',
        0 => 'DEACTIVE',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'sys_voxy_maintenance';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'system_id', 'time_start', 'time_end', 'description', 'status',

            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'system_id'        => array(
                'type'              => 'select',
                'target_model'      => 'm_sys_system_config',
                'target_value'      => 'id',
                'target_display'    => 'name',
                'where_condition'   => array(
                    'm.status'          => 1,
                ),
            ),
            'time_start' => array(
                'type'          => 'datetimepicker',
                'required'      => 'required',
                'readonly'      => 'true'
            ),
            'time_end' => array(
                'type'          => 'datetimepicker',
                'required'      => 'required',
                'readonly'      => 'true'
            ),
            'description' => array(
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
            'id'            => 'ID',
            'system_id'     => 'Hệ thống',
            'time_start'    => 'Bắt đầu',
            'time_end'      => 'Kết thúc',
            'description'   => 'Mô tả',
            'status'        => 'Trạng thái',
        );
        $this->_field_table = Array(
            'm.id'          => 'ID',
            'system_name'   => 'Tên hệ thống',
            'system_domain' => 'Domain hệ thống',
            'm.time_start'  => 'Bắt đầu',
            'm.time_end'    => 'Kết thúc',
            'm.status'      => 'Trạng thái',
            'm.description' => 'Lý do bảo trì',
            'm.created_at'  => 'Thời gian tạo',
            'au.user_name'  => 'Admin tạo',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*, sys.name AS system_name, sys.domain AS system_domain, au.user_name, au.full_name');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('sys_system_table AS sys' ,'sys.id = m.system_id');
        $this->db->join('admin_users AS au' ,'au.id = m.created_by', 'left');
    }

    /**
     * Ham lay cau hinh bao tri
     *
     * @param string    $system: domain cua he thong can bao tri
     * @return bool     false: khong bao tri va nguoc lai
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_status_maintenance($system = ''){
        $date_now = date('Y-m-d H:i:s', time());
        $this->setting_select();
        $this->db->where('m.status', 1);
        $this->db->where('m.time_start <='  , $date_now);
        $this->db->where('m.time_end >='    , $date_now);
        if($system){
            $this->db->where('sys.domain'   , $system);
        }
        $this->db->order_by('m.time_end', 'ASC');
        try {
            $query = $this->db->get();
            if ($query->num_rows()) {
                return $query->first_row();
            }
        } catch (Exception $ex){

        }
        return false;
    }

}