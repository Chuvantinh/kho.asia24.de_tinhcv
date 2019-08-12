<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/***
 * Class Admin_rest_access
 *
 * @author chuvantinh1991@gmail.com
 */
class Admin_rest_access extends manager_base
{

    public function __construct() {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "admin_rest_access",
            "view"      => "admin_rest_access",
            "model"     => "m_admin_rest_access",
            "object"    => "Rest Access"
        );
    }

    public function ajax_list_data($data = array())
    {
        parent::ajax_list_data($data);
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

        parent::edit_save($id, $data, $data_return, $re_validate);
    }
}