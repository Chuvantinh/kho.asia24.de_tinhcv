<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_kiemkho extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_kiemkho",
            "view" => "voxy_kiemkho",
            "model" => "m_voxy_kiemkho",
            "object" => " Kiểm Kho"
        );
    }

    public function index()
    {
        $this->manager();
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
        $this->load->model('m_voxy_nha_cung_cap');
        //$data['category'] = $this->m_voxy_category->get_category();

        $data['list_status'] = $this->data->arr_status2;
        $data['list_nha_cc'] = $this->m_voxy_nha_cung_cap->get_all_title();

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
        $list_field = array('vendor', 'status');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'vendor':
                        if ($data['vendor'] != '') {
                            $where_data['m.vendor'] = $data['vendor'];
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
            'custom_where' => $where_data,
            'custom_like' => $like_data
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

        if ($data_get && is_array($data_get)) {
            $this->data->custom_conds = $this->get_search_condition($data_get);
        } else {
//            $json_conds = $this->session->userdata('arr_package_search');
//            $json_conds = json_decode($json_conds, TRUE);
//            if (count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0) {
//                $this->data->custom_conds = $this->get_search_condition();
//            } else {
//                $this->data->custom_conds = $json_conds;
//            }
        }

        parent::ajax_list_data($data);
    }

    public function ajax_list_data_new($data = Array())
    {
        $this->load->model('m_voxy_kiemkho');
        $data_get = $this->input->post();

        $where_condition = "";
        if ($data_get && is_array($data_get)) {
            $where_condition = $this->get_search_condition_new($data_get);
        }

        if ($this->session->userdata("limit") === FALSE) {
            $this->session->set_userdata("limit", 20);
        }
        if (!$this->session->userdata("order")) {
            $this->session->set_userdata("order", NULL);
        }
        if (!$this->session->userdata("search_string")) {
            $this->session->set_userdata("search_string", "");
        }

        $condition = $this->input->post();
        $search_string = isset($condition["q"]) ? $condition["q"] : $this->session->userdata("search_string");
        $limit = intval(isset($condition["limit"]) ? $condition["limit"] : $this->session->userdata("limit"));
        $order = isset($condition["order"]) ? $condition["order"] : $this->session->userdata("order");
        $currentPage = intval(isset($condition["page"]) ? $condition["page"] : 0);

        if ($limit < 0) {
            $limit = 0;
        }

        /* Nếu thay đổi số record hiển thị trên 1 trang hoặc thay đổi từ khóa tìm kiếm thì đặt lại thành trang 1 */
        if (($limit != $this->session->userdata("limit")) || ($search_string != $this->session->userdata("search_string"))) {
            $currentPage = 1;
        }
        $post = ($currentPage - 1) * $limit;
        if ($post < 0) {
            $post = 0;
            $currentPage = 1;
        }
        $orderData = $this->_check_data_order_record($order);
        $order = $orderData["string_order"];

        $this->session->set_userdata("limit", $limit);
        $this->session->set_userdata("order", $order);
        $this->session->set_userdata("search_string", $search_string);

        $totalItem = -1;
        $record = $this->m_voxy_kiemkho->get_list_table($search_string, $where_condition, $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->m_voxy_kiemkho->get_list_table($search_string, $where_condition, 0, 0, $order));
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

        $link = "#";
        $data["pagging"] = $this->_get_pagging($total_page, $currentPage, $this->pagging_item_display, $link);
        $tempData = $this->_add_colum_action($record);
        $data = array_merge($data, $tempData);

        $data["key_name"] = $this->data->get_key_name();
        $data["limit"] = $limit;
        $data["search_string"] = $search_string;
        $data["from"] = $post + 1;
        $data["to"] = $post + $limit;
        if ($data["to"] > $totalItem) {
            $data["to"] = $totalItem;
        }
        $data["total"] = $totalItem;
        $data["order"] = $orderData["array_order"];

        $viewFile = "base_manager/default_table";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'top10_import.php')) {
            $viewFile = $this->name["view"] . '/' . 'table';
        }

        if (isset($this->name["modules"]) && $this->name["modules"]) {
            if (file_exists(APPPATH . "modules/" . $this->name["modules"] . "/views/" . $this->name["view"] . '/' . 'top10_import.php')) {
                $viewFile = $this->name["view"] . '/' . 'table';
                $content = $this->load->view($viewFile, $data, true);
            } else {
                $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            }
        } else {
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        }
        if ($this->input->is_ajax_request()) {
            //$data_return["callback"]    = "get_manager_data_response";
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    protected function get_search_condition_new($data = array())
    {
        if (!count($data)) {
            $data = $this->input->post();
        }

        $where_data = array();
        $like_data = array();
        $list_field = array('vendor', 'ngay_giao_hang', 'ngay_dat_hang', 'status');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'ngay_dat_hang':
                        if ($data['ngay_dat_hang'] != '') {
                            $where_data['m.ngay_dat_hang'] = $data['ngay_dat_hang'];
                        }
                        break;
                    case 'ngay_giao_hang':
                        if ($data['ngay_giao_hang'] != '') {
                            $where_data['m.ngay_giao_hang'] = $data['ngay_giao_hang'];
                        }
                        break;
                    case 'status':
                        if ($data['status'] != '') {
                            $where_data['m.status'] = $data['status'];
                        }
                        break;
                    case 'vendor':
                        if ($data['vendor'] != '') {
                            $where_data['m.vendor'] = $data['vendor'];
                        }
                        break;
                }
            }
        }

        $data_return = array(
            'custom_where' => $where_data,
            'custom_like' => $like_data
        );
        //$this->session->set_userdata('arr_package_search', json_encode($data_return));
        return $data_return;
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        $this->load->model('m_voxy_category', 'category');
        $this->load->model('m_voxy_nha_cung_cap');
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

            if (isset($record->created_at) && intval($record->created_at)) {
                $record->created_at = date('d-m-Y H:i', intval($record->created_at));
            }
            if (isset($record->parent_status) && isset($this->category->arr_status)) {
                $record->parent_status = (isset($this->category->arr_status[$record->parent_status]) ? $this->category->arr_status[$record->parent_status] : $record->parent_status);
            }
            if (isset($record->expri_day) && isset($record->expri_day)) {
                //$record->expri_day = date('d-m-Y H:i', intval($record->expri_day));
            }

            if (isset($record->vendor)) {
                $record->vendor = $this->m_voxy_nha_cung_cap->get_title($record->vendor);
            }

            if (isset($record->mark_as_complete)) {
                if ($record->mark_as_complete == "0") {
                    $record->mark_as_complete = "null";
                } elseif ($record->mark_as_complete == "all") {
                    $record->mark_as_complete = "Tất cả";
                } elseif ($record->mark_as_complete == "Cho") {
                    $record->mark_as_complete = "Chờ";
                } elseif ($record->mark_as_complete == "Mot Phan") {
                    $record->mark_as_complete = "Một phần";
                } elseif ($record->mark_as_complete == "Hoan Thanh") {
                    $record->mark_as_complete = "Hoàn thành";
                } else {

                }
            }
        }
        return $record;
    }

    public function add($data = Array())
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }

        $data_return["callback"] = isset($data['callback']) ? $data['callback'] : "get_form_add_response";
        if (!isset($data["save_link"])) {
            $data["save_link"] = site_url($this->name['class'] . '/add_save');
        }
        if (!isset($data["list_input"])) {
            $data["list_input"] = $this->_get_form();
        }
        if (!isset($data["title"])) {
            $data["title"] = $title = 'Thêm dữ liệu ' . $this->name['object'];
        }

        if (!isset($data["status"])) {
            $data["status"] = 0;
        }

        $viewFile = "base_manager/default_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'form.php')) {
            $viewFile = $this->name["view"] . '/' . 'form';
        }
        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        if ($this->input->is_ajax_request()) {
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
        $head_page = $this->load->view($this->path_theme_view . 'base_manager/header_add', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'head_add.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_add', $data, true);
        }

        $title = 'Thêm ' . $this->name['object'];

        $this->master_page($content, $head_page, $title);
    }

    public function add_save($data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }
        $this->load->model('m_voxy_package');

        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }


        if (isset($data["information"])) {
            $list_product = array();//xu ly lai san pham add vao database
            $i = 0;
            $tongtien = 0;
            $location_add = "";
            foreach ($data["information"] as $item) {
                $i++;

                if ($data['status'] == 1) {//check update location

                    if (isset($item['variant_id']) && $item['variant_id'] != "") {
                        $id_new = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                    } else {
                        $id_new = false;
                    }
                    //get location truoc khi upate.
                    $get_location_before = $this->m_voxy_package->check_location_already($id_new);

                    if ($get_location_before != false) {////
                        //check vi tri co giong nhau ko, k cho duplicate
                        if (strcmp($get_location_before, $item['location']) == 0) {
                            $item['location'] = $item['location'];
                            $this->m_voxy_package->update_location_variant1($id_new, $item['location']);//to database
                        } else {
                            $item['location'] = $item['location'] . "," . $get_location_before;
                            $location_ex = explode(",", $item['location']);
                            $arr_location = array_unique($location_ex);
                            $arr_loca_end = implode(",", $arr_location);
                            $this->m_voxy_package->update_location_variant1($id_new, $arr_loca_end);//to database
                        }
                    }////

                }

                if (isset($data["quantity"])) {
                    foreach ($data["quantity"] as $key => $item_quantity) {
                        if ($key == $item['variant_id']) {
                            $item["sl_kiemkho"] = $item_quantity;

                            if (strpos($item['location'], "v1") == false || strpos($item['location'], "v2") == false) {
                                $item['sailech'] = 0;
                                $item["sl_kiemkho"] = 0;
                                $item['sl_kho']= 0;
                                $item["sl_kiemkho"] = 0;
                                $item['thanhtien'] = 0;
                                $tongtien += $item['thanhtien'];
                            }else{
                                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
                                if (isset($item['variant_id']) && $item['variant_id'] != "") {
                                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                                } else {
                                    $idnew = false;
                                }

                                if ($check_variant1 == true) {
                                    //gia von la gia mua
                                    if ($idnew != false) {
                                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                                    } else {
                                        $giavon = 0;
                                    }
                                }

                                if ($check_variant2 == true) {
                                    if ($idnew != false) {
                                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                                    } else {
                                        $giavon = 0;
                                    }
                                }

                                $item['sailech'] = (double)$item["sl_kiemkho"] - (double)$item['sl_kho'];

                                $item['thanhtien'] = (double)$item["sl_kiemkho"] * (double)$giavon;
                                $tongtien += $item['thanhtien'];
                            }

                            $list_product[$i] = $item;
                        }
                    }
                }
            }
        }

        $data['name'] = trim($data['name']);
        if (!isset($list_product)) {
            $list_product = "";
        }
        $data['product_variants'] = json_encode($list_product);
        $data['total_price'] = $tongtien;

        unset($data["search_pro"]);
        unset($data["information"]);
        unset($data["quantity"]);
        unset($data["input_location"]);

        //$data['date_save'] = date("m-d-Y H:i:s");
        //du lieu post lay dc# tu form them
        //var_dump($data);die;
        $insert_id = $this->data->add($data);

        //update location for products has not a location in the ware house

        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data;
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";
            $data_return["msg"] = "Thêm thành công";
            echo json_encode($data_return);
            return $insert_id;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    public function edit($id = 0, $data = Array())
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }

        $this->load->model('m_voxy_package');

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

        if (!isset($data["status"])) {
            $data["status"] = $this->data->_get_status($id);
        }

        //nhung gia tri selected cua thang location
        if (!isset($data["products"])) {
            $data_product_raw = $this->data->get_products_selected($id);//tra ve array
            $arr = array();
            $i = 0;
            foreach (json_decode($data_product_raw) as $key => $item) {
                $i++;
                $row = get_object_vars($item);

                $arr[$row['location'] . "-" . $i] = $row;
            }
            ksort($arr);

            $arr_json = json_encode($arr);

            $data["products_history"] = $arr_json;

            //$data["products_history"] = $this->data->get_products_selected($id);//tra ve array
        }
        if (!isset($data["title"])) {
            $data["title"] = $title = "Cập Nhật " . $this->name["object"];
        }

        $viewFile = "base_manager/default_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'form.php')) {
            $viewFile = $this->name["view"] . '/' . 'form';
        }
        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);

        $data_return["record_data"] = $this->data->get_one($id);
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
        $this->load->model('m_voxy_package');

        $data_return["callback"] = "save_form_edit_response";

        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if (isset($data["information"])) {
            $list_product = array();//xu ly lai san pham add vao database
            $i = 0;
            $tongtien = 0;
            $location_add = "";
            foreach ($data["information"] as $item) {
                $i++;

                if ($data['status'] == 1) {//check update location

                    if (isset($item['variant_id']) && $item['variant_id'] != "") {
                        $id_new = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                    } else {
                        $id_new = false;
                    }
                    //get location truoc khi upate.
                    $get_location_before = $this->m_voxy_package->check_location_already($id_new);

                    if ($get_location_before != false) {////
                        //check vi tri co giong nhau ko, k cho duplicate
                        if (strcmp($get_location_before, $item['location']) == 0) {
                            $item['location'] = $item['location'];
                            $this->m_voxy_package->update_location_variant1($id_new, $item['location']);//to database
                        } else {
                            $item['location'] = $item['location'] . "," . $get_location_before;
                            $location_ex = explode(",", $item['location']);
                            $arr_location = array_unique($location_ex);
                            $arr_loca_end = implode(",", $arr_location);
                            $this->m_voxy_package->update_location_variant1($id_new, $arr_loca_end);//to database
                        }
                    }////
                }

                if (isset($data["quantity"])) {
                    foreach ($data["quantity"] as $key => $item_quantity) {

                        if ($key == $item['variant_id']) {
                            $item["sl_kiemkho"] = $item_quantity;

                            if (strpos($item['location'], "v1") == false || strpos($item['location'], "v2") == false) {
                                $item['sailech'] = 0;
                                $item["sl_kiemkho"] = 0;
                                $item['sl_kho']= 0;
                                $item["sl_kiemkho"] = 0;
                                $item['thanhtien'] = 0;
                                $tongtien += $item['thanhtien'];
                            }else{
                                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
                                if (isset($item['variant_id']) && $item['variant_id'] != "") {
                                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                                } else {
                                    $idnew = false;
                                }

                                if ($check_variant1 == true) {
                                    //gia von la gia mua
                                    if ($idnew != false) {
                                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                                    } else {
                                        $giavon = 0;
                                    }
                                }

                                if ($check_variant2 == true) {
                                    if ($idnew != false) {
                                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                                    } else {
                                        $giavon = 0;
                                    }
                                }

                                $item['sailech'] = (double)$item["sl_kiemkho"] - (double)$item['sl_kho'];

                                $item['thanhtien'] = (double)$item["sl_kiemkho"] * (double)$giavon;
                                $tongtien += $item['thanhtien'];
                            }

                            $list_product[$i] = $item;
                        }
                    }
                }
            }
        }


        $data['name'] = trim($data['name']);
        if (!isset($list_product)) {
            $list_product = "";
        }
        $data['product_variants'] = json_encode($list_product);
        $data['total_price'] = $tongtien;

        unset($data["search_pro"]);
        unset($data["information"]);
        unset($data["quantity"]);
        unset($data["input_location"]);


        $update = $this->data->update($id, $data);
        if ($update) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";
            $data_return["msg"] = "Sửa thành công";
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
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package');

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

            //from listid get list id_shopify

            $list_id_shopify = $this->m_voxy_package->get_id_shopify($list_id);
            //remove per Curl in model list id shopify
            $result = $this->m_voxy_connect_api_tinhcv->shopify_delete_product($list_id_shopify);
            //du lieu tra ve sau khi delete
            if (!$result) {
                $data_return["state"] = 0; /* state = 0 : delete that bai */
                $data_return["msg"] = "Xoá bản ghi không thành công trên hệ thống  may chu";
            } else {
                $data_return["state"] = 1; /* state = 1 : delete thành công */
                $data_return["msg"] = "Xoá bản ghi thành công trên hệ thống may chu";
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
                            'pack_code' => isset($one_history->pack_code) ? $one_history->pack_code : '',
                            'value_old' => json_encode($one_history),
                            'value_new' => '',
                            'action' => 'delete'
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

    public function view($id = 0, $data = Array())
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }

        $data_return["callback"] = isset($data['callback']) ? $data['callback'] : "get_data_view_response";
        $id = intval($id);
        if (!$id) {
            $data_return["state"] = 0;
            $data_return["msg"] = "ID dữ liệu không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        if (!isset($data["save_link"])) {
            $data["save_link"] = site_url($this->name["class"] . "/edit_save");
        }
        if (!isset($data["list_input"])) {
            $data["list_input"] = $this->_get_form($id);
        }
        $data["title"] = $title = "Xem dữ liệu " . $this->name["object"];

        if (!isset($data["status"])) {
            $data["status"] = $this->data->_get_status($id);
        }

        //nhung gia tri selected cua thang location
        if (!isset($data["products"])) {

            $data["products_history"] = $this->data->get_products_selected($id);//tra ve array
        }

        $viewFile = "base_manager/default_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'form.php')) {
            $viewFile = $this->name["view"] . '/' . 'form';
        }
        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);

        $data_return["record_data"] = $this->data->get_one($id);
        if ($this->input->is_ajax_request()) {
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }

        $head_page = $this->load->view($this->path_theme_view . 'base_manager/header_view', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header_view.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_view', $data, true);
        }
        $title = "Sửa " . $this->name["object"];

        $this->master_page($content, $head_page, $title);
    }

    public function search_pro()
    {
        $this->load->model('m_location');
        $this->load->model('m_voxy_package');
        $text = $this->input->post('request');
        $list_product_before = $this->m_voxy_package->get_search_pro($text);

        //them location
        if (strpos($text, "AF") !== false || strpos($text, "AH") !== false || strpos($text, "AK") !== false) {
            $data['text'] = $text;
        } else {
            $data['text'] = "";
        }

        $list_all_location = $this->m_location->get_all_location();
        $arr_location_after = array();
        $j = 0;
        foreach ($list_all_location as $item) {//check location not used
            $check_used = $this->m_voxy_package->check_location_used($item['name']);
            if ($check_used == false) {
                if (strpos($item['name'], $text) !== false) {
                    $j++;
                    $array_infor_extra = array();
                    $array_infor_extra['inventory_quantity1'] = "";
                    $array_infor_extra['inventory_quantity2'] = "";
                    $array_infor_extra['location'] = $item['name'];
                    $array_infor_extra['title'] = "#";
                    $array_infor_extra['sku1'] = "";
                    $array_infor_extra['sku2'] = "";
                    $array_infor_extra['option1'] = "#";
                    $array_infor_extra['option2'] = "#";
                    $array_infor_extra['cat_id'] = "";
                    $array_infor_extra['id_shopify'] = "";
                    $array_infor_extra['id_shopify'] = "";
                    $array_infor_extra['variant1_id'] = "v1-".$j;
                    $array_infor_extra['variant2_id'] = "v2-".$j;

                    $arr_location_after[$item['name']. "-" .$j][] = $array_infor_extra;
                }
            }
        }
        ksort($arr_location_after);
        $data['extra_location'] = $arr_location_after;
        //end them location

        $export = array();
        $i = 0;
        foreach ($list_product_before as $item) {
            if (strpos($item['location'], $text) !== false) {
                if (strlen($item['location']) > 10) {
                    $location = explode(",", $item['location']);
                    foreach ($location as $local_item) {
                        $i++;
                        $item['location'] = $local_item;
                        $export[$local_item . "-" . $i][] = $item;
                    }
                } else {
                    $i++;
                    $export[$item['location'] . "-" . $i][] = $item;
                }
            } else {//truong hop tim theo ten san pham, hien thi tat ca k phai cat location
                $i++;
                $export[$item['location'] . "-" . $i][] = $item;
            }
        }
        ksort($export);//sap xep theo location giam dan

        $export_end = array_merge($arr_location_after, $export);//tong hop 2 cai vao de in ra het
        ksort($export_end);
        //var_dump($export);die;
        $data['list_products'] = $export_end;

        $data_return = array();
        if ($data['list_products'] == false) {
            $data_return["state"] = 0;
            $data_return["msg"] = "";
            $data_return["html"] = "K tim thay san pham";
            echo json_encode($data_return);
            return FALSE;
        } else {
            $viewFile = '/voxy_kiemkho/search_pro';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }
        //nut cap nhat he thong
    public function export_product_excel()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_kiemkho');
        $this->load->model('m_voxy_package');
        $this->load->model('m_location');

        $list_id = $this->input->get('list_id');
        $arr_list_id = explode(",", $list_id);
        if($list_id == "" || $list_id == null){
            die("ban phai chon ID truoc, sau do moi nhan hanh dong cap nhat he thong");
        }
        if ($list_id != false) {  //in theo cac cai don dc tao ben duoi
            $list_product_variants = $this->m_voxy_kiemkho->get_product_variants($arr_list_id);

            $export_step1 = array();
            $i = 0;

            foreach ($list_product_variants as $item) {
                $product_variants = get_object_vars(json_decode(($item['product_variants'])));
                foreach ($product_variants as $key => $row) {
                    $row = get_object_vars($row);

                    /*
                    if (strlen($row['location']) > 10) {//that is 2 positon, we can sort out
                        $location = explode(",", $row['location']);
                        foreach ($location as $local_item) {
                            $i++;
                            $row['location'] = $local_item;
                            $export_step1[$local_item . "-" . $i][] = $row;
                        }
                    } else {
                        $i++;
                        $export_step1[$row['location'] . "-" . $i][] = $row;
                    }
                    */
                    $export_step1[] = $row;
                }
            }

            $export_step2 = array();
            $chiso_remove = array();


            foreach ($export_step1 as $key => $item1) {
                foreach ($export_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item1['variant_id'] == $item2['variant_id']) {
                            if ($item1['sl_kiemkho'] == "") {
                                $item1['sl_kiemkho'] = 0;
                            }

                            if ($item2['sl_kiemkho'] == "") {
                                $item2['sl_kiemkho'] = 0;
                            }
                            $item1['sl_kiemkho'] = $item1['sl_kiemkho'] + $item2['sl_kiemkho'];

                            $item1['location'] = $item1['location'] . "," . $item2['location'];
                            $location_ex = explode(",", $item1['location']);
                            $arr_location = array_unique($location_ex);
                            $arr_loca_end = implode(",", $arr_location);
                            $idnew = $this->m_voxy_package->get_id_from_variant($item1['variant_id']);
                            $this->m_voxy_package->update_location_variant1($idnew, $arr_loca_end);//to database
                            $item1['location'] = $arr_loca_end;
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }

                $export_step2[$key] = $item1;
            }


            //remove nhung thang giong di
            foreach ($export_step2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export_step2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

            $export = $export_step2;

        } else {
            $list_all_location = $this->m_location->get_all_location();
            $arr_location_after = array();
            foreach ($list_all_location as $item) {//check location not used
                $check_used = $this->m_voxy_package->check_location_used($item['name']);
                if ($check_used == false) {
                    $arr_location_after[] = $item['name'];
                }
            }

            $list_product_variants = $this->m_voxy_package->get_all_products_inkho();//dieu kien la vi tri ko duoc null
            //tao 2 san pham co variant
            $list1 = array();
            $list2 = array();
            $count = 0;
            foreach ($list_product_variants as $key => $vari) {
                if ($vari['option1'] != "") {
                    $count++;
                    $list1[$count]['location'] = $vari['location'];
                    $list1[$count]['title'] = $vari['title'];
                    $list1[$count]['sl_kho'] = $vari['inventory_quantity1'];
                    $list1[$count]['sl_kiemkho'] = "";
                    $list1[$count]['variant_title'] = $vari['option1'];
                    $list1[$count]['product_id'] = $vari['id_shopify'];
                }

                if ($vari['option2'] != "") {
                    $count++;
                    $list2[$count]['location'] = $vari['location'];
                    $list2[$count]['title'] = $vari['title'];
                    $list2[$count]['sl_kho'] = $vari['inventory_quantity2'];
                    $list2[$count]['sl_kiemkho'] = "";
                    $list2[$count]['variant_title'] = $vari['option2'];
                    $list2[$count]['product_id'] = $vari['id_shopify'];
                }
            }
            $list_tong = array_merge($list1, $list2);

            $export = array();
            $i = 0;
            foreach ($list_tong as $item) {
                if (strlen($item['location']) > 10) {
                    $location = explode(",", $item['location']);
                    foreach ($location as $local_item) {
                        $i++;
                        $item['location'] = $local_item;
                        $export[$local_item . "-" . $i][] = $item;
                    }
                } else {
                    $i++;
                    $export[$item['location'] . "-" . $i][] = $item;
                }
            }
        }

        ksort($export);

//Khởi tạo đối tượng
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle("Danh sach san pham  kiem kho");

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(80);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        //$excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
            ));
        $excel->getActiveSheet()->getStyle('A3:U3')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray2);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        //$excel->getActiveSheet()->setCellValue('A1', 'Location');

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'Group');
        $excel->getActiveSheet()->setCellValue('C3', 'Vị Trí');
        $excel->getActiveSheet()->setCellValue('D3', 'Tên');
        $excel->getActiveSheet()->setCellValue('E3', 'Tồn Kho');
        $excel->getActiveSheet()->setCellValue('F3', 'Thực tế');//le
        $excel->getActiveSheet()->setCellValue('G3', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('H3', 'Sai Lệch');
        $excel->getActiveSheet()->setCellValue('I3', 'Giá vốn');
        $excel->getActiveSheet()->setCellValue('J3', 'Thành tiền');


        $excel->getActiveSheet()->setCellValue('C1', 'Báo Cáo Kiểm Kho');
        $excel->getActiveSheet()->setCellValue('C2', date('Y-m-d'));
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 4;
        $stt = 0;
        //begin product to print
        //foreach ($list_product_variants as $row_all) {//san pham
        //$row2 = get_object_vars(json_decode($row_all['product_variants']));

        if ($list_id != false) { // theo list id ben duoi

            $tongtien = 0;

            foreach ($export as $key => $row) {
                $stt++;
                //$row = get_object_vars($row);
                $location = "";
                if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                    $array_location = explode(',', $row['location']);
                    foreach ($array_location as $key => $loca) {
                        $location .= $loca . ' ';
                    }
                    $group = substr($location, 0, 4);
                } else {
                    $location = $row['location'];
                    $group = substr($location, 0, 4);
                }

                $title = $this->m_voxy_package->get_title_productid($row['product_id']);

                $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                if ($check_variant1 == true) {
                    //gia von la gia mua
                    if ($idnew != false) {
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    } else {
                        $giavon = 0;
                    }
                }

                if ($check_variant2 == true) {
                    if ($idnew != false) {
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    } else {
                        $giavon = 0;
                    }
                }
                if ($row['sl_kiemkho'] == "") {
                    $row['sl_kiemkho'] = 0;
                }
                $tongtien += $giavon * (int)$row['sl_kiemkho'];

                if ($check_variant1 == true) {
                    //$get_sl_old = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                    //update soluong vao database
                    $this->m_voxy_package->update_inventory1($row['variant_id'], $row['sl_kiemkho']);
                }

                if ($check_variant2 == true) {
                    //$get_sl_old = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                    //update soluong vao database
                    $this->m_voxy_package->update_inventory2($row['variant_id'], $row['sl_kiemkho']);
                }

                $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $group);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $location);//vitri
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $title);//le
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_kho']);//gia von
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sl_kiemkho']);//thanh tien
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['variant_title']);
                $excel->getActiveSheet()->setCellValue('H' . $numRow, (double)$row['sl_kiemkho'] - (double)$row['sl_kho']);
                $excel->getActiveSheet()->setCellValue('I' . $numRow, $giavon);
                $excel->getActiveSheet()->setCellValue('J' . $numRow, $giavon * $row['sl_kiemkho']);
                $numRow++;
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray2);
            }
            $excel->getActiveSheet()->setCellValue('I' . $numRow, "Tổng tiền: " . $giavon * $row['sl_kiemkho']);
        } else {
            foreach ($export as $key => $row2) {
                foreach ($row2 as $row) {
                    $stt++;
                    //$row = get_object_vars($row);
                    $location = "";
                    if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                        $array_location = explode(',', $row['location']);
                        foreach ($array_location as $key => $loca) {
                            $location .= $loca . ' ';
                            $group = substr($location, 0, 4);
                        }
                    } else {
                        $location = $row['location'];
                        $group = substr($location, 0, 4);
                    }

                    $title = $this->m_voxy_package->get_title_productid($row['product_id']);

                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $group);
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, $location);//vitri
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $title);//le
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_kho']);//gia von
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sl_kiemkho']);//thanh tien
                    $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['variant_title']);
                    $excel->getActiveSheet()->setCellValue('H' . $numRow, (double)$row['sl_kiemkho'] - (double)$row['sl_kho']);
                    $numRow++;
                    //add style
                    $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                    $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
                }
            }
        }

        if (isset($arr_location_after)) {
            foreach ($arr_location_after as $key => $row) {
                $stt++;
                $group = substr($row, 0, 4);
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $group);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row);//vitri
                $excel->getActiveSheet()->setCellValue('D' . $numRow, "");//le
                $excel->getActiveSheet()->setCellValue('E' . $numRow, "");//gia von
                $excel->getActiveSheet()->setCellValue('F' . $numRow, "");//thanh tien
                $excel->getActiveSheet()->setCellValue('G' . $numRow, "");
                $excel->getActiveSheet()->setCellValue('H' . $numRow, "");
                $numRow++;
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);

            }
        }

        //$excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
        //}
        //}
        //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$tongtien);

// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=Baocao-kiemkho' . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function report_products()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_kiemkho');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_xuathang');

        $date1 = $this->input->get('date1');
        $date2 = $this->input->get('date2');
        if($date2 == "" || $date1 == ""){
            die('Ban phai chon ngay');
        }
        $laixe = "";
        //tih toan san pham dc xuat hang theo ngay. laixe = all
        $_all_products = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tong($date1, $date2, $laixe);//bang infor xuathang
        $_all_products_le = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_le($date1, $date2, $laixe); //bang infor xuathang le
        $_all_products_xuattaikho = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_taikho($date1, $date2, $laixe); //bang transfer_outkho//laixe theo id

        $all_products['result_catid'] = array_merge($_all_products['result_catid'], $_all_products_le['result_catid'], $_all_products_xuattaikho['result_catid']);
        ksort($all_products['result_catid']);
        $all_products['export2'] = array_merge($_all_products['export2'], $_all_products_le['export2'], $_all_products_xuattaikho['export2']);
        $all_products['array_note_products'] = array_merge($_all_products['array_note_products'], $_all_products_le['array_note_products']);
//--------------------------------------------------------------------------------------------------------------------------------------
        //loai bo nhung thang giong nhau tang quantity len and variant id
        //$all_products['result_catid'] = array_unique($all_products['result_catid']);//loai bo cate giong nhau
        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($all_products['export2'] as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($all_products['export2'] as $key2 => $item2) {
                if ($key2 > $key) {
                    if (isset($item['variant_id']) && isset($item2['variant_id'])) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            if (isset($item['quantity']) && isset($item2['quantity'])) {
                                $item['quantity'] = $item['quantity'] + $item2['quantity'];
                                $chiso_remove[$key2] = $key2;
                            }
                        }
                    }
                }
            }
            $export2[] = $item;
        }

        //remove nhung thang giong di
        foreach ($export2 as $key => $item) {
            foreach ($chiso_remove as $key_reomove => $item_remove) {
                unset($export2[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        $sorting = "sl_xuat";
        if ($sorting == "location") {
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['location'])) {
                    $row['location'] = "";
                }
                $wek[$key] = $row['location'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_ASC, $export2);
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['quantity'])) {
                    $row['quantity'] = "";
                }
                $wek[$key] = $row['quantity'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }
        $all_products['export2'] = $export2;

        //end tinh toan
        //--------------------------------------------------

        $list_product_variants_kitruoc = $this->m_voxy_kiemkho->get_product_variants_kitruoc_date();

        $list_soluong_hangnhap = $this->m_voxy_kiemkho->get_hangnhap_date($date1, $date2);
        $list_soluong_hangtrave = $this->m_voxy_kiemkho->get_hangve_date($date1, $date2);
        $list_soluong_hangxuat = $export2;

        $list_soluong_hangtralai = $this->m_voxy_kiemkho->get_hangtralai_nhacc_date($date1, $date2);
        $list_soluong_hanghong = $this->m_voxy_kiemkho->get_hanghong_date($date1, $date2);

        $list_product_variants_thucte = $this->m_voxy_kiemkho->get_product_variants_thucte_date($date1, $date2);

        if ($list_product_variants_kitruoc != false) {
            $array_product_variants_kitruoc = array();
            foreach ($list_product_variants_kitruoc as $item) {
                foreach (json_decode($item['variants']) as $row) {
                    $array_product_variants_kitruoc[] = get_object_vars($row);
                }
            }
        }

        if ($list_soluong_hangnhap != false) {
            $array_list_soluong_hangnhap_step1 = array();
            foreach ($list_soluong_hangnhap as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $array_list_soluong_hangnhap_step1[] = get_object_vars($row);
                }
            }
            //cong tong hang nhap
            $array_list_soluong_hangnhap = array();
            $chiso_remove = array();
            foreach ($array_list_soluong_hangnhap_step1 as $key => $item) {
                foreach ($array_list_soluong_hangnhap_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hangnhap[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hangnhap as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hangnhap[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }
        }


        if ($list_soluong_hangtrave != false) {
            $array_list_soluong_hangtrave_step1 = array();
            foreach ($list_soluong_hangtrave as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $array_list_soluong_hangtrave_step1[] = get_object_vars($row);
                }
            }

            //cong tong hang ve
            $array_list_soluong_hangtrave = array();
            $chiso_remove_hangve = array();
            foreach ($array_list_soluong_hangtrave_step1 as $key => $item) {
                foreach ($array_list_soluong_hangtrave_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove_hangve[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hangtrave[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hangtrave as $key => $item) {
                foreach ($chiso_remove_hangve as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hangtrave[$item_remove]);
                    unset($chiso_remove_hangve[$key_reomove]);
                }
            }
        }

        if ($list_soluong_hangxuat != false) {
            $array_list_soluong_hangxuat = array();
            foreach ($list_soluong_hangxuat as $item) {
                $array_list_soluong_hangxuat[] = $item;
            }
            //hang xuat xu ly ben tren roi nen ko can nua
        }

        if ($list_soluong_hangtralai != false) {
            $array_list_soluong_hangtralai_step1 = array();
            foreach ($list_soluong_hangtralai as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $array_list_soluong_hangtralai_step1[] = get_object_vars($row);
                }
            }

            //cong tong hang tralai
            $array_list_soluong_hangtralai = array();
            $chiso_remove_tralai = array();
            foreach ($array_list_soluong_hangtralai_step1 as $key => $item) {
                foreach ($array_list_soluong_hangtralai_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove_tralai[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hangtralai[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hangtralai as $key => $item) {
                foreach ($chiso_remove_tralai as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hangtralai[$item_remove]);
                    unset($chiso_remove_tralai[$key_reomove]);
                }
            }
        }


        if ($list_soluong_hanghong != false) {
            $array_list_soluong_hanghong_step1 = array();
            foreach ($list_soluong_hanghong as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $_row = get_object_vars($row);
                    if($_row['donhang'] != ""){
                        $array_list_soluong_hanghong_step1[] = get_object_vars($row);
                    }
                }
            }

            //cong tong hang tralai
            $array_list_soluong_hanghong = array();
            $chiso_remove_hanghong = array();
            foreach ($array_list_soluong_hanghong_step1 as $key => $item) {
                foreach ($array_list_soluong_hanghong_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove_hanghong[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hanghong[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hanghong as $key => $item) {
                foreach ($chiso_remove_hanghong as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hanghong[$item_remove]);
                    unset($chiso_remove_hanghong[$key_reomove]);
                }
            }

        }

        if ($list_product_variants_thucte != false) {
            $array_list_product_variants_thucte = array();
            foreach ($list_product_variants_thucte as $item) {
                foreach (json_decode($item['variants']) as $row) {
                    $array_list_product_variants_thucte[] = get_object_vars($row);
                }
            }
        }

        $export = array();// xuat ra excel

        foreach ($array_product_variants_kitruoc as $item) {
            $idnew = $this->m_voxy_package->get_id_from_variant($item['v_id']);
            $check_variant1 = $this->m_voxy_package->check_variant1($item['v_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($item['v_id']);

            $infor_product = $this->m_voxy_package->get_all_infor($idnew);
            if($infor_product){
                foreach ($infor_product as $pro) {
                    if ($check_variant1 == true) {
                        $item['sku'] = $pro['sku1'];
                    }
                    if ($check_variant2 == true) {
                        $item['sku'] = $pro['sku2'];
                    }
                    $item['title'] = $pro['title'];
                }
            }else{
                $item['title'] = "";
                $item['sku'] = "";
            }


            if (isset($array_list_soluong_hangnhap)) {
                foreach ($array_list_soluong_hangnhap as $item_nhap) {
                    if ($item_nhap['variant_id'] == $item['v_id']) {
                        $item['sl_nhap'] = $item_nhap['sl_nhap'];
                    }
                }
            }

            if (isset($array_list_soluong_hangtrave)) {
                foreach ($array_list_soluong_hangtrave as $item_trave) {
                    if ($item_trave['variant_id'] == $item['v_id']) {
                        $item['sl_trave'] = $item_trave['sl_nhap'];
                    }
                }
            }

            if (isset($array_list_soluong_hangxuat)) {
                foreach ($array_list_soluong_hangxuat as $item_xuat) {
                    if(isset($item_xuat['variant_id'])){
                        if ($item_xuat['variant_id'] == $item['v_id']) {
                            $item['sl_xuat'] = $item_xuat['quantity'];
                        }
                    }
                }
            }

            if (isset($array_list_soluong_hangtralai)) {
                foreach ($array_list_soluong_hangtralai as $item_tralai_nhacc) {
                    if ($item_tralai_nhacc['variant_id'] == $item['v_id']) {
                        $item['sl_tralai_nhacc'] = $item_tralai_nhacc['quantity'];
                    }
                }
            }

            if (isset($array_list_soluong_hanghong)) {
                foreach ($array_list_soluong_hanghong as $item_hong) {
                    if ($item_hong['variant_id'] == $item['v_id']) {
                        $item['sl_hong'] = $item_hong['sl_nhap'];
                    }
                }
            }

            if (isset($array_list_product_variants_thucte)) {
                foreach ($array_list_product_variants_thucte as $item_thucte) {
                    if ($item_thucte['v_id'] == $item['v_id']) {
                        $item['sl_thucte'] = $item_thucte['sl'];
                    }
                }
            }
            $export[] = $item;
        }

//Khởi tạo đối tượng
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle("Danh sach san pham  kiem kho");

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        //$excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
            ));
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray2);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        //$excel->getActiveSheet()->setCellValue('A1', 'Location');

        $excel->getActiveSheet()->setCellValue('A1', 'STT');
        $excel->getActiveSheet()->setCellValue('B1', 'SKU');
        $excel->getActiveSheet()->setCellValue('C1', 'Tên hàng');
        $excel->getActiveSheet()->setCellValue('D1', 'SL cuối kì trước');
        $excel->getActiveSheet()->setCellValue('E1', 'SL nhập');//le
        $excel->getActiveSheet()->setCellValue('F1', 'Sl trả về');
        $excel->getActiveSheet()->setCellValue('G1', 'Sl xuất kho');
        $excel->getActiveSheet()->setCellValue('H1', 'Sl trả về nhacc');
        $excel->getActiveSheet()->setCellValue('I1', 'SL hàng hỏng');
        $excel->getActiveSheet()->setCellValue('J1', 'Sl Nên Có');
        $excel->getActiveSheet()->setCellValue('K1', 'Sl HIỆN TẠI');
        $excel->getActiveSheet()->setCellValue('L1', 'Chênh lệch');
        $excel->getActiveSheet()->setCellValue('M1', 'Lý do');

// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 2;
        $stt = 0;
        //begin product to print
        //foreach ($list_product_variants as $row_all) {//san pham
        //$row2 = get_object_vars(json_decode($row_all['product_variants']));

        foreach ($export as $key => $row) {
            $stt++;

            if (!isset($row['sl_nhap'])) {
                $row['sl_nhap'] = 0;
            }

            if (!isset($row['sl_trave'])) {
                $row['sl_trave'] = 0;
            }

            if (!isset($row['sl_xuat'])) {
                $row['sl_xuat'] = 0;
            }

            if (!isset($row['sl_tralai_nhacc'])) {
                $row['sl_tralai_nhacc'] = 0;
            }

            if (!isset($row['sl_hong'])) {
                $row['sl_hong'] = 0;
            }

            if (!isset($row['sl_thucte'])) {
                $row['sl_thucte'] = 0;
            }

            $giatri_cuoiki = $row['sl'] + $row['sl_nhap'] + $row['sl_trave'] - $row['sl_xuat'] - $row['sl_tralai_nhacc'] - $row['sl_hong'];

            $check_variant1 = $this->m_voxy_package->check_variant1($row['v_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($row['v_id']);
            if ($check_variant1 == true) {
                $soluong_hientaitrongkho = $this->m_voxy_package->get_quantity_now_variant1($row['v_id']);
            }

            if ($check_variant2 == true) {
                $soluong_hientaitrongkho = $this->m_voxy_package->get_quantity_now_variant2($row['v_id']);
            }

            $chenhlech = $giatri_cuoiki - $soluong_hientaitrongkho;

            $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);//vitri
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);//le
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['sl']);//gia von
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_nhap']);//thanh tien
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sl_trave']);
            $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['sl_xuat']);
            $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['sl_tralai_nhacc']);
            $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['sl_hong']);
            $excel->getActiveSheet()->setCellValue('J' . $numRow, $giatri_cuoiki);
            $excel->getActiveSheet()->setCellValue('K' . $numRow, $soluong_hientaitrongkho);
            $excel->getActiveSheet()->setCellValue('L' . $numRow, $chenhlech);
            $excel->getActiveSheet()->setCellValue('M' . $numRow, "");
            $numRow++;
            //add style
            $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
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

        }


        //$excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
        //}
        //}
        //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$tongtien);

// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=List-kiemkho' . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function report_values()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_kiemkho');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_xuathang');

        $date1 = $this->input->get('date1');
        $date2 = $this->input->get('date2');
        if($date2 == "" || $date1 == ""){
            die('Ban phai chon ngay');
        }
        $laixe = "";
        //tih toan san pham dc xuat hang theo ngay. laixe = all
        $_all_products = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tong($date1, $date2, $laixe);//bang infor xuathang
        $_all_products_le = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_le($date1, $date2, $laixe); //bang infor xuathang le
        $_all_products_xuattaikho = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_taikho($date1, $date2, $laixe); //bang transfer_outkho//laixe theo id

        $all_products['result_catid'] = array_merge($_all_products['result_catid'], $_all_products_le['result_catid'], $_all_products_xuattaikho['result_catid']);
        ksort($all_products['result_catid']);
        $all_products['export2'] = array_merge($_all_products['export2'], $_all_products_le['export2'], $_all_products_xuattaikho['export2']);
        $all_products['array_note_products'] = array_merge($_all_products['array_note_products'], $_all_products_le['array_note_products']);
//--------------------------------------------------------------------------------------------------------------------------------------
        //loai bo nhung thang giong nhau tang quantity len and variant id
        //$all_products['result_catid'] = array_unique($all_products['result_catid']);//loai bo cate giong nhau
        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($all_products['export2'] as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($all_products['export2'] as $key2 => $item2) {
                if ($key2 > $key) {
                    if (isset($item['variant_id']) && isset($item2['variant_id'])) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            if (isset($item['quantity']) && isset($item2['quantity'])) {
                                $item['quantity'] = (int)$item['quantity'] + (int)$item2['quantity'];
                                $chiso_remove[$key2] = $key2;//index of same product and then remove it
                            }
                        }
                    }
                }
            }
            $export2[] = $item;
        }

        //remove nhung thang giong di
        foreach ($export2 as $key => $item) {
            foreach ($chiso_remove as $key_reomove => $item_remove) {
                unset($export2[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        $sorting = "sl_xuat";
        if ($sorting == "location") {
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['location'])) {
                    $row['location'] = "";
                }
                $wek[$key] = $row['location'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_ASC, $export2);
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['quantity'])) {
                    $row['quantity'] = "";
                }
                $wek[$key] = $row['quantity'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }

        if ($export2 != false) {
            $total_price_sL_hangxuat = 0;
            foreach ($export2 as $item) {
                //foreach (json_decode($item['product_variants']) as $row) {
                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
                if (isset($row['variant_id']) && $row['variant_id'] != "") {
                    $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                } else {
                    $idnew = false;
                }

                if ($check_variant1 == true) {
                    //gia von la gia mua
                    if ($idnew != false) {
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    } else {
                        $giavon = 0;
                    }

                }
                if ($check_variant2 == true) {
                    if ($idnew != false) {
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    } else {
                        $giavon = 0;
                    }
                }
                $total_price_sL_hangxuat += $item['quantity'] * $giavon;
            }
        }
        //end tinh toan

        $total_kitruoc = $this->m_voxy_kiemkho->get_total_product_variants_kitruoc_date();
        $total_hangnhap = $this->m_voxy_kiemkho->get_total_hangnhap_date($date1, $date2);
        $total_hangtrave = $this->m_voxy_kiemkho->get_total_hangve_date($date1, $date2);

        $total_hangtralai = $this->m_voxy_kiemkho->get_total_hangtralai_nhacc_date($date1, $date2);
        $total_hanghong = $this->m_voxy_kiemkho->get_total_hanghong_date($date1, $date2);
        $total_thucte = $this->m_voxy_kiemkho->get_total_product_variants_thucte_date($date1, $date2);

//Khởi tạo đối tượng
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle("Danh sach san pham  kiem kho");

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        //$excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
            ));
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray2);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        //$excel->getActiveSheet()->setCellValue('A1', 'Location');

        $excel->getActiveSheet()->setCellValue('A1', 'STT');
        $excel->getActiveSheet()->setCellValue('B1', 'GT cuối kì trước');
        $excel->getActiveSheet()->setCellValue('C1', 'GT nhập');//le
        $excel->getActiveSheet()->setCellValue('D1', 'GT trả về');
        $excel->getActiveSheet()->setCellValue('E1', 'GT xuất kho');
        $excel->getActiveSheet()->setCellValue('F1', 'GT trả về nhacc');
        $excel->getActiveSheet()->setCellValue('G1', 'GT hàng hỏng');
        $excel->getActiveSheet()->setCellValue('H1', 'GT Thực Tế Kiểm');
        $excel->getActiveSheet()->setCellValue('I1', 'Chênh lệch');
        $excel->getActiveSheet()->setCellValue('J1', 'Lý do');

// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 2;
        //begin product to print
        //foreach ($list_product_variants as $row_all) {//san pham
        //$row2 = get_object_vars(json_decode($row_all['product_variants']));
        if ($total_hangnhap == false) {
            $total_hangnhap = 0;
        }

        if ($total_hangtrave == false) {
            $total_hangtrave = 0;
        }

        if ($total_price_sL_hangxuat == false) {
            $total_price_sL_hangxuat = 0;
        }
        if ($total_hangtralai == false) {
            $total_hangtralai = 0;
        }

        if ($total_hanghong == false) {
            $total_hanghong = 0;
        }

        $chenhlech = $total_kitruoc + $total_hangnhap + $total_hangtrave - $total_price_sL_hangxuat - $total_hangtralai - $total_hanghong;

        $excel->getActiveSheet()->setCellValue('A' . $numRow, 1);
        $excel->getActiveSheet()->setCellValue('B' . $numRow, $total_kitruoc);//vitri
        $excel->getActiveSheet()->setCellValue('C' . $numRow, $total_hangnhap);//le
        $excel->getActiveSheet()->setCellValue('D' . $numRow, $total_hangtrave);//gia von
        $excel->getActiveSheet()->setCellValue('E' . $numRow, $total_price_sL_hangxuat);//thanh tien
        $excel->getActiveSheet()->setCellValue('F' . $numRow, $total_hangtralai);
        $excel->getActiveSheet()->setCellValue('G' . $numRow, $total_hanghong);
        $excel->getActiveSheet()->setCellValue('H' . $numRow, $total_thucte);
        $excel->getActiveSheet()->setCellValue('I' . $numRow, $chenhlech);
        $excel->getActiveSheet()->setCellValue('J' . $numRow, "");
        $numRow++;
        //add style
        $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
        $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray2);

        //$excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
        //}
        //}
        //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$tongtien);

// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=List-kiemkho' . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    //san pham k dc kiem kho, chung to la 0 , nen se cap nhat la 0
    public function update_products_rest()
    {
        $this->load->model('m_voxy_kiemkho');
        $this->load->model('m_voxy_package');
        $list_id = $this->input->get('list_id');
        $arr_list_id = explode(",", $list_id);
        if($list_id == "" || $list_id == null){
            die("ban phai chon ID truoc, sau do moi nhan hanh dong nay");
        }
        $list_product_kiemkho = $this->m_voxy_kiemkho->get_products_kiemkho($arr_list_id);
        $array_list_kiemkho = array();
        foreach ($list_product_kiemkho as $item) {
            $__item = get_object_vars(json_decode($item['product_variants']));
            foreach ($__item as $pro) {
                $array_list_kiemkho[] = get_object_vars($pro);
            }
        }
        //cong tong vao for cho nhanh
        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($array_list_kiemkho as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($array_list_kiemkho as $key2 => $item2) {
                if ($key2 > $key) {
                    if (isset($item['variant_id']) && isset($item2['variant_id'])) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
            }
            $export2[] = $item;
        }


        //remove nhung thang giong di
        foreach ($export2 as $key => $item) {
            foreach ($chiso_remove as $key_reomove => $item_remove) {
                unset($export2[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }
        //var_dump($export2);die;
        //cong tong vao for cho nhanh

        //get all san pham trong kho hien tai
        $list_product_all_trongkho = $this->m_voxy_package->get_nur_product_inkho();//khoang 786 san pham
        $array_list_double_tronkho1 = array();
        $array_list_double_tronkho2 = array();

        foreach ($list_product_all_trongkho as $item) {
            $save = array();
            $save['variant_id'] = $item['variant1_id'];
            $save['sl'] = $item['inventory_quantity1'];
            $array_list_double_tronkho1[] = $save;

            $save2 = array();
            $save2['variant_id'] = $item['variant2_id'];
            $save2['sl'] = $item['inventory_quantity2'];
            $array_list_double_tronkho2[] = $save2;

        }
        $array_list_double_tronkho = array_merge($array_list_double_tronkho1, $array_list_double_tronkho2);


        $chiso_remove_new = array();
        foreach ($array_list_double_tronkho as $key1 => $in_kho) {
            foreach ($export2 as $key2 => $dakiem) {
                if ($dakiem['variant_id'] == $in_kho['variant_id']) {
                    $chiso_remove_new[$key1] = $in_kho['variant_id'];
                }
            }
        }

        foreach ($array_list_double_tronkho as $key => $item) {
            foreach ($chiso_remove_new as $key_reomove => $item_remove) {
                unset($array_list_double_tronkho[$key_reomove]);
                unset($chiso_remove_new[$key_reomove]);
            }
        }

        //var_dump($array_list_double_tronkho);//list cuoicung khoang 614 san pham
        //die;

        foreach ($array_list_double_tronkho as $item) {//list can update ve 0

            $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
            if ($check_variant1 == true) {
                $this->m_voxy_package->update_inventory1($item['variant_id'], 0);
            }

            if ($check_variant2 == true) {
                $this->m_voxy_package->update_inventory2($item['variant_id'], 0);
            }

        }

        //in list excel
        require_once APPPATH . "/third_party/PHPExcel.php";
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle("Danh sach san pham  kiem kho");

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
            ));
        $excel->getActiveSheet()->getStyle('A3:U3')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A4:U4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray2);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        //$excel->getActiveSheet()->setCellValue('A1', 'Location');

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'Tên');
        $excel->getActiveSheet()->setCellValue('C3', 'Sl trước');
        $excel->getActiveSheet()->setCellValue('D3', 'Sl sau');
        $excel->getActiveSheet()->setCellValue('E3', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('F3', 'SKu');

        $excel->getActiveSheet()->setCellValue('B1', 'Báo Cáo Sản Phẩm Không Được Kiểm');
        $excel->getActiveSheet()->setCellValue('B2', date('Y-m-d'));
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 4;
        $stt = 0;
        //begin product to print
        //foreach ($list_product_variants as $row_all) {//san pham
        //$row2 = get_object_vars(json_decode($row_all['product_variants']));

        foreach ($array_list_double_tronkho as $key => $row) {
            $stt++;

            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
            $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
            if($idnew){
                //get all information
                $information = $this->m_voxy_package->get_all_infor($idnew);

                if ($check_variant1 == true) {
                    foreach ($information as $item){
                        $sku = $item['sku1'];
                        $variant_title = $item['option1'];
                        $title = $item['title'];
                    }

                }

                if ($check_variant2 == true) {
                    foreach ($information as $item) {
                        $sku = $item['sku2'];
                        $variant_title = $item['option2'];
                        $title = $item['title'];
                    }
                }


                $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $title);//vitri
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['sl']);//le
                $excel->getActiveSheet()->setCellValue('D' . $numRow, 0);//le
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $variant_title);//le
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $sku);//le
                $numRow++;
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
            }
        }
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=Baocao-sanpham_khongkiem' . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');

    }

    //sau khi kiem kho, tong hop san pham, update vao database
    public function report_products_check()
    {
        $this->load->model('m_voxy_kiemkho');
        $this->load->model('m_voxy_package');
        $list_id = $this->input->get('list_id');
        $arr_list_id = explode(",", $list_id);
        if($list_id == "" || $list_id == null){
            die("ban phai chon ID truoc, sau do moi nhan hanh dong nay");
        }
        $list_product_kiemkho = $this->m_voxy_kiemkho->get_products_kiemkho($arr_list_id);
        $array_list_kiemkho = array();
        foreach ($list_product_kiemkho as $item) {
            $__item = get_object_vars(json_decode($item['product_variants']));
            foreach ($__item as $pro) {
                $array_list_kiemkho[] = get_object_vars($pro);
            }
        }
        //cong tong vao for cho nhanh

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($array_list_kiemkho as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($array_list_kiemkho as $key2 => $item2) {
                if ($key2 > $key) {
                    if (isset($item['variant_id']) && isset($item2['variant_id'])) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_kiemkho'] = (double)$item['sl_kiemkho'] + (double)$item2['sl_kiemkho'];
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
            }
            $export2[] = $item;
        }


        //remove nhung thang giong di
        foreach ($export2 as $key => $item) {
            foreach ($chiso_remove as $key_reomove => $item_remove) {
                unset($export2[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }
        //cong tong vao for cho nhanh

        //in list excel
        require_once APPPATH . "/third_party/PHPExcel.php";
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle("Danh sach san pham  kiem kho");

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
            ));
        $excel->getActiveSheet()->getStyle('A3:U3')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A4:U4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray2);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        //$excel->getActiveSheet()->setCellValue('A1', 'Location');

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'Tên');
        $excel->getActiveSheet()->setCellValue('C3', 'SKU');
        $excel->getActiveSheet()->setCellValue('D3', 'Location');
        $excel->getActiveSheet()->setCellValue('E3', 'Sl Kho');
        $excel->getActiveSheet()->setCellValue('F3', 'SL Kiểm');
        $excel->getActiveSheet()->setCellValue('G3', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('H3', 'Sai Lệch');
        $excel->getActiveSheet()->setCellValue('I3', 'Giá vốn');
        $excel->getActiveSheet()->setCellValue('J3', 'Thành tiền');


        $excel->getActiveSheet()->setCellValue('B1', 'Báo Cáo Sản Phẩm Đã Được Kiểm');
        $excel->getActiveSheet()->setCellValue('B2', date('Y-m-d'));
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 4;
        $stt = 0;
        //begin product to print
        //foreach ($list_product_variants as $row_all) {//san pham
        //$row2 = get_object_vars(json_decode($row_all['product_variants']));
        $array_save_to_database = array();

        foreach ($export2 as $key => $row) {
            $stt++;
            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);

            $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
            if($idnew){
                if ($check_variant1 == true) {
                    //gia von la gia mua
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    }else{
                        $giavon = 0;
                    }

                }
                if ($check_variant2 == true) {
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    }else{
                        $giavon = 0;
                    }
                }
                $thanhtien = $giavon * $row['sl_kiemkho'];

                //get all information
                $information = $this->m_voxy_package->get_all_infor($idnew);

                foreach ($information as $item){
                    $title = $item['title'];
                }

                $array_save = array();
                $array_save['sl_old'] = $row['sl_kho'];
                $array_save['sl'] = $row['sl_kiemkho'];
                $array_save['v_id'] = $row['variant_id'];
                $array_save_to_database[] = $array_save;

                $location_ex = explode(",", $row['location']);
                $arr_location = array_unique($location_ex);
                $arr_loca_end = implode(",", $arr_location);



                $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $title);//vitri
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['sku']);//le
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $arr_loca_end);//le
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_kho']);//le
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sl_kiemkho']);//le
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['variant_title']);//le
                $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['sailech']);//le
                $excel->getActiveSheet()->setCellValue('I' . $numRow, $giavon);//le
                $excel->getActiveSheet()->setCellValue('J' . $numRow, $thanhtien);//le
                $numRow++;
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray2);
            }
        }

        $json_kitruoc = json_encode($array_save_to_database);
        $insert_id = $this->m_voxy_kiemkho->add_kiemkho_kitruoc($json_kitruoc);//them vao de theo doi ki truoc, ki sau
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=Baocao-sanpham_khongkiem' . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');

    }
    //xuat file de di kiem. roi sau do nhap so luong thuc te vao sau.
    public function xuatfile_kiemkho(){
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_kiemkho');
        $this->load->model('m_voxy_package');
        $this->load->model('m_location');

        $list_id = $this->input->get('list_id');
        $arr_list_id = explode(",", $list_id);

        if ($list_id != false) {  //in theo cac cai don dc tao ben duoi
            $list_product_variants = $this->m_voxy_kiemkho->get_product_variants($arr_list_id);

            $export_step1 = array();
            $i = 0;

            foreach ($list_product_variants as $item) {
                $product_variants = get_object_vars(json_decode(($item['product_variants'])));
                foreach ($product_variants as $key => $row) {
                    $row = get_object_vars($row);

                    /*
                    if (strlen($row['location']) > 10) {//that is 2 positon, we can sort out
                        $location = explode(",", $row['location']);
                        foreach ($location as $local_item) {
                            $i++;
                            $row['location'] = $local_item;
                            $export_step1[$local_item . "-" . $i][] = $row;
                        }
                    } else {
                        $i++;
                        $export_step1[$row['location'] . "-" . $i][] = $row;
                    }
                    */
                    $export_step1[] = $row;
                }
            }

            $export_step2 = array();
            $chiso_remove = array();


            foreach ($export_step1 as $key => $item1) {
                foreach ($export_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item1['variant_id'] == $item2['variant_id']) {
                            if ($item1['sl_kiemkho'] == "") {
                                $item1['sl_kiemkho'] = 0;
                            }

                            if ($item2['sl_kiemkho'] == "") {
                                $item2['sl_kiemkho'] = 0;
                            }
                            $item1['sl_kiemkho'] = $item1['sl_kiemkho'] + $item2['sl_kiemkho'];

                            $item1['location'] = $item1['location'] . "," . $item2['location'];
                            if (strpos($item['location'], "v1") == false || strpos($item['location'], "v2") == false) {
                                $item1['location'] = $item['location'];
                            }else{
                                $location_ex = explode(",", $item1['location']);
                                $arr_location = array_unique($location_ex);
                                $arr_loca_end = implode(",", $arr_location);

                                $idnew = $this->m_voxy_package->get_id_from_variant($item1['variant_id']);
                                $this->m_voxy_package->update_location_variant1($idnew, $arr_loca_end);//to database
                                $item1['location'] = $arr_loca_end;
                                $chiso_remove[$key2] = $key2;
                            }
                        }
                    }
                }

                $export_step2[$key] = $item1;
            }


            //remove nhung thang giong di
            foreach ($export_step2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export_step2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

            $export = $export_step2;

        } else {
            $list_all_location = $this->m_location->get_all_location();
            $arr_location_after = array();
            foreach ($list_all_location as $item) {//check location not used
                $check_used = $this->m_voxy_package->check_location_used($item['name']);
                if ($check_used == false) {
                    $arr_location_after[] = $item['name'];
                }
            }

            $list_product_variants = $this->m_voxy_package->get_all_products_inkho();//dieu kien la vi tri ko duoc null
            //tao 2 san pham co variant
            $list1 = array();
            $list2 = array();
            $count = 0;
            foreach ($list_product_variants as $key => $vari) {
                if ($vari['option1'] != "") {
                    $count++;
                    $list1[$count]['location'] = $vari['location'];
                    $list1[$count]['title'] = $vari['title'];
                    $list1[$count]['sl_kho'] = $vari['inventory_quantity1'];
                    $list1[$count]['sl_kiemkho'] = "";
                    $list1[$count]['variant_title'] = $vari['option1'];
                    $list1[$count]['product_id'] = $vari['id_shopify'];
                }

                if ($vari['option2'] != "") {
                    $count++;
                    $list2[$count]['location'] = $vari['location'];
                    $list2[$count]['title'] = $vari['title'];
                    $list2[$count]['sl_kho'] = $vari['inventory_quantity2'];
                    $list2[$count]['sl_kiemkho'] = "";
                    $list2[$count]['variant_title'] = $vari['option2'];
                    $list2[$count]['product_id'] = $vari['id_shopify'];
                }
            }
            $list_tong = array_merge($list1, $list2);

            $export = array();
            $i = 0;
            foreach ($list_tong as $item) {
                if (strlen($item['location']) > 10) {
                    $location = explode(",", $item['location']);
                    foreach ($location as $local_item) {
                        $i++;
                        $item['location'] = $local_item;
                        $export[$local_item . "-" . $i][] = $item;
                    }
                } else {
                    $i++;
                    $export[$item['location'] . "-" . $i][] = $item;
                }
            }
        }

        ksort($export);

//Khởi tạo đối tượng
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle("Danh sach san pham  kiem kho");

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(80);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        //$excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
            ));
        $excel->getActiveSheet()->getStyle('A3:U3')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray2);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        //$excel->getActiveSheet()->setCellValue('A1', 'Location');

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'Group');
        $excel->getActiveSheet()->setCellValue('C3', 'Vị Trí');
        $excel->getActiveSheet()->setCellValue('D3', 'Tên');
        //$excel->getActiveSheet()->setCellValue('E3', 'Tồn Kho');
        //$excel->getActiveSheet()->setCellValue('F3', 'Thực tế');//le
        $excel->getActiveSheet()->setCellValue('E3', 'Đơn vị');
        //$excel->getActiveSheet()->setCellValue('H3', 'Sai Lệch');
        //$excel->getActiveSheet()->setCellValue('I3', 'Giá vốn');
        //$excel->getActiveSheet()->setCellValue('J3', 'Thành tiền');


        $excel->getActiveSheet()->setCellValue('C1', 'List Kiem Kho');
        $excel->getActiveSheet()->setCellValue('C2', date('Y-m-d'));
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 4;
        $stt = 0;
        //begin product to print
        //foreach ($list_product_variants as $row_all) {//san pham
        //$row2 = get_object_vars(json_decode($row_all['product_variants']));

        if ($list_id != false) { // theo list id ben duoi

            $tongtien = 0;

            foreach ($export as $key => $row) {
                $stt++;
                //$row = get_object_vars($row);
                $location = "";
                if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                    $array_location = explode(',', $row['location']);
                    foreach ($array_location as $key => $loca) {
                        $location .= $loca . ' ';
                    }
                    $group = substr($location, 0, 4);
                } else {
                    $location = $row['location'];
                    $group = substr($location, 0, 4);
                }

                if ($row['product_id'] == "") {
                    $row['sl_kiemkho'] = "";
                    $giavon = 0;
                    $title = "";
                }else{
                    $title = $this->m_voxy_package->get_title_productid($row['product_id']);

                    $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                    $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                    $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);


                    if ($check_variant1 == true) {
                        //gia von la gia mua
                        if ($idnew != false) {
                            $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                        } else {
                            $giavon = 0;
                        }
                    }

                    if ($check_variant2 == true) {
                        if ($idnew != false) {
                            $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                        } else {
                            $giavon = 0;
                        }
                    }
                    if ($row['sl_kiemkho'] == "") {
                        $row['sl_kiemkho'] = 0;
                    }
                    $tongtien += $giavon * (int)$row['sl_kiemkho'];
                }

                $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $group);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $location);//vitri
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $title);//le
                //$excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_kho']);//gia von
                //$excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sl_kiemkho']);//thanh tien
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['variant_title']);
                //$excel->getActiveSheet()->setCellValue('H' . $numRow, (double)$row['sl_kiemkho'] - (double)$row['sl_kho']);
                //$excel->getActiveSheet()->setCellValue('I' . $numRow, $giavon);
                //$excel->getActiveSheet()->setCellValue('J' . $numRow, $giavon * $row['sl_kiemkho']);
                $numRow++;
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
                //$excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
                //$excel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray2);
            }
            //$excel->getActiveSheet()->setCellValue('I' . $numRow, "Tổng tiền: " . $giavon * $row['sl_kiemkho']);
        } else {
            foreach ($export as $key => $row2) {
                foreach ($row2 as $row) {
                    $stt++;
                    //$row = get_object_vars($row);
                    $location = "";
                    if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                        $array_location = explode(',', $row['location']);
                        foreach ($array_location as $key => $loca) {
                            $location .= $loca . ' ';
                            $group = substr($location, 0, 4);
                        }
                    } else {
                        $location = $row['location'];
                        $group = substr($location, 0, 4);
                    }

                    $title = $this->m_voxy_package->get_title_productid($row['product_id']);

                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $group);
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, $location);//vitri
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $title);//le
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_kho']);//gia von
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sl_kiemkho']);//thanh tien
                    $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['variant_title']);
                    $excel->getActiveSheet()->setCellValue('H' . $numRow, (double)$row['sl_kiemkho'] - (double)$row['sl_kho']);
                    $numRow++;
                    //add style
                    $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                    $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                    $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
                }
            }
        }

        if (isset($arr_location_after)) {
            foreach ($arr_location_after as $key => $row) {
                $stt++;
                $group = substr($row, 0, 4);
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $group);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row);//vitri
                $excel->getActiveSheet()->setCellValue('D' . $numRow, "");//le
                $excel->getActiveSheet()->setCellValue('E' . $numRow, "");//gia von
                $excel->getActiveSheet()->setCellValue('F' . $numRow, "");//thanh tien
                $excel->getActiveSheet()->setCellValue('G' . $numRow, "");
                $excel->getActiveSheet()->setCellValue('H' . $numRow, "");
                $numRow++;
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);

            }
        }

        //$excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
        //}
        //}
        //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$tongtien);

// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=List-kiemkho' . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function report_products_theongay()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_kiemkho');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_kiemkho_theongay');

        $date1 = $this->input->get('date1');
        $date2 = $this->input->get('date2');
        if($date2 == "" || $date1 == ""){
            die('Ban phai chon ngay');
        }
        $laixe = "";
        //tih toan san pham dc xuat hang theo ngay. laixe = all
        $_all_products = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tong($date1, $date2, $laixe);//bang infor xuathang
        $_all_products_le = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_le($date1, $date2, $laixe); //bang infor xuathang le
        $_all_products_xuattaikho = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_taikho($date1, $date2, $laixe); //bang transfer_outkho//laixe theo id

        $all_products['result_catid'] = array_merge($_all_products['result_catid'], $_all_products_le['result_catid'], $_all_products_xuattaikho['result_catid']);
        ksort($all_products['result_catid']);
        $all_products['export2'] = array_merge($_all_products['export2'], $_all_products_le['export2'], $_all_products_xuattaikho['export2']);
        $all_products['array_note_products'] = array_merge($_all_products['array_note_products'], $_all_products_le['array_note_products']);
//--------------------------------------------------------------------------------------------------------------------------------------
        //loai bo nhung thang giong nhau tang quantity len and variant id
        //$all_products['result_catid'] = array_unique($all_products['result_catid']);//loai bo cate giong nhau
        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($all_products['export2'] as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($all_products['export2'] as $key2 => $item2) {
                if ($key2 > $key) {
                    if (isset($item['variant_id']) && isset($item2['variant_id'])) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            if (isset($item['quantity']) && isset($item2['quantity'])) {
                                $item['quantity'] = $item['quantity'] + $item2['quantity'];
                                $chiso_remove[$key2] = $key2;
                            }
                        }
                    }
                }
            }
            $export2[] = $item;
        }

        //remove nhung thang giong di
        foreach ($export2 as $key => $item) {
            foreach ($chiso_remove as $key_reomove => $item_remove) {
                unset($export2[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        $sorting = "sl_xuat";
        if ($sorting == "location") {
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['location'])) {
                    $row['location'] = "";
                }
                $wek[$key] = $row['location'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_ASC, $export2);
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['quantity'])) {
                    $row['quantity'] = "";
                }
                $wek[$key] = $row['quantity'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }
        $all_products['export2'] = $export2;

        //end tinh toan
        //--------------------------------------------------

        $array_product_variants_kitruoc_date1 = $this->m_voxy_kiemkho_theongay->get_product_variants_kitruoc_date_new($date1);
        $array_product_variants_kitruoc_date2 = $this->m_voxy_kiemkho_theongay->get_product_variants_kitruoc_date_new($date2);

        $list_soluong_hangnhap = $this->m_voxy_kiemkho->get_hangnhap_date($date1, $date2);
        $list_soluong_hangtrave = $this->m_voxy_kiemkho->get_hangve_date($date1, $date2);
        $list_soluong_hangxuat = $export2;

        $list_soluong_hangtralai = $this->m_voxy_kiemkho->get_hangtralai_nhacc_date($date1, $date2);
        $list_soluong_hanghong = $this->m_voxy_kiemkho->get_hanghong_date($date1, $date2);

        $list_product_variants_thucte = $this->m_voxy_kiemkho->get_product_variants_thucte_date($date1, $date2);

        if ($list_soluong_hangnhap != false) {
            $array_list_soluong_hangnhap_step1 = array();
            foreach ($list_soluong_hangnhap as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $array_list_soluong_hangnhap_step1[] = get_object_vars($row);
                }
            }
            //cong tong hang nhap
            $array_list_soluong_hangnhap = array();
            $chiso_remove = array();
            foreach ($array_list_soluong_hangnhap_step1 as $key => $item) {
                foreach ($array_list_soluong_hangnhap_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hangnhap[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hangnhap as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hangnhap[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }
        }


        if ($list_soluong_hangtrave != false) {
            $array_list_soluong_hangtrave_step1 = array();
            foreach ($list_soluong_hangtrave as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $array_list_soluong_hangtrave_step1[] = get_object_vars($row);
                }
            }

            //cong tong hang ve
            $array_list_soluong_hangtrave = array();
            $chiso_remove_hangve = array();
            foreach ($array_list_soluong_hangtrave_step1 as $key => $item) {
                foreach ($array_list_soluong_hangtrave_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove_hangve[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hangtrave[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hangtrave as $key => $item) {
                foreach ($chiso_remove_hangve as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hangtrave[$item_remove]);
                    unset($chiso_remove_hangve[$key_reomove]);
                }
            }
        }

        if ($list_soluong_hangxuat != false) {
            $array_list_soluong_hangxuat = array();
            foreach ($list_soluong_hangxuat as $item) {
                $array_list_soluong_hangxuat[] = $item;
            }
            //hang xuat xu ly ben tren roi nen ko can nua
        }

        if ($list_soluong_hangtralai != false) {
            $array_list_soluong_hangtralai_step1 = array();
            foreach ($list_soluong_hangtralai as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $array_list_soluong_hangtralai_step1[] = get_object_vars($row);
                }
            }

            //cong tong hang tralai
            $array_list_soluong_hangtralai = array();
            $chiso_remove_tralai = array();
            foreach ($array_list_soluong_hangtralai_step1 as $key => $item) {
                foreach ($array_list_soluong_hangtralai_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove_tralai[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hangtralai[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hangtralai as $key => $item) {
                foreach ($chiso_remove_tralai as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hangtralai[$item_remove]);
                    unset($chiso_remove_tralai[$key_reomove]);
                }
            }
        }


        if ($list_soluong_hanghong != false) {
            $array_list_soluong_hanghong_step1 = array();
            foreach ($list_soluong_hanghong as $item) {
                foreach (json_decode($item['product_variants']) as $row) {
                    $_row = get_object_vars($row);
                    if($_row['donhang'] != ""){
                        $array_list_soluong_hanghong_step1[] = get_object_vars($row);
                    }
                }
            }

            //cong tong hang tralai
            $array_list_soluong_hanghong = array();
            $chiso_remove_hanghong = array();
            foreach ($array_list_soluong_hanghong_step1 as $key => $item) {
                foreach ($array_list_soluong_hanghong_step1 as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                            $chiso_remove_hanghong[$key2] = $key2;
                        }
                    }
                }
                $array_list_soluong_hanghong[] = $item;
            }

            //remove nhung thang giong di
            foreach ($array_list_soluong_hanghong as $key => $item) {
                foreach ($chiso_remove_hanghong as $key_reomove => $item_remove) {
                    unset($array_list_soluong_hanghong[$item_remove]);
                    unset($chiso_remove_hanghong[$key_reomove]);
                }
            }

        }

        if ($list_product_variants_thucte != false) {
            $array_list_product_variants_thucte = array();
            foreach ($list_product_variants_thucte as $item) {
                foreach (json_decode($item['variants']) as $row) {
                    $array_list_product_variants_thucte[] = get_object_vars($row);
                }
            }
        }

        $export = array();// xuat ra excel

        foreach ($array_product_variants_kitruoc_date1 as $item) {

            $idnew = $this->m_voxy_package->get_id_from_variant($item['v_id']);
            $check_variant1 = $this->m_voxy_package->check_variant1($item['v_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($item['v_id']);

            $infor_product = $this->m_voxy_package->get_all_infor($idnew);
            if($infor_product){
                foreach ($infor_product as $pro) {
                    if ($check_variant1 == true) {
                        $item['sku'] = $pro['sku1'];
                        $item['variant_title'] = $pro['option1'];
                    }
                    if ($check_variant2 == true) {
                        $item['sku'] = $pro['sku2'];
                        $item['variant_title'] = $pro['option2'];
                    }
                    $item['title'] = $pro['title'];
                }
            }else{
                $item['title'] = "";
                $item['sku'] = "";
            }


            if (isset($array_list_soluong_hangnhap)) {
                foreach ($array_list_soluong_hangnhap as $item_nhap) {
                    if ($item_nhap['variant_id'] == $item['v_id']) {
                        $item['sl_nhap'] = $item_nhap['sl_nhap'];
                    }
                }
            }

            if (isset($array_list_soluong_hangtrave)) {
                foreach ($array_list_soluong_hangtrave as $item_trave) {
                    if ($item_trave['variant_id'] == $item['v_id']) {
                        $item['sl_trave'] = $item_trave['sl_nhap'];
                    }
                }
            }

            if (isset($array_list_soluong_hangxuat)) {
                foreach ($array_list_soluong_hangxuat as $item_xuat) {
                    if(isset($item_xuat['variant_id'])){
                        if ($item_xuat['variant_id'] == $item['v_id']) {
                            $item['sl_xuat'] = $item_xuat['quantity'];
                        }
                    }
                }
            }

            if (isset($array_list_soluong_hangtralai)) {
                foreach ($array_list_soluong_hangtralai as $item_tralai_nhacc) {
                    if ($item_tralai_nhacc['variant_id'] == $item['v_id']) {
                        $item['sl_tralai_nhacc'] = $item_tralai_nhacc['quantity'];
                    }
                }
            }

            if (isset($array_list_soluong_hanghong)) {
                foreach ($array_list_soluong_hanghong as $item_hong) {
                    if ($item_hong['variant_id'] == $item['v_id']) {
                        $item['sl_hong'] = $item_hong['sl_nhap'];
                    }
                }
            }

            if (isset($array_list_product_variants_thucte)) {
                foreach ($array_list_product_variants_thucte as $item_thucte) {
                    if ($item_thucte['v_id'] == $item['v_id']) {
                        $item['sl_thucte'] = $item_thucte['sl'];
                    }
                }
            }
            foreach ($array_product_variants_kitruoc_date2 as $item_date2){
                if(isset($item['v_id'])){
                    if($item['v_id'] == $item_date2['v_id']){
                        $item['sl_date2'] = $item_date2['sl'];
                    }
                }
            }
            $export[] = $item;
        }

//Khởi tạo đối tượng
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle("Danh sach san pham  kiem kho");

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        //$excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
            ));

        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray2);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        //$excel->getActiveSheet()->setCellValue('A1', 'Location');

        $excel->getActiveSheet()->setCellValue('A1', 'STT');
        $excel->getActiveSheet()->setCellValue('B1', 'SKU');
        $excel->getActiveSheet()->setCellValue('C1', 'Tên hàng');
        $excel->getActiveSheet()->setCellValue('D1', $date1);
        $excel->getActiveSheet()->setCellValue('E1', 'SL nhập');//le
        $excel->getActiveSheet()->setCellValue('F1', 'Sl trả về');
        $excel->getActiveSheet()->setCellValue('G1', 'Sl xuất kho');
        $excel->getActiveSheet()->setCellValue('H1', 'Sl trả về nhacc');
        $excel->getActiveSheet()->setCellValue('I1', 'SL hàng hỏng');
        $excel->getActiveSheet()->setCellValue('J1', $date2);
        $excel->getActiveSheet()->setCellValue('K1', 'Chênh lệch');
        $excel->getActiveSheet()->setCellValue('L1', 'Đơn vị');

// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 2;
        $stt = 0;
        //begin product to print
        //foreach ($list_product_variants as $row_all) {//san pham
        //$row2 = get_object_vars(json_decode($row_all['product_variants']));

        foreach ($export as $key => $row) {
            $stt++;

            if (!isset($row['sl_nhap'])) {
                $row['sl_nhap'] = 0;
            }

            if (!isset($row['sl_trave'])) {
                $row['sl_trave'] = 0;
            }

            if (!isset($row['sl_xuat'])) {
                $row['sl_xuat'] = 0;
            }

            if (!isset($row['sl_tralai_nhacc'])) {
                $row['sl_tralai_nhacc'] = 0;
            }

            if (!isset($row['sl_hong'])) {
                $row['sl_hong'] = 0;
            }

            if (!isset($row['sl_thucte'])) {
                $row['sl_thucte'] = 0;
            }

            $giatri_cuoiki = $row['sl'] + $row['sl_nhap'] + $row['sl_trave'] - $row['sl_xuat'] - $row['sl_tralai_nhacc'] - $row['sl_hong'];

            //$check_variant1 = $this->m_voxy_package->check_variant1($row['v_id']);
            //$check_variant2 = $this->m_voxy_package->check_variant2($row['v_id']);
//            if ($check_variant1 == true) {
//                $soluong_hientaitrongkho = $this->m_voxy_package->get_quantity_now_variant1($row['v_id']);
//            }
//
//            if ($check_variant2 == true) {
//                $soluong_hientaitrongkho = $this->m_voxy_package->get_quantity_now_variant2($row['v_id']);
//            }

            $chenhlech = $giatri_cuoiki - $row['sl_date2'];

            $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);//vitri
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);//le
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['sl']);//sl date 1
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_nhap']);//thanh tien
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sl_trave']);
            $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['sl_xuat']);
            $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['sl_tralai_nhacc']);
            $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['sl_hong']);
            $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['sl_date2']);//sl date 2
            $excel->getActiveSheet()->setCellValue('K' . $numRow, $chenhlech);
            $excel->getActiveSheet()->setCellValue('L' . $numRow, $row['variant_title']);
            $numRow++;
            //add style
            $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
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

        }


        //$excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
        //}
        //}
        //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$tongtien);

// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=List-kiemkho' . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }
}