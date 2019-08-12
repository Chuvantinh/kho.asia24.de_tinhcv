<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Sys_weight_config
 *
 * @author chuvantinh1991@gmail.com
 */
class Sys_weight_config extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "sys_weight_config",
            "view"      => "sys_weight_config",
            "model"     => "m_sys_weight_config",
            "object"    => "System Weight"
        );
    }

    public function add_save($data = Array(), $data_return = Array(), $re_validate = true)
    {
        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if(isset($data['weight']) && !$this->data->check_weight($data['weight'])){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Weight đã tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        if(isset($data['connect']) && !$this->data->check_connect($data['connect'])){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Chuỗi nối đã tồn tại !";
            echo json_encode($data_return);
            return FALSE;
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
        $data_return["callback"]    = "get_form_edit_response";
        $data_return["state"]       = 0;
        $data_return["msg"]         = "Bản ghi không thể xóa hoặc sửa đổi !";
        echo json_encode($data_return);
        return FALSE;
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
}