<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Sys_level_mapping
 *
 * @author chuvantinh1991@gmail.com
 */
class Sys_level_mapping extends manager_base
{

    public function __construct() {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "sys_level_mapping",
            "view"      => "sys_level_mapping",
            "model"     => "m_sys_level_mapping",
            "object"    => "Mapping Voxy Native level"
        );
    }

    public function ajax_list_data($data = array())
    {
        parent::ajax_list_data($data);
    }

    /**
     * ham tuy bien du lieu khi hien thi ra Admin
     * @param array|Object $record
     * @return array|Object
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $form               = $this->data->get_form();
        $key_table          = $this->data->get_key_name();
        $list_voxy_level    = $this->data->get_list_voxy_level();
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
                if(isset($record->status) && isset($this->data->arr_status)){
                    $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
                }
                if(isset($record->voxy_level) && is_array($list_voxy_level)){
                    $record->voxy_level = (isset($list_voxy_level[$record->voxy_level]) ? $list_voxy_level[$record->voxy_level] : $record->voxy_level);
                }
            }
        }
        return $record;
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
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if(isset($data['native_level'])){
            $data['native_level']   = strtolower(trim($data['native_level']));
        }
        if(isset($data['voxy_level'])){
            $data['voxy_level']     = intval($data['voxy_level']);
        }
        if(isset($data['voxy_level']) && isset($data['native_level'])){
            if($this->data->check_exist_mapping($data['voxy_level'], $data['native_level'])){
                $data_return["state"]   = 0;
                $data_return["msg"]     = "Mapping đã tồn tại, bạn vui lòng kiểm tra lại !";
                echo json_encode($data_return);
                return FALSE;
            }
        }

        parent::add_save($data, $data_return, $re_validate);
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
        $id = intval($id);
        if(isset($data['native_level'])){
            $data['native_level']   = strtolower(trim($data['native_level']));
        }
        if(isset($data['voxy_level'])){
            $data['voxy_level']     = intval($data['voxy_level']);
        }
        if(isset($data['voxy_level']) && isset($data['native_level'])){
            if($this->data->check_exist_mapping($data['voxy_level'], $data['native_level'], $id)){
                $data_return["state"]   = 0;
                $data_return["msg"]     = "Mapping đã tồn tại, bạn vui lòng kiểm tra lại !";
                echo json_encode($data_return);
                return FALSE;
            }
        }
        parent::edit_save($id, $data, $data_return, $re_validate);
    }


}