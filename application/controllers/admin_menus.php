<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Admin_menus
 *
 * @author chuvantinh1991@gmail.com
 */
class Admin_menus extends manager_base
{

    public function __construct() {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "admin_menus",
            "view"      => "admin_menus",
            "model"     => "m_admin_menus",
            "object"    => "Menu"
        );
    }

    public function ajax_list_data($data = array())
    {
        parent::ajax_list_data($data);
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
        if (sizeof($data) == 0)
        {
            $data = $this->input->post();
        }
        $id = intval($id);
        if($id && $id == $data['parent_id'])
        {
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Không thể chọn CHÍNH Menu làm Menu cha được !";
            echo json_encode($data_return);
            return FALSE;
        }

        parent::edit_save($id, $data, $data_return, $re_validate);
    }
}