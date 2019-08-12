<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_category extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_category",
            "view" => "voxy_category",
            "model" => "m_voxy_category",
            "object" => "Danh Mục"
        );
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $form = $this->data->get_form();
        $key_table = $this->data->get_key_name();
        $this->load->model('m_voxy_category_parents');
        /* Tùy biến dữ liệu các cột */
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

            if (isset($record->created_at) && intval($record->created_at)) {
                $record->created_at = date('d-m-Y H:i:s', intval($record->created_at));
            }

            if (isset($record->cat_parents) && isset($record->cat_parents)) {
                $record->cat_parents = $this->m_voxy_category_parents->get_cat_title_parent($record->cat_parents);
            }

            if (isset($record->status) && isset($this->data->arr_status)) {
                $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }

        }
        return $record;
    }

    public function add_save($data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }
        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if (!isset($data['title'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Titel SP  không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        // Check titel category
        $exist_status = $this->data->check_exist_title($data['title']);
        if ($exist_status === NULL) {
            $data_return["state"] = 0;
            $data_return["msg"] = "Yêu cầu nhập thông tin tieu de danh muc !";
            echo json_encode($data_return);
            return FALSE;
        } else if ($exist_status === TRUE) {
            $data_return["state"] = 0;
            $data_return["msg"] = "Tieu de danh muc san pham đã tồn tại, vui lòng lòng nhập mã khác !";
            echo json_encode($data_return);
            return FALSE;
        }

        if ($re_validate) {
            $data_all = $this->_validate_form_data($data);
            if (!$data_all["state"]) {
                $data_return["data"] = $data;
                $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
                $data_return["msg"] = "Dữ liệu gửi lên không hợp lệ";
                $data_return["error"] = $data_all["error"];
                echo json_encode($data_return);
                return FALSE;
            } else {
                $data = $data_all["data"];
            }
        }

        $insert_id = $this->data->add($data);
        $this->data->update_id_category($insert_id,$insert_id);

        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            try {
                $this->load->model('m_voxy_package_history', 'package_history');
                $one_history = $this->data->get_one($insert_id, 'object');
                $data_history = array(
                    'pack_code' => $insert_id,
                    'value_old' => '',
                    'value_new' => json_encode($one_history),
                    'action' => 'add_category'
                );
                $this->package_history->add($data_history);

            } catch (Exception $ex) {
                // chi de tranh anh huong den viec gui thong tin ve nguoi dung
            }

            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data;
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Thêm bản ghi thành công vào cơ sở dữ liệu";
            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            echo json_encode($data_return);
            return $insert_id;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
            echo json_encode($data_return);
            return FALSE;
        }

        parent::add_save($data, $data_return, $re_validate);
    }

    public function edit_save($id = 0, $data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }
        $this->load->model('m_voxy_category', 'category');

        $data_return["callback"] = "save_form_edit_response";
        $id = intval($id);
        if (!$id) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Bản ghi không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        if (!$this->data->is_editable($id)) {
            $data_return["state"] = 0;
            $data_return["msg"] = "Bản ghi không thể sửa đổi hoặc bản ghi không còn tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if (!isset($data['title'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu title Code không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (!isset($data['cat_id'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Category không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        $data['cat_id'] = intval(trim($data['cat_id']));


        // Check titel category
        $exist_status = $this->data->check_exist_title($data['title']);
        if ($exist_status === NULL) {
            $data_return["state"] = 0;
            $data_return["msg"] = "Yêu cầu nhập thông tin tiêu đề danh mục !";
            echo json_encode($data_return);
            return FALSE;
        } else if ($exist_status === TRUE) {
            $data_return["state"] = 0;
            $data_return["msg"] = "Tiêu đề danh mục đã tồn tại , vui lòng lòng nhập mã khác !";
            echo json_encode($data_return);
            return FALSE;
        }

        if ($re_validate) {
            $data_all = $this->_validate_form_data($data, $id);
            if (!$data_all["state"]) {
                $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
                $data_return["msg"] = "Dữ liệu gửi lên không hợp lệ !";
                $data_return["error"] = $data_all["error"];
                echo json_encode($data_return);
                return FALSE;
            } else {
                $data = $data_all["data"];
            }
        }
        $data_history = $this->data->get_one($id, 'object');
        $update = $this->data->update($id, $data);
        if ($update) {

            try {
                $this->load->model('m_voxy_package_history', 'package_history');

                $data_history = array(
                    'pack_code' => $id,
                    'value_old' => json_encode($data_history),
                    'value_new' => json_encode($this->data->get_one($id, 'object')),
                    'action' => 'edit_category'
                );
                $this->package_history->add($data_history);

            } catch (Exception $ex) {
                // chi de tranh anh huong den viec gui thong tin ve nguoi dung
            }

            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */

            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
            echo json_encode($data_return);
            return FALSE;
        }
        parent::edit_save($id, $data, $data_return, $re_validate);
    }

    public function delete($id = 0, $data = Array())
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return FALSE;
        }

        $data_return["callback"] = "delete_respone";
        $id = intval($id);
        if ($this->input->post() || $id > 0) {
            if (isset($data["list_id"]) && sizeof($data["list_id"])) {
                $list_id = $data["list_id"];
            } else {
                if ($this->input->post() && $id == "0") {
                    $list_id = $this->input->post("list_id");
                } elseif ($id > 0) {
                    $list_id = Array($id);
                }
            }

            $this->load->model('m_voxy_category');
            // lay du lieu luu lich su xoa
            $data_history = array();
            foreach ($list_id as $one_id) {
                $data_history[] = $this->data->get_one($one_id, 'object');
            }

            $affted_row = $this->data->delete_by_id($list_id);
            if ($affted_row) {
                try {
                    $this->load->model('m_voxy_package_history', 'package_history');
                    foreach ($data_history as $one_history) {
                        $data_history = array(
                            'pack_code' => $list_id,
                            'value_old' => json_encode($one_history),
                            'value_new' => '',
                            'action' => 'delete_category'
                        );
                        $this->package_history->add($data_history);
                    }
                } catch (Exception $ex) {
                    // chi de tranh anh huong den viec gui thong tin ve nguoi dung
                }
                $data_return["list_id"] = $list_id;
                $data_return["state"] = 1;
                $data_return["msg"] = "Xóa bản ghi thành công !";
            } else {
                $data_return["list_id"] = $list_id;
                $data_return["state"] = 0;
                $data_return["msg"] = "Bản ghi đã được xóa từ trước hoặc không thể bị xóa. Vui lòng tải lại trang !";
            }

            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"] = 0;
            $data_return["msg"] = "Không xác định được ID dữ liệu !";
            echo json_encode($data_return);
            return FALSE;
        }

        parent::delete($id, $data);
    }
}