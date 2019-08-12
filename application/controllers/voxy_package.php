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
            "class" => "voxy_package",
            "view" => "voxy_package",
            "model" => "m_voxy_package",
            "object" => " Hàng Hoá"
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
        $this->load->model('m_voxy_category');
        $data['category'] = $this->m_voxy_category->get_category();

        $data['list_status'] = $this->data->arr_status;
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
        $list_field = array('cat_id', 'status');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);

                switch ($value) {
                    case 'cat_id':
                        if ($data['cat_id'] != '') {
                            $where_data['m.cat_id'] = $data['cat_id'];
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
            $json_conds = $this->session->userdata('arr_package_search');
            $json_conds = json_decode($json_conds, TRUE);
            if (count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0) {
                $this->data->custom_conds = $this->get_search_condition();
            } else {
                $this->data->custom_conds = $json_conds;
            }
        }

        parent::ajax_list_data_voxy_package($data);
    }

    public function ajax_list_data_checkhang($data = Array())
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
        $search_string  = $condition['q'];
        $limit          = 20;
        $order = "";
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

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        $this->load->model('m_voxy_category', 'category');
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
            $record->custom_check = "<input type='checkbox' style='width:18px;' name='_e_check_all' data-id='" . $record->$key_table . "' />";

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
                if ($record->expri_day == "null") {
                    $record->expri_day = "";
                }
            }
            if (isset($record->location) && isset($record->location)) {
                //$record->expri_day = date('d-m-Y H:i', intval($record->expri_day));
                if ($record->location == "null") {
                    $record->location = "";
                }
            }
            if (isset($record->cat_id) && isset($record->cat_id)) {
                $record->cat_id = $this->category->get_cat_title($record->cat_id);
            }

            if (strlen($record->location) > 11) {//xu ly chuoi location overlengt 12
                $array_location = explode(',', $record->location);
                $location = "";
                if(is_array($array_location)){
                    foreach ($array_location as $key => $loca) {
                        $location .= $loca . '<br>';
                    }
                }
                $record->location = $location;
            }

            if($record->heso_convert != 0 and $record->heso_convert > 0){
                //tinh tu karton sang packung
                if($record->inventory_quantity2 > 0){//neu nho hon thi moi tinh
                    $sl_karton = $record->inventory_quantity2;
                    $sl_packung_old = $record->inventory_quantity1;

                    $sl_packung = $sl_karton * $record->heso_convert;
                    if($sl_packung_old > 0){
                        $sl_packung = $sl_packung +  $sl_packung_old;
                    }
                    $sl_packung = round($sl_packung,2);
                    //$this->m_voxy_package->update_inventory1($record->variant1_id, $sl_packung);
                }
                //tinh tu packung sang karton
                //if($record->inventory_quantity1 > 0){//neu nho hon thi moi tinh
                   // $sl_packung = $record->inventory_quantity1;
                    //$sl_karton = round(($sl_packung / $record->heso_convert),2);
                    //$sl_packung = round($sl_packung,2);
                    //$this->m_voxy_package->update_inventory2($record->variant2_id, $sl_karton);
                //}
            }
        }

        return $record;
    }

    public function add_barcode($data = Array())
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
            $data["title"] = $title = 'Thêm' . $this->name['object'];
        }

        $viewFile = "base_manager/barcode_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'barcode_form.php')) {
            $viewFile = $this->name["view"] . '/' . 'barcode_form';
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

        if (!isset($data['title'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Titel SP  không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        if (!isset($data['inventory_quantity1'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Phải nhập hàng loại option Lẻ";
            echo json_encode($data_return);
            return FALSE;
        }

        $data['title'] = trim($data['title']);

        if (isset($data['location'])) {
            $location = implode(',', $data['location']);
        } else {
            $location = "";
        }

        $data['location'] = $location;
        //option
        $option1 = ($data['option1'] != null && $data['option1'] != "Default Title") ? $data['option1'] : "Packung";
        if ($data['option1'] != null && $data['option1'] == "Default Title") {
            $data['option1'] = "Packung";
        }

        //get id thang cao nhat, sau do gan vao sku
        $id_next_now = $this->data->get_nex_autocriment_id();

        $data['id_shopify'] = "p-".$id_next_now;
        $data['variant1_id'] = "v1-".$id_next_now;
        $data['variant2_id'] = "v2-".$id_next_now;
        //$data['sku1'] = (isset($data['sku1']) && $data['sku1'] != null) ? $data['sku1'] :$id_next_now;
        //option karton
        //$option2 = ($data['option2'] != null) ? $data['option2'] : "Karton";
        $option2 = $data['option2'] ;
        //$data['option2'] = ($data['option2'] != null) ? $data['option2'] : "Karton";
        //$data['sku2'] = (isset($data['sku2']) && $data['sku2'] != null) ? $data['sku2'] :"S".$id_next_now;

        //gia mua sỉ lẻ
        $data['gia_mua_le'] = isset($data['gia_mua_le'])?$data['gia_mua_le']:"";
        $data['gia_mua_si'] = isset($data['gia_mua_si'])?$data['gia_mua_si']:"";

        if($data['heso_gb_si'] != ""){
            $data['si_midest_price'] = $data['gia_mua_si'] / (1 - ($data['heso_gb_si'] / 100));
            $data['si_midest_price'] = round($data['si_midest_price'],2);
        }

        if($data['heso_gb_le'] != ""){
            $data['le_midest_price'] = $data['gia_mua_le'] / (1 - ($data['heso_gb_le'] / 100));
            $data['le_midest_price'] = round($data['le_midest_price'],2);
        }

        if ($option1 == $option2) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "2 Loại sản phẩm phải khác nhau";
            echo json_encode($data_return);
            return FALSE;
        }


        //them data vao database
        $insert_id = $this->data->add($data);

        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            try {
                $this->load->model('m_voxy_package_history', 'package_history');
                $one_history = $this->data->get_one($insert_id, 'object');
                $data_history = array(
                    'pack_code' => $insert_id,
                    'value_old' => '',
                    'value_new' => json_encode($one_history),
                    'action' => 'add_product'
                );
                $this->package_history->add($data_history);

            } catch (Exception $ex) {
                // chi de tranh anh huong den viec gui thong tin ve nguoi dung
            }

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

        //nhung gia tri selected cua thang location
        if (!isset($data["location_selected"])) {

            $data["location_selected"] = $this->data->get_location_selected($id);//tra ve array
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

    public function edit_barcode($id = 0, $data = Array())
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

        //nhung gia tri selected cua thang location
        if (!isset($data["location_selected"])) {

            $data["location_selected"] = $this->data->get_location_selected($id);//tra ve array
        }

        if (!isset($data["title"])) {
            $data["title"] = $title = "Cập nhật" . $this->name["object"];
        }

        $viewFile = "base_manager/barcode_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'barcode_form.php')) {
            $viewFile = $this->name["view"] . '/' . 'barcode_form';
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

        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package');

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
        $data['title'] = trim($data['title']);

        if (isset($data['location'])) {
            $location = implode(',', $data['location']);
        } else {
            $location = "";
        }
        $data['location'] = $location;

        //option
        $option1 = ($data['option1'] != null && $data['option1'] != "Default Title") ? $data['option1'] : "Packung";
        if ($data['option1'] != null && $data['option1'] == "Default Title") {
            $data['option1'] = "Packung";
        }
        $data['sku1'] = (isset($data['sku1']) && $data['sku1'] != null) ? $data['sku1'] : $id;

        //option karton
        $option2 = ($data['option2'] != null) ? $data['option2'] : "";
        $data['option2'] = ($data['option2'] != null) ? $data['option2'] : "";
        $data['sku2'] = (isset($data['sku2']) && $data['sku2'] != null) ? $data['sku2'] : 'S' . $id;

        //gia mua sỉ lẻ
        $data['gia_mua_le'] = isset($data['gia_mua_le'])?$data['gia_mua_le']:0;
        $data['gia_mua_si'] = isset($data['gia_mua_si'])?$data['gia_mua_si']:0;

        if($data['heso_gb_si'] != ""){
            $data['si_midest_price'] = $data['gia_mua_si'] / (1 - ($data['heso_gb_si'] / 100));
            $data['si_midest_price'] = number_format($data['si_midest_price'],2);
        }

        if($data['heso_gb_le'] != ""){
            $data['le_midest_price'] = $data['gia_mua_le'] / (1 - ($data['heso_gb_le'] / 100));
            $data['le_midest_price'] = number_format($data['le_midest_price'],2);
        }

        if ($option1 == $option2) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "2 Loại sản phẩm phải khác nhau";
            echo json_encode($data_return);
            return FALSE;
        }

        $value_old = $this->data->get_one($id, 'object');
        $update = $this->data->update($id, $data);//update to database
        if ($update) {
            try {
                $this->load->model('m_voxy_package_history', 'package_history');

                $data_history = array(
                    'pack_code' => $id,
                    'value_old' => json_encode($value_old),
                    'value_new' => json_encode($this->data->get_one($id, 'object')),
                    'action' => 'edit_product'
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

        if($this->USER->user_name != "tinhcv" || $this->USER->user_name != "long"){
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Bạn không có quyền xóa sản phẩm";
            echo json_encode($data_return);
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
                            'pack_code' => json_encode($list_id),
                            'value_old' => json_encode($one_history),
                            'value_new' => '',
                            'action' => 'delete_product'
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
        $this->load->model('m_voxy_package');

        if (isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            if ($object) {
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    for ($row = 3; $row <= $highestRow; $row++) {
                        //if ($row == 770) {
                          //  die("ko chay san pham nua,xoa thang 500");
                        //}
                        $cat_id = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        if($id != "" && $id != null && $id != false){
                            $product_id = "p-".$worksheet->getCellByColumnAndRow(1, $row)->getValue();
                            $variant1_id = "v1-".$worksheet->getCellByColumnAndRow(1, $row)->getValue();
                            $variant2_id = "v2-".$worksheet->getCellByColumnAndRow(1, $row)->getValue();

                            $sku1 = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                            $title = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                            if($title == null){
                                $title = "##";
                            }
                            $option1 = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                            if($option1 == null){
                                $option1 = "##";
                            }
                            $data_add_database = array(
                                'cat_id' => $cat_id,
                                'id_shopify' => $product_id,
                                'title' => $title,

                                'variant1_id' => $variant1_id,
                                'option1' => $option1,
                                'sku1' => $sku1,

                                'variant2_id' => $variant2_id,
                            );
                            $this->data->add($data_add_database);
                        }
                    }
                }
            }
            //end add product to collection
            echo 'Data Imported successfully';
        }
    }

    //function cho quet barcode nhap san pham
    function getid_product()
    {
        $barcode = $this->input->post('barcode');
        $this->load->model('m_voxy_package');
        $id = $this->m_voxy_package->get_id($barcode);
        echo json_encode($id);
    }

    //transfer
    public function get_product_from_id_products()
    {
        $id_products = $this->input->post('products');
        $id_transfer = $this->input->post('id_transfer');

        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_transfer');

        $data['list_products_old'] = $this->m_voxy_package->get_data_from_id($id_products);

        foreach ($data['list_products_old'] as $item) {
            $get_quantity = $this->m_voxy_transfer->get_quantity($id_transfer);//get product_variants column
            if (isset($get_quantity) && $get_quantity != null) {
                foreach (json_decode($get_quantity) as $item2) {
                    $_item2 = get_object_vars($item2);

                    if ($_item2['id'] == $item['id']) {
                        $item['quantity_packung'] = $_item2['quantity_packung'];
                        $item['quantity_verpackung'] = $_item2['quantity_verpackung'];
                        $item['receive_packung'] = isset($_item2['receive_packung']) ? $_item2['receive_packung'] : "";
                        $item['receive_verpackung'] = isset($_item2['receive_verpackung']) ? $_item2['receive_verpackung'] : "";
                    }
                }
            }
            $data['list_products'][] = $item;
        }

        $data_return = array();
        if (!$data) {
            $data_return["state"] = 0;
            $data_return["msg"] = "";
            $data_return["html"] = "Chưa có sản phẩm nào được chọn";
            echo json_encode($data_return);
            return FALSE;
        } else {
            $viewFile = '/voxy_transfer/form_variants_products';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    //them san pham form xuat hang , tab xuat hang
    public function get_product_from_id_products_xuathang()
    {
        $id_products = $this->input->post('products');
        $id_transfer = $this->input->post('id_transfer');

        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_transfer');

        $data['list_products'] = $this->m_voxy_package->get_data_from_id($id_products);

        $data_return = array();
        if (!$data) {
            $data_return["state"] = 0;
            $data_return["msg"] = "";
            $data_return["html"] = "Chưa có sản phẩm nào được chọn";
            echo json_encode($data_return);
            return FALSE;
        } else {
            $viewFile = '/voxy_package_xuathang/form_variants_products';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
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
            $viewFile = '/voxy_package_xuathang/search_pro';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    function getid_product_from_location()
    {
        $location = $this->input->post('location');
        $this->load->model('m_voxy_package');
        $id = $this->m_voxy_package->getid_product_from_location($location);
        if (is_array($id)) {
            $id_new = array();
            foreach ($id as $item) {
                $id_new[] = $item['id'];
            }
        }
        //$data_return["callback"] = isset($data['callback']) ? $data['callback'] : "get_form_edit_response";
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
        if (!isset($data["list_input"])) {
            $data["list_input"] = $this->_get_form($id);
        }

        if (!isset($data["title"])) {
            $data["title"] = "Những sản phẩm có vị trí : " . $location;
        }

        //$viewFile = "base_manager/list_product";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . 'location' . '/' . 'list_product.php')) {
            $viewFile = 'location/list_product';
        }
        $data["record_data"] = $this->data->get_data_from_id($id_new);

        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);

        if ($this->input->is_ajax_request()) {
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    function get_product_from_multi_location()
    {
        $location = $this->input->post('location');
        $this->load->model('m_voxy_package');
        $id = array();
        $_location = array();
        foreach ($location as $item) {
            $__id = $this->m_voxy_package->getid_product_from_location($item);
            if (is_array($__id)) {
                foreach ($__id as $item2) {
                    $id[] = $item2['id'];
                }
            }

            if ($__id == false) {
                $_location[] = $item;
            }

        }
        $id_new = array_unique($id);

        foreach ($id_new as $key => $item) {
            if ($item == false) {
                unset($id_new[$key]);
            }
        }

        if (file_exists(APPPATH . "views/" . $this->path_theme_view . 'location' . '/' . 'list_product.php')) {
            $viewFile = 'location/list_product_in_default_form';
        }

        if ($this->data->get_data_from_id($id_new) != false) {
            $data["record_data"] = $this->data->get_data_from_id($id_new);
            $data["location_null"] = implode(',', $_location);
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        } else {
            $data["record_data"] = "";
            $data_return["state"] = 0;
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;

        }
    }

    //in list san pham , trang index san pham
    public function export_product_excel()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package');
        $date = date('Y-m-d');

        $export = $this->m_voxy_package->get_all_product();

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

        $excel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font'  => array(
                'size'  => 12,
                'name'  => 'Time New Roman'
            ));
        $excel->getActiveSheet()->getStyle('A1:Y1')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:Y2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A3:Y3')->applyFromArray($styleArray2);
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

        $excel->getActiveSheet()->setCellValue('V2', 'Product ID');
        $excel->getActiveSheet()->setCellValue('W2', 'Variant ID-1');
        $excel->getActiveSheet()->setCellValue('X2', 'Variant ID-2');
        $excel->getActiveSheet()->setCellValue('Y2', 'Cat ID');

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

                $excel->getActiveSheet()->setCellValue('V' . $numRow, $row['id_shopify']);
                $excel->getActiveSheet()->setCellValue('W' . $numRow, $row['variant1_id']);
                $excel->getActiveSheet()->setCellValue('X' . $numRow, $row['variant2_id']);
                $excel->getActiveSheet()->setCellValue('Y' . $numRow, $row['cat_id']);

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

                $excel->getActiveSheet()->getStyle('V')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('W')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('Y')->applyFromArray($styleArray2);
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

    public function export_product_excel_inkho()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package');
        $date = date('Y-m-d');

        $export = $this->m_voxy_package->get_nur_product_inkho();

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
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Time New Roman',
                'color' => array('rgb' => 'FF0000'),
            )
        );

        $styleArray_private = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFEFD5')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleArray_private2 = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ADFF2F')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleArray2 = array(
            'font'  => array(
                'size'  => 12,
                'name'  => 'Time New Roman'
            ));
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Time New Roman'
            ));

        $styleArraycot3 = array(
            'font'  => array(
                'size'  => 13,
                'name'  => 'Time New Roman'
            ));
        $excel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray1);
        $excel->getActiveSheet()->getStyle('C1:I1')->applyFromArray($styleArray_private);
        $excel->getActiveSheet()->getStyle('J1:P1')->applyFromArray($styleArray_private2);
        $excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A3:U3')->applyFromArray($styleArraycot3);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */

        $excel->getActiveSheet()->mergeCells('C1:I1');
        $excel->getActiveSheet()->mergeCells('J1:P1');
        $excel->getActiveSheet()->setCellValue('C1', 'Nhóm Lẻ');
        $excel->getActiveSheet()->setCellValue('J1', 'Nhóm Sỉ');

        $excel->getActiveSheet()->setCellValue('A1', 'Ngày:' . $date);
        $excel->getActiveSheet()->setCellValue('A2', 'Danh mục');
        $excel->getActiveSheet()->setCellValue('B2', 'Tên Sản Phẩm');

        $excel->getActiveSheet()->setCellValue('C2', 'SKU Lẻ');//le
        $excel->getActiveSheet()->setCellValue('D2', 'Variant_le');

        $excel->getActiveSheet()->setCellValue('E2', 'Số lượng lẻ');
        $excel->getActiveSheet()->setCellValue('F2', 'Giá mua lẻ €');
        $excel->getActiveSheet()->setCellValue('G2', 'Giá trị kho');
        $excel->getActiveSheet()->setCellValue('H2', 'Giá bán lẻ €');
        $excel->getActiveSheet()->setCellValue('I2', 'Giá trị bán');

        $excel->getActiveSheet()->setCellValue('J2', 'SKU Sỉ');//si
        $excel->getActiveSheet()->setCellValue('K2', 'Variant_Si');

        $excel->getActiveSheet()->setCellValue('L2', 'Số Lượng Sỉ');
        $excel->getActiveSheet()->setCellValue('M2', 'Giá mua sỉ €');
        $excel->getActiveSheet()->setCellValue('N2', 'Giá trị kho');
        $excel->getActiveSheet()->setCellValue('O2', 'Giá bán sỉ €');
        $excel->getActiveSheet()->setCellValue('P2', 'Giá trị bán');

        //$excel->getActiveSheet()->setCellValue('Q2', 'Vị trí');
        //$excel->getActiveSheet()->setCellValue('R2', 'Keyword');
        //$excel->getActiveSheet()->setCellValue('S2', 'Mwst');

        //$excel->getActiveSheet()->setCellValue('H2', 'Theo Xe');
        //$excel->getActiveSheet()->setCellValue('G1', 'Tổng tiền thêm 5 eu phi shipping: € '.$total_price);
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 3;
        if ($export != null) {
            $this->load->model('m_voxy_category');
            foreach ($export as $row) {
                $cat_title = $this->m_voxy_category->get_cat_title($row['cat_id']);
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $cat_title);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['title']);

                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['sku1']);//le
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['option1']);

                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['inventory_quantity1']);
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['gia_mua_le']);
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['gia_mua_le'] * $row['inventory_quantity1']);
                $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['price1']);
                $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['price1'] * $row['inventory_quantity1']);

                $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['sku2']);//si
                $excel->getActiveSheet()->setCellValue('K' . $numRow, $row['option2']);

                $excel->getActiveSheet()->setCellValue('L' . $numRow, $row['inventory_quantity2']);
                $excel->getActiveSheet()->setCellValue('M' . $numRow, $row['gia_mua_si']);
                $excel->getActiveSheet()->setCellValue('N' . $numRow, $row['gia_mua_si'] * $row['inventory_quantity2']);
                $excel->getActiveSheet()->setCellValue('O' . $numRow, $row['price2']);
                $excel->getActiveSheet()->setCellValue('P' . $numRow, $row['price2'] * $row['inventory_quantity2']);

                //$excel->getActiveSheet()->setCellValue('Q' . $numRow, $row['location']);
                //$excel->getActiveSheet()->setCellValue('R' . $numRow, $row['keyword_si']);
                //$excel->getActiveSheet()->setCellValue('S' . $numRow, $row['mwst']);
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
                //$excel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray2);
                //$excel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray2);
                //$excel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray2);
                $numRow++;
            }
            //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$total_price);
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=SP-' . 'Tonkho-'.$date . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }
}
