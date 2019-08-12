<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_used_package
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_used_package extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "voxy_used_package",
            "view"      => "voxy_used_package",
            "model"     => "m_voxy_used_package",
            "object"    => "Used Package"
        );
    }

    /**
     * @param array $data   Mang du lieu truyen ra view
     *
     * @author chuvantinh1991@gmail.com
     */
    public function manager($data = array())
    {
        $this->load->model('m_voxy_package');
        $json_conds = $this->session->userdata('arr_used_package_search');
        $json_conds = json_decode($json_conds, TRUE);

        $data['form_conds']             = (array) $json_conds;
        $data['list_native_parent']     = $this->m_voxy_package->arr_native_package_parent;
        unset($data['list_native_parent'][0]);
        $data['list_pack_type']         = $this->m_voxy_package->arr_package_type;
        unset($data['list_pack_type'][0]);
        $data['list_package_status']    = $this->data->arr_package_status;

        $data['ajax_link_search_voxy_user']  = site_url('admin_search_voxy_user_ajax/search_ajax_voxy_user');

        parent::manager($data);
    }

    /**
     * Ham xu ly thong tin tim kiem
     * @param array $data
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_search_condition($data = array()) {
        if (!count($data)) {
            $data = $this->input->get();
        }

        $where_data = array();
        $like_data  = array();
        $list_field = array('student_id', 'native_parent', 'pack_type', 'package_status', 'from_end_time', 'to_end_time');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'student_id':
                        if ($data['student_id'] != "") {
                            $where_data['student_id'] = $data['student_id'];
                        }
                        break;

                    case 'native_parent':
                        if ($data['native_parent'] != '') {
                            $where_data['vid.native_parent'] = $data['native_parent'];
                        }
                        break;
                    case 'pack_type':
                        if ($data['pack_type'] != '') {
                            $where_data['vid.package_type'] = $data['pack_type'];
                        }
                        break;
                    case 'package_status':
                        if ($data['package_status'] != '') {
                            $where_data['m.status'] = $data['package_status'];
                        }
                        break;
                    case 'from_end_time':
                        if ($data['from_end_time'] != '') {
                            $where_data['m.end_time >='] = strtotime($data['from_end_time']);
                        }
                        break;
                    case 'to_end_time':
                        if ($data['to_end_time'] != '') {
                            $where_data['m.end_time <='] = strtotime($data['to_end_time'] . '23:59:59');
                        }
                        break;
                }
            }
        }

        $data_return = array(
            'custom_where'  => $where_data,
            'custom_like'   => $like_data
        );

        $this->session->set_userdata('arr_used_package_search', json_encode($data_return));
        return $data_return;
    }

    /**
     * Hàm lấy dữ liệu của một danh sách bản ghi
     * Hàm này có cấu trúc nhận dữ liệu POST khá phức tạp bao gồm
     *      - q     => chuỗi tìm kiếm
     *      - limit => Số bản ghi muốn lấy ra
     *      - order => sắp xếp theo thứ tự nào
     *      - page  => trang đang xem
     * Mặc định các biến này được quản lý ở file form.js, chỉ cần quan tâm khi viết đè
     * @param Array $data Biến muốn gửi thêm để hiển thị ra view(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @return json Gửi dữ liệu json về client
     */
    public function ajax_list_data($data = Array())
    {
        $data_get = $this->input->get();
        if($data_get && is_array($data_get)){
            $this->data->custom_conds = $this->get_search_condition($data_get);
        } else {
            $json_conds = $this->session->userdata('arr_used_package_search');
            $json_conds = json_decode($json_conds, TRUE);
            if(count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0){
                $this->data->custom_conds = $this->get_search_condition();
            } else {
                $this->data->custom_conds = $json_conds;
            }
        }
        parent::ajax_list_data($data);
    }

    /**
     * Hàm thêm cột vào bản ghi trước khi đưa ra bảng quản lý
     * Mặc định hàm này sẽ thêm 2 cột là cột chứa 3 nút (thêm, sửa xóa) và cột "input"
     * @param Array $record Mảng chứa các bản ghi
     * @return type
     */
    protected function _add_colum_action($record)
    {
        $form = $this->data->get_form();
        $dataReturn = Array();
        $dataReturn["schema"]   = $form["schema"];
        $dataReturn["rule"]     = $form["rule"];
        $dataReturn["colum"]    = $form["field_table"];

        /* Thêm cột action */
        // $dataReturn["colum"]["custom_action"] = "Action";
        /* Thêm cột check */
        // $dataReturn["colum"]["custom_check"] = "<input type='checkbox' class='e_check_all' />";

        $record = $this->_process_data_table($record);
        $dataReturn["record"] = $record;
        return $dataReturn;
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            $record->custom_action = '<div class="action">';
            $record->custom_action .= '<a class="detail e_ajax_link icon16 i-eye-3 " per="1" href="' . site_url($this->url["view"] . $record->$key_table) . '" title="Xem"></a>';
            if (!isset($record->editable) || (isset($record->editable) && $record->editable)) {
                $record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
            }
            $record->custom_action .= '</div>';

            if(isset($record->status) && isset($this->data->arr_status)){
                $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }
            if(isset($record->created_at) && intval($record->created_at)){
                $record->created_at = date('d-m-Y H:i', intval($record->created_at));
            }
            if(isset($record->start_time) && intval($record->start_time) && intval($record->start_time) > 0){
                $record->start_time = date('d-m-Y', intval($record->start_time));
            }
            if(isset($record->end_time) && intval($record->end_time) && intval($record->end_time) > 0){
                $record->end_time = date('d-m-Y', intval($record->end_time));
            }
        }
        return $record;
    }
}