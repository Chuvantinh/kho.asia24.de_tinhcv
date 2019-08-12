<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_transfer_hanghong_hangve extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_transfer_hanghong_hangve",
            "view" => "voxy_transfer_hanghong_hangve",
            "model" => "m_voxy_transfer_hanghong_hangve",
            "object" => " Hàng Hỏng, Hàng Về, Hàng Thiếu, Hàng Chia Thêm"
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
        $this->load->model('m_voxy_transfer');
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
        $record     = $this->m_voxy_transfer->get_list_table($search_string, $where_condition, $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->m_voxy_transfer->get_list_table($search_string, $where_condition, 0, 0, $order));
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
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'top10_import.php')) {
            $viewFile = $this->name["view"] . '/' . 'table';
        }

        if (isset($this->name["modules"]) && $this->name["modules"]) {
            if (file_exists(APPPATH . "modules/" . $this->name["modules"] . "/views/" . $this->name["view"] . '/' . 'top10_import.php')) {
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
    
    protected function get_search_condition_new($data = array())
    {
        if (!count($data)) {
            $data = $this->input->post();
        }

        $where_data = array();
        $like_data = array();
        $list_field = array('vendor','ngay_giao_hang','ngay_dat_hang', 'status');
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
        $this->load->model('m_voxy_shippers');
        $this->load->model('m_dongxuan_ship_areas');

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
            //var_dump($this->m_voxy_shippers->get_name((int)$record->laixe));die;
            if (isset($record->laixe)) {
                $record->laixe = $this->m_voxy_shippers->get_name((int)$record->laixe);
            }

            if (isset($record->tracking_nummer)) {
                $record->tracking_nummer = $this->m_dongxuan_ship_areas->get_name_tour($record->tracking_nummer);
            }


            if (isset($record->mark_as_complete)) {
                if ($record->mark_as_complete == "0"){
                    $record->mark_as_complete = "null";
                }elseif ($record->mark_as_complete == "all") {
                    $record->mark_as_complete = "Tất cả";
                }elseif ($record->mark_as_complete == "Cho") {
                    $record->mark_as_complete = "Chờ";
                }elseif ($record->mark_as_complete == "Mot Phan") {
                    $record->mark_as_complete = "Một phần";
                } elseif ($record->mark_as_complete == "Hoan Thanh") {
                    $record->mark_as_complete = "Hoàn thành";
                }else {

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
            $data["save_link"]  = site_url($this->name['class'] . '/add_save');
        }
        if (!isset($data["list_input"])) {
            $data["list_input"] = $this->_get_form();
        }
        if (!isset($data["title"])) {
            $data["title"]      = $title = 'Thêm dữ liệu ' . $this->name['object'];
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
            $data_return["state"]   = 1;
            $data_return["html"]    = $content;
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
        $this->load->model('m_voxy_package_orders');

        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if(isset($data["information"])){
            $list_product = array();//xu ly lai san pham add vao database
            $i = 0;
            $tongtien = 0;
            foreach ($data["information"] as $item){
                $i++;
                if (isset($data["quantity"])) {
                    foreach ($data["quantity"] as $key => $item_quantity){
                        if($key == $item['variant_id']){
                            $item["sl_nhap"] = $item_quantity;
                            //$item['thanhtien'] = $item["giatri"] * (int)$item["sl_nhap"];
                            //$list_product[$i] = $item;
                        }
                    }
                }

                if(isset($data['donhang'])){
                    foreach ($data['donhang'] as $key2 => $item_donhang){
                        if($key2 == $item['variant_id']){
                            $item['donhang'] = $item_donhang;
                        }
                    }
                }

                if(isset($data['input_giatri'])){

                    foreach ($data['input_giatri'] as $key => $input_giatri){
                        if($key == $item['variant_id']){
                            $item['thanhtien'] = $input_giatri;
                        }

                    }
                }

                $tongtien += $item['thanhtien'];

                $list_product[$i] = $item;
            }
        }
        //$data['name'] = trim($data['name']);
        if(!isset($list_product)){
            $list_product = "";
        }
        $data['product_variants'] = json_encode($list_product);
        $data['total_price'] = $tongtien;

        unset($data["search_pro"]);
        unset($data["information"]);
        unset($data["quantity"]);
        unset($data["donhang"]);
        unset($data["input_giatri"]);

        $data['date_save'] = date("m-d-Y H:i:s");
        //du lieu post lay dc# tu form them
        $insert_id = $this->data->add($data);

        //add vao bang voxy_package_orders
        if($data['status'] != 5){//not save temporary
            if($data['status'] == 3){
                $column = "hangve";
            }else if($data['status'] == 4){
                $column = "hanghong";
            }else if($data['status'] == 6){
                $column= "hangthieu";
            }else{//7
                $column = "hangthem";
            }

            $export2 = array();
            foreach ($list_product as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                $arr_temporary = array();
                $arr_temporary['variant_id'] = $item['variant_id'];
                $arr_temporary['sl_nhap'] = $item['sl_nhap'];
                $arr_temporary['thanhtien'] = $item['thanhtien'];
                $export2[$item['donhang']][] = $arr_temporary;
            }

            foreach ($export2 as $key => $item){
                $add = json_encode($item);
                $this->m_voxy_package_orders->add_hanghong_hangve($column,$key,$add);
            }
        }
        //end add vao bang voxy_package_orders

        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data;
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            if($data['status'] == 1 || $data['status'] == 3) {//3 la hang tra ve
                $data_return["msg"] = "Cập nhật Thành Công hàng về vào cơ sở dữ liệu";
                foreach ($list_product as $item) { //add inventory
                    if ($item['sl_nhap'] == "") {
                        $item['sl_nhap'] = 0;
                    }
                    $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                    $check_variant1_id = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2_id = $this->m_voxy_package->check_variant2($item['variant_id']);

                    if ($check_variant1_id == true) {
                        $this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
                    }

                    if ($check_variant2_id == true) {
                        $this->m_voxy_package->update_plus_inventory2($item['sl_nhap'], $id);//in DB
                    }
                }
            }else{
                $data_return["msg"] = "Hàng về nhưng KHÔNG được cập nhật vào cơ sở dữ liệu";
            }
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

    public function edit($id = 0, $data = Array())
    {
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

        if (!isset($data["status"])) {
            $data["status"] = $this->data->_get_status($id);
        }

        //nhung gia tri selected cua thang location
        if (!isset($data["products"])) {

            $data["products_history"] = $this->data->get_products_selected($id);//tra ve array
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
        $this->load->model('m_voxy_package_orders');

        $data_return["callback"] = "save_form_edit_response";

        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

//        if (!isset($data['name'])) {
//            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
//            $data_return["msg"] = "Không có dữ liệu khách hàng!";
//            echo json_encode($data_return);
//            return FALSE;
//        }
//
//        if (!isset($data['date'])) {
//            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
//            $data_return["msg"] = "Dữ liệu Ngày không tồn tại!";
//            echo json_encode($data_return);
//            return FALSE;
//        }

        if(isset($data["information"])){
            $list_product = array();//xu ly lai san pham add vao database
            $i = 0;
            $tongtien = 0;
            foreach ($data["information"] as $item){
                $i++;
                if (isset($data["quantity"])) {
                    foreach ($data["quantity"] as $key => $item_quantity){
                        if($key == $item['variant_id']){
                            $item["sl_nhap"] = $item_quantity;
                            //$item['donhang_number'] = $item['donhang'];
                            //$item['thanhtien'] = $item["giatri"] * (int)$item["sl_nhap"];
                            //$tongtien += $item['thanhtien'];
                            //$list_product[$i] = $item;
                        }
                    }
                }

                if(isset($data['donhang'])){
                    foreach ($data['donhang'] as $key2 => $item_donhang){
                        if($key2 == $item['variant_id']){
                            $item['donhang'] = $item_donhang;
                        }
                    }
                }
                if(isset($data['input_giatri'])){

                    foreach ($data['input_giatri'] as $key => $input_giatri){
                        if($key == $item['variant_id']){
                            $item['thanhtien'] = $input_giatri;
                        }

                    }
                }

                $tongtien += $item['thanhtien'];

                $list_product[$i] = $item;
            }
        }
        //$data['name'] = trim($data['name']);
        if(!isset($list_product)){
            $list_product = "";
        }
        $data['product_variants'] = json_encode($list_product);
        $data['total_price'] = $tongtien;

        unset($data["search_pro"]);
        unset($data["information"]);
        unset($data["quantity"]);
        unset($data["donhang"]);
        unset($data["input_giatri"]);

        $data['date_save'] = date("m-d-Y H:i:s");

        //add vao bang voxy_package_orders
        if($data['status'] != 5){//not save temporary
            if($data['status'] == 3){
                $column = "hangve";
            }else if($data['status'] == 4){
                $column = "hanghong";
            }else if($data['status'] == 6){
                $column= "hangthieu";
            }else{//7
                $column = "hangthem";
            }

            $export2 = array();
            foreach ($list_product as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                $arr_temporary = array();
                $arr_temporary['variant_id'] = $item['variant_id'];
                $arr_temporary['sl_nhap'] = $item['sl_nhap'];
                $arr_temporary['thanhtien'] = $item['thanhtien'];
                $export2[$item['donhang']][] = $arr_temporary;
            }

            foreach ($export2 as $key => $item){
                $add = json_encode($item);
                $this->m_voxy_package_orders->add_hanghong_hangve($column,$key,$add);
            }
        }
        //end add vao bang voxy_package_orders

        $update = $this->data->update($id, $data);
        if ($update) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            if($data['status'] == 1 || $data['status'] == 3){//1 nhap kho, 3 hang tra ve
                $data_return["msg"] = "Cập nhật Thành Công hàng về vào cơ sở dữ liệu. Bạn đã chọn NHẬP KHO";
                //cong inventory
                foreach ($list_product as $item){
                    if ($item['sl_nhap'] == "") {
                        $item['sl_nhap'] = 0;
                    }
                    $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                    $check_variant1_id = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2_id = $this->m_voxy_package->check_variant2($item['variant_id']);

                    if ($check_variant1_id == true) {
                        $this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
                    }
                    if ($check_variant2_id == true) {
                        $this->m_voxy_package->update_plus_inventory2($item['sl_nhap'], $id);//in DB
                    }
                }
            }else{
                $data_return["msg"] = "Hàng CHƯA nhập vào kho ";
            }
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
            $data_return["state"]   = 0;
            $data_return["msg"]     = "ID dữ liệu không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        if (!isset($data["save_link"])) {
            $data["save_link"]      = site_url($this->name["class"] . "/edit_save");
        }
        if (!isset($data["list_input"])) {
            $data["list_input"]     = $this->_get_form($id);
        }
        $data["title"]              = $title = "Xem dữ liệu " . $this->name["object"];

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
        $this->load->model('m_voxy_package');
        $text = $this->input->post('request');
        $data['list_products'] = $this->m_voxy_package->get_search_pro($text);

        $data_return = array();
        if ($data['list_products'] == false) {
            $data_return["state"] = 0;
            $data_return["msg"] = "";
            $data_return["html"] = "K tim thay san pham";
            echo json_encode($data_return);
            return FALSE;
        } else {
            $viewFile = '/voxy_transfer_hanghong_hangve/search_pro';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    public function get_price_for_product(){
        $this->load->model('m_voxy_package_orders');
        $variant_id = $this->input->post('variant_id');
        $donhang_nummer = $this->input->post('donhang_nummer');
        $line_items = $this->m_voxy_package_orders->get_price($donhang_nummer);

        if($line_items != false){
            $price = 0;
            foreach (json_decode($line_items) as $item){
                $item2 = get_object_vars($item);
                if((int)$variant_id == (int)$item2['variant_id']){
                    $price = $item2['price'];
                }
            }
        }else{
            $price = 0;
        }

        $data_return = array();
        if ($line_items == false) {
            $data_return["state"] = 0;
            $data_return["msg"] = "";
            $data_return["price"] = $price;
            echo json_encode($data_return);
            return FALSE;
        } else {
            //$viewFile = '/voxy_transfer_hanghong_hangve/search_pro';
            //$content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["price"] = $price;
            echo json_encode($data_return);
            return TRUE;
        }
    }
}