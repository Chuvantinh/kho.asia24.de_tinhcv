<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 06/14/2018
 * Time: 15:19
 *
 * @author chuvantinh1991@gmail.com
 */

class admin_tool_logs extends manager_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "admin_tool_logs",
            "view"      => "admin_tool_logs",
            "model"     => "m_admin_tool_logs",
            "object"    => "Admin Tools Logs"
        );
    }

    /**
     * @param array $data   Mang du lieu truyen ra view
     *
     * @author chuvantinh1991@gmail.com
     */
    public function manager($data = array())
    {
        $json_conds = $this->session->userdata('arr_admin_tool_logs_search');
        $json_conds = json_decode($json_conds, TRUE);

        $data['form_conds']             = (array) $json_conds;
        $data['list_group_function']    = Array();
        $data['list_is_error']          = Array(
            'NO'    => 'Không gặp lỗi',
            'YES'   => 'Gặp lỗi'
        );
        $data['list_used_admin']         = Array(
            'ADMIN'     => 'Admin hệ thống',
            'CRONJOB'   => 'Tự động'
        );

        $list_group_function = $this->data->get_list_group_function();
        if($list_group_function && is_array($list_group_function)){
            foreach ($list_group_function as $key => $value){
                $data['list_group_function'][$key] = $value->group_function;
            }
        }

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
        $list_field = array('group_function', 'is_error', 'used_admin');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'group_function':
                        if ($data['group_function'] != '') {
                            $where_data['m.group_function'] = $data['group_function'];
                        }
                        break;
                case 'is_error':
                    if ($data['is_error'] != '') {
                        $where_data['is_error'] = $data['is_error'];
                    }
                    break;
                case 'used_admin':
                    if ($data['used_admin'] != '') {
                        $where_data['used_admin'] = $data['used_admin'];
                    }
                    break;
                }
            }
        }

        $data_return = array(
            'custom_where'  => $where_data,
            'custom_like'   => $like_data
        );

        $this->session->set_userdata('arr_admin_tool_logs_search', json_encode($data_return));
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
            $json_conds = $this->session->userdata('arr_admin_tool_logs_search');
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

    /**
     * Trường xử lý bản ghi trước khi hiển thị ra bảng
     * @param Array|Object $record
     * @return Array|Object
     */
    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $form       = $this->data->get_form();
        $key_table  = $this->data->get_key_name();
        /* Tùy biến dữ liệu các cột */
        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            $record->custom_action = '<div class="action">';
            $record->custom_action .= '<a class="detail e_ajax_link icon16 i-eye-3 " per="1" href="' . site_url($this->url["view"] . $record->$key_table) . '" title="Xem"></a>';
            if (!isset($record->editable) || (isset($record->editable) && $record->editable)) {
                $record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
                $record->custom_action .= '<a class="delete e_ajax_confirm e_ajax_link icon16 i-remove" per="1" href="' . site_url($this->url["delete"] . $record->$key_table) . '" title="Xóa"></a>';
            }
            $record->custom_action .= '</div>';
            $record->custom_check = "<input type='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            foreach ($form["field_table"] as $keyColum => $valueColum) {
                if (isset($form["rule"][$keyColum])) {
                    if (isset($form["rule"][$keyColum]['type']) && $form["rule"][$keyColum]['type'] == 'checkbox') {
                        if ($record->$keyColum) {
                            $record->$keyColum = "<input type='checkbox' name='" . $keyColum . "' disabled='disabled' checked='checked' />";
                        } else {
                            $record->$keyColum = "<input type='checkbox' name='" . $keyColum . "' disabled='disabled' />";
                        }
                    } elseif (isset($form["rule"][$keyColum]['type']) && $form["rule"][$keyColum]['type'] == 'file') {
                        $record->$keyColum = "<div class='center'><img src='" . $record->$keyColum . "' /></div>";
                    } elseif (isset($form["rule"][$keyColum]['type']) && ($form["rule"][$keyColum]['type'] == 'datetime' || $form["rule"][$keyColum]['type'] == 'date')) {
                        $temp = strtotime($record->$keyColum);
                        if ($form["rule"][$keyColum]['type'] == 'datetime') {
                            $record->$keyColum = date("d-m-Y H:i:s", $temp);
                        } else {
                            $record->$keyColum = date("d-m-Y", $temp);
                        }
                    }
                }
            }
            if(isset($record->status) && isset($this->data->arr_status)){
                $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }
            if(isset($record->created_at) && intval($record->created_at)){
                $record->created_at = date('d-m-Y H:i:s', $record->created_at);
            }
            if(isset($record->params) && $record->params != ''){
                $record->params = json_decode($record->params);
                if($record->params && is_object($record->params)){
                    $list_params = '';
                    foreach ($record->params as $key => $value){
                        $list_params .= '<pre>' . $key .': ' . $value . '</pre>';
                    }
                    $record->params = $list_params;
                }
            }
            if(isset($record->curl_info) && $record->curl_info != ''){
                $record->curl_info = json_decode($record->curl_info);
                if($record->curl_info && is_object($record->curl_info)){
                    $list_curl_info = '';
                    foreach ($record->curl_info as $key => $value){
                        $list_curl_info .= '<pre>' . $key .': ' . (is_string($value) ? $value : json_encode($value)) . '</pre>';
                    }
                    $record->curl_info = $list_curl_info;
                }
            }
            if(isset($record->response) && $record->response != ''){
                $record->response = json_decode($record->response);
                if($record->response && is_object($record->response)){
                    $list_ajax_return = '';
                    foreach ($record->response as $key => $value){
                        $list_ajax_return .= '<pre>' . $key .': ' . (is_string($value) ? $value : json_encode($value)) . '</pre>';
                    }
                    $record->response = $list_ajax_return;
                }
            }
        }
        return $record;
    }

}