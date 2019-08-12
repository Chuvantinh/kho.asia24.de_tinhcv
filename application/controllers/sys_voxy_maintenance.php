<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Sys_voxy_maintenance
 *
 * @author chuvantinh1991@gmail.com
 */
class Sys_voxy_maintenance extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "sys_voxy_maintenance",
            "view"      => "sys_voxy_maintenance",
            "model"     => "m_sys_voxy_maintenance",
            "object"    => "Bảo trì hệ thống"
        );
    }

    /**
     * Hàm gọi view hiển thị form <b>thêm</b> bản ghi
     * @param Array $data Biến muốn gửi thêm để hiển thị ra view(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @return action trả dữ liệu về phía client (json nếu là ajax, html nếu ko)
     */
    public function add($data = array()) {
        $field_form = $this->data->get_field_form();
        unset($field_form['system_id']);
        $this->data->set_field_form($field_form);
        $this->load->model("m_sys_system_config", 'sys_system');
        $data["list_system"] = $this->sys_system->get_list(array("m.status" => 1));
        parent::add($data);
    }

    /**
     * Hàm xử lý lưu trữ bản ghi mới
     * @param Array $data Biến muốn gửi thêm để <b>hiển thị ra view</b>(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @param Array $data_return Biến muốn gửi thêm <b>vào kết quả trả về</b>(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @param boolean $re_validate Có cần validate lại dữ liệu hay không?
     * @return action trả dữ liệu về phía client (json nếu là ajax, html nếu ko)
     */
    public function add_save($data = Array(), $data_return = Array(), $re_validate = true)
    {
        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if(isset($data['time_start']) && isset($data['time_end']) &&
            !empty($data['time_start']) && !empty($data['time_end']) &&
            (strtotime($data['time_start']) >= strtotime($data['time_end']))
        ){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Ngày kết thúc cần lớn hơn ngày bắt đầu !";
            echo json_encode($data_return);
            return FALSE;
        }

        if(!(isset($data['system_id']) && $data['system_id'])){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Yêu cầu lựa chọn Hệ thống cần bảo trì !";
            echo json_encode($data_return);
            return FALSE;
        }

        foreach ($data['system_id'] as $one_system_id) {
            $insert = $data;
            $insert['system_id'] = $one_system_id;
            $insert_data [] = $insert;
        }

        $num_affrow = $this->data->add_muti($insert_data);

        $data[$this->data->get_key_name()] = 0;
        if ($num_affrow) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data;
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Thêm bản ghi thành công";
            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";
            echo json_encode($data_return);
            return $num_affrow;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    /**
     * Hàm xử lý lưu trữ bản ghi mới
     * Trong cơ sở dữ liệu có trường 'is_editable' = 0 thì sẽ ko chỉnh sửa được
     * @param int $id id của bản ghi cần sửa
     * @param Array $data Biến muốn gửi thêm để <b>hiển thị ra view</b>(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @param Array $data_return Biến muốn gửi thêm <b>vào kết quả trả về</b>(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @param boolean $re_validate Có cần validate lại dữ liệu hay không?
     * @return json trả dữ liệu về phía client JSON
     */
    public function edit_save($id = 0, $data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if(isset($data['time_start']) && isset($data['time_end']) &&
            !empty($data['time_start']) && !empty($data['time_end']) &&
            (strtotime($data['time_start']) >= strtotime($data['time_end']))
        ){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Ngày kết thúc cần lớn hơn ngày bắt đầu !";
            echo json_encode($data_return);
            return FALSE;
        }

        parent::edit_save($id, $data, $data_return, $re_validate);
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
        $key_table  = $this->data->get_key_name();
        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            $record->custom_action = '<div class="action"><a class="detail e_ajax_link icon16 i-eye-3 " per="1" href="' . site_url($this->url["view"] . $record->$key_table) . '" title="Xem"></a>';
            if (!isset($record->editable) || (isset($record->editable) && $record->editable)) {
                $record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
                $record->custom_action .= '<a class="delete e_ajax_confirm e_ajax_link icon16 i-remove" per="1" href="' . site_url($this->url["delete"] . $record->$key_table) . '" title="Xóa"></a></div>';
            }
            $record->custom_check   = "<input type='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            if(isset($record->status) && isset($this->data->arr_status)){
                $record->status     = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }
            if(isset($record->created_at) && intval($record->created_at)){
                $record->created_at = date('d-m-Y H:i:s', intval($record->created_at));
            }
        }
        return $record;
    }
}