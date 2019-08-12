<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "voxy_package",
            "view"      => "voxy_package",
            "model"     => "m_voxy_package",
            "object"    => "Package"
        );
    }

    public function index()
    {
        $this->manager();
    }

    /**
     * @param array $data   Mang du lieu truyen ra view
     *
     * @author chuvantinh1991@gmail.com
     */
    public function manager($data = array())
    {
        $json_conds = $this->session->userdata('arr_package_search');
        $json_conds = json_decode($json_conds, TRUE);

        $data['form_conds']         = (array) $json_conds;
        $data['list_native_parent'] = $this->data->arr_native_package_parent;
        unset($data['list_native_parent'][0]);
        $data['list_pack_type']     = $this->data->arr_package_type;
        unset($data['list_pack_type'][0]);
        $data['list_status']        = $this->data->arr_status;

        parent::manager($data);
    }

    /**
     * Ham xu ly thong tin tim kiem
     * @param array $data
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_search_condition($data = array()) {
        if (!count($data)) {
            $data = $this->input->get();
        }

        $where_data = array();
        $like_data  = array();
        $list_field = array('native_parent', 'pack_type', 'status');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'native_parent':
                        if ($data['native_parent'] != '') {
                            $where_data['m.native_parent'] = $data['native_parent'];
                        }
                        break;
                    case 'pack_type':
                        if ($data['pack_type'] != '') {
                            $where_data['m.pack_type'] = $data['pack_type'];
                        }
                        break;
                    case 'status':
                        if ($data['status'] != '') {
                            $where_data['m.status'] = $data['status'];
                        }
                        break;
                }
            }
        }

        $data_return = array(
            'custom_where'  => $where_data,
            'custom_like'   => $like_data
        );

        $this->session->set_userdata('arr_package_search', json_encode($data_return));
        return $data_return;
    }

    /**
     * Hàm lấy dữ liệu của một danh sách bản ghi
     * Hàm này có cấu trúc nhận dữ liệu POST khá phức tạp bao gồm
     *      - q     => chuỗi tìm kiếm
     *      - limit => Số bản ghi muốn lấy ra
     *      - order => sắp xếp theo thứ tự nào
     *      - page  => trang đang xem
     * Mặc định các biến này được quản lý ở file form.js, chỉ cần quan tâm khi viết đè
     * @param Array $data Biến muốn gửi thêm để hiển thị ra view(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @return json Gửi dữ liệu json về client
     */
    public function ajax_list_data($data = Array())
    {
        $data_get = $this->input->get();
        if($data_get && is_array($data_get)){
            $this->data->custom_conds = $this->get_search_condition($data_get);
        } else {
            $json_conds = $this->session->userdata('arr_package_search');
            $json_conds = json_decode($json_conds, TRUE);
            if(count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0){
                $this->data->custom_conds = $this->get_search_condition();
            } else {
                $this->data->custom_conds = $json_conds;
            }
        }
        parent::ajax_list_data($data);
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        $this->load->model('m_voxy_category', 'category');
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

            if(isset($record->status) && isset($this->data->arr_status)){
                $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }
            if(isset($record->created_at) && intval($record->created_at)){
                $record->created_at = date('d-m-Y H:i', intval($record->created_at));
            }
            if(isset($record->parent_status) && isset($this->category->arr_status)){
                $record->parent_status = (isset($this->category->arr_status[$record->parent_status]) ? $this->category->arr_status[$record->parent_status] : $record->parent_status);
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

        if(!isset($data['pack_code'])){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Package Code không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if(!isset($data['cat_id'])){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Category không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if(!isset($data['native_parent'])){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Native Parent Code không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if(!isset($data['pack_type'])){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Package Type không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        $data['pack_code']      = strtoupper(str_replace(' ', '', trim($data['pack_code'])));
        $data['cat_id']         = intval(trim($data['cat_id']));
        $data['native_parent']  = strtoupper(trim($data['native_parent']));

        if(!$data['pack_code'] || !$data['cat_id'] || !$data['native_parent'] || !$data['pack_type']){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Package Code và Category không chính xác !";
            echo json_encode($data_return);
            return FALSE;
        }

        $this->load->model('m_voxy_category', 'category');
        $cat_info = $this->category->get_one(array('id' => $data['cat_id'], 'status' => 1), 'object');
        if(!(is_object($cat_info) && isset($cat_info->cat_code))){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Category không tồn tại !";
            echo json_encode($data_return);
            return FALSE;

        }
        // = IN HOA: CAT_CODE + NATIVE_PARENT_CODE + PACK_CODE
        $data['pack_code']  = strtoupper(trim($cat_info->cat_code) . '-' . $data['native_parent']. '-' . $data['pack_code']);
        $exist_status       = $this->data->check_exist_pack_code($data['pack_code']);
        if($exist_status === NULL){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Yêu cầu nhập thông tin Package Code !";
            echo json_encode($data_return);
            return FALSE;
        } else if($exist_status === TRUE){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Package Code đã tồn tại, vui lòng lòng nhập mã khác !";
            echo json_encode($data_return);
            return FALSE;
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
            $data_return["key_name"]    = $this->data->get_key_name();
            $data_return["record"]      = $data;
            $data_return["state"]       = 1; /* state = 1 : insert thành công */
            $data_return["msg"]         = "Thêm bản ghi thành công";
            $data_return["redirect"]    = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            try {
                $value_new = $this->data->get_one($insert_id, 'object');
                $data_history = array(
                    'pack_code' => $data['pack_code'],
                    'value_old' => '',
                    'value_new' => json_encode($value_new),
                    'action' => 'add'
                );
                $this->load->model('m_voxy_package_history', 'package_history');
                $this->package_history->add($data_history);
            } catch (Exception $ex){

            }

            echo json_encode($data_return);
            return $insert_id;
        } else {
            $data_return["state"]   = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"]     = "Thêm bản ghi thất bại, vui lòng thử lại sau";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    public function edit($id = 0, $data = Array())
    {
        $data['readonly_cat_id']        = true;
        $data['readonly_pack_code']     = true;
        $data['readonly_native_parent'] = true;
        $data['readonly_pack_type']     = true;

        parent::edit($id, $data);
    }

    public function edit_save($id = 0, $data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }

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

        if(!isset($data['pack_code'])){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Package Code không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if(!isset($data['cat_id'])){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Category không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        $data['pack_code']  = strtoupper(str_replace(' ', '', trim($data['pack_code'])));
        $data['cat_id']     = intval(trim($data['cat_id']));

        if(!$data['pack_code'] || !$data['cat_id']){
            $data_return["state"]   = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"]     = "Dữ liệu Package Code và Category không chính xác !";
            echo json_encode($data_return);
            return FALSE;
        }

        $this->load->model('m_voxy_category', 'category');
        $cat_info = $this->category->get_one(array('id' => $data['cat_id'], 'status' => 1), 'object');
        if(!(is_object($cat_info) && isset($cat_info->cat_code))){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Category không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        $exist_status   = $this->data->check_exist_pack_code($data['pack_code'], $id);
        if($exist_status === NULL){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Yêu cầu nhập thông tin Package Code !";
            echo json_encode($data_return);
            return FALSE;
        } else if($exist_status === TRUE){
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Package Code đã tồn tại, vui lòng lòng nhập mã khác !";
            echo json_encode($data_return);
            return FALSE;
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

        $value_old  = $this->data->get_one($id, 'object');
        $update     = $this->data->update($id, $data);
        if ($update) {
            $data_return["key_name"]    = $this->data->get_key_name();
            $data_return["record"]      = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"]       = 1; /* state = 1 : insert thành công */
            $data_return["msg"]         = "Sửa bản ghi thành công !";
            $data_return["redirect"]    = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            try{
                $value_new  = $this->data->get_one($id, 'object');
                $data_history = array(
                    'pack_code' => $data['pack_code'],
                    'value_old' => json_encode($value_old),
                    'value_new' => json_encode($value_new),
                    'action' => 'update'
                );
                $this->load->model('m_voxy_package_history', 'package_history');
                $this->package_history->add($data_history);
            } catch (Exception $ex){

            }

            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"]   = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"]     = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
            echo json_encode($data_return);
            return FALSE;
        }
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

            // lay du lieu luu lich su xoa
            $data_history = array();
            foreach ($list_id as $one_id){
                $data_history[] = $this->data->get_one($one_id, 'object');
            }

            $affted_row = $this->data->delete_by_id($list_id);
            if ($affted_row) {
                try {
                    $this->load->model('m_voxy_package_history', 'package_history');
                    foreach ($data_history as $one_history){
                        $data_history = array(
                            'pack_code' => isset($one_history->pack_code) ? $one_history->pack_code : '',
                            'value_old' => json_encode($one_history),
                            'value_new' => '',
                            'action' => 'delete'
                        );
                        $this->package_history->add($data_history);
                    }
                } catch (Exception $ex){
                    // chi de tranh anh huong den viec gui thong tin ve nguoi dung
                }

                $data_return["list_id"] = $list_id;
                $data_return["state"]   = 1;
                $data_return["msg"]     = "Xóa bản ghi thành công !";
            } else {
                $data_return["list_id"] = $list_id;
                $data_return["state"]   = 0;
                $data_return["msg"]     = "Bản ghi đã được xóa từ trước hoặc không thể bị xóa. Vui lòng tải lại trang !";
            }

            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"]   = 0;
            $data_return["msg"]     = "Không xác định được ID dữ liệu !";
            echo json_encode($data_return);
            return FALSE;
        }
    }
}