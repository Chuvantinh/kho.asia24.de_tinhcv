<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Sys_system_config
 *
 * @author chuvantinh1991@gmail.com
 */
class Sys_system_config extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "sys_system_config",
            "view"      => "sys_system_config",
            "model"     => "m_sys_system_config",
            "object"    => "System"
        );
    }

    public function add_save($data = Array(), $data_return = Array(), $re_validate = true)
    {
        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if(isset($data['domain'])){
            $this->load->library('ThuyVu_lib');
            $data['domain'] = trim($data['domain']);
            $data['domain'] = $this->thuyvu_lib->StripUnicode($data['domain']);
            $data['domain'] = strtolower($data['domain']);
            $data['domain'] = $this->thuyvu_lib->cut_short_domain($data['domain']);
            if($this->data->check_exist_domain($data['domain'])){
                $data_return["state"]   = 0;
                $data_return["msg"]     = "Domain hệ thống đã tồn tại !";
                echo json_encode($data_return);
                return FALSE;
            }
        }

        parent::add_save($data, $data_return, $re_validate);
    }

    /**
     * Chan ko cho pheo sua ban ghi
     * @param int $id
     * @param array $data
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    public function edit($id = 0, $data = Array())
    {
        $data['readonly_domain']    = true;
        $data['readonly_weight_id'] = true;

        parent::edit($id, $data);
    }

    /**
     * Chan khong cho phep xoa ban ghi
     * @param int $id
     * @param array $data
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    public function delete($id = 0, $data = Array())
    {
        $data_return["callback"]    = "delete_respone";
        $data_return["state"]       = 0;
        $data_return["msg"]         = "Bản ghi không thể xóa hoặc sửa đổi !";
        echo json_encode($data_return);
        return FALSE;
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
        $dataReturn["colum"]["custom_action"] = "Action";
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
                // $record->custom_action .= '<a class="delete e_ajax_confirm e_ajax_link icon16 i-remove" per="1" href="' . site_url($this->url["delete"] . $record->$key_table) . '" title="Xóa"></a>';
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
        }
        return $record;
    }
}