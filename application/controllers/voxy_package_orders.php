<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package_orders
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package_orders extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_package_orders",
            "view" => "voxy_package_orders",
            "model" => "m_voxy_package_orders",
            "object" => "Đơn Hàng"
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
        $this->load->model('m_voxy_category');
        $data['category'] = $this->m_voxy_category->get_category();

        $data['list_status'] = $this->data->arr_status;

        $this->load->model('m_voxy_package_orders');
        $data['shipper'] = $this->m_voxy_package_orders->get_all_shipper_id();
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
        $list_field = array('ngay_dat_hang', 'ngay_giao_hang', 'laixe');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
//                    case 'cat_id':
//                        if ($data['cat_id'] != '') {
//                            $where_data['m.cat_id'] = $data['cat_id'];
//                        }
//                        break;
//                    case 'status':
//                        if ($data['status'] != '') {
//                            $where_data['m.status'] = $data['status'];
//                        }
//                        break;

                    case 'ngay_dat_hang':
                        if ($data['ngay_dat_hang'] != '') {
                            $where_data['m.created_time'] = $data['ngay_dat_hang'];
                        }
                        break;
                    case 'ngay_giao_hang':
                        if ($data['ngay_giao_hang'] != '') {
                            $where_data['m.shipped_at'] = $data['ngay_giao_hang'];
                        }
                        break;
                    case 'laixe':
                        if ($data['laixe'] != "") {
                            $where_data['m.shipper_id'] = $data['laixe'];
                        }
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

    protected function get_search_condition_new($data = array())
    {
        if (!count($data)) {
            $data = $this->input->get();
        }
        $where_data = array();
        $like_data = array();
        $list_field = array('ngay_dat_hang', 'ngay_giao_hang', 'laixe');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                //$data[$value] = trim($data[$value]);
                switch ($value) {
//                    case 'cat_id':
//                        if ($data['cat_id'] != '') {
//                            $where_data['m.cat_id'] = $data['cat_id'];
//                        }
//                        break;
//                    case 'status':
//                        if ($data['status'] != '') {
//                            $where_data['m.status'] = $data['status'];
//                        }
//                        break;

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
                    case 'laixe':
                        if ($data['laixe'] != "") {
                            $where_data['m.laixe'] = $data['laixe'];
                        }
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

        $data_get = $this->input->post();

        if ($data_get && is_array($data_get)) {
            $this->data->custom_conds = $this->get_search_condition($data_get);
        } else {
            $json_conds = $this->session->userdata('arr_package_search');
            $json_conds = json_decode($json_conds, TRUE);

            if (isset($json_conds)) {
                if (count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0) {
                    $this->data->custom_conds = $this->get_search_condition();
                } else {
                    $this->data->custom_conds = $json_conds;
                }
            }
        }

        //var_dump($this->data->custom_conds);die;
        parent::ajax_list_data_orders($data);
    }


    public function ajax_list_data_xuathangtaikho($data = Array())
    {
        $this->load->model('m_voxy_package_orders');
        $data_get = $this->input->get();

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
        $record = $this->m_voxy_package_orders->get_list_table_xuathangtaikho($search_string, $where_condition, $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->m_voxy_package_orders->get_list_table_xuathangtaikho($search_string, $where_condition, 0, 0, $order));
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

        $viewFile = 'voxy_package_xuathang/' . 'table';
        $content = $this->load->view($viewFile, $data, true);

        if ($this->input->is_ajax_request()) {
            //$data_return["callback"]    = "get_manager_data_response";
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    //cha hieu sao cai dieu kien no ko vao sql. the nen phai tao  function nay, eo hieu luon.
    public function ajax_list_data_new($data = Array())
    {
        $this->load->model('m_voxy_package_orders');
        $data_get = $this->input->get();

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
        $record = $this->m_voxy_package_orders->get_list_table($search_string, $where_condition, $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->m_voxy_package_orders->get_list_table($search_string, $where_condition, 0, 0, $order));
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
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'table.php')) {
            $viewFile = $this->name["view"] . '/' . 'table';
        }

        if (isset($this->name["modules"]) && $this->name["modules"]) {
            if (file_exists(APPPATH . "modules/" . $this->name["modules"] . "/views/" . $this->name["view"] . '/' . 'table.php')) {
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

    //xu ly du lieu truoc khi ra table
    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        /* Tùy biến dữ liệu các cột */
        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            $record->custom_action = '';
            if (!isset($record->editable) || (isset($record->editable) && $record->editable)) {
                $record->custom_action .= '<a style="margin-left: 2px !important; margin-right: 2px !important;" class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
                //$record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="'. base_url('htmltopdf/pdf_order')."?order_number=".$record->order_number.'" >PDF</a>';
                $record->custom_action .= '<a style="margin-top: 3px;width: 50px;display: inline-block; margin-left: 2px !important; text-align:center;margin-right: 2px !important;" target="_blank" class="btn-danger" href="' . base_url('htmltopdf/pdf_order_nhathang') . "?order_number=" . $record->order_number . '" >PDF</a>';
                $record->custom_action .= '<a style="margin-top: 3px;width: 50px;display: inline-block; margin-left: 2px !important; text-align:center;margin-right: 2px !important;" target="" class="btn-success" href="' . base_url('voxy_package_orders/excel') . "?order_number=" . $record->order_number . '" >Excel</a>';
                $record->custom_action .= '<a class="do_ghino btn-warning" data-order-number="'.$record->order_number.'" style="text-align:center;margin-top: 3px;width: 50px;display: inline-block; margin-left: 2px !important; margin-right: 2px !important;" class="btn-warning" href="#">Ghi Nợ</a>';
                //$record->custom_action .= '<a class="do_thanhtoan btn-success" data-order-number="'.$record->order_number.'" style="text-align:center;margin-top: 3px;width: 50px;display: inline-block; margin-left: 2px !important; margin-right: 2px !important;" class="btn-success" href="#">Paid</a>';
            }

            $record->custom_check = "<input type='checkbox' style='font-size: 100px;width:20px;' class='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            if ($record->total_price) {
                $record->total_price = "€" . number_format($record->total_price, 2);
            }

            if ($record->total_price_before) {
                $record->total_price_before = "€" . number_format($record->total_price_before, 2);
            }

            $customer = "";
            if ($record->customer) {
                $json_customer = get_object_vars(json_decode($record->customer));
                if (isset($json_customer['d_first_name'])) {
                    $frist_name = $json_customer['d_first_name'];
                } elseif (isset($json_customer['first_name'])) {
                    $frist_name = $json_customer['first_name'];
                } else {
                    $frist_name = "";
                }

                if (isset($json_customer['d_last_name'])) {
                    $last_name = $json_customer['d_last_name'];
                } elseif (isset($json_customer['last_name'])) {
                    $last_name = $json_customer['last_name'];
                } else {
                    $last_name = "";
                }
                $customer = $frist_name . "&nbsp" . $last_name;
            }

            if ($record->key_word_customer == NULL) {
                $record->key_word_customer = $customer;
            }

            if (isset($record->tongtien_no)) {
                if ($record->tongtien_no == "") {
                    $record->tongtien_no = " ";
                }
            }

            if (isset($record->created_time)) {
                $date = date_create($record->created_time);
                $record->_created_time = date_format($date, 'Y-m-d');
                $record->created_time = date_format($date, 'd/m/Y');
            }

            if (isset($record->shipped_at)) {
                $date = date_create($record->shipped_at);
                $record->shipped_at = date_format($date, 'd/m/Y');
            }

            if (isset($record->time_fulfillments) && $record->time_fulfillments != null) {
                $date2 = date_create($record->time_fulfillments);
                $record->_time_fulfillments = date_format($date2, 'Y-m-d');
                $record->time_fulfillments = date_format($date2, 'g:ia on l d/m/Y');
            } else {
                $record->time_fulfillments = "";
            }


            if (isset($record->time_paid) && $record->time_paid != null) {
                $date5 = date_create($record->time_paid);
                $record->time_paid = date_format($date5, 'g:ia on l d/m/Y');
                $record->_time_paid = date_format($date5, 'Y-m-d');
            } else {
                $record->time_paid = "";
            }

            if (isset($record->check_nhat_hang) && $record->check_nhat_hang == 1) {
                $record->check_nhat_hang = "Xong";
            } else {
                $record->check_nhat_hang = "";
            }

            //Ft days
            if (isset($record->_time_fulfillments) && $record->_time_fulfillments != null && isset($record->_time_paid) && $record->_time_paid != null) {
                $datetime1 = date_create($record->_time_paid);
                $datetime2 = date_create($record->_time_fulfillments);
                $interval = date_diff($datetime1, $datetime2);
                $record->time_fulfillments_update = $interval->format('%R%a days full');
            } else {
                $timnow = date('Y-m-d');
                if (isset($record->_time_paid) && $record->_time_paid != null) {
                    $datetime1 = date_create($record->_time_paid);
                    $date_now = date_create($timnow);
                    $interval2 = date_diff($datetime1, $date_now);
                    $record->time_fulfillments_update = $interval2->format('%R%a days paid');
                } else {
                    $datetime1 = date_create($record->_created_time);
                    $date_now = date_create($timnow);
                    $interval2 = date_diff($datetime1, $date_now);
                    $record->time_fulfillments_update = $interval2->format('%R%a days created');
                }

            }

            if (isset($record->time_refund) && $record->time_refund != null) {
                $date3 = date_create($record->time_refund);
                $record->time_refund = date_format($date3, 'g:ia on l d/m/Y');
                $record->_time_refund = date_format($date3, 'Y-m-d');
            } else {
                $record->time_refund = "";
            }

            //refund days
            if (isset($record->_time_refund) && $record->_time_refund != null && isset($record->_time_fulfillments) && $record->_time_fulfillments != null) {
                $datetime3 = date_create($record->_time_fulfillments);
                $datetime4 = date_create($record->_time_refund);
                $interval2 = date_diff($datetime3, $datetime4);
                $record->refund_days = $interval2->format('%R%a days');
            } else {
                $record->refund_days = "";
            }

            if($record->tongtien_no == 0){
                $record->tongtien_no = "";
            }

        }
        return $record;
    }

//    public function get()
//    {
//        $this->load->model('m_voxy_package_orders');
//        $list_id = $this->input->post('list_id');
//        $id_order = array();
//        if($list_id != false){
//            foreach (get_object_vars(json_decode($list_id))['list_id'] as $item) {
//                $id_order[] = $this->m_voxy_package_orders->get_id_order($item);
//
//            }
//        }else{
//            $id_order = false;
//        }
//
//        //get order in shopify       per function shopify_get_orders
//
//        $this->load->model("m_voxy_package_orders");
//        $this->load->model('m_voxy_connect_api_tinhcv');
//        $result = $this->m_voxy_connect_api_tinhcv->shopify_get_orders($id_order);
//
//        //if result ok , now add data to system quan ly kho
//        if (isset($result['errors']) || isset($result['error_message'])) {
//            $data_return["state"] = 0; /* state = 0 : error */
//            $data_return["msg"] = "Get bản ghi không thành công trên hệ thống  onlineshop";
//            echo json_encode($data_return);
//            return FALSE;
//        } else {
//
//            $data = array();
//            if(isset($result['orders'])){
//                foreach ($result['orders'] as $key => $item) {
//                    $_item = get_object_vars($item);
//                    $data[$key] = $_item;
//                }
//            }else {
//                if(isset($result) && is_array($result)){
//                    foreach ($result as $item){
//                        $_item = get_object_vars($item['order']);
//                        $data[] = $_item;
//                    }
//                }
//            }
//
//            //them data vao database
//            $data_add = array();
//            foreach ($data as $key2 => $item) {
//                $data_add[$key2]['id_order'] = $item['id'];
//                $data_add[$key2]['created_time'] = $item['created_at'];
//
//                $data_add[$key2]['order_number'] = $item['order_number'];
//                if(isset($item['customer'])){
//                    $data_add[$key2]['customer'] = json_encode($item['customer']);
//                }
//                $data_add[$key2]['financial_status'] = $item['financial_status'];
//                $data_add[$key2]['fulfillment_status'] = $item['fulfillment_status'] == null ? "Unfulfilled" : $item['fulfillment_status'];
//                $data_add[$key2]['total_price'] = $item['total_price'];
//                //xu ly add them location and expriday for tung thang product
//                foreach ($item['line_items'] as $_item2 ){
//                    $item2 = get_object_vars($_item2);
//                    $_item2->location = $this->m_voxy_package_orders->get_location($item2['product_id']);
//                    $_item2->expri_day = $this->m_voxy_package_orders->get_expriday($item2['product_id']);
//                }
//                //end xu ly
//                $data_add[$key2]['line_items'] = json_encode($item['line_items']);
//                $data_add[$key2]['note'] = isset($item['note']) ? $item['note'] : "null";
//                if(isset($item['shipping_address'])) {
//                    $data_add[$key2]['shipping_address'] = json_encode($item['shipping_address']);
//                }
//                if(isset($item['billing_address'])) {
//                    $data_add[$key2]['billing_address'] = json_encode($item['billing_address']);
//                }
//                $data_add[$key2]['status'] = 0;
//
//                if ($item['closed_at'] != null) {
//                    $data_add[$key2]['status'] = "black";//black
//                } elseif ($item['cancelled_at'] != null) {
//                    $data_add[$key2]['status'] = "red";//red
//                } elseif ($item['cancelled_at'] == null && $item['closed_at'] == null) {
//                    $data_add[$key2]['status'] = "blue";//blue
//                } else {
//                }
//
//                //get shipper_id for oder
//                $data_add[$key2]['shipper_id'] = $this->m_voxy_package_orders->get_shipper_id($item['id']);
//                $data_add[$key2]['shipper_name'] = $this->m_voxy_package_orders->get_name_shipper($data_add[$key2]['shipper_id'] );
//                $data_add[$key2]['shipped_at'] = $this->m_voxy_package_orders->get_shipped_at($item['id']);
//            }
//            //check oder da ton tai, neu ton tai thi update , else add
//            foreach ($data_add as $key => $item) {
//                if ($this->m_voxy_package_orders->get_order_number($item['order_number']) == true) {
//                    //nur update
//                    $id = $this->m_voxy_package_orders->get_order_number($item['order_number']);
//                    $insert_id = $this->data->update($id, $data_add[$key]);
//                    $data_return["msg"] = "sua  bản ghi thành công vào database và shopify";
//                    $data_return["key_name"] = $this->data->get_key_name();
//                } else {
//                    // insert into
//                    $insert_id = $this->data->add($data_add[$key]);
//                    $data_return["msg"] = "Thêm bản ghi thành công vào database và shopify";
//                    $data_return["key_name"] = $this->data->get_key_name();
//                }
//            }
//
////            if ($insert_id) {
////                $data_return["key_name"] = $this->data->get_key_name();
////                $data_return["record"] = $data;
////                $data_return["state"] = 1; /* state = 1 : insert thành công */
////
////            } else {
////                $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
////                $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
////            }
//            $data_return['orders'] = $result;
//            $data_return["state"] = 1; /* state = 1 : ok  */
//            //$data_return["msg"] = "Get bản ghi thanh cong (OK )trên hệ thống shopify  va them vao he thong quan ly kho";
//            echo json_encode($data_return);
//            return TRUE;
//        }
//        //end add data to database
//    }

    public function get()
    {
        $this->load->model("m_voxy_package_xuathang");
        $this->load->model("m_voxy_package_kunden");
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_orders');

        $list_id = $this->input->post('list_id');

        if ($list_id != false) {
            foreach (get_object_vars(json_decode($list_id))['list_id'] as $item) {
                $id_order = $this->m_voxy_package_orders->get_order_number_from_id($item);
            }
        } else {
            $id_order = false;
        }

        if ($id_order != false) {
            $data = $this->m_voxy_package_xuathang->get_order_from_mysql_odernumber($id_order);
        } else {
            $data = $this->m_voxy_package_xuathang->get_order_from_mysql();
        }

        //them data vao database
        $data_add = array();
        if ($data != false) {
            foreach ($data as $key2 => $item) {

                $data_add[$key2]['id_order'] = $item['order_name'];
                $data_add[$key2]['created_time'] = $item['created_at'];
                $data_add[$key2]['order_number'] = $item['local_order_id'];

                $data_add[$key2]['ship_area_id'] = $item['ship_area_id'];

                if (isset($item['customer_id'])) {
                    $data_add[$key2]['customer'] = $this->m_voxy_package_kunden->get_default_address($item['customer_id']);
                }

                if (isset($item['customer_id'])) {
                    $key_word_customer = $this->m_voxy_package_kunden->get_keyword($item['customer_id']);
                }

                $data_add[$key2]['total_price'] = $item['total_price'];
                $data_add[$key2]['total_price_before'] = $item['total_price'];

                $data_add[$key2]['status'] = "blue";

                $array = array();
                $item_local_order_new = str_replace(array("\\", "u005E", "u002C", "u007C", "u0027"), "", $item['local_order']);

                //var_dump($item_local_order_new);die;

                if (json_decode($item_local_order_new) != null) {
                    $tamthoi = get_object_vars(json_decode($item_local_order_new));
                    foreach ($tamthoi['line_items'] as $key => $_item2) {
                        $item3 = get_object_vars($_item2);
                        $_item2->location = $this->m_voxy_package_xuathang->get_location($item3['product_id']);
                        $array[] = $_item2;
                    }
                }

                if ($array) {
                    $data_add[$key2]['line_items'] = json_encode($array);
                }

                //get shipper_id for oder
                $data_add[$key2]['customer_id'] = (int)$item['customer_id'];
                $data_add[$key2]['key_word_customer'] = $key_word_customer;

                $data_add[$key2]['shipper_id'] = $item['shipper_id'];

                $data_add[$key2]['take_by_lan1'] = $item['shipper_id'];
                $data_add[$key2]['take_by_lan2'] = $item['shipper_id'];
                $data_add[$key2]['take_by_lan3'] = $item['shipper_id'];
                $data_add[$key2]['take_by_lan4'] = $item['shipper_id'];
                $data_add[$key2]['take_by_lan5'] = $item['shipper_id'];

                $data_add[$key2]['shipped_at'] = $item['shipped_at'];
                $data_add[$key2]['shipper_name'] = $this->m_voxy_package_xuathang->get_name_shipper($data_add[$key2]['shipper_id']);
            }
        }
        //check oder da ton tai, neu ton tai thi update , else add
        if ($data_add != null) {
            foreach ($data_add as $key => $item) {
                if ($this->m_voxy_package_xuathang->get_order_number($item['order_number']) == true) {
                    //nur update
                    $id = $this->m_voxy_package_xuathang->get_order_number($item['order_number']);

                    $check_edit_kho = $this->m_voxy_package_orders->check_edit_kho($id);

                    if ($check_edit_kho != 1) {

                        $update_id = $this->data->update($id, $data_add[$key]);
                    }

                    $data_return["msg"] = "sua  bản ghi thành công vào database và shopify";
                    $data_return["key_name"] = $this->data->get_key_name();
                } else {//insert
                    $insert_id = $this->data->add($data_add[$key]);
                    $data_return["msg"] = "Thêm bản ghi thành công vào database và shopify";
                    $data_return["key_name"] = $this->data->get_key_name();
                }
            }
        }

        $data_return["state"] = 1; /* state = 1 : ok  */
        $data_return["msg"] = "Get bản ghi thanh cong o mysql";
        echo json_encode($data_return);
        return TRUE;
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

        if (!isset($data["title"])) {
            $data["title"] = $title = "Sửa dữ liệu " . $this->name["object"];
        }

        $data_return["record_data"] = $this->data->get_one($id);

        $oder_number = get_object_vars($data_return["record_data"])['order_number'];
        //hien thi du lieu shipping , cu get the nay la lay dc
        if (get_object_vars($data_return["record_data"])['shipping_address'] != null) {
            $data_decode_shipping = get_object_vars(json_decode(get_object_vars($data_return["record_data"])['shipping_address']));
        } else {
            $data_decode_shipping = "";
        }

        $data_obj = get_object_vars($data_return["record_data"]);

        if (!isset($data["list_input"])) {
            //data pro la list product trong order
            $data_pro = array();
            $this->load->model('m_voxy_package_orders');
            foreach (json_decode($data_obj['line_items']) as $key => $item) {
                //$_item_id = get_object_vars($item)['id'];
                //$item->expri_day = $this->m_voxy_package_orders->get_expriday($_item_id);
                //$item->location = $this->m_voxy_package_orders->get_location($_item_id);
                $item->oder_number = $oder_number;
                $data_pro[$key] = get_object_vars($item);
            }

            $data["list_input"] = $this->_get_form2($id, $data_pro);
            $data['thanhtoan_lan1'] = $data_obj['thanhtoan_lan1'];
            $data['thanhtoan_lan2'] = $data_obj['thanhtoan_lan2'];
            $data['thanhtoan_lan3'] = $data_obj['thanhtoan_lan3'];
            $data['thanhtoan_lan4'] = $data_obj['thanhtoan_lan4'];
            $data['thanhtoan_lan5'] = $data_obj['thanhtoan_lan5'];

            $arr = array();
            foreach ($this->m_voxy_package_orders->get_all_shipper_id() as $item){
                $item = get_object_vars($item);
                $arr[] = $item;
            }
            $data['data_shippers'] = $arr;

            if($data_obj['take_by_lan1'] == 0 || $data_obj['take_by_lan1'] == " "){
                $data['take_by_lan1'] = $data_obj['shipper_id'];
            }else{
                $data['take_by_lan1'] = $data_obj['take_by_lan1'];
            }

            if($data_obj['take_by_lan2'] == 0 || $data_obj['take_by_lan2'] == ""){
                $data['take_by_lan2'] = $data_obj['shipper_id'];
            }else{
                $data['take_by_lan2'] = $data_obj['take_by_lan2'];
            }


            if($data_obj['take_by_lan2'] == 0 || $data_obj['take_by_lan2'] == ""){
                $data['take_by_lan2'] = $data_obj['shipper_id'];
            }else{
                $data['take_by_lan2'] = $data_obj['take_by_lan2'];
            }

            if($data_obj['take_by_lan3'] == 0 || $data_obj['take_by_lan3'] == ""){
                $data['take_by_lan3'] = $data_obj['shipper_id'];
            }else{
                $data['take_by_lan3'] = $data_obj['take_by_lan3'];
            }

            if($data_obj['take_by_lan4'] == 0 || $data_obj['take_by_lan4'] == ""){
                $data['take_by_lan4'] = $data_obj['shipper_id'];
            }else{
                $data['take_by_lan4'] = $data_obj['take_by_lan4'];
            }

            if($data_obj['take_by_lan5'] == 0 || $data_obj['take_by_lan5'] == ""){
                $data['take_by_lan5'] = $data_obj['shipper_id'];
            }else{
                $data['take_by_lan5'] = $data_obj['take_by_lan5'];
            }

            $data['tongtien_no'] = $data_obj['tongtien_no'];
            $data['total_price'] = $data_obj['total_price'];
        }

        //hien thi vao view form
        $viewFile = "base_manager/default_form";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'form.php')) {
            $viewFile = $this->name["view"] . '/' . 'form';
        }

        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);

        $data_return["record_data"] = $data_obj;
        if ($data_decode_shipping != null) {
            $data_return["record_data"]["s_first_name"] = $data_decode_shipping['first_name'];
            $data_return["record_data"]["s_last_name"] = $data_decode_shipping['last_name'];
            $data_return["record_data"]["s_address1"] = $data_decode_shipping['address1'];
            $data_return["record_data"]["s_zip"] = $data_decode_shipping['zip'];
            $data_return["record_data"]["s_city"] = $data_decode_shipping['city'];
            $data_return["record_data"]["s_phone"] = $data_decode_shipping['phone'];
        }

        //hien thi du lieu billing , cu get the nay la lay dc
        if ($data_obj['billing_address'] != null) {
            $data_decode_billing = get_object_vars(json_decode($data_obj['billing_address']));
        } else {
            $data_decode_billing = "";
        }

        if ($data_decode_billing != null) {
            $data_return["record_data"]["b_first_name"] = $data_decode_billing['first_name'];
            $data_return["record_data"]["b_last_name"] = $data_decode_billing['last_name'];
            $data_return["record_data"]["b_address1"] = $data_decode_billing['address1'];
            $data_return["record_data"]["b_zip"] = $data_decode_billing['zip'];
            $data_return["record_data"]["b_city"] = $data_decode_billing['city'];
            $data_return["record_data"]["b_phone"] = $data_decode_billing['phone'];
        }
        if ($this->input->is_ajax_request()) {
            $data_return["state"] = 1;
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
        // day la data for form , header ..
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

        $data_add_database = array();

        if ($data != "" || $data != null) {
            $list_product = array();
            $total_price = 0;

            $hangve = array();
            $hanghong = array();
            $hangthieu = array();
            $hangthem = array();
//            echo "<pre>";
//            var_dump($data["information"]);
//            echo "</pre>";
//            die;
            foreach ($data["information"] as $item) {
                    $sosanh = strstr($item['title'], "#");
                if(strlen($sosanh) > 0){
                    $data_return["state"] = 0;
                    $data_return["msg"] = "Ban Phải thanh đổi tất cả sản phẩm #, sang sản phẩm có trong kho mới Lưu được.Danke ";
                    echo json_encode($data_return);
                    return FALSE;
                }


                if ($item['hangve'] == "") {
                    $item['hangve'] = 0;
                }
                if ($item['hanghong'] == "") {
                    $item['hanghong'] = 0;
                }
                if ($item['hangthieu'] == "") {
                    $item['hangthieu'] = 0;
                }
                if ($item['hangthem'] == "") {
                    $item['hangthem'] = 0;
                }

                $soluong_end = $item['quantity'] - $item['hangve'] - $item['hanghong'] - $item['hangthieu'] + $item['hangthem'];

                $total_price += (double)$soluong_end * (double)$item['price'];

                $list_product[] = $item;
                //xu ly them hang ve, hong, thieu thua
                if ($item['hangve'] != 0 && $item['hangve'] != "" && $item['hangve'] != false) {
                    $arr_hangve = array();
                    $arr_hangve['variant_id'] = $item['variant_id'];
                    $arr_hangve['sl_nhap'] = $item['hangve'];
                    $arr_hangve['thanhtien'] = $item['hangve'] * (double)$item['price'];
                    $hangve[] = $arr_hangve;
                }

                if ($item['hanghong'] != 0 && $item['hanghong'] != "" && $item['hanghong'] != false) {
                    $arr_hanghong = array();
                    $arr_hanghong['variant_id'] = $item['variant_id'];
                    $arr_hanghong['sl_nhap'] = $item['hanghong'];
                    $arr_hanghong['thanhtien'] = $item['hanghong'] * (double)$item['price'];
                    $hanghong[] = $arr_hanghong;
                }

                if ($item['hangthieu'] != 0 && $item['hangthieu'] != "" && $item['hangthieu'] != false) {
                    $arr_hangthieu = array();
                    $arr_hangthieu['variant_id'] = $item['variant_id'];
                    $arr_hangthieu['sl_nhap'] = $item['hangthieu'];
                    $arr_hangthieu['thanhtien'] = $item['hangthieu'] * (double)$item['price'];
                    $hangthieu[] = $arr_hangthieu;
                }


                if ($item['hangthem'] != 0 && $item['hangthem'] != "" && $item['hangthem'] != false) {
                    $arr_hangthem = array();
                    $arr_hangthem['variant_id'] = $item['variant_id'];
                    $arr_hangthem['sl_nhap'] = $item['hangthem'];
                    $arr_hangthem['thanhtien'] = $item['hangthem'] * (double)$item['price'];
                    $hangthem[] = $arr_hangthem;
                }

                //xu ly them hang ve, hong, thieu thua
            }
            if ($hangve) {
                $data_add_database['hangve'] = json_encode($hangve);
            }

            if ($hangthieu) {
                $data_add_database['hangthieu'] = json_encode($hangthieu);
            }
            if ($hanghong) {
                $data_add_database['hanghong'] = json_encode($hanghong);
            }
            if ($hangthem) {
                $data_add_database['hangthem'] = json_encode($hangthem);
            }

            $data_add_database['line_items'] = json_encode($list_product);
            $data_add_database['edit_kho'] = 1;
            $data_add_database['note'] = $data['note'];

            if ($data['thanhtoan_lan1'] == "") {
                $data['thanhtoan_lan1'] = NULL;
            }
            if ($data['thanhtoan_lan2'] == "") {
                $data['thanhtoan_lan2'] = NULL;
            }
            if ($data['thanhtoan_lan3'] == "") {
                $data['thanhtoan_lan3'] = NULL;
            }
            if ($data['thanhtoan_lan4'] == "") {
                $data['thanhtoan_lan4'] = NULL;
            }
            if ($data['thanhtoan_lan5'] == "") {
                $data['thanhtoan_lan5'] = NULL;
            }

            if ($data['tongtien_no'] == "") {
                $data['tongtien_no'] = NULL;
            }

            $data_add_database['thanhtoan_lan1'] = $data['thanhtoan_lan1'];
            $data_add_database['thanhtoan_lan2'] = $data['thanhtoan_lan2'];
            $data_add_database['thanhtoan_lan3'] = $data['thanhtoan_lan3'];
            $data_add_database['thanhtoan_lan4'] = $data['thanhtoan_lan4'];
            $data_add_database['thanhtoan_lan5'] = $data['thanhtoan_lan5'];

            $data_add_database['take_by_lan1'] = $data['take_by_lan1'];
            $data_add_database['take_by_lan2'] = $data['take_by_lan2'];
            $data_add_database['take_by_lan3'] = $data['take_by_lan3'];
            $data_add_database['take_by_lan4'] = $data['take_by_lan4'];
            $data_add_database['take_by_lan5'] = $data['take_by_lan5'];

            $data_add_database['tongtien_no'] = $data['tongtien_no'];

            $data_add_database['time_lan1'] = $data['time_lan1'];
            $data_add_database['time_lan2'] = $data['time_lan2'];
            $data_add_database['time_lan3'] = $data['time_lan3'];
            $data_add_database['time_lan4'] = $data['time_lan4'];
            $data_add_database['time_lan5'] = $data['time_lan5'];

            //$total_price = number_format($total_price,2);
            $data_add_database['total_price'] = $total_price;
        }

        //var_dump($data_add_database);die;

        //xu ly data default addresse to json
        //$data_json_shipping = array();
        //$data_json_billing = array();

        /*
        $data_json_shipping['first_name'] = $data['s_first_name'];
        $data_json_shipping['last_name'] = $data['s_last_name'];
        $data_json_shipping['address1'] = $data['s_address1'];
        $data_json_shipping['zip'] = $data['s_zip'];
        $data_json_shipping['city'] = $data['s_city'];
        $data_json_shipping['phone'] = $data['s_phone'];
        $json_shipping = json_encode($data_json_shipping);

        $data_json_billing['first_name'] = $data['b_first_name'];
        $data_json_billing['last_name'] = $data['b_last_name'];
        $data_json_billing['address1'] = $data['b_address1'];
        $data_json_billing['zip'] = $data['b_zip'];
        $data_json_billing['city'] = $data['b_city'];
        $data_json_billing['phone'] = $data['b_phone'];
        $json_billing = json_encode($data_json_billing);

        */
        //add vao mang moi

        // $data_add_database['shipping_address'] = $json_shipping;
        //$data_add_database['billing_address'] = $json_billing;

        //day du lieu len shopify
        //data oder update shipping and billing
        /*$orders_data['order'] = array(
            'id' => $data['id_order'],
            'note' => $data['note'],
            'shipping_address' =>
                array(
                    'address1' => $data['s_address1'],
                    'city' => $data['s_city'],
                    'phone' => $data['s_phone'],
                    'zip' => $data['s_zip'],
                    'last_name' => $data['s_last_name'],
                    'first_name' => $data['s_first_name'],
                )
            ,
            'billing_address' =>
                array(
                    'address1' => $data['b_address1'],
                    'city' => $data['b_city'],
                    'phone' => $data['b_phone'],
                    'zip' => $data['b_zip'],
                    'last_name' => $data['b_last_name'],
                    'first_name' => $data['b_first_name'],
                )
            ,
        );*/

        $update = $this->data->update($id, $data_add_database);

        if ($update) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sửa bản ghi thành công in database KHO !";
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

    /**
     * Hàm xử lý dữ liệu hiển thị
     * @return Object
     */
    protected function _get_form2($id = 0, $data_pro)
    {
        $data = $this->data->get_form();
        $list_input = Array();
        $list_input_2 = Array();
        $list_input['data_pro'] = $data_pro;
        foreach ($data["field_form"] as $key => $item) {
            /* Nếu trường ko có rule hoặc có kiểu là hidden */
            if (!isset($data["rule"][$key]) || (isset($data["rule"][$key]['type']) && $data["rule"][$key]['type'] == 'invisible')) {
                continue;
            }

            $temp = new stdClass();
            $name_temp = explode(".", $key);
            $temp->name = end($name_temp);
            $temp->rule = $data["rule"][$key];
            $temp->string_rule = $this->data->get_display_rule($data["rule"][$key]);
            $temp->label = $data["field_form"][$key];
            if (isset($data["rule"][$key]['type'])) {
                if ($data["rule"][$key]['type'] == 'select') {
                    //Kiểm tra xem $data[$key] có thỏa mãn $form["rule"][$key] không!!
                    $list_rule = $data["rule"][$key];
                    if (isset($list_rule["target_model"]) && isset($list_rule["target_value"]) && isset($list_rule["target_display"])) {
                        if (isset($this->optionModel)) {
                            $this->optionModel = NULL;
                        }
                        $modelName = "option" . $key;
                        $this->load->model($list_rule["target_model"], $modelName);
                        $getString = $list_rule["target_value"] . " AS value, " . $list_rule["target_display"] . " AS display";
                        $temp->option = Array();
                        $nullItem = new stdClass();
                        $nullItem->value = '0';
                        $nullItem->display = "- Lưa chọn một giá trị -";
                        $temp->option[] = $nullItem;
                        $whereString = (isset($list_rule["where_condition"]) && count($list_rule["where_condition"])) ? $list_rule["where_condition"] : '';
                        $temp->option = array_merge($temp->option, $this->$modelName->get_list_option($getString, $whereString));
                    } else if (isset($list_rule["array_list"]) && is_array($list_rule["array_list"])) {
                        $temp->option = Array();
                        foreach ($list_rule["array_list"] as $value => $display) {
                            $nullItem = new stdClass();
                            $nullItem->value = $value;
                            $nullItem->display = $display;
                            $temp->option[] = $nullItem;
                        }
                    }
                } elseif ($data["rule"][$key]['type'] == 'file') {
                    $temp->rule = $data["rule"][$key];
                } elseif ($data["rule"][$key]['type'] == 'file') {
                    // chua xu ly gi ca
                }
                if ($id) {
                    if ($data["rule"][$key]['type'] == 'password') {
                        if (isset($data["rule"][$key]['recheck'])) {
                            $temp->string_rule = "type=password";
                        }
                    }
                }
            }
            $list_input_2[$key] = $temp;
        }
        $list_input_3 = array_merge($list_input, $list_input_2);
        return $list_input_3;
    }

    public function excel()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_kunden');
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }
        $data = $this->m_voxy_package_orders->get_order($order_number);

        $_export = array();
        $customer_id = "";
        foreach ($data as $item) {
            $customer_id = $item['customer_id'];

            foreach (json_decode($item['line_items']) as $key2 => $item2) {
                $_export[$key2] = get_object_vars($item2);
            }
        }
        $kunden_nummer = $this->m_voxy_package_kunden->get_id_khachhang($customer_id);
        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        foreach ($_export as $key => $row) {
            $band[$key] = $row['title'];
            $auflage[$key] = $row['sku'];
        }
        $band = array_column($_export, 'title');
        $auflage = array_column($_export, 'sku');
        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $_export);

//Khởi tạo đối tượng
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Ordernumber' . $order_number);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:M2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:M3')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A4:M4')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A5:M5')->getFont()->setBold(true);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', "Kunden");
        $excel->getActiveSheet()->setCellValue('B1', $kunden_nummer);

        $excel->getActiveSheet()->setCellValue('D1', 'LIL Internationale Lebensmitteln GmbH ');
        $excel->getActiveSheet()->setCellValue('D2', 'Herzbergstr. 128-139,10365 Berlin');
        $excel->getActiveSheet()->setCellValue('D3', 'Email: info@lilgmbh.de');
        $excel->getActiveSheet()->setCellValue('D4', 'Bestellungsnummer-' . $order_number);

        $excel->getActiveSheet()->setCellValue('A5', 'Pos');
        $excel->getActiveSheet()->setCellValue('B5', 'Menge');
        $excel->getActiveSheet()->setCellValue('C5', 'Unit');
        $excel->getActiveSheet()->setCellValue('D5', 'Artikelbezeichnung');
        $excel->getActiveSheet()->setCellValue('E5', 'Einzelpreis EUR');
        $excel->getActiveSheet()->setCellValue('F5', 'USt. %');
        $excel->getActiveSheet()->setCellValue('G5', 'Gesamtpreis EUR');

// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2

        //$_data = json_decode($data);
        $numRow = 6;
        $total_price = 0;
        $stt = 0;
        $tongtien_thue7 = 0;
        $tongtien_thue19 = 0;
        $tongtien_netto = 0;
        if ($_export != null) {

            foreach ($_export as $row) {
                $stt++;
                //$row = get_object_vars($_row);
                if (!isset($row['hangve'])) {
                    $row['hangve'] = 0;
                }
                if (!isset($row['hangthieu'])) {
                    $row['hangthieu'] = 0;
                }
                if (!isset($row['hanghong'])) {
                    $row['hanghong'] = 0;
                }
                if (!isset($row['hangthem'])) {
                    $row['hangthem'] = 0;
                }

                $sl_cuoicung = $row['quantity'] - $row['hangve'] - $row['hangthieu'] - $row['hanghong'] + $row['hangthem'];
                $thanhtien = $sl_cuoicung * $row['price'];
                $tongtien_netto += $thanhtien;
                $mwst = $this->m_voxy_package->get_mwst ($row['sku']);
                //gia brutto

                if($mwst == 7){
                    //$thanhtien = round($thanhtien * 1.07,2);//gia brutto
                    $gia_brutto = round($row['price'] * 1.07,2);
                    $thanhtien_brutto = $gia_brutto * $row['quantity'];
                    $tongtien_thue7 += round($thanhtien_brutto * 0.07,2);
                }else{
                    $gia_brutto = round($row['price'] * 1.19,2);
                    $thanhtien_brutto = $gia_brutto * $row['quantity'];
                    $tongtien_thue19 += round($thanhtien_brutto * 0.19,2);
                }

                $total_price += $thanhtien_brutto;

                $excel->getActiveSheet()->setCellValue('A' . $numRow, $stt);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['quantity']);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['sku']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['title']);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $gia_brutto);
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $mwst);
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $thanhtien_brutto);
                $numRow++;
            }
            $numRow++;

            $excel->getActiveSheet()->setCellValue('D' . $numRow, "Gesamt Netto");
            $excel->getActiveSheet()->setCellValue('G' . $numRow,number_format($tongtien_netto, 2));

            $excel->getActiveSheet()->setCellValue('D' . ($numRow + 1) , "zzgl. 7,00 % USt.");
            $excel->getActiveSheet()->setCellValue('G' . ($numRow + 1),number_format($tongtien_thue7, 2));

            $excel->getActiveSheet()->setCellValue('D' . ($numRow + 2) , "zzgl. 19,00 % USt.");
            $excel->getActiveSheet()->setCellValue('G' . ($numRow + 2),number_format($tongtien_thue19, 2));

            $excel->getActiveSheet()->setCellValue('D' . ($numRow + 3) , "Gesamtbetrag");
            $excel->getActiveSheet()->setCellValue('G' . ($numRow + 3),number_format($total_price, 2));
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $order_number . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function excel_day()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package_orders');
        if (isset($_GET["date"])) {
            $date = $_GET["date"];
        } else {
            $date = date("Y-m-d");
        }

        $result = $this->m_voxy_package_orders->get_data_pdf($date);

        //xu ly du lieu
        $_export = array();
        $i = 0;
        if ($result == null) {
            var_dump('Không có sản phẩm nào vào ngày này, mời bạn quay lại chọn ngày khác');
            die;
        } else {
            foreach ($result as $item) {
                foreach (json_decode($item['line_items']) as $key2 => $item2) {
                    $i++;
                    $_export[$i] = get_object_vars($item2);
                }
            }
        }

        //ghep location like key to sort
        $export = array();
        $export2 = array();
        $chiso_remove = array();

        foreach ($_export as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau

            foreach ($_export as $key2 => $item2) {
                if ($key2 > $key) {
                    if ($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] && $item['name'] == $item2['name']) {
                        $item['quantity'] = $item['quantity'] + $item2['quantity'];
                        $chiso_remove[$key2 - 1] = $key2 - 1;
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
//        //gan location key
//        foreach($export2 as $key3 => $item){
//
//            if($item["location"] == false){
//                $item["location"] = "";
//            }
//            $export[$item["location"]] = $item;
//
//        }
//        //ksort tag theo khoa, krsort giam theo khoa hehe :D
//        ksort($export);

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        foreach ($export2 as $key => $row) {
            $band[$key] = $row['location'];
            $auflage[$key] = $row['id'];
        }
        $band = array_column($export2, 'location');
        $auflage = array_column($export2, 'id');
        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $export2);

//Khởi tạo đối tượng
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Đơn tổng theo ngay_' . $date);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
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
        $excel->getActiveSheet()->setCellValue('B2', 'Loại Sản Phẩm');
        $excel->getActiveSheet()->setCellValue('C2', 'Giá €');
        $excel->getActiveSheet()->setCellValue('D2', 'Số Lượng');
        $excel->getActiveSheet()->setCellValue('E2', 'Giá tổng €');
        $excel->getActiveSheet()->setCellValue('F2', 'Vị trí');
        //$excel->getActiveSheet()->setCellValue('G2', 'Ngày hết hạn');
        //$excel->getActiveSheet()->setCellValue('H2', 'Theo Xe');
        //$excel->getActiveSheet()->setCellValue('G1', 'Tổng tiền thêm 5 eu phi shipping: € '.$total_price);
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2

        //$_data = json_decode($data);
        $total_price = 0;
        $numRow = 3;
        if ($export2 != null) {
            foreach ($export2 as $row) {
                //$row = get_object_vars($_row);
                $total_price += $row['price'] * $row['quantity'];
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $row['title']);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['variant_title']);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['price']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['quantity']);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['price'] * $row['quantity']);
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['location']);
                //$excel->getActiveSheet()->setCellValue('G' . $numRow, $row['expri_day']);
                //$excel->getActiveSheet()->setCellValue('H' . $numRow, "dressen");
                $numRow++;
            }
            $excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : " . number_format($total_price, 2));
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $date . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
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
            $viewFile = '/voxy_package_orders/search_pro';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    public function search_pro_for_title()
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
            $viewFile = '/voxy_package_orders/search_pro_for_title';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    public function refund_product()
    {
        $this->load->model('m_voxy_package');
        $variant_id = $this->input->post("variant_id");
        $hangve = $this->input->post("hangve");

        $data_return = array();
        if ($variant_id == false || $hangve == 0 || $hangve == "") {
            $data_return["state"] = 0;
            $data_return["msg"] = "Cập nhật Inventory thất bại";
            $data_return["html"] = "K tim thay san pham";
            echo json_encode($data_return);
            return FALSE;
        } else {
            $check_variant1 = $this->m_voxy_package->check_variant1($variant_id);
            $check_variant2 = $this->m_voxy_package->check_variant2($variant_id);
            $id = $this->m_voxy_package->get_id_from_variant($variant_id);

            if ($check_variant1 == true) {
                $this->m_voxy_package->update_plus_inventory1($hangve, $id);
            }

            if ($check_variant2 == true) {
                $this->m_voxy_package->update_plus_inventory2($hangve, $id);
            }

            $data_return["state"] = 1;
            $data_return["msg"] = "Cập nhật Inventory thành công";
            echo json_encode($data_return);
            return TRUE;
        }
    }

    public function xml_lexware()
    {
        $this->load->model('m_voxy_package_orders');
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }else{
            if(isset($_GET["id"])){
                $list_id = json_decode($_GET["id"]);
                $order_number = get_object_vars($list_id)['list_id'];
            }
        }

        if(is_array($order_number)){
            $data = $this->m_voxy_package_orders->get_order($order_number);
        }else{
            $data = $this->m_voxy_package_orders->get_order($order_number);
        }

        //begin sort all products
        $_export = array();
        foreach ($data as $item) {
            foreach (json_decode($item['line_items']) as $key2 => $item2) {
                $_export[$key2] = get_object_vars($item2);
            }
        }

        foreach ($_export as $key => $row) {
            $band[$key] = $row['title'];
            $auflage[$key] = $row['sku'];
        }
        $band = array_column($_export, 'title');
        $auflage = array_column($_export, 'sku');
        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $_export);
        //end sort array list product
        
        $xml = new DOMDocument();
        $xml->encoding = 'ISO-8859-1';
        $xml->xmlVersion = '1.0';
        $xml->formatOutput = true;

        $ORDER_LIST = $xml->createElement("ORDER_LIST");
        $xml->appendChild($ORDER_LIST);

        $ORDER = $xml->createElement("ORDER", " ");
        $xmlns = new DOMAttr('xmlns', 'http://www.opentrans.org/XMLSchema/1.0');
        $xsi = new DOMAttr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $version = new DOMAttr('version', '1.0');
        $type = new DOMAttr('type', 'standar');
        $ORDER->setAttributeNode($xmlns);
        $ORDER->setAttributeNode($xsi);
        $ORDER->setAttributeNode($version);
        $ORDER->setAttributeNode($type);
        $ORDER_LIST->appendChild($ORDER);

        $ORDER_HEADER = $xml->createElement("ORDER_HEADER", "");
        $ORDER->appendChild($ORDER_HEADER);

        $CONTROL_INFO = $xml->createElement("CONTROL_INFO");
        $ORDER_HEADER->appendChild($CONTROL_INFO);

        $GENERATOR_INFO = $xml->createElement("GENERATOR_INFO");
        $CONTROL_INFO->appendChild($GENERATOR_INFO);

        $GENERATION_DATE = $xml->createElement("GENERATION_DATE", date("Y-m-d hh:ss"));
        $CONTROL_INFO->appendChild($GENERATION_DATE);

        $ORDER_INFO = $xml->createElement("ORDER_INFO");
        $ORDER_HEADER->appendChild($ORDER_INFO);

        $ORDER_ID = $xml->createElement("ORDER_ID", 1);
        $ORDER_INFO->appendChild($ORDER_ID);
        $ORDER_DATE = $xml->createElement("ORDER_DATE", date("Y-m-d"));
        $ORDER_INFO->appendChild($ORDER_DATE);


        $ORDER_ITEM_LIST = $xml->createElement("ORDER_ITEM_LIST", "");
        $ORDER->appendChild($ORDER_ITEM_LIST);

        $ORDER_ITEM = $xml->createElement("ORDER_ITEM");
        $ORDER_ITEM_LIST->appendChild($ORDER_ITEM);

        $LINE_ITEM_ID = $xml->createElement("LINE_ITEM_ID", 0);
        $ORDER_ITEM->appendChild($LINE_ITEM_ID);

        $ARTICLE_ID = $xml->createElement("ARTICLE_ID", 0);
        $ORDER_ITEM->appendChild($ARTICLE_ID);

        $SUPPLIER_AID = $xml->createElement("SUPPLIER_AID", 123);
        $ARTICLE_ID->appendChild($SUPPLIER_AID);

        $DESCRIPTION_SHORT = $xml->createElement("DESCRIPTION_SHORT", "desc short");
        $ARTICLE_ID->appendChild($DESCRIPTION_SHORT);

        $DESCRIPTION_LONG = $xml->createElement("DESCRIPTION_LONG", "desc long");
        $ARTICLE_ID->appendChild($DESCRIPTION_LONG);

        $QUANTITY = $xml->createElement("QUANTITY", 1);
        $ORDER_ITEM->appendChild($QUANTITY);

        $ORDER_UNIT = $xml->createElement("ORDER_UNIT", "Stuck");
        $ORDER_ITEM->appendChild($ORDER_UNIT);

        $ARTICLE_PRICE = $xml->createElement("ARTICLE_PRICE");
        $ARTICLE_PRICE->setAttributeNode(new DOMAttr('type', 'net_list'));
        $ORDER_ITEM->appendChild($ARTICLE_PRICE);

        $PRICE_AMOUNT = $xml->createElement("PRICE_AMOUNT", 97.55);
        $ARTICLE_PRICE->appendChild($PRICE_AMOUNT);

        $PRICE_LINE_AMOUNT = $xml->createElement("PRICE_LINE_AMOUNT", 97.55);
        $ARTICLE_PRICE->appendChild($PRICE_LINE_AMOUNT);

        $TAX = $xml->createElement("TAX", 0.19);
        $ARTICLE_PRICE->appendChild($TAX);

        $ORDER_SUMMARY = $xml->createElement("ORDER_SUMMARY");
        $ORDER->appendChild($ORDER_SUMMARY);

        $TOTAL_ITEM_NUM = $xml->createElement("TOTAL_ITEM_NUM", 1);
        $ORDER_SUMMARY->appendChild($TOTAL_ITEM_NUM);

        $TOTAL_AMOUNT = $xml->createElement("TOTAL_AMOUNT", 99.75);
        $ORDER_SUMMARY->appendChild($TOTAL_AMOUNT);

        $file_xml = $xml->save("xml/$order_number.xml");
        if ($file_xml) {
            echo "Thanh cong";
            echo '
            <a href="../xml/'.$order_number.'.xml" download>
                Dowload File XML
            </a>
            ';
        } else {
            echo "That bai";
        }
        //$this->delete_file();
    }

    public function delete_file(){
        $files = glob('xml/*'); //get all file names
        foreach($files as $file){
            if(is_file($file))
                unlink($file); //delete file
        }
    }

    public function xml()
    {
        $this->load->model('m_voxy_package_orders');
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }else{
            if(isset($_GET["id"])){
                $list_id = json_decode($_GET["id"]);
                $order_number = get_object_vars($list_id)['list_id'];
            }
        }

        if(is_array($order_number)){
            $data = $this->m_voxy_package_orders->get_order($order_number);
        }else{
            $data = $this->m_voxy_package_orders->get_order($order_number);
        }

        //begin sort all products
        $_export = array();
        foreach ($data as $item) {
            foreach (json_decode($item['line_items']) as $key2 => $item2) {
                $_export[$key2] = get_object_vars($item2);
            }
        }

        foreach ($_export as $key => $row) {
            $band[$key] = $row['title'];
            $auflage[$key] = $row['sku'];
        }
        $band = array_column($_export, 'title');
        $auflage = array_column($_export, 'sku');
        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $_export);
        //end sort array list product

        $xml = new DOMDocument();
        $xml->encoding = 'utf-8';
        $xml->xmlVersion = '1.0';
        $xml->formatOutput = true;

        $Bestellungenliste = $xml->createElement("Bestellungenliste");
        $xmlns = new DOMAttr('xmlns', 'http://NetConnections/XML/Bestellungen/v1_0');
        $version = new DOMAttr('version', '1.0');
        $Bestellungenliste->setAttributeNode($xmlns);
        $Bestellungenliste->setAttributeNode($version);
        $xml->appendChild($Bestellungenliste);

        $Bestellung = $xml->createElement("Bestellung", " ");
        $Bestellungenliste->appendChild($Bestellung);

        $BestellNr = $xml->createElement("BestellNr",100000076);
        $Bestellung->appendChild($BestellNr);

        $KundenNr = $xml->createElement("KundenNr",2);
        $Bestellung->appendChild($KundenNr);

        $Kundengruppe = $xml->createElement("Kundengruppe","General");
        $Bestellung->appendChild($Kundengruppe);

        $UStID = $xml->createElement("UStID",100000076);
        $Bestellung->appendChild($UStID);

        $Bestelldatum = $xml->createElement("Bestelldatum","2009-11-11T09:05:13+01:00");
        $Bestellung->appendChild($Bestelldatum);

        $Bestellstatus = $xml->createElement("Bestellstatus","In Bearbeitung");
        $Bestellung->appendChild($Bestellstatus);

        $Bemerkung = $xml->createElement("Bemerkung","");
        $Bestellung->appendChild($Bemerkung);
//kunden adress
        $Kundenadresse = $xml->createElement("Kundenadresse");
        $Bestellung->appendChild($Kundenadresse);

        $Anrede = $xml->createElement("Anrede","Herr");
        $Kundenadresse->appendChild($Anrede);
        $Firma = $xml->createElement("Firma","OscWare GmbH");
        $Kundenadresse->appendChild($Firma);
        $Ansprechpartner = $xml->createElement("Ansprechpartner","Thomas Klein");
        $Kundenadresse->appendChild($Ansprechpartner);
        $Nachname = $xml->createElement("Nachname","Muller");
        $Kundenadresse->appendChild($Nachname);
        $Vorname = $xml->createElement("Vorname","Thomas");
        $Kundenadresse->appendChild($Vorname);
        $Strasse = $xml->createElement("Strasse","pichel");
        $Kundenadresse->appendChild($Strasse);
        $Hausnummer = $xml->createElement("Hausnummer",131);
        $Kundenadresse->appendChild($Hausnummer);
        $PLZ = $xml->createElement("PLZ",109);
        $Kundenadresse->appendChild($PLZ);
        $Ort = $xml->createElement("Ort","Berlin");
        $Kundenadresse->appendChild($Ort);
        $Land = $xml->createElement("Land","Deutschland");
        $Kundenadresse->appendChild($Land);
        $LandISO = $xml->createElement("LandISO","DE");
        $Kundenadresse->appendChild($LandISO);
        $Telefon = $xml->createElement("Telefon",1234);
        $Kundenadresse->appendChild($Telefon);
        $Mobiltelefon = $xml->createElement("Mobiltelefon", 123);
        $Kundenadresse->appendChild($Mobiltelefon);
        $Fax = $xml->createElement("Fax",99);
        $Kundenadresse->appendChild($Fax);
        $Emailadresse = $xml->createElement("Emailadresse","chuvantinh1991€gmail.com");
        $Kundenadresse->appendChild($Emailadresse);
//kunden adress

        //rechnung adresse
        $Rechnungsadresse = $xml->createElement("Rechnungsadresse");
        $Bestellung->appendChild($Rechnungsadresse);

        $Anrede_r = $xml->createElement("Anrede","Herr");
        $Rechnungsadresse->appendChild($Anrede_r);
        $Firma_r = $xml->createElement("Firma","OscWare GmbH");
        $Rechnungsadresse->appendChild($Firma_r);
        $Ansprechpartner_r = $xml->createElement("Ansprechpartner","Thomas Klein");
        $Rechnungsadresse->appendChild($Ansprechpartner_r);
        $Nachname_r = $xml->createElement("Nachname","Muller");
        $Rechnungsadresse->appendChild($Nachname_r);
        $Vorname_r = $xml->createElement("Vorname","Thomas");
        $Rechnungsadresse->appendChild($Vorname_r);
        $Strasse_r = $xml->createElement("Strasse","pichel");
        $Rechnungsadresse->appendChild($Strasse_r);
        $Hausnummer_r = $xml->createElement("Hausnummer",131);
        $Rechnungsadresse->appendChild($Hausnummer_r);
        $PLZ_r = $xml->createElement("PLZ",109);
        $Rechnungsadresse->appendChild($PLZ_r);
        $Ort_r = $xml->createElement("Ort","Berlin");
        $Rechnungsadresse->appendChild($Ort_r);
        $Land_r = $xml->createElement("Land","Deutschland");
        $Rechnungsadresse->appendChild($Land_r);
        $LandISO_r = $xml->createElement("LandISO","DE");
        $Rechnungsadresse->appendChild($LandISO_r);
        $Telefon_r = $xml->createElement("Telefon",1234);
        $Rechnungsadresse->appendChild($Telefon_r);
        $Mobiltelefon_r = $xml->createElement("Mobiltelefon", 123);
        $Rechnungsadresse->appendChild($Mobiltelefon_r);
        $Fax_r = $xml->createElement("Fax",99);
        $Rechnungsadresse->appendChild($Fax_r);
        $Emailadresse_r = $xml->createElement("Emailadresse","nguyenduclong@gmail.com");
        $Rechnungsadresse->appendChild($Emailadresse_r);
        //rechnung adresse

        //liefer adresse
        $Lieferadresse = $xml->createElement("Lieferadresse");
        $Bestellung->appendChild($Lieferadresse);

        $Anrede_f = $xml->createElement("Anrede","Herr");
        $Lieferadresse->appendChild($Anrede_f);
        $Firma_f = $xml->createElement("Firma","OscWare GmbH");
        $Lieferadresse->appendChild($Firma_f);
        $Ansprechpartner_f = $xml->createElement("Ansprechpartner","Thomas Klein");
        $Lieferadresse->appendChild($Ansprechpartner_f);
        $Nachname_f = $xml->createElement("Nachname","Muller");
        $Lieferadresse->appendChild($Nachname_f);
        $Vorname_f = $xml->createElement("Vorname","Thomas");
        $Lieferadresse->appendChild($Vorname_f);
        $Strasse_f = $xml->createElement("Strasse","pichel");
        $Lieferadresse->appendChild($Strasse_f);
        $Hausnummer_f = $xml->createElement("Hausnummer",131);
        $Lieferadresse->appendChild($Hausnummer_f);
        $PLZ_f = $xml->createElement("PLZ",109);
        $Lieferadresse->appendChild($PLZ_f);
        $Ort_f = $xml->createElement("Ort","Berlin");
        $Lieferadresse->appendChild($Ort_f);
        $Land_f = $xml->createElement("Land","Deutschland");
        $Lieferadresse->appendChild($Land_f);
        $LandISO_f = $xml->createElement("LandISO","DE");
        $Lieferadresse->appendChild($LandISO_f);
        $Telefon_f = $xml->createElement("Telefon",1234);
        $Lieferadresse->appendChild($Telefon_f);
        $Mobiltelefon_f = $xml->createElement("Mobiltelefon", 123);
        $Lieferadresse->appendChild($Mobiltelefon_f);
        $Fax_f = $xml->createElement("Fax",99);
        $Lieferadresse->appendChild($Fax_f);
        $Emailadresse_f = $xml->createElement("Emailadresse","nguyenduclong@gmail.com");
        $Lieferadresse->appendChild($Emailadresse_f);
        //liefer adresse


        //Zahlungsart
        $Zahlungsart = $xml->createElement("Zahlungsart");
        $Bestellung->appendChild($Zahlungsart);

        $Name = $xml->createElement("Name","invoice");
        $Zahlungsart->appendChild($Name);
        $Bezeichnung = $xml->createElement("Bezeichnung","null");
        $Zahlungsart->appendChild($Bezeichnung);
        $Beschreibung = $xml->createElement("Beschreibung","Zahlung per Vorkasse");
        $Zahlungsart->appendChild($Beschreibung);
        $Bruttopreis = $xml->createElement("Bruttopreis",0.00);
        $Zahlungsart->appendChild($Bruttopreis);
        $Nettopreis = $xml->createElement("Nettopreis",0.00);
        $Zahlungsart->appendChild($Nettopreis);
        $Steuersatz = $xml->createElement("Steuersatz",19);
        $Zahlungsart->appendChild($Steuersatz);
        //Zahlungsart

        //Artikelliste
        $Artikelliste = $xml->createElement("Artikelliste");
        $Bestellung->appendChild($Artikelliste);

        $Artikel = $xml->createElement("Artikel");
        $Artikelliste->appendChild($Artikel);

        $ArtikelNr = $xml->createElement("ArtikelNr",1032);
        $Artikel->appendChild($ArtikelNr);
        $Bezeichnung_a = $xml->createElement("Bezeichnung",1032);
        $Artikel->appendChild($Bezeichnung_a);
        $Beschreibung_a = $xml->createElement("Beschreibung",1032);
        $Artikel->appendChild($Beschreibung_a);
        $Menge_a = $xml->createElement("Menge",1032);
        $Artikel->appendChild($Menge_a);
        $Bruttopreis_a = $xml->createElement("Bruttopreis",1032);
        $Artikel->appendChild($Bruttopreis_a);
        $Nettopreis_a = $xml->createElement("Nettopreis",1032);
        $Artikel->appendChild($Nettopreis_a);
        $Steuersatz_a = $xml->createElement("Steuersatz",1032);
        $Artikel->appendChild($Steuersatz_a);
        $Einheit_a = $xml->createElement("Einheit",1032);
        $Artikel->appendChild($Einheit_a);
        $Gewicht_a = $xml->createElement("Gewicht",1032);
        $Artikel->appendChild($Gewicht_a);
        $EAN_a = $xml->createElement("EAN",1032);
        $Artikel->appendChild($EAN_a);
        //Artikelliste

        $file_xml = $xml->save("xml/order.xml");
        if ($file_xml) {
            echo "Thanh cong";
            echo '
            <a href="../xml/order.xml" download>
                Dowload File XML
            </a>
            ';
        } else {
            echo "That bai";
        }
        //$this->delete_file();
    }


//for doi chieu
    public function orders_compare($data = Array()){
        $this->load->model('m_voxy_package_orders');
        $this->session->set_userdata("search_string", "");
        $data["add_link"]           = isset($data["add_link"])              ? $data["add_link"]         : $this->url["add"];
        $data["get_link"]           = isset($data["get_link"])              ? $data["get_link"]         : $this->url["get"];
        $data["delete_list_link"]   = isset($data["delete_list_link"])      ? $data["delete_list_link"] : site_url($this->url["delete"]);
        $data["ajax_data_link"]     = isset($data["ajax_data_link"])        ? $data["ajax_data_link"]   : site_url($this->name["class"] . "/ajax_list_data");
        $data["form_url"]           = isset($data["form_url"])              ? $data["form_url"]         : $data["ajax_data_link"];
        $data["form_conds"]         = isset($data["form_conds"])            ? $data["form_conds"]       : array();
        $data["title"] = $title     = "Quản lý " . (isset($data["title"])   ? $data["title"]            : $this->name["object"]);
        $viewFile                   = "base_manager/default_manager";

        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'compare.php')) {
            $viewFile = $this->name["view"] . '/' . 'compare';
        }

        $date_liefer = $this->input->post('date_liefer');
        $shipper_id = $this->input->post('shipper_id');
        if($shipper_id == false){
            echo ("Bitte chọn lái xe, để có thể đối chiếu nợ thành công");die;
        }
        $data['list_orders'] = $this->m_voxy_package_orders->get_list_orders($date_liefer,$shipper_id);

        $content    = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        $head_page  = $this->load->view($this->path_theme_view . 'base_manager/header_manager', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header_manager.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_manager', $data, true);
        }
        $this->master_page($content, $head_page, $title);
    }

    public function handeln_compare(){
        $this->load->model('m_voxy_package_orders');
        $list_orders = $this->input->post('list_orders');
       $data_upate = array();
       foreach ($list_orders as $item){
           if($item['data_compare'] != ""){
               $data_upate[] = $item;
           }
       }

       if(is_array($data_upate)){
           foreach ($data_upate as $item){
               $arr = array(
                   'data_compare' => $item['data_compare'],
                   'status_compare' => $item['status_compare']
               );
               $this->m_voxy_package_orders->update($item['order_id'],$arr);
           }
       }

       $data_return = array();
       if($data_upate){
           $data_return['status'] = 1;
           echo json_encode($data_return);
           return true;
       }else{
           $data_return['status'] = 0;
           echo json_encode($data_return);
           return true;
       }
    }
    //end doi chieu nono

    //begin chi phi lai xe
    public function add_chiphi_laixe($data = Array()){
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_shippers');
        $this->session->set_userdata("search_string", "");
        $data["add_link"]           = isset($data["add_link"])              ? $data["add_link"]         : $this->url["add"];
        $data["get_link"]           = isset($data["get_link"])              ? $data["get_link"]         : $this->url["get"];
        $data["delete_list_link"]   = isset($data["delete_list_link"])      ? $data["delete_list_link"] : site_url($this->url["delete"]);
        $data["ajax_data_link"]     = isset($data["ajax_data_link"])        ? $data["ajax_data_link"]   : site_url($this->name["class"] . "/ajax_list_data");
        $data["form_url"]           = isset($data["form_url"])              ? $data["form_url"]         : $data["ajax_data_link"];
        $data["form_conds"]         = isset($data["form_conds"])            ? $data["form_conds"]       : array();
        $data["title"] = $title     = "Quản lý " . (isset($data["title"])   ? $data["title"]            : $this->name["object"]);
        $viewFile                   = "base_manager/default_manager";

        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'chiphi_laixe.php')) {
            $viewFile = $this->name["view"] . '/' . 'chiphi_laixe';
        }

        $date_liefer = $this->input->post('date_liefer');
        $shipper_id = $this->input->post('shipper_id');
        //$data['list_orders'] = $this->m_voxy_package_orders->get_list_orders($date_liefer,$shipper_id);
        $data['date_liefer'] = $date_liefer;
        if($shipper_id){
            foreach ($shipper_id as $item){
                $shipper_name[] = $this->m_voxy_shippers->get_name($item);
            }
        }
        if(!isset($shipper_name) || ! isset($date_liefer)){
            echo "Bitte chọn lái xe và ngày tháng";die();
        }

        $data['shipper_name'] = $shipper_name;
        $data['shipper_id'] = $shipper_id;
        $content    = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        $head_page  = $this->load->view($this->path_theme_view . 'base_manager/header_manager', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header_manager.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_manager', $data, true);
        }
        $this->master_page($content, $head_page, $title);
    }

    public function handeln_chiphi_laixe(){
        $this->load->model('m_voxy_chiphi_laixe');
        $this->load->model('m_voxy_shippers');

        $list_check_end = $this->input->post('list_check_end');
        if(! $list_check_end){
            die('Bitte Chon Lai xe');
        }
        $data_add = array();
        foreach ($list_check_end as $key => $item){
            $laixe_id = $this->m_voxy_shippers->get_id($item['laixe']);
            $data_add['laixe_id'] = $laixe_id;

            $data_add['bienso'] = $item['bienso'];
            $data_add['loaixe'] = $item['loaixe'];
            $data_add['chu_so_huu'] = $item['chu_so_huu'];
            $data_add['ghichu'] = $item['ghi_chu'];

            $data_add['tienxang'] = (double)$item['tien_xang'];
            $data_add['tienthuexe'] = (double)$item['tien_thue_xe'];
            $data_add['khauhaoxe'] = (double)$item['khau_hao_xe'];
            $data_add['chiphikhac'] = (double)$item['chi_phi_khac'];

            $data_add['nopthieu_laixe'] = (double)$item['nopthieu_laixe'];
            $data_add['ly_do_nopthieu'] = $item['ly_do_nopthieu'];

            $data_add['nopthua_laixe'] = (double)$item['nopthua_laixe'];
            $data_add['ly_do_nopthua'] = $item['ly_do_nopthua'];


            $data_add['tongchiphi'] = (double)$item['tien_xang'] + (double)$item['tien_thue_xe'] + (double)$item['khau_hao_xe'] + (double)$item['chi_phi_khac'];

            $data_add['lydo'] = $item['ly_do'];
            $data_add['shipped_at'] = $item['ngay_giao_hang'];
            $id = $this->m_voxy_chiphi_laixe->check_import($laixe_id, $item['ngay_giao_hang']);
            if($id != false){
                $this->m_voxy_chiphi_laixe->update($id, $data_add);
            }else{
                $this->m_voxy_chiphi_laixe->add($data_add);
            }
        }

        $data_return = array();
        if($data_add){
            $data_return['status'] = 1;
            echo json_encode($data_return);
            return true;
        }else{
            $data_return['status'] = 0;
            echo json_encode($data_return);
            return true;
        }
    }
    //end chi phi lai xe

    public function do_ghino(){
        $this->load->model('m_voxy_package_orders');
        $order_number = $this->input->post('order_number');
        $list_id = $this->input->post('list_id');


        if($order_number != false){//truong hop chi co 1 order
            $id = $this->m_voxy_package_orders->get_id_order_number($order_number);
            $shipped_at = $this->m_voxy_package_orders->get_shipped_at_voxy_order($id);
            $total_price = $this->m_voxy_package_orders->get_total_price($id);
            $data = array(
                'thanhtoan_lan1' => 0,
                'time_lan1' => $shipped_at,
                'tongtien_no' => $total_price
            );
            $update = $this->m_voxy_package_orders->update($id,$data);
            $list_orders = "";
        }

        if($list_id){//1 list order luon de ghi no
            $_arr = json_decode($list_id);
            $arr = $_arr->list_id;
            if(is_array($arr)){
                $list_orders = array();

                foreach ($arr as $key => $id){
                    $shipped_at = $this->m_voxy_package_orders->get_shipped_at_voxy_order($id);
                    $total_price = $this->m_voxy_package_orders->get_total_price($id);

                    $list_orders[$key]['order_number'] = $this->m_voxy_package_orders->get_order_number_from_id($id);
                    $list_orders[$key]['tongtien_no'] = $total_price;

                    $data = array(
                        'thanhtoan_lan1' => 0,
                        'time_lan1' => $shipped_at,
                        'tongtien_no' => $total_price
                    );
                    $this->m_voxy_package_orders->update($id,$data);
                }
                $update = 1;
            }
        }

            if (isset($update)) {
                $data_return["state"] = 1; /* state = 1 : insert thành công */
                $data_return["msg"] = "Sửa bản ghi thành công";
                $data_return['tongtien_no'] =  $total_price;
                $data_return['list_orders'] =  $list_orders;
                echo json_encode($data_return);
                return TRUE;
            } else {
                $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
                $data_return["msg"] = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
                echo json_encode($data_return);
                return FALSE;
            }

    }

    public function do_thanhtoan(){
        $this->load->model('m_voxy_package_orders');
        $order_number = $this->input->post('order_number');
        $list_id = $this->input->post('list_id');
        if($order_number != false){
            $id = $this->m_voxy_package_orders->get_id_order_number($order_number);
            //$shipped_at = $this->m_voxy_package_orders->get_shipped_at_voxy_order($id);
            $shipped_at = date('Y-m-d');
            $total_price = $this->m_voxy_package_orders->get_total_price($id);
            $data = array(
                'thanhtoan_lan1' => $total_price,
                'time_lan1' => $shipped_at,
                'tongtien_no' => 0
            );
            $update = $this->m_voxy_package_orders->update($id,$data);
        }

        if($list_id){//nut thanh toan ben tren
            $_arr = json_decode($list_id);
            $arr = $_arr->list_id;
            if(is_array($arr)){
                $list_orders = array();
                foreach ($arr as $id){
                    //$shipped_at = $this->m_voxy_package_orders->get_shipped_at_voxy_order($id);
                    $shipped_at = date('Y-m-d');
                    $total_price = $this->m_voxy_package_orders->get_total_price($id);
                    $list_orders[] = $this->m_voxy_package_orders->get_order_number_from_id($id);
                    $data = array(
                        'thanhtoan_lan1' => $total_price,
                        'time_lan1' => $shipped_at,
                        'tongtien_no' => 0
                    );
                    $this->m_voxy_package_orders->update($id,$data);
                }
                $update = 1;
            }

        }

        if ($update) {
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sửa bản ghi thành công";
            $data_return['tongtien_no'] =  "";
            $data_return['list_orders'] =  $list_orders;
            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
            echo json_encode($data_return);
            return FALSE;
        }

    }
}