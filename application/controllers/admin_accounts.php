<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Admin_accounts
 *
 * @author chuvantinh1991@gmail.com
 */
class Admin_accounts extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "admin_accounts",
            "view"      => "admin_accounts",
            "model"     => "m_admin_accounts",
            "object"    => "Tài khoản"
        );
    }

    protected function _validate_form_data($data, $id = 0)
    {
        if ($id && ($data["password"] != $data["_password"])) {
            if (!isset($data["_password"]) || strlen($data["_password"]) == 0) {
                unset($data["password"]);
                unset($data["_password"]);
            } else {
                $data_return = Array();
                $data_return["state"]               = FALSE; /* state = 0 : dữ liệu không hợp lệ */
                $data_return["error"]["_password"]  = "Mật khẩu nhập lại không chính xác !";
                return $data_return;
            }
        }
        return parent::_validate_form_data($data, $id);
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

        // lay role ID cua User de set role
        $role_id = 0;
        if(isset($data['role_id'])){
            $role_id = (intval($data['role_id']) ? intval($data['role_id']) : 0);
            unset($data['role_id']);
        }

        if ($re_validate) {
            $data_all = $this->_validate_form_data($data);
            if (!$data_all["state"]) {
                $data_return["data"]    = $data;
                $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
                $data_return["msg"]     = "Dữ liệu gửi lên không hợp lệ";
                $data_return["error"]   = $data_all["error"];
                echo json_encode($data_return);
                return FALSE;
            } else {
                $data = $data_all["data"];
            }
        }

        $insert_id = $this->data->add($data);
        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            $this->update_user_role($insert_id, $role_id); // update role

            $data_return["key_name"]    = $this->data->get_key_name();
            $data_return["record"]      = $data;
            $data_return["state"]       = 1; /* state = 1 : insert thành công */
            $data_return["msg"]         = "Thêm bản ghi thành công";
            $data_return["redirect"]    = isset($data_return['redirect']) ? $data_return['redirect'] : "";
            echo json_encode($data_return);
            return $insert_id;
        } else {
            $data_return["state"]   = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"]     = "Thêm bản ghi thất bại, vui lòng thử lại sau";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    public function edit($id = 0, $data = array())
    {
        $data['readonly_user_name'] = true;

        parent::edit($id, $data);
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
        $data_return["callback"] = "save_form_edit_response";
        $id = intval($id);
        if (!$id) {
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Bản ghi không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (!$this->data->is_editable($id)) {
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Bản ghi không thể sửa đổi hoặc bản ghi không còn tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        // lay role ID cua User de set role
        $role_id = 0;
        if(isset($data['role_id'])){
            $role_id = (intval($data['role_id']) ? intval($data['role_id']) : 0);
            unset($data['role_id']);
        }

        if ($re_validate) {
            $data_all = $this->_validate_form_data($data, $id);
            if (!$data_all["state"]) {
                $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
                $data_return["msg"]     = "Dữ liệu gửi lên không hợp lệ !";
                $data_return["error"]   = $data_all["error"];
                echo json_encode($data_return);
                return FALSE;
            } else {
                $data = $data_all["data"];
            }
        }

        $update = $this->data->update($id, $data);
        if ($update) {
            $this->update_user_role($id, $role_id); // update role

            $data_return["key_name"]    = $this->data->get_key_name();
            $data_return["record"]      = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"]       = 1; /* state = 1 : insert thành công */
            $data_return["msg"]         = "Sửa bản ghi thành công !";
            $data_return["redirect"]    = isset($data_return['redirect']) ? $data_return['redirect'] : "";
            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"]   = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"]     = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    private function update_user_role($user_id = 0, $role_id = 0){
        $return_data = false;
        if(!$user_id || !$role_id){
            return $return_data;
        }
        $this->load->model('m_admin_user_role', 'user_role');
        $list_user_role = $this->user_role->get_list(array('m.user_id' => $user_id));
        try{
            $this->db->trans_begin();
            $_user_role = false; // da tung ton tai user_role hay chua
            if($list_user_role && is_array($list_user_role)){
                foreach ($list_user_role as $one_user_role){
                    if($one_user_role->role_id == $role_id){
                        $_user_role = true;
                    } else {
                        $this->user_role->delete_by_id($one_user_role->id);
                    }
                }
            }
            if($_user_role){
                $this->db->trans_commit();
            } else {
                if($user_role_id = $this->user_role->add(array('user_id' => $user_id, 'role_id' => $role_id))){
                    $return_data = $user_role_id;
                    $this->db->trans_commit();
                } else {
                    $this->db->trans_rollback();
                }
            }
        } catch (Exception $e){
            $this->db->trans_rollback();
        }
        return $return_data;
    }

    /**
     * @param array $data
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function ajax_list_data($data = array())
    {
        parent::ajax_list_data($data);
    }
}