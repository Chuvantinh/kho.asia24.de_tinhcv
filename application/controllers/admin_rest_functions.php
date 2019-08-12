<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Admin_rest_functions
 *
 * @author chuvantinh1991@gmail.com
 */
class Admin_rest_functions extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "admin_rest_functions",
            "view"      => "admin_rest_functions",
            "model"     => "m_admin_rest_functions",
            "object"    => "Rest Function"
        );
    }

    public function ajax_list_data($data = array())
    {
        parent::ajax_list_data($data);
    }

    protected function manager($data = Array())
    {
        $data['update_link'] = site_url($this->name['class'] . '/update_rest_function');
        parent::manager($data);
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
            }
            $record->custom_action .= '</div>';

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

    /**
     * Ham cap nhat danh sach Function vao bang Rest Function
     *
     * @author chuvantinh1991@gmail.com
     */
    public function update_rest_function()
    {
        $this->load->library('ControllerList');
        $controller_list        = $this->controllerlist->getApiController();
        $list_not_method        = Array(
            '__construct',
            '__destruct',
            '_remap',
            'response',
            'get',
            'options',
            'head',
            'post',
            'put',
            'delete',
            'patch',
            'validation_errors',
            'get_clientname',
            'get_client_domain',
            'get_client_info',
            '_success',
            '_error',
        );

        $arr_data_method = Array();
        foreach($controller_list as $controller_name => $arr_method) {
            foreach($arr_method as $method){
                $name_function = $controller_name . '_' . $method;
                if(in_array($method, $list_not_method) || $this->data->check_exist_by_controller_action($controller_name, $method)) {
                    continue;
                }
                $arr_data_method[] = Array(
                    'name' 			=> $name_function,
                    'controller' 	=> $controller_name,
                    'action' 		=> $method,
                    'description'   => str_replace('_', ' ', str_replace('-', ' ', $name_function)),
                    'status'		=> 1,
                );
            }
        }

        if($arr_data_method){
            $this->data->add_muti($arr_data_method);
        }

        redirect($this->name['class']);
    }

}