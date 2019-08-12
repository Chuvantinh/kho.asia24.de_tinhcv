<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_nha_cung_cap
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_nha_cung_cap extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "voxy_nha_cung_cap",
            "view"      => "voxy_nha_cung_cap",
            "model"     => "m_voxy_nha_cung_cap",
            "object"    => "Nhà cung cấp"
        );
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        $this->load->model('m_voxy_package');
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

        if (!isset($data['name'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Tên nhà cung cấp không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        $name = $data['name'];
        if ($this->data->check_title($name) == true){
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Tên đã tồn tại, mời nhập lại";
            echo json_encode($data_return);
            return FALSE;
        }
        //them data vao database
        $insert_id = $this->data->add($data);
        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data;
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Thêm bản ghi thành công vào database và may chu";
            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            echo json_encode($data_return);
            return $insert_id;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
            echo json_encode($data_return);
            return FALSE;
        }
    }
}
?>