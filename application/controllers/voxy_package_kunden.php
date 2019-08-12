<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package_kunden extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_package_kunden",
            "view" => "voxy_package_kunden",
            "model" => "m_voxy_package_kunden",
            "object" => "Khách Hàng"
        );
    }

    /**
     * @param array $data Mang du lieu truyen ra view
     *
     * @author chuvantinh1991@gmail.com
     */
    public function manager($data = array())
    {
        $json_conds = $this->session->userdata('arr_package_search');
        $json_conds = json_decode($json_conds, TRUE);

        $data['form_conds'] = (array)$json_conds;
        $this->load->model('m_voxy_package_kunden');
        // $data['category'] = $this->m_voxy_package_kunden->get_category();

        $data['list_status'] = $this->data->arr_status;
        //$data['information_orders'] = $this->data->get_all_order();

        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package_baocao_tonghop');
        $data['shipper'] = $this->m_voxy_package_baocao_tonghop->get_all_shipper_id();
        $data['shipper_area_id'] = $this->m_voxy_package_baocao_tonghop->get_all_shipper_area_id();
        $data['data_sort_debt'] = array(
            'all' => 'Tất cả',
            'debt' => 'Còn nợ',
            'debt_short' => 'Nợ Gối',
            'debt_long' => 'Nợ Tháng',
        );
        parent::manager($data);
    }

    /**
     * Ham xu ly thong tin tim kiem
     * @param array $data
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_search_condition($data = array())
    {
        if (!count($data)) {
            $data = $this->input->get();
        }

        $where_data = array();
        $like_data = array();
        $list_field = array('q', 'limit','order','date_liefer', 'date_liefer_end','data_shipper_id', 'data_shipper_are_id','data_sort_debt');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
               // $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'q':
                        if ($data['q'] != '') {
                            $where_data['q'] = $data['q'];
                        }
                        break;
                    case 'limit':
                        if ($data['limit'] != '') {
                            $where_data['limit'] = $data['limit'];
                        }
                        break;

                    case 'order':
                        if ($data['order'] != '') {
                            $where_data['order'] = $data['order'];
                        }
                        break;

                    case 'date_liefer':
                        if ($data['date_liefer'] != '') {
                            $where_data['date_liefer'] = $data['date_liefer'];
                        }
                        break;
                    case 'date_liefer_end':
                        if ($data['date_liefer_end'] != '') {
                            $where_data['date_liefer_end'] = $data['date_liefer_end'];
                        }
                        break;
                    case 'data_shipper_id':
                        if ($data['data_shipper_id'] != '') {
                            $where_data['data_shipper_id'] = $data['data_shipper_id'];
                        }
                        break;
                    case 'data_shipper_are_id':
                        if ($data['data_shipper_are_id'] != '') {
                            $where_data['data_shipper_are_id'] = $data['data_shipper_are_id'];
                        }
                    case 'data_sort_debt':
                        if ($data['data_sort_debt'] != '') {
                            $where_data['data_sort_debt'] = $data['data_sort_debt'];
                        }
                        break;
                }
            }
        }

        $data_return = array(
            'custom_where' => $where_data,
            'custom_like' => $like_data
        );
        $this->session->set_userdata('where_data', $where_data);
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
        $data_post = $this->input->get();

        if ($data_post && is_array($data_post)) {
            $this->data->custom_conds = $this->get_search_condition($data_post);
        } else {
//            $json_conds = $this->session->userdata('arr_package_search');
//            $json_conds = json_decode($json_conds, TRUE);
//            if ($json_conds['custom_where'] != "" && $json_conds['custom_like'] != "") {
//                $this->data->custom_conds = $this->get_search_condition();
//            } else {
//                $this->data->custom_conds = $json_conds;
//            }
        }
        $this->ajax_list_data_kunden($data);
    }

    public function ajax_list_data_kunden($data = Array())
    {
        if ($this->session->userdata("limit") === FALSE) {
            $this->session->set_userdata("limit", 20);
        }
        if (!$this->session->userdata("order")) {
            $this->session->set_userdata("order", NULL);
        }
        if (!$this->session->userdata("search_string")) {
            $this->session->set_userdata("search_string", "");
        }

        $condition      = $this->input->post();
        $search_string  = isset($condition["q"]) ? $condition["q"] : $this->session->userdata("search_string");
        $limit          = intval(isset($condition["limit"]) ? $condition["limit"] : $this->session->userdata("limit"));
        $order          = isset($condition["order"]) ? $condition["order"] : $this->session->userdata("order");
        $currentPage    = intval(isset($condition["page"]) ? $condition["page"] : 0);

        if ($limit < 0) {
            $limit = 0;
        }

        /* Nếu thay đổi số record hiển thị trên 1 trang hoặc thay đổi từ khóa tìm kiếm thì đặt lại thành trang 1 */
        if (($limit != $this->session->userdata("limit")) || ($search_string != $this->session->userdata("search_string"))) {
            $currentPage    = 1;
        }
        $post = ($currentPage - 1) * $limit;
        if ($post < 0) {
            $post           = 0;
            $currentPage    = 1;
        }
        $orderData  = $this->_check_data_order_record($order);
        $order      = $orderData["string_order"];

        $this->session->set_userdata("limit", $limit);
        $this->session->set_userdata("order", $order);
        $this->session->set_userdata("search_string", $search_string);

        $totalItem  = -1;
        $record     = $this->data->get_list_table($search_string, Array(), $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->data->get_list_table($search_string, Array(), 0, 0, $order));
            }
        }

        if ($limit != 0) {
            $total_page = (int)($totalItem / $limit);
        } else {
            $total_page = 0;
        }
        if (($total_page * $limit) < $totalItem) {
            $total_page += 1;
        }

        $link               = "#";
        $data["pagging"]    = $this->_get_pagging($total_page, $currentPage, $this->pagging_item_display, $link);
        $tempData           = $this->_add_colum_action($record);
        $data               = array_merge($data, $tempData);

        $data["key_name"]   = $this->data->get_key_name();
        $data["limit"]      = $limit;
        $data["search_string"] = $search_string;
        $data["from"]       = $post + 1;
        $data["to"]         = $post + $limit;
        if ($data["to"] > $totalItem) {
            $data["to"]     = $totalItem;
        }
        $data["total"]      = $totalItem;
        $data["order"]      = $orderData["array_order"];

        $viewFile = "base_manager/default_table";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'table.php')) {
            $viewFile = $this->name["view"] . '/' . 'table';
        }

        if (isset($this->name["modules"]) && $this->name["modules"]) {
            if (file_exists(APPPATH . "modules/" . $this->name["modules"] . "/views/" . $this->name["view"] . '/' . 'table.php')) {
                $viewFile   = $this->name["view"] . '/' . 'table';
                $content    = $this->load->view($viewFile, $data, true);
            } else {
                $content    = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            }
        } else {
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        }
        if ($this->input->is_ajax_request()) {
            //$data_return["callback"]    = "get_manager_data_response";
            $data_return["state"]       = 1;
            $data_return["html"]        = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    //xu ly du lieu truoc khi ra table
    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        //var_dump($key_table);die;
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

            if (isset($record->state) && isset($this->data->arr_status)) {
                $record->state = (isset($this->data->arr_status[$record->state]) ? $this->data->arr_status[$record->state] : $record->status);
            }

            if (isset($record->created_at) && intval($record->created_at)) {
                $record->created_at = date('d-m-Y H:i', intval($record->created_at));
            }

            if (isset($record->created_at)) {
                $record->created_at = date('d-m-Y H:i', intval($record->created_at));
            }

            $record->tongtien = number_format($record->tongtien, 2) . " €";
            $record->conno = number_format($record->conno, 2) . " €";

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

        if (!isset($data['first_name'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Tên khách hàng  không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (!isset($data['last_name'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Category không tồn tại !";
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

        //them data vao database
        //xu ly data default addresse to json
        $data_json = array();
        $data_add_database = array();

        $data_json['d_last_name'] = $data['d_last_name'];
        $data_json['d_first_name'] = $data['d_first_name'];
        $data_json['address1'] = $data['address1'];
        $data_json['zip'] = $data['zip'];
        $data_json['city'] = $data['city'];
        $data_json['phone'] = $data['d_phone'];
        $data_json['default'] = $data['default'];
        //add vao mang moi
        $data_add_database['id'] = $data['id'];
        $data_add_database['id_customer'] = $data['id_customer'];
        $data_add_database['first_name'] = $data['first_name'];
        $data_add_database['last_name'] = $data['last_name'];
        $data_add_database['email'] = $data['email'];
        $data_add_database['multipass_identifier'] = $data['multipass_identifier'];
        $data_add_database['note'] = $data['note'];
        $data_add_database['state'] = $data['state'];

        $data_json_encode = json_encode($data_json);
        $data_add_database['default_address'] = $data_json_encode;

        $insert_id = $this->data->add($data_add_database);

        //du lieu tra ve sau khi post
        if (!$insert_id) {
            $data_return["state"] = 0; /* state = 1 : insert khong thành công */
            $data_return["msg"] = "Thêm bản ghi khong thành công vao máy chủ Email ";
            echo json_encode($data_return);
            return FALSE;
        } else {
            //post oke moi cho vao database

            $data[$this->data->get_key_name()] = $insert_id;
            try {
                $this->load->model('m_voxy_package_history', 'package_history');

                    $data_history = array(
                        'pack_code' => $insert_id,
                        'value_old' => '',
                        'value_new' => json_encode($this->data->get_one($insert_id, 'object')),
                        'action' => 'add_kunden'
                    );
                    $this->package_history->add($data_history);

            } catch (Exception $ex) {
                // chi de tranh anh huong den viec gui thong tin ve nguoi dung
            }
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Thêm bản ghi thành công vao máy chủ";
        }


        $data_return["key_name"] = $this->data->get_key_name();
        $data_return["record"] = $data;
        $data_return["state"] = 1; /* state = 1 : insert thành công */
        $data_return["msg"] = "Thêm bản ghi thành công vào database và máy chủ";
        $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";

        echo json_encode($data_return);
        return true;

    }

    public function edit($id = 0, $data = Array())
    {
        $data['readonly_cat_id'] = true;
        $data['readonly_pack_code'] = true;
        $data['readonly_native_parent'] = true;
        $data['readonly_pack_type'] = true;
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }
        $data_return["callback"] = isset($data['callback']) ? $data['callback'] : "get_form_edit_response";
        if (!$id) {
            $data_return["state"] = 0;
            $data_return["msg"] = "ID dữ liệu không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (!$this->data->is_editable($id)) {
            $data_return["state"] = 0;
            $data_return["msg"] = "Bản ghi không thể sửa đổi hoặc bản ghi không còn tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (!isset($data["save_link"])) {
            $data["save_link"] = site_url($this->name["class"] . "/edit_save/" . $id);
        }
        if (!isset($data["list_input"])) {
            $data["list_input"] = $this->_get_form($id);
        }

        if (!isset($data["title"])) {
            $data["title"] = $title = "Sửa dữ liệu " . $this->name["object"];
        }

        $viewFile = "base_manager/default_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'form.php')) {
            $viewFile = $this->name["view"] . '/' . 'form';
        }
        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);

        $data_return["record_data"] = $this->data->get_one($id);

        if ($data_return["record_data"]->default_address != null) {
            $data_decode_fromrecodedata = get_object_vars(json_decode(get_object_vars($data_return["record_data"])['default_address']));
        }

        $data_return["record_data"] = get_object_vars($data_return["record_data"]);
        if (isset($data_decode_fromrecodedata)) {
            if (isset($data_decode_fromrecodedata['d_first_name'])) {
                $data_return["record_data"]["d_first_name"] = $data_decode_fromrecodedata['d_first_name'];
            }
            if (isset($data_decode_fromrecodedata['d_last_name'])) {
                $data_return["record_data"]["d_last_name"] = $data_decode_fromrecodedata['d_last_name'];
            }
            if (isset($data_decode_fromrecodedata['address1'])) {
                $data_return["record_data"]["address1"] = $data_decode_fromrecodedata['address1'];
            }
            if (isset($data_decode_fromrecodedata['zip'])) {
                $data_return["record_data"]["zip"] = $data_decode_fromrecodedata['zip'];
            }
            if (isset($data_decode_fromrecodedata['city'])) {
                $data_return["record_data"]["city"] = $data_decode_fromrecodedata['city'];
            }
            $data_return["record_data"]["default"] = true;
        }

        if ($this->input->is_ajax_request()) {
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
        $head_page = $this->load->view($this->path_theme_view . 'base_manager/header_edit', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header_edit.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_edit', $data, true);
        }
        $title = "Sửa " . $this->name["object"];

        $this->master_page($content, $head_page, $title);
    }

    public function edit_save($id = 0, $data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }

        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if (!isset($data['first_name'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Ten Khach Hang  không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }
        if (!isset($data['last_name'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Ho cua khach hang không tồn tại !";
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
        //xu ly data default addresse to json
        $data_json = array();
        $data_add_database = array();

        $data_json['d_last_name'] = $data['d_last_name'];
        $data_json['d_first_name'] = $data['d_first_name'];
        $data_json['address1'] = $data['address1'];
        $data_json['zip'] = $data['zip'];
        $data_json['city'] = $data['city'];
        $data_json['default'] = $data['default'];
        //add vao mang moi
        $data_add_database['id'] = $data['id'];
        $data_add_database['id_customer'] = $data['id_customer'];
        $data_add_database['first_name'] = $data['first_name'];
        $data_add_database['last_name'] = $data['last_name'];
        $data_add_database['email'] = $data['email'];
        $data_add_database['multipass_identifier'] = $data['multipass_identifier'];
        $data_add_database['note'] = $data['note'];
        $data_add_database['state'] = $data['state'];
        $data_add_database['debt'] = $data['debt'];

        $data_json_encode = json_encode($data_json);
        $data_add_database['default_address'] = $data_json_encode;

        //day du lieu len shopify
        //data customer
        $customer_data['customer'] = array(
            'id' => $data['id_customer'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'note' => $data['note'],
            'tags' => $data['multipass_identifier'],
            'verified_email' => true,
            'addresses' => [
                array(
                    'address1' => $data['address1'],
                    'city' => $data['city'],
                    'province' => 'ON',
                    'phone' => $data['d_phone'],
                    'zip' => $data['zip'],
                    'last_name' => $data['d_last_name'],
                    'first_name' => $data['d_first_name'],
                    'country' => 'DE',
                    'default' => $data['default']
                )
            ]
        );
        $data_history = $this->data->get_one($id, 'object');
        $update = $this->data->update($id, $data_add_database);
        if ($update) {
            $value_new = $this->data->get_one($id, 'object');
            try {
                $this->load->model('m_voxy_package_history', 'package_history');

                    $data_history = array(
                        'pack_code' => $id,
                        'value_old' => json_encode($data_history),
                        'value_new' => json_encode($value_new),
                        'action' => 'update_kunden'
                    );
                    $this->package_history->add($data_history);

            } catch (Exception $ex) {
                // chi de tranh anh huong den viec gui thong tin ve nguoi dung
            }

            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sửa bản ghi thành công !";
            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
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
                            'action' => 'delete_kunden'
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
    }

    function import()
    {
        $this->load->library('excel');
        $this->load->model('m_voxy_package_kunden');
        $this->load->model('m_voxy_connect_api_tinhcv');
        if (isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            if ($object) {
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    //$highestColumn = $worksheet->getHighestColumn();
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $multipass_identifier = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $firma = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $name = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $adress1 = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        //$country = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        //$country_code = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $ort = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                        $post_leizahl = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                        $phone = $worksheet->getCellByColumnAndRow(9, $row)->getValue();

                        $data_json = array();
                        $data_add_database = array();

                        $data_json['d_last_name'] = '';
                        $data_json['d_first_name'] = $name;
                        $data_json['address1'] = $adress1;
                        $data_json['zip'] = $post_leizahl;
                        $data_json['city'] = $ort;
                        $data_json['phone'] = $phone;
                        $data_json['default'] = true;
                        //add vao mang moi
                        //$data_add_database['id'] = $data['id'];
                        //$data_add_database['id_customer'] = $data['id_customer'];
                        $data_add_database['first_name'] = $name;
                        $data_add_database['last_name'] = '';
                        $data_add_database['email'] = '';
                        $data_add_database['multipass_identifier'] = $multipass_identifier;
                        $data_add_database['note'] = '';
                        $data_add_database['state'] = 0;

                        $data_json_encode = json_encode($data_json);
                        $data_add_database['default_address'] = $data_json_encode;

                        //du lieu post lay dc tu form them
                        $customer_data['customer'] = array(
                            'first_name' => $name,
                            'last_name' => '',
                            'email' => '',
                            'verified_email' => true,
                            'note' => '',
                            'tags' => $multipass_identifier,
                            'addresses' => [
                                array(
                                    'address1' => $adress1,
                                    'city' => $ort,
                                    'company' => $firma,
                                    'province' => 'ON',
                                    'phone' => $phone,
                                    'zip' => $post_leizahl,
                                    'last_name' => '',
                                    'first_name' => $name,
                                    'country' => 'DE',
                                    'default' => true
                                )
                            ]
                        );

                        //day du lieu len shopify
                        $data_post = json_encode($customer_data);
                        $result = $this->m_voxy_connect_api_tinhcv->shopify_add_kunden($data_post);

                        //post oke moi cho vao database
                        $insert_id = $this->data->add($data_add_database);

                        //get id_customer tu ket qua tra ve , cap nhat lai vao database
                        $array = get_object_vars($result["customer"]);
                        $id_customer = $array['id'];
                        $this->m_voxy_package_kunden->update_id_customer($insert_id, $id_customer);
                        //end update id shopify to database

                    }
                }
            }
            //end add product to collection

            echo 'Data Imported successfully';

        }
    }

    public function export_kunden()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package_kunden');
        $date = date('Y-m-d');

        $export = $this->m_voxy_package_kunden->get_all_kunden();

        //ksort tag theo khoa, krsort giam theo khoa hehe :D
        //ksort($export);

//Khởi tạo đối tượng
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Sản Phẩm ' . $date);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);//barcode le
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);//barcode si
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Ngày:' . $date);
        $excel->getActiveSheet()->setCellValue('A2', 'Tên Sản Phẩm');
        $excel->getActiveSheet()->setCellValue('B2', 'Danh mục');
        $excel->getActiveSheet()->setCellValue('C2', 'Min Le Price');
        $excel->getActiveSheet()->setCellValue('D2', 'Min Si Price');
        $excel->getActiveSheet()->setCellValue('E2', 'Variant_le');//le
        $excel->getActiveSheet()->setCellValue('F2', 'Giá Lẻ €');
        $excel->getActiveSheet()->setCellValue('G2', 'Số Lượng Lẻ');
        $excel->getActiveSheet()->setCellValue('H2', 'Barcode Lẻ');
        $excel->getActiveSheet()->setCellValue('I2', 'SKU Lẻ');//het le
        $excel->getActiveSheet()->setCellValue('J2', 'Variant_Si');//si
        $excel->getActiveSheet()->setCellValue('K2', 'Giá Sỉ €');
        $excel->getActiveSheet()->setCellValue('L2', 'Số Lượng Sỉ');
        $excel->getActiveSheet()->setCellValue('M2', 'Barcode Sỉ');
        $excel->getActiveSheet()->setCellValue('N2', 'SKU Sỉ');//het si
        $excel->getActiveSheet()->setCellValue('O2', 'Vị trí');
        $excel->getActiveSheet()->setCellValue('P2', 'Ngày hết hạn');
        $excel->getActiveSheet()->setCellValue('Q2', 'Loại Sản Phẩm');
        $excel->getActiveSheet()->setCellValue('R2', 'Keyword');
        $excel->getActiveSheet()->setCellValue('S2', 'Mwst');
        $excel->getActiveSheet()->setCellValue('T2', 'Giá mua lẻ');
        $excel->getActiveSheet()->setCellValue('U2', 'Giá mua sỉ');

        //$excel->getActiveSheet()->setCellValue('H2', 'Theo Xe');
        //$excel->getActiveSheet()->setCellValue('G1', 'Tổng tiền thêm 5 eu phi shipping: € '.$total_price);
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 3;
        if ($export != null) {
            $this->load->model('m_voxy_category');
            foreach ($export as $row) {
                $cat_title = $this->m_voxy_category->get_cat_title($row['cat_id']);
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $row['title']);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $cat_title);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['le_midest_price']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['si_midest_price']);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['option1']);//le
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['price1']);
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['inventory_quantity1']);
                $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['barcode1']);
                $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['sku1']);//hetle
                $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['option2']);//si
                $excel->getActiveSheet()->setCellValue('K' . $numRow, $row['price2']);
                $excel->getActiveSheet()->setCellValue('L' . $numRow, $row['inventory_quantity2']);
                $excel->getActiveSheet()->setCellValue('M' . $numRow, $row['barcode2']);
                $excel->getActiveSheet()->setCellValue('N' . $numRow, $row['sku2']);//het si
                $excel->getActiveSheet()->setCellValue('O' . $numRow, $row['location']);
                $excel->getActiveSheet()->setCellValue('P' . $numRow, $row['expri_day']);
                $excel->getActiveSheet()->setCellValue('Q' . $numRow, $row['product_type']);
                $excel->getActiveSheet()->setCellValue('R' . $numRow, $row['keyword_si']);
                $excel->getActiveSheet()->setCellValue('S' . $numRow, $row['mwst']);
                $excel->getActiveSheet()->setCellValue('T' . $numRow, $row['gia_mua_le']);
                $excel->getActiveSheet()->setCellValue('U' . $numRow, $row['gia_mua_si']);
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('T')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('U')->applyFromArray($styleArray2);
                $numRow++;
            }
            //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$total_price);
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=SP-' . $date . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function get_infor_customer_orders()
    {
        $id_customer = $this->input->post("id_customer");
        if($this->input->post("date_liefer") != false){
            $date_liefer = $this->input->post("date_liefer");
        }else{
            $date_liefer = "";
        }

        if($this->input->post("date_liefer_end") != false){
            $date_liefer_end = $this->input->post("date_liefer_end");
        }else{
            $date_liefer_end = "";
        }

        $data['infor'] = $this->data->get_all_order($id_customer,$date_liefer,$date_liefer_end);//sua chi hien thi no, ngay 24.04 .2019

        $viewFile = '/voxy_package_kunden/infor_orders';
        $html = $this->load->view($this->path_theme_view . $viewFile, $data, true);

        $data_return = array();
        if ($html) {
            $data_return['status'] = 1;
            $data_return['record'] = $html;
            echo json_encode($data_return);
            return true;
        } else {
            $data_return['status'] = 0;//fail
            $data_return['record'] = "";
            echo json_encode($data_return);
            return false;
        }
    }

    public function excel_no_kunden(){
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package_kunden');

        if ($this->input->get('id') != "undefined") {
            $id = $this->input->get('id');
        }else{
            var_dump("CHọn ID khách hàng bên dưới sau đó nhấn Excel để in !");die;
        }

        if($this->input->get("date_liefer") != false){
            $date_liefer = $this->input->get("date_liefer");
        }else{
            $date_liefer = "";
        }

        if($this->input->get("date_liefer_end") != false){
            $date_liefer_end = $this->input->get("date_liefer_end");
        }else{
            $date_liefer_end = "";
        }

        if ($this->input->get('id') != "undefined") {
            $list_id = get_object_vars(json_decode($id))['list_id'];
            $data = $this->m_voxy_package_kunden->get_all_infor_no($list_id, $date_liefer, $date_liefer_end);
        }else{
            var_dump("CHọn khách hàng bên dưới sau đó nhấn Excel để in !");die;
        }
        //sau nay co the in atat ca , nhung chua lam. tam thoi lam cai ghi no theo select check box

//Khởi tạo đối tượng
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Báo cáo nợ - khách hàng ');

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:F2')->getFont()->setBold(true);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Báo cáo nợ theo khách hàng');

        $excel->getActiveSheet()->setCellValue('A2', 'STT');
        $excel->getActiveSheet()->setCellValue('B2', 'Tên khách');
        $excel->getActiveSheet()->setCellValue('C2', 'Đơn hàng');
        $excel->getActiveSheet()->setCellValue('D2', 'Số tiền €');
        $excel->getActiveSheet()->setCellValue('E2', 'Còn nợ');
        $excel->getActiveSheet()->setCellValue('F2', 'Ngày giao hàng');

// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2

        //$_data = json_decode($data);
        $numRow = 3;
        $total_price = 0;
        $total_no = 0;
        if ($data != null) {
            $stt = 0;
            foreach ($data as $item) {
                if($item['first_name'] && $item['last_name']) {
                    $name = $item['first_name'] . $item['last_name'];
                }else{
                    $name = "";
                }
                if($name == ""){
                    $name = $item['multipass_identifier'];
                }
                if($item['infor']){
                    foreach ($item['infor'] as $row){
                        $stt++;
                        $total_price += $row['total_price'];
                        $total_no += $row['tongtien_no'];
                        if($row['tongtien_no'] == ""){
                            $row['tongtien_no'] = 0;
                        }
                        $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                        $excel->getActiveSheet()->setCellValue('B' . $numRow, $name);
                        $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['order_number']);
                        $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['total_price']);
                        $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['tongtien_no']);
                        $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['shipped_at']);
                        $numRow++;
                    }
                }
            }
            $num = $numRow++;
            $excel->getActiveSheet()->setCellValue('C' . $num,"Tổng");
            $excel->getActiveSheet()->setCellValue('D' . $num,number_format($total_price, 2));
            $excel->getActiveSheet()->setCellValue('E' . $num,number_format($total_no, 2));
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=Bao_cao_no_khach_hang.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function excel_no_kunden_all(){
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package_kunden');

        if ($this->input->get('id') != "undefined") {
            $id = $this->input->get('id');
        }else{
            var_dump("CHọn ID khách hàng bên dưới sau đó nhấn Excel để in !");die;
        }

        if($this->input->get("date_liefer") != false){
            $date_liefer = $this->input->get("date_liefer");
        }else{
            $date_liefer = "";
        }

        if($this->input->get("date_liefer_end") != false){
            $date_liefer_end = $this->input->get("date_liefer_end");
        }else{
            $date_liefer_end = "";
        }

        if ($this->input->get('id') != "undefined") {
            $list_id = get_object_vars(json_decode($id))['list_id'];
            $data = $this->m_voxy_package_kunden->get_all_infor_no_all($list_id, $date_liefer, $date_liefer_end);

        }else{
            var_dump("CHọn khách hàng bên dưới sau đó nhấn Excel để in !");die;
        }
        //sau nay co the in atat ca , nhung chua lam. tam thoi lam cai ghi no theo select check box

//Khởi tạo đối tượng
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Báo cáo nợ - khách hàng ');

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:F2')->getFont()->setBold(true);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Báo cáo nợ theo khách hàng');

        $excel->getActiveSheet()->setCellValue('A2', 'STT');
        $excel->getActiveSheet()->setCellValue('B2', 'Tên khách');
        $excel->getActiveSheet()->setCellValue('C2', 'Số tiền €');
        $excel->getActiveSheet()->setCellValue('D2', 'Còn nợ');

// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2

        //$_data = json_decode($data);
        $numRow = 3;
        $total_price = 0;
        $total_no = 0;
        if ($data != null) {
            $stt = 0;
            foreach ($data as $item) {
                if($item['first_name'] && $item['last_name']) {
                    $name = $item['first_name'] . $item['last_name'];
                }else{
                    $name = "";
                }
                if($name == ""){
                    $name = $item['multipass_identifier'];
                }
                if($item['infor']){
                    foreach ($item['infor'] as $row){
                        $stt++;
                        $total_price += $row['total_price'];
                        $total_no += $row['tongtien_no'];
                        if($row['tongtien_no'] == ""){
                            $row['tongtien_no'] = 0;
                        }
                        $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                        $excel->getActiveSheet()->setCellValue('B' . $numRow, $name);
                        $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['total_price']);
                        $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['tongtien_no']);
                        $numRow++;
                    }
                }
            }
            $num = $numRow++;
            $excel->getActiveSheet()->setCellValue('B' . $num,"Tổng");
            $excel->getActiveSheet()->setCellValue('C' . $num,number_format($total_price, 2));
            $excel->getActiveSheet()->setCellValue('D' . $num,number_format($total_no, 2));
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=Bao_cao_no_khach_hang.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

}