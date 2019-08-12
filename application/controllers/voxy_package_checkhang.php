<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package_checkhang extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_package_checkhang",
            "view" => "voxy_package_checkhang",
            "model" => "m_voxy_package_checkhang",
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

        parent::ajax_list_data($data);
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
        }
        return $record;
    }

    public function add_barcode($data = Array())
    {
        //for voxy package
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
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'barcode_form_checkhang.php')) {
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

    public function add_barcode_checkhang($data = Array())
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

        $viewFile = "base_manager/barcode_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'barcode_form_checkhang.php')) {
            $viewFile = $this->name["view"] . '/' . 'barcode_form_checkhang';
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
            $data_return["msg"] = "Phải nhập SL Le";
            echo json_encode($data_return);
            return FALSE;
        }
        if (!isset($data['inventory_quantity2'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Phải nhập SL Si";
            echo json_encode($data_return);
            return FALSE;
        }

        //du lieu post lay dc tu form them
        //$cat_id = $data['cat_id'];
        $title = trim($data['title']);
        $data['title'] = trim($data['title']);
        //$expri_day = $data['expri_day'];

        if (isset($data['location'])) {
            $location = implode(',', $data['location']);
            $data['location'] = $location;
        } else {
            $location = "";
            $data['location'] = $location;
        }

        $description = isset($data['description']) ? $data['description'] : "";
        $product_type = isset($data['product_type']) ? $data['product_type'] : "";
        $vendor = isset($data['vendor']) ? $data['vendor'] : "";
        $data['status'] = isset($data['status']) ? $data['status'] : "";
        //option
        $option1 = ($data['option1'] != null && $data['option1'] != "Default Title") ? $data['option1'] : "Packung";
        if ($data['option1'] != null && $data['option1'] == "Default Title") {
            $data['option1'] = "Packung";
        }

        $price1 = isset($data['price1'])?$data['price1']:0;
        $barcode1 = isset($data['barcode1'])?$data['barcode1']:0;
        //get id thang cao nhat, sau do gan vao sku
        $id_next_now = $this->data->get_nex_autocriment_id();
        $data['sku1'] = (isset($data['sku1']) && $data['sku1'] != null) ? $data['sku1'] : 'P' . $id_next_now;
        $sku1 = $data['sku1'];
        $inventory_quantity1 = $data['inventory_quantity1'];
        //option karton
        $option2 = ($data['option2'] != null) ? $data['option2'] : "Karton";
        $data['option2'] = ($data['option2'] != null) ? $data['option2'] : "Karton";
        $price2 = isset($data['price2'])?$data['price2']:0;
        $barcode2 = isset($data['barcode2'])?$data['barcode2']:0;
        $data['sku2'] = (isset($data['sku2']) && $data['sku2'] != null) ? $data['sku2'] : 'K' . $id_next_now;
        $sku2 = $data['sku2'];
        $inventory_quantity2 = $data['inventory_quantity2'];
        $keyword_si = isset($data['keyword_si']) ? $data['keyword_si'] : "";
        //$cat_id = 91459911769;//grupp linh tinh
        $cat_id = $data['cat_id'];
        $data['status'] = 1;
            //gia mua sỉ lẻ
        $data['gia_mua_le'] = isset($data['gia_mua_le'])?$data['gia_mua_le']:0;
        $data['gia_mua_si'] = isset($data['gia_mua_si'])?$data['gia_mua_si']:0;

        if (isset($option2) && $option2 != null) {
            //if co variants
            $product_data['product'] = array(
                'title' => $title,
                'body_html' => $description,
                'vendor' => $vendor,
                'product_type' => $product_type,
                'tags' => $keyword_si,
                'variants' => [
                    array(
                        'option1' => $option1,
                        //'price' => $price1,
                        'inventory_policy' => 'continue',
                        'inventory_management' => 'shopify',
                        'inventory_quantity' => $inventory_quantity1,
                        'sku' => $sku1,
                        'barcode' => $barcode1,
                    ),
                    array(
                        'option1' => $option2,
                        //'price' => $price2,
                        'inventory_policy' => 'continue',
                        'inventory_management' => 'shopify',
                        'inventory_quantity' => $inventory_quantity2,
                        'sku' => $sku2,
                        'barcode' => $barcode2,
                    )
                ],
            );
        } else {
            // else ko co variants  , chi co 1 san pham duy nhat
            $product_data['product'] = array(
                'title' => $title,
                'body_html' => $description,
                'vendor' => $vendor,
                'product_type' => $product_type,
                'collection_id' => $cat_id,
                'tags' => $keyword_si,
                'variants' => [array(
                    'option1' => $option1,
                    //'price' => $price1,
                    'inventory_policy' => 'continue',
                    'inventory_management' => 'shopify',
                    'inventory_quantity' => $inventory_quantity1,
                    'sku' => $sku1,
                    'barcode' => $barcode1,
                )]
            );
        }

        //day du lieu len may chu
        $data_post = json_encode($product_data);
        $this->load->model('m_voxy_connect_api_tinhcv');
        $result = $this->m_voxy_connect_api_tinhcv->shopify_add_product($data_post);

        //du lieu tra ve sau khi post
        if (!$result) {
            //$this->response(array('status' => 'failed'),200);
            $data_return["state"] = 0; /* state = 1 : insert thành công */
            $data_return["msg"] = "Thêm bản ghi khong thành công vào máy chủ";
        } else {
            //$this->response(array('status' => 'success'), 1709);
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Thêm bản ghi thành công vào máy chủ";
        }

        //them data vao database
        unset($data['inventory_sl']);//loai bo thang nay di moi dung database
        if ($result) {
            $insert_id = $this->data->add($data);
        }

        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            //get id_shopify tu ket qua tra ve , cap nhat lai vao database
            $array = get_object_vars($result["product"]);
            $id_shopify = $array['id'];
            $variants = $array['variants'];//update variant
            if (is_array($variants)) {
                if (isset($array['variants'][0])) {
                    $id_item_variant1 = get_object_vars($array['variants'][0])['id'];
                } else {
                    $id_item_variant1 = "";
                }

                if (isset($array['variants'][1])) {
                    $id_item_variant2 = get_object_vars($array['variants'][1])['id'];
                } else {
                    $id_item_variant2 = "";
                }
            }
            $this->m_voxy_package->update_variant_id_shopify($insert_id, $id_item_variant1, $id_item_variant2);
            $this->m_voxy_package->update_id_shopify($insert_id, $id_shopify);
            //end update id shopify to database

            //add product to a collection
            $_data_add_collection['collect'] = array(
                'product_id' => $id_shopify,
                'collection_id' => $cat_id,
            );
            $data_add_collection = json_encode($_data_add_collection);
            $this->m_voxy_connect_api_tinhcv->shopify_add_product_to_collection($data_add_collection);
            //end add product to collection

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
    {//cho voxy_package
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

        //$viewFile = "base_manager/barcode_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'barcode_form_checkhang.php')) {
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

    public function edit_barcode_checkhang($id = 0, $data = Array())
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
            $data["save_link"] = site_url($this->name["class"] . "/edit_save_checkhang/" . $id);
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

        //$viewFile = "base_manager/barcode_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'barcode_form_checkhang.php')) {
            $viewFile = $this->name["view"] . '/' . 'barcode_form_checkhang';
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
        if (!isset($data['id_shopify'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu id_shopify không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        $data['cat_id'] = intval(trim($data['cat_id']));
        //get data post and edit in shopify before

        //du lieu post lay dc tu form them
        $cat_id = $data['cat_id'];

        $id_shopify = $data['id_shopify'];
        $variant1_id = $data['variant1_id'];
        $variant2_id = $data['variant2_id'];
        $title = trim($data['title']);
        $data['title'] = trim($data['title']);
        //$expri_day = $data['expri_day'];

        if (isset($data['location'])) {
            $location = implode(',', $data['location']);
        } else {
            $location = "";
        }

        $description = isset($data['description']) ? $data['description'] : "";
        $product_type = isset($data['product_type']) ? $data['product_type'] : "";
        $vendor = isset($data['vendor']) ? $data['vendor'] : "";
        //$inventory = $data['inventory'];
        $status = isset($data['status']) ? $data['status'] : "";

        if ($status == 0) {
            $status = true;
            $published = 'published';
        } else {
            $status = false;
            $published = 'unpublished';
        }
        //option
        $option1 = ($data['option1'] != null && $data['option1'] != "Default Title") ? $data['option1'] : "Packung";
        if ($data['option1'] != null && $data['option1'] == "Default Title") {
            $data['option1'] = "Packung";
        }
        $price1 = $data['price1'];
        $barcode1 = $data['barcode1'];
        //get id hien tai, sau do gan vao sku
        $data['sku1'] = (isset($data['sku1']) && $data['sku1'] != null) ? $data['sku1'] : 'P' . $id;
        $sku1 = $data['sku1'];
        $inventory_quantity1 = $data['inventory_quantity1'];
        //option karton
        $option2 = ($data['option2'] != null) ? $data['option2'] : "";
        $data['option2'] = ($data['option2'] != null) ? $data['option2'] : "";
        $price2 = $data['price2'];
        $barcode2 = $data['barcode2'];
        $data['sku2'] = (isset($data['sku2']) && $data['sku2'] != null) ? $data['sku2'] : 'K' . $id;
        $sku2 = $data['sku2'];
        $inventory_quantity2 = $data['inventory_quantity2'];
        $keyword_si = isset($data['keyword_si']) ? $data['keyword_si'] : "";

        //gia mua sỉ lẻ
        $data['gia_mua_le'] = isset($data['gia_mua_le'])?$data['gia_mua_le']:0;
        $data['gia_mua_si'] = isset($data['gia_mua_si'])?$data['gia_mua_si']:0;

        if ($option1 == $option2) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "2 Loại sản phẩm phải khác nhau";
            echo json_encode($data_return);
            return FALSE;
        }

        $product_data['product'] = array(
            'id' => $id_shopify,
            'title' => $title,
            'body_html' => $description,
            'vendor' => $vendor,
            'product_type' => $product_type,
            'published' => $status,
            'published_at' => date('c', strtotime("+5 minutes")),
            'published_scope' => 'global',
            'published_status' => $published,
            'tags' => $keyword_si
        );

        //edit  product to a collection
        if ($id_shopify != "") {
            $collec_id = $this->m_voxy_connect_api_tinhcv->shopify_get_collection_product($id_shopify);
            //$collection_id_old = $this->data->get_old_collection_id($id);
            $result_cat_id = null;
            if (is_array($collec_id) && isset($collec_id['collects'])) {
                foreach ($collec_id['collects'] as $item) {
                    $_item = get_object_vars($item);
                    $this->m_voxy_connect_api_tinhcv->shopify_remove_product_to_collection($_item['id']);
                }
            }
            //end remove alt collection
            $_data_add_collection['collect'] = array(
                'product_id' => $id_shopify,
                'collection_id' => $cat_id,
            );
            $data_add_collection = json_encode($_data_add_collection);
            $this->m_voxy_connect_api_tinhcv->shopify_add_product_to_collection($data_add_collection);
        }
        //end edit product to collection

        if($option1 == ""){//remove option1
            $this->m_voxy_connect_api_tinhcv->shopify_remove_variant_product($id_shopify, $variant1_id);
        }else{
            if (isset($variant1_id) && $id_shopify != "") {
                $variant1['variant'] = array(
                    'id' => $variant1_id,
                    'option1' => $option1,
                    //'price' => $price1,
                    'inventory_policy' => 'continue',
                    'inventory_management' => 'shopify',
                    'inventory_quantity' => $inventory_quantity1,
                    'sku' => $sku1,
                    'barcode' => $barcode1
                );
                $data_edit1 = json_encode($variant1);
                $this->m_voxy_connect_api_tinhcv->shopify_edit_variant_product($variant1_id, $data_edit1);

            }
        }

        if($option2 == ""){//remove option2
            $this->m_voxy_connect_api_tinhcv->shopify_remove_variant_product($id_shopify, $variant2_id);
        }else{
            if (isset($variant2_id) && $id_shopify != "") {
                $variant2['variant'] = array(
                    'id' => $variant2_id,
                    'option1' => $option2,
                    //'price' => $price2,
                    'inventory_policy' => 'continue',
                    'inventory_management' => 'shopify',
                    'inventory_quantity' => $inventory_quantity2,
                    'sku' => $sku2,
                    'barcode' => $barcode2
                );
                $data_edit2 = json_encode($variant2);
                $this->m_voxy_connect_api_tinhcv->shopify_edit_variant_product($variant2_id, $data_edit2);
            }
            if ($variant2_id == "" && $id_shopify != "") {
                $variant2['variant'] = array(
                    'option1' => $option2,
                    //'price' => $price2,
                    'inventory_policy' => 'continue',
                    'inventory_management' => 'shopify',
                    'inventory_quantity' => $inventory_quantity2,
                    'sku' => $sku2,
                    'barcode' => $barcode2
                );
                $data_add2 = json_encode($variant2);
                $this->m_voxy_connect_api_tinhcv->shopify_add_variant_product($id_shopify, $data_add2);
            }
        }

        //day du lieu len shopify
        $data_post = json_encode($product_data);
        if ($id_shopify != "") {
            $result = $this->m_voxy_connect_api_tinhcv->shopify_edit_product($id_shopify, $data_post);
        } else {
            $result = $this->m_voxy_connect_api_tinhcv->shopify_add_product($data_post);
        }

        //du lieu tra ve sau khi post
        if (isset($result)) {
            $data_return["state"] = 0; /* state = 1 : insert không thành công */
            $data_return["msg"] = "Sửa bản ghi không thành công vao shopify, thanh cong chi o Database";
        } else {
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sửa bản ghi thành công vao shopify";
        }
        //end to shopify

        //$value_old = $this->data->get_one($id, 'object');
        unset($data['inventory_sl']);//loai bo thang nay di moi dung database
        if (isset($data['location']) && $data['location'] != null) {
            $data['location'] = implode(',', $data['location']);
        } else {
            $data['location'] = "";
        }

        if ($result["product"]) {
            $array = get_object_vars($result["product"]);
        }

        if ($id_shopify == "") {//neu san pham nao chua co id shopify
            $id_shopify = $array['id'];
            $data['id_shopify'] = $array['id'];
            //update noch mal id shopify neu chua co
            $this->m_voxy_package->update_id_shopify($id, $id_shopify);

            $_data_add_collection['collect'] = array(
                'product_id' => $id_shopify,
                'collection_id' => $cat_id,
            );
            $data_add_collection = json_encode($_data_add_collection);
            $this->m_voxy_connect_api_tinhcv->shopify_add_product_to_collection($data_add_collection);
        }
        $update = $this->data->update($id, $data);//update to database
        //update lai id variant
        if (isset($result["product"])) {
            $variants = $array['variants'];
            if (is_array($variants)) {
                if (isset($array['variants'][0])) {
                    $id_item_variant1 = get_object_vars($array['variants'][0])['id'];
                }

                if (isset($array['variants'][1])) {
                    $id_item_variant2 = get_object_vars($array['variants'][1])['id'];
                } else {
                    $id_item_variant2 = "";
                }
            }
            $this->m_voxy_package->update_variant_id_shopify($id, $id_item_variant1, $id_item_variant2);
        }
        //end
        if ($update) {
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

    public function edit_save_checkhang($id = 0, $data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }

        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_checkhang');

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
        $_id_shopify = $this->m_voxy_package->get_id_shopify($id);
        $id_shopify = $_id_shopify[0]['id_shopify'];
        $variant1_id = $this->m_voxy_package->get_variant1_id_shopify($id);
        $variant2_id = $this->m_voxy_package->get_variant2_id_shopify($id);
        $title = trim($data['title']);
        $cat_id = trim($data['cat_id']);
        $data['title'] = trim($data['title']);

        if (isset($data['location'])) {
            $location = implode(',', $data['location']);
            $data['location'] = $location;
        } else {
            $location = "";
            $data['location'] = $location;
        }
        $inventory_quantity1 = ($data['inventory_quantity1'] != "") ? $data['inventory_quantity1'] : 0;
        $inventory_quantity2 = ($data['inventory_quantity2'] != "") ? $data['inventory_quantity2'] : 0;

        if (isset($variant2_id) || isset($variant1_id)) {
            $variant1['variant'] = array(
                'id' => $variant1_id,
                'inventory_quantity' => $inventory_quantity1
            );

            $variant2['variant'] = array(
                'id' => $variant2_id,
                'inventory_quantity' => $inventory_quantity2
            );
        }
        $data_edit1 = json_encode($variant1);
        $data_edit2 = json_encode($variant2);
        if ($id_shopify != "") {
            $result1 = $this->m_voxy_connect_api_tinhcv->shopify_edit_variant_product($variant1_id, $data_edit1);
            $result2 = $this->m_voxy_connect_api_tinhcv->shopify_edit_variant_product($variant2_id, $data_edit2);
        }

        //edit  product to a collection
        if ($id_shopify != "") {
            $collec_id = $this->m_voxy_connect_api_tinhcv->shopify_get_collection_product($id_shopify);
            //$collection_id_old = $this->data->get_old_collection_id($id);
            $result_cat_id = null;
            if (is_array($collec_id) && isset($collec_id['collects'])) {
                foreach ($collec_id['collects'] as $item) {
                    $_item = get_object_vars($item);
                    $this->m_voxy_connect_api_tinhcv->shopify_remove_product_to_collection($_item['id']);
                }
            }
            //end remove alt collection
            $_data_add_collection['collect'] = array(
                'product_id' => $id_shopify,
                'collection_id' => $cat_id,
            );
            $data_add_collection = json_encode($_data_add_collection);
            $this->m_voxy_connect_api_tinhcv->shopify_add_product_to_collection($data_add_collection);
        }
        //end edit product to collection

        $data_edit_package = array();
        $data_edit_package['cat_id'] = $cat_id;
        $data_edit_package['title'] = $title;
        $data_edit_package['location'] = $location;
        $data_edit_package['inventory_quantity1'] = $inventory_quantity1;
        $data_edit_package['inventory_quantity2'] = $inventory_quantity2;
        $update = $this->m_voxy_package->update($id, $data_edit_package); //update data in databse voxy_package

        $data_history = array();
        $data_history['title'] = $title;
        $data_history['id_product'] = $id;
        $data_history['location'] = $location;
        $data_history['inventory_quantity1'] = $inventory_quantity1;
        $data_history['inventory_quantity2'] = $inventory_quantity2;
        $this->m_voxy_package_checkhang->add_checkhang($data_history); //add history to database table checkhang

        if ($update) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            if (isset($result1) || isset($result2)) {
                $data_return["msg"] = "Sửa bản ghi thành công database and he thong may chu";
            } else {
                $data_return["msg"] = "Sửa bản ghi thành công nur database !";
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

    public function get()
    {
        //get kunden in shopify    per function shopify_get_kunden
        $id_shopify = $this->input->post("id_shopify");
        $this->load->model("m_voxy_package");
        $this->load->model('m_voxy_connect_api_tinhcv');
        $result = $this->m_voxy_connect_api_tinhcv->shopify_get_products($id_shopify);

        //if result ok , now add data to system quan ly kho
        if (isset($result['errors']) || isset($result['error_message']) || $result['products'] == null) {
            $data_return["state"] = 0; /* state = 0 : error */
            $data_return["msg"] = "Get bản ghi không thành công trên hệ thống  may chu";
            echo json_encode($data_return);
            return FALSE;
        } else {
            //them data vao database
            $data_add = array();
            foreach ($result['products'] as $key2 => $_item) {
                $item = get_object_vars($_item);
                $data_add[$key2]['id_shopify'] = $item['id'];
                // get collection id  chua dc thanh cong
//                if (isset($item['collection_id'])) {
//                    $data_add[$key2]['cat_id'] = $item['collection_id'];
//                } else {
//                    $collection_id_array = $this->m_voxy_connect_api_tinhcv->shopify_get_collection_id($item['id']);
//                    if(isset($collection_id_array)) {
//                        $data_add[$key2]['cat_id'] = get_object_vars($collection_id_array['collects'][0])['collection_id'];
//                    }else {
//                        $data_add[$key2]['cat_id'] = "";
//                    }
//                }

                $data_add[$key2]['title'] = $item['title'];
                $data_add[$key2]['created_at'] = $item['created_at'];
                $data_add[$key2]['description'] = $item['body_html'];
                $data_add[$key2]['product_type'] = $item['product_type'];
                $data_add[$key2]['vendor'] = $item['vendor'];
                $data_add[$key2]['status'] = 1;
                if (isset($item['variants'])) {
                    $variant_0 = null;
                    $variant_1 = null;
                    if (isset($item['variants'][0]) && $item['variants'][0] != null) {
                        $variant_0 = get_object_vars($item['variants'][0]);
                    }

                    if (isset($item['variants'][1]) && $item['variants'][1] != null) {
                        $variant_1 = get_object_vars($item['variants'][1]);
                    }

                    if ($variant_0 != null) {
                        $data_add[$key2]['variant1_id'] = $variant_0['id'];
                        $data_add[$key2]['option1'] = $variant_0['title'];
                        $data_add[$key2]['price1'] = $variant_0['price'];
                        $data_add[$key2]['barcode1'] = $variant_0['barcode'];
                        $data_add[$key2]['sku1'] = $variant_0['sku'];
                        $data_add[$key2]['inventory_quantity1'] = $variant_0['inventory_quantity'];
                    }

                    if ($variant_1 != null) {
                        //opiton2 karton
                        $data_add[$key2]['variant2_id'] = $variant_1['id'];
                        $data_add[$key2]['option2'] = $variant_1['title'];
                        $data_add[$key2]['price2'] = $variant_1['price'];
                        $data_add[$key2]['barcode2'] = $variant_1['barcode'];
                        $data_add[$key2]['sku2'] = $variant_1['sku'];
                        $data_add[$key2]['inventory_quantity2'] = $variant_1['inventory_quantity'];
                    }

                    //get id_location vs id_expriday for database from id collection,sau do cap nhat vao database,de sau nay edit dc
//                        $result_location_expriday = $this->m_voxy_connect_api_tinhcv->shopify_get_products_metafield($item['id']);
//                        $value_location = null;
//                        $value_expriday = null;
//                        if (isset($result_location_expriday)) {
//                            foreach ($result_location_expriday['metafields'] as $_item) {
//                                $item = get_object_vars($_item);
//                                if ($item['key'] == 'expri_day') {
//                                    $data_add[$key2]['id_expriday'] = $item['id'];
//                                    $value_expriday = $item['value'];
//                                } elseif ($item['key'] == 'location') {
//                                    $data_add[$key2]['id_location'] = $item['id'];
//                                    $value_location = $item['value'];
//                                } else {
//
//                                }
//                            }
//                        }
//                        $data_add[$key2]['location'] = $value_location;
//                        $data_add[$key2]['expri_day'] = $value_expriday;
                    //end xu ly metafield
                }
            }

            //check product da ton tai, neu ton tai thi update , else add
            foreach ($data_add as $key => $item) {
                if ($this->m_voxy_package->check_id_shopify($item['id_shopify']) == true) {
                    //nur update
                    $id_shopify = $this->m_voxy_package->check_id_shopify($item['id_shopify']);
                    $insert_id = $this->data->update($id_shopify, $data_add[$key]);

                    $data_return["msg"] = "Sửa bản ghi thành công vào database và máy chủ";
                } else {
                    // insert into table
                    $insert_id = $this->data->add($data_add[$key]);
                    $data_return["msg"] = "Thêm bản ghi thành công vào database và máy chủ";
                }
            }
        }
        if ($insert_id) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data_add;
            $data_return["state"] = 1; /* state = 1 : insert thành công */

        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
        }
        echo json_encode($data_return);
        return TRUE;
    }

    function import()
    {
        $this->load->library('excel');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_connect_api_tinhcv');
        if (isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            if ($object) {
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    for ($row = 2; $row <= $highestRow; $row++) {
                        if ($row == 60) {
                            die("ko chay san pham nua");
                        }
                        $sku_le = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $sku_si = "S" . $sku_le;
                        $title = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $cat_id = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $title_le = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $title_si = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $mwst = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $price_le = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                        $price_si = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                        $min_gia_le = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                        $min_gia_si = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                        $match_code = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                        $location = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                        $soluong_le = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                        $barcode_le = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                        $soluong_si = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                        $barcode_si = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                        $expriday = $worksheet->getCellByColumnAndRow(17, $row)->getValue();

                        $data_add_database = array(
                            'cat_id' => $cat_id,
                            'title' => $title,

                            'option1' => $title_le,
                            //'price1' => $price_le,
                            'barcode1' => $barcode_le,
                            'sku1' => $sku_le,
                            'inventory_quantity1' => $soluong_le,

                            'option2' => $title_si,
                            //'price2' => $price_si,
                            'barcode2' => $barcode_si,
                            'sku2' => $sku_si,
                            'inventory_quantity2' => $soluong_si,

                            'mwst' => $mwst,
                            'keyword_si' => $match_code,
                            'le_midest_price' => $min_gia_le,
                            'si_midest_price' => $min_gia_si,
                            'location' => $location,
                            'expri_day' => $expriday,


                        );

                        //data for shopify
                        $product_data['product'] = array(
                            'title' => $title,
                            'published' => true,
                            'published_at' => date('c', strtotime("+99 minutes")),
                            'published_scope' => 'global',
                            'published_status' => true,
                            'tags' => $match_code,
                            'variants' => [
                                array(
                                    'option1' => $title_le,
                                    //'price' => $price_le,
                                    'inventory_policy' => 'continue',
                                    'inventory_management' => 'shopify',
                                    'sku' => $sku_le,
                                    'barcode' => $barcode_le,
                                ),
                                array(
                                    'option1' => $title_si,
                                    //'price' => $price_si,
                                    'inventory_policy' => 'continue',
                                    'inventory_management' => 'shopify',
                                    'sku' => $sku_si,
                                    'barcode' => $barcode_si,
                                )
                            ]
                        );

                        //add vao shopify theo tung array luon
                        $insert_id = $this->data->add($data_add_database); // them data vao DB
                        $data_post = json_encode($product_data);
                        $result = $this->m_voxy_connect_api_tinhcv->shopify_add_product($data_post);

                        // get id_shopify and variant_id
                        if ($result) {
                            $array = get_object_vars($result["product"]);
                            $id_shopify = $array['id'];
                            $variants = $array['variants'];
                            if (is_array($variants)) {
                                $id_item_variant1 = get_object_vars($array['variants'][0])['id'];
                                $id_item_variant2 = get_object_vars($array['variants'][1])['id'];
                            }
                        }

                        $this->m_voxy_package->update_id_shopify($insert_id, $id_shopify);
                        $this->m_voxy_package->update_variant_id_shopify($insert_id, $id_item_variant1, $id_item_variant2);
                        //add product to a collection
                        $_data_add_collection['collect'] = array(
                            'product_id' => $id_shopify,
                            'collection_id' => $cat_id,
                        );
                        $data_add_collection = json_encode($_data_add_collection);
                        $this->m_voxy_connect_api_tinhcv->shopify_add_product_to_collection($data_add_collection);
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
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Sản Phẩm ' . $date);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Ngay:' . $date);
        $excel->getActiveSheet()->setCellValue('A2', 'Tên Sản Phẩm');
        $excel->getActiveSheet()->setCellValue('B2', 'Danh mục');
        $excel->getActiveSheet()->setCellValue('C2', 'Giá Lẻ €');
        $excel->getActiveSheet()->setCellValue('D2', 'Số Lượng Lẻ');
        $excel->getActiveSheet()->setCellValue('E2', 'Barcode Lẻ');
        $excel->getActiveSheet()->setCellValue('F2', 'SKU Lẻ');//het le
        $excel->getActiveSheet()->setCellValue('G2', 'Giá Sỉ €');
        $excel->getActiveSheet()->setCellValue('H2', 'Số Lượng Sỉ');
        $excel->getActiveSheet()->setCellValue('I2', 'Barcode Sỉ');
        $excel->getActiveSheet()->setCellValue('J2', 'SKU Sỉ');//het si
        $excel->getActiveSheet()->setCellValue('K2', 'Vị trí');
        $excel->getActiveSheet()->setCellValue('L2', 'Ngày hết hạn');
        $excel->getActiveSheet()->setCellValue('M2', 'Loại Sản Phẩm');

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
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['price1']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['inventory_quantity1']);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['barcode1']);
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['sku1']);//hetle
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['price2']);
                $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['inventory_quantity2']);
                $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['barcode2']);
                $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['sku2']);//het si
                $excel->getActiveSheet()->setCellValue('K' . $numRow, $row['location']);
                $excel->getActiveSheet()->setCellValue('L' . $numRow, $row['expri_day']);
                $excel->getActiveSheet()->setCellValue('M' . $numRow, $row['product_type']);
                //$excel->getActiveSheet()->setCellValue('H' . $numRow, "dressen");
                $numRow++;
            }
            //$excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$total_price);
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=SP-' . $date . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');

        $data_return = array();
        $data_return['msg'] = "oke in dc roi ";
        echo $data_return;

    }

}
