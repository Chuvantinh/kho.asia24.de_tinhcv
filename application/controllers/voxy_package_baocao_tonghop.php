<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class voxy_package_baocao_tonghop
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package_baocao_tonghop extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_package_baocao_tonghop",
            "view" => "voxy_package_baocao_tonghop",
            "model" => "m_voxy_package_baocao_tonghop",
            "object" => "Báo cáo KINH DOANH"
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

        $this->load->model('m_voxy_package_baocao_tonghop');
        $data['shipper'] = $this->m_voxy_package_baocao_tonghop->get_all_shipper_id();
        $data['shipper_area_id'] = $this->m_voxy_package_baocao_tonghop->get_all_shipper_area_id();
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
        $this->load->model('m_voxy_package_xuathang');
        $data_get = $this->input->get();

        $where_condition = "";
        if ($data_get && is_array($data_get)) {
            $where_condition = $this->get_search_condition_new_baocaotonghop($data_get);
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
        $record     = $this->m_voxy_package_xuathang->get_list_table_xuathangtaikho_baocaotonghop($search_string, $where_condition, $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->m_voxy_package_xuathang->get_list_table_xuathangtaikho_baocaotonghop($search_string, $where_condition, 0, 0, $order));
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

        //$viewFile = 'voxy_package_xuathang/' . 'table';
        $viewFile = 'voxy_package_baocao_tonghop/' . 'table';
        $content    = $this->load->view($viewFile, $data, true);

        if ($this->input->is_ajax_request()) {
            //$data_return["callback"]    = "get_manager_data_response";
            $data_return["state"]       = 1;
            $data_return["html"]        = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    protected function get_search_condition_new_baocaotonghop($data = array())
    {
        if (!count($data)) {
            $data = $this->input->get();
        }
        $where_data = array();
        $like_data = array();
        $list_field = array('tungay','denngay','laixe');
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

                    case 'tungay':
                        if ($data['tungay'] != '') {
                            $where_data['m.tungay'] = $data['tungay'];
                        }
                        break;
                    case 'denngay':
                        if ($data['denngay'] != '') {
                            $where_data['m.denngay'] = $data['denngay'];
                        }
                        break;
                    case 'laixe':
                        if($data['laixe'] != ""){
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
                //$record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
                //$record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="'. base_url('htmltopdf/pdf_order')."?order_number=".$record->order_number.'" >PDF</a>';
                $record->custom_action .= '<a target="_blank" style="margin-right: 4px;" class="btn-danger" href="' . base_url('htmltopdf/pdf_order_kinhdoanh') . "?order_number=" . $record->order_number . '" >PDF</a>';
                $record->custom_action .= '<a style="margin-right: 4px;" class="btn-success" href="' . base_url('htmltopdf/pdf_order_kinhdoanh_excel') . "?order_number=" . $record->order_number . '" >Excel</a>';
                //$record->custom_action .= '<a target="_blank" class="btn-info" href="' . base_url('voxy_package_baocao_tonghop/xuathang_le') . "?order_number=" . $record->order_number . '&shipper_id=' . $record->shipper_id . '&shipped_at=' . $record->shipped_at . '" >Xuất</a>';
            }

            $record->custom_check = "<input type='checkbox' style='width:20px;' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            if ($record->total_price) {
                $record->total_price = "€" . number_format($record->total_price, 2);
            }

            if ($record->check_xuathang) {
                $record->check_xuathang = "đã xuất";
            }

            $customer = "";
            if ($record->customer) {
                $json_customer = get_object_vars(json_decode($record->customer));
                if(isset($json_customer['d_first_name'])){
                    $frist_name = $json_customer['d_first_name'];
                }elseif(isset($json_customer['first_name'])){
                    $frist_name = $json_customer['first_name'];
                }else{
                    $frist_name = "";
                }

                if(isset($json_customer['d_last_name'])){
                    $last_name = $json_customer['d_last_name'];
                }elseif(isset($json_customer['last_name'])){
                    $last_name = $json_customer['last_name'];
                }else{
                    $last_name = "";
                }
                $customer = $frist_name. "&nbsp" . $last_name;
            }

            if($record->key_word_customer == NULL){
                $record->key_word_customer = $customer;
            }

            if (isset($record->created_time)) {
                $date = date_create($record->created_time);
                $record->_created_time = date_format($date, 'Y-m-d');
                //$record->created_time = date_format($date, 'g:ia \o\n l d/m/Y');
            }

            if (isset($record->shipped_at)) {
                $date = date_create($record->shipped_at);
                $record->shipped_at = date_format($date, 'd/m/Y');
            }


            if (isset($record->time_fulfillments) && $record->time_fulfillments != null) {
                $date2 = date_create($record->time_fulfillments);
                $record->_time_fulfillments = date_format($date2, 'Y-m-d');
                $record->time_fulfillments = date_format($date2, 'g:ia \o\n l d/m/Y');
            } else {
                $record->time_fulfillments = "";
            }


            if (isset($record->time_paid) && $record->time_paid != null) {
                $date5 = date_create($record->time_paid);
                $record->time_paid = date_format($date5, 'g:ia \o\n l d/m/Y');
                $record->_time_paid = date_format($date5, 'Y-m-d');
            } else {
                $record->time_paid = "";
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
                $record->time_refund = date_format($date3, 'g:ia \o\n l d/m/Y');
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


        }
        return $record;
    }

    public function get_old()
    {
        $this->load->model('m_voxy_package_baocao_tonghop');
        $list_id = $this->input->post('list_id');
        $id_order = array();
        if ($list_id != false) {
            foreach (get_object_vars(json_decode($list_id))['list_id'] as $item) {
                $id_order[] = $this->m_voxy_package_baocao_tonghop->get_id_order($item);

            }
        } else {
            $id_order = false;
        }

        //get order in shopify       per function shopify_get_orders

        $this->load->model("m_voxy_package_baocao_tonghop");
        $this->load->model('m_voxy_connect_api_tinhcv');
        $result = $this->m_voxy_connect_api_tinhcv->shopify_get_orders($id_order);

        //if result ok , now add data to system quan ly kho
        if (isset($result['errors']) || isset($result['error_message'])) {
            $data_return["state"] = 0; /* state = 0 : error */
            $data_return["msg"] = "Get bản ghi không thành công trên hệ thống  onlineshop";
            echo json_encode($data_return);
            return FALSE;
        } else {

            $data = array();
            if (isset($result['orders'])) {
                foreach ($result['orders'] as $key => $item) {
                    $_item = get_object_vars($item);
                    $data[$key] = $_item;
                }
            } else {
                if (isset($result) && is_array($result)) {
                    foreach ($result as $item) {
                        $_item = get_object_vars($item['order']);
                        $data[] = $_item;
                    }
                }
            }

            //them data vao database
            $data_add = array();
            foreach ($data as $key2 => $item) {
                $data_add[$key2]['id_order'] = $item['id'];
                $data_add[$key2]['created_time'] = $item['created_at'];

                $data_add[$key2]['order_number'] = $item['order_number'];
                if (isset($item['customer'])) {
                    $data_add[$key2]['customer'] = json_encode($item['customer']);
                }
                $data_add[$key2]['financial_status'] = $item['financial_status'];
                $data_add[$key2]['fulfillment_status'] = $item['fulfillment_status'] == null ? "Unfulfilled" : $item['fulfillment_status'];
                $data_add[$key2]['total_price'] = $item['total_price'];
                //xu ly add them location and expriday for tung thang product
                foreach ($item['line_items'] as $_item2) {
                    $item2 = get_object_vars($_item2);
                    $_item2->location = $this->m_voxy_package_baocao_tonghop->get_location($item2['product_id']);
                    $_item2->expri_day = $this->m_voxy_package_baocao_tonghop->get_expriday($item2['product_id']);
                }
                //end xu ly
                $data_add[$key2]['line_items'] = json_encode($item['line_items']);
                $data_add[$key2]['note'] = isset($item['note']) ? $item['note'] : "null";
                if (isset($item['shipping_address'])) {
                    $data_add[$key2]['shipping_address'] = json_encode($item['shipping_address']);
                }
                if (isset($item['billing_address'])) {
                    $data_add[$key2]['billing_address'] = json_encode($item['billing_address']);
                }
                $data_add[$key2]['status'] = 0;

                if ($item['closed_at'] != null) {
                    $data_add[$key2]['status'] = "black";//black
                } elseif ($item['cancelled_at'] != null) {
                    $data_add[$key2]['status'] = "red";//red
                } elseif ($item['cancelled_at'] == null && $item['closed_at'] == null) {
                    $data_add[$key2]['status'] = "blue";//blue
                } else {
                }

                //get shipper_id for oder
                $data_add[$key2]['shipper_id'] = $this->m_voxy_package_baocao_tonghop->get_shipper_id($item['id']);
                $data_add[$key2]['shipper_name'] = $this->m_voxy_package_baocao_tonghop->get_name_shipper($data_add[$key2]['shipper_id']);
            }
            //check oder da ton tai, neu ton tai thi update , else add
            foreach ($data_add as $key => $item) {
                if ($this->m_voxy_package_baocao_tonghop->get_order_number($item['order_number']) == true) {
                    //nur update
                    $id = $this->m_voxy_package_baocao_tonghop->get_order_number($item['order_number']);
                    $insert_id = $this->data->update($id, $data_add[$key]);
                    $data_return["msg"] = "sua  bản ghi thành công vào database và shopify";
                    $data_return["key_name"] = $this->data->get_key_name();
                } else {
                    // insert into
                    $insert_id = $this->data->add($data_add[$key]);
                    $data_return["msg"] = "Thêm bản ghi thành công vào database và shopify";
                    $data_return["key_name"] = $this->data->get_key_name();
                }
            }

//            if ($insert_id) {
//                $data_return["key_name"] = $this->data->get_key_name();
//                $data_return["record"] = $data;
//                $data_return["state"] = 1; /* state = 1 : insert thành công */
//
//            } else {
//                $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
//                $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
//            }
            $data_return['orders'] = $result;
            $data_return["state"] = 1; /* state = 1 : ok  */
            //$data_return["msg"] = "Get bản ghi thanh cong (OK )trên hệ thống shopify  va them vao he thong quan ly kho";
            echo json_encode($data_return);
            return TRUE;
        }
        //end add data to database
    }

    public function get()
    {
        $this->load->model("m_voxy_package_baocao_tonghop");
        $this->load->model("m_voxy_package_kunden");
        $this->load->model('m_voxy_connect_api_tinhcv');

        $data = $this->m_voxy_package_baocao_tonghop->get_order_from_mysql();

        //them data vao database
        $data_add = array();
        foreach ($data as $key2 => $item) {
            $data_add[$key2]['id_order'] = $item['order_name'];
            $data_add[$key2]['created_time'] = $item['created_at'];
            $data_add[$key2]['order_number'] = $item['local_order_id'];

            if (isset($item['customer_id'])) {
                $data_add[$key2]['customer'] = $this->m_voxy_package_kunden->get_default_address($item['customer_id']);
            }
            //$data_add[$key2]['financial_status'] = $item['financial_status'];
            //$data_add[$key2]['fulfillment_status'] = $item['fulfillment_status'] == null ? "Unfulfilled" : $item['fulfillment_status'];
            $data_add[$key2]['total_price'] = $item['total_price'];

            //$data_add[$key2]['note'] = isset($item['note']) ? $item['note'] : "null";
            if (isset($item['shipping_address'])) {
                //$data_add[$key2]['shipping_address'] = json_encode($item['shipping_address']);
            }
            if (isset($item['billing_address'])) {
                //$data_add[$key2]['billing_address'] = json_encode($item['billing_address']);
            }
            $data_add[$key2]['status'] = "blue";


            $array = array();
            if(json_decode($item['local_order']) != null){
                $tamthoi = get_object_vars(json_decode($item['local_order']));
                    foreach ( $tamthoi['line_items'] as $key => $_item2) {
                        $item3 = get_object_vars($_item2);
                        $_item2->location = $this->m_voxy_package_baocao_tonghop->get_location($item3['product_id']);
                        $array[] = $_item2;
                    }
            }
            if($array){
                $data_add[$key2]['line_items'] = json_encode($array);
            }

            //get shipper_id for oder
            $data_add[$key2]['shipper_id'] = $item['shipper_id'];
            $data_add[$key2]['shipped_at'] = $item['shipped_at'];
            $data_add[$key2]['shipper_name'] = $this->m_voxy_package_baocao_tonghop->get_name_shipper($data_add[$key2]['shipper_id']);
        }

        //check oder da ton tai, neu ton tai thi update , else add
        foreach ($data_add as $key => $item) {
            if ($this->m_voxy_package_baocao_tonghop->get_order_number($item['order_number']) == true) {
                //nur update
                $id = $this->m_voxy_package_baocao_tonghop->get_order_number($item['order_number']);
                $update_id = $this->data->update($id, $data_add[$key]);
                $data_return["msg"] = "sua  bản ghi thành công vào database và shopify";
                $data_return["key_name"] = $this->data->get_key_name();
            } else {//insert
                $insert_id = $this->data->add($data_add[$key]);
                $data_return["msg"] = "Thêm bản ghi thành công vào database và shopify";
                $data_return["key_name"] = $this->data->get_key_name();
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
            $this->load->model('m_voxy_package_baocao_tonghop');
            foreach (json_decode($data_obj['line_items']) as $key => $item) {
                $_item_id = get_object_vars($item)['id'];
                //$item->expri_day = $this->m_voxy_package_baocao_tonghop->get_expriday($_item_id);
                //$item->location = $this->m_voxy_package_baocao_tonghop->get_location($_item_id);
                $item->oder_number = $oder_number;
                $data_pro[$key] = get_object_vars($item);
            }
            $data["list_input"] = $this->_get_form2($id, $data_pro);
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
        $data_json_shipping = array();
        $data_json_billing = array();
        $data_add_database = array();

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

        //add vao mang moi
        $data_add_database['note'] = $data['note'];
        $data_add_database['shipping_address'] = $json_shipping;
        $data_add_database['billing_address'] = $json_billing;

        //day du lieu len shopify
        //data oder update shipping and billing
        $orders_data['order'] = array(
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
        );
        $id_order = $data['id_order'];
        $data_post = json_encode($orders_data);
        $this->load->model('m_voxy_connect_api_tinhcv');
        $result = $this->m_voxy_connect_api_tinhcv->shopify_edit_orders($id_order, $data_post);
        //du lieu tra ve sau khi post
        if (!$result) {
            //$this->response(array('status' => 'failed'),200);
            $data_return["state"] = 0; /* state = 0 : insert ko thành công */
            $data_return["msg"] = "Sua bản ghi khong thành công vao onlineshop";
            echo json_encode($data_return);
            return FALSE;
        } else {
            //$this->response(array('status' => 'success'), 1709);
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sua bản ghi thành công vao onlineshop";
        }
        //end to shopify

        $value_old = $this->data->get_one($id, 'object');
        $update = $this->data->update($id, $data_add_database);
        if ($update) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sửa bản ghi thành công in database and onlineshop !";
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
        $this->load->model('m_voxy_package_baocao_tonghop');
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }
        if (isset($_GET["total_price"])) {
            //5 eu tien phi shipping
            $total_price = $_GET["total_price"] + 5;
        }
        $data = $this->m_voxy_package_baocao_tonghop->get_order($order_number);
        $_export = array();
        foreach ($data[0] as $item) {
            foreach (json_decode($item) as $key2 => $item2) {
                $_export[$key2] = get_object_vars($item2);
            }
        }

//        //ghep location like key to sort
//        $export = array();
//        foreach($_export as $key => $item){
//            if($item["location"] == false){
//                $item["location"] = $key."_NULL";
//            }
//            $export[$item["location"]] = $item;
//        }
//        //ksort tag theo khoa, krsort giam theo khoa hehe :D
//        ksort($export);

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        foreach ($_export as $key => $row) {
            $band[$key] = $row['location'];
            $auflage[$key] = $row['id'];
        }
        $band = array_column($_export, 'location');
        $auflage = array_column($_export, 'id');
        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $_export);
//Khởi tạo đối tượng
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Thông tin đơn hàng số ' . $order_number);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Thông tin đơn hàng số ' . $order_number);
        $excel->getActiveSheet()->setCellValue('A2', 'Tên SP');
        $excel->getActiveSheet()->setCellValue('B2', 'Gia €');
        $excel->getActiveSheet()->setCellValue('C2', 'Số Lượng');
        $excel->getActiveSheet()->setCellValue('D2', 'Giá tổng €');
        $excel->getActiveSheet()->setCellValue('E2', 'Vị trí');
        $excel->getActiveSheet()->setCellValue('F2', 'Ngày hết hạn');
        //$excel->getActiveSheet()->setCellValue('G2', 'Tai xe');
        //$excel->getActiveSheet()->setCellValue('G1', 'Tổng tiền thêm 5 eu phi shipping: € '.$total_price);
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2

        //$_data = json_decode($data);
        $numRow = 3;
        if ($_export != null) {
            foreach ($_export as $row) {
                //$row = get_object_vars($_row);
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $row['title']);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['price']);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['quantity']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['price'] * $row['quantity']);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['location']);
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['expri_day']);
                //$excel->getActiveSheet()->setCellValue('G' . $numRow, "dressen");
                $numRow++;
            }
            $excel->getActiveSheet()->setCellValue('B' . $numRow++, "Tong tien € : " . number_format($total_price, 2));
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
        $this->load->model('m_voxy_package_baocao_tonghop');
        if (isset($_GET["date"])) {
            $date = $_GET["date"];
        } else {
            $date = date("Y-m-d");
        }

        $result = $this->m_voxy_package_baocao_tonghop->get_data_pdf($date);

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
        $excel->getActiveSheet()->setCellValue('G2', 'Ngày hết hạn');
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
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['expri_day']);
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


    // xuat hang don tong voxy_package_baocao_tonghop/xuathang
    public function xuathang()
    {
        // theo tai xe nao
        $shipper_id = $this->input->post('shipper_id');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package');
        $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);
        // vi tri trong kho hang
        $kho = $this->input->post('kho');

        if ($kho == "all") {
            $name_kho = "Tổng LIL";
        } elseif ($kho == "lil") {
            $name_kho = "Kho LIL";
        } elseif ($kho == "AKL") {
            $name_kho = "Kho Lạnh";
        } else {
            $name_kho = "Cửa Hàng";
        }

        $date_time = $this->input->post('date_for_orders');

        $ngayxuathang = ($date_time == "") ? date('Y-m-d') : $date_time;
        $data = array();
        $data['sorting'] = $this->input->post('sorting');
        $data['date_time'] = $ngayxuathang;
        $data['shipper_name'] = $shipper_name;
        $data['shipper_id'] = $shipper_id;
        $data['name_kho'] = $name_kho;
        $data['kho'] = $kho;
        $data['list_products'] = $this->m_voxy_package->get_all_product();//chi dung cho add product
//infor product chỉnh sửa ở bảng infor xuất hàng
        $data['all_products'] = $this->data->xuathang($ngayxuathang, $shipper_id);//tat ca cac san pham trong don hang
        if ($data['all_products']['export2'] == null) {
            $data['thongbao'] = "ĐƠN HÀNG ĐÃ ĐƯỢC XUẤT RA KHỎI KHO";
        }
        $data['history_xuathang'] = json_decode($this->data->get_variants($ngayxuathang, $shipper_name));
        $data['history_xuathang_list_product'] = json_decode($this->data->get_list_product_infor_checkhang($ngayxuathang, $shipper_name));

        $this->session->set_userdata("search_string", "");
        $data["add_link"] = isset($data["add_link"]) ? $data["add_link"] : $this->url["add"];
        $data["get_link"] = isset($data["get_link"]) ? $data["get_link"] : $this->url["get"];
        $data["delete_list_link"] = isset($data["delete_list_link"]) ? $data["delete_list_link"] : site_url($this->url["delete"]);
        $data["ajax_data_link"] = isset($data["ajax_data_link"]) ? $data["ajax_data_link"] : site_url($this->name["class"] . "/ajax_list_data");
        $data["form_url"] = isset($data["form_url"]) ? $data["form_url"] : $data["ajax_data_link"];
        $data["form_conds"] = isset($data["form_conds"]) ? $data["form_conds"] : array();
        $data["title"] = $title = "Quản lý " . (isset($data["title"]) ? $data["title"] : $this->name["object"]);
        $viewFile = "base_manager/default_manager";

        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'table_xuathang.php')) {
            $viewFile = $this->name["view"] . '/' . 'table_xuathang';
        }
        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        $head_page = $this->load->view($this->path_theme_view . 'base_manager/header_manager', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header_manager.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_manager', $data, true);
        }
        $this->master_page($content, $head_page, $title);
    }

    public function minus_inventory()
    {
        $this->load->model('m_voxy_package_baocao_tonghop');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_orders');
        $list_id = $this->input->post('list_id');//de update inventory
        $list_order = $this->input->post('list_order');//list order for check_hang to 1
        $list_order = explode(",", $list_order);

        //update check_xuat hang o list tong
        if (isset($list_order) && is_array($list_order)) {
            foreach ($list_order as $order) {
                $data_update_check_xuathang = array(
                    "check_xuathang" => 1,
                );
                $this->m_voxy_package_orders->update_checked_xuathang($order, $data_update_check_xuathang);//add checked in talbe orders
            }
        }

        if ($list_id) {
            foreach ($list_id as $item) {
                if ($item['quantity'] == "") {
                    $item['quantity'] = 0;
                }

                if (['variant_id'] != "" && $item['variant_id'] != null) {
                    $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                    $check_variant1_id = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2_id = $this->m_voxy_package->check_variant2($item['variant_id']);

                    if ($check_variant1_id == true) {
                        $this->m_voxy_package->update_minus_inventory1($item['quantity'], $id);// tru du lieu san PHAM
                    }
                    if ($check_variant2_id == true) {
                        $this->m_voxy_package->update_minus_inventory2($item['quantity'], $id);//in DB // tru du lieu san PHAM
                    }
                }

            }
        }
        $list_products = $this->input->post('list_products');//them vao history

        $date = $this->input->post('date');//them vao history
        $laixe = $this->input->post('laixe');//them vao history

        $data_history = array();

        $data_history['date'] = $date;
        $data_history['laixe'] = $laixe;
        $data_history['list_products'] = json_encode($list_products);
        $data_history['variants'] = json_encode($list_id);


        if ($list_id) {//khi co san pham dc check box thi moi add vao history
            $id_history = $this->m_voxy_package_baocao_tonghop->check_update($data_history['date'], $data_history['laixe']);
            if ($id_history != false) {
                $variants_in_history = $this->m_voxy_package_baocao_tonghop->get_variants($data_history['date'], $data_history['laixe']);
                $_variant_in_history = json_decode($variants_in_history);//column variants

                $data_history_update = array();

                $data_history_update['date'] = $date;
                $data_history_update['laixe'] = $laixe;


                $list_id_new = array();
                foreach ($list_id as $item) {
                    foreach ($_variant_in_history as $item2) {

                        if ((int)$item['variant_id'] == (int)$item2->variant_id) {//neu thang moi add vao co trong database
                            $item['variant_id'] = $item2->variant_id;
                            $item['quantity'] = (int)$item2->quantity + (int)$item['quantity'];
                            if ($item['quantity_need'] == $item['quantity']) {
                                $item['data_da_xuat'] = "ja";
                            } else {
                                $item['data_da_xuat'] = "nein";
                            }
                        }
                    }
                    if ($item['quantity_need'] == $item['quantity']) {
                        $item['data_da_xuat'] = "ja";
                    } else {
                        $item['data_da_xuat'] = "nein";
                    }
                    $list_id_new[] = $item;
                }

                $data_history_update['variants'] = json_encode($list_id_new);
                $data_history_update['list_products'] = json_encode($list_products);
                $this->m_voxy_package_baocao_tonghop->update_infor_xuathang($data_history_update, $id_history);//nur update
            } else {
                $list_id_new = array();
                foreach ($list_id as $item) {
                    if ($item['quantity_need'] == $item['quantity']) {
                        $item['data_da_xuat'] = "ja";
                    } else {
                        $item['data_da_xuat'] = "nein";
                    }
                    $list_id_new[] = $item;
                }
                $data_history['variants'] = json_encode($list_id_new);
                $this->m_voxy_package_baocao_tonghop->add_infor_xuathang($data_history);//add new history
            }
        }

        $data = array();
        if ($list_id) {
            $data['state'] = 1;
            echo json_encode($data);
            return true;
        } else {
            $data['state'] = 0;
            echo json_encode($data);
            return true;
        }
    }

    public function minus_inventory_le()
    {
        $this->load->model('m_voxy_package_baocao_tonghop');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_orders');
        $list_id = $this->input->post('list_id');//de update inventory

        if ($list_id) {
            foreach ($list_id as $item) {
                if ($item['quantity'] == "") {
                    $item['quantity'] = 0;
                }
                $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                $check_variant1_id = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2_id = $this->m_voxy_package->check_variant2($item['variant_id']);

                if ($check_variant1_id == true) {
                    $this->m_voxy_package->update_minus_inventory1($item['quantity'], $id);//in DB
                }

                if ($check_variant2_id == true) {
                    $this->m_voxy_package->update_minus_inventory2($item['quantity'], $id);//in DB
                }
            }
        }
        $list_products = $this->input->post('list_products');//them vao history
        $date = $this->input->post('date');//them vao history
        $laixe = $this->input->post('laixe');//them vao history
        $order_number = $this->input->post('order_number');//them vao history

        $data_history = array();
        //foreach ($infor_other as $item){
        $data_history['date'] = $date;
        $data_history['laixe'] = $laixe;
        $data_history['list_products'] = json_encode($list_products);
        $data_history['variants'] = json_encode($list_id);
        $data_history['order_number'] = $order_number;
        //}

        if ($list_id) {//khi co san pham dc check box thi moi add vao history
            $id_history = $this->m_voxy_package_baocao_tonghop->check_update_le($order_number);
            if ($id_history != false) {
                $variants_in_history = $this->m_voxy_package_baocao_tonghop->get_variants_le($order_number);
                $_variant_in_history = json_decode($variants_in_history);

                $data_history_update = array();
                //foreach ($infor_other as $item){
                $data_history_update['date'] = $date;
                $data_history_update['laixe'] = $laixe;
                $data_history_update['order_number'] = $order_number;
                //}

                $list_id_new = array();
                foreach ($list_id as $item) {
                    foreach ($_variant_in_history as $item2) {
                        if ((int)$item['variant_id'] == (int)$item2->variant_id) {
                            $item['variant_id'] = $item2->variant_id;
                            $item['quantity'] = (int)$item2->quantity + (int)$item['quantity'];
                            if ($item['quantity_need'] == $item['quantity']) {
                                $item['data_da_xuat'] = "ja";
                            } else {
                                $item['data_da_xuat'] = "nein";
                            }
                        }
                    }
                    if ($item['quantity_need'] == $item['quantity']) {
                        $item['data_da_xuat'] = "ja";
                    } else {
                        $item['data_da_xuat'] = "nein";
                    }
                    $list_id_new[] = $item;
                }
                $data_history_update['variants'] = json_encode($list_id_new);
                $data_history_update['list_products'] = json_encode($list_products);
                $this->m_voxy_package_baocao_tonghop->update_infor_xuathang_le($data_history_update, $id_history);//nur update
                $order_id = $this->m_voxy_package_orders->get_order_number($order_number);//get id of order
                $data_update_check_xuathang = array(
                    "check_xuathang" => 1,
                );
                $this->m_voxy_package_orders->update_checked_xuathang($order_id, $data_update_check_xuathang);//add checked in talbe orders
            } else {
                $list_id_new = array();
                foreach ($list_id as $item) {
                    if ($item['quantity_need'] == $item['quantity']) {
                        $item['data_da_xuat'] = "ja";
                    } else {
                        $item['data_da_xuat'] = "nein";
                    }
                    $list_id_new[] = $item;
                }
                $data_history['variants'] = json_encode($list_id_new);
                $this->m_voxy_package_baocao_tonghop->add_infor_xuathang_le($data_history);//add new history
                $order_id = $this->m_voxy_package_orders->get_order_number($order_number);//get id of order
                $data_update_check_xuathang = array(
                    "check_xuathang" => 1,
                );
                $this->m_voxy_package_orders->update_checked_xuathang($order_id, $data_update_check_xuathang);//add checked in talbe orders
            }
        }

        $data = array();
        if ($list_id) {
            $data['state'] = 1;
            echo json_encode($data);
            return true;
        } else {
            $data['state'] = 0;
            echo json_encode($data);
            return true;
        }
    }

    public function xuathang_le()
    {
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package');
        // theo tai xe nao
        $shipper_id = $this->input->get('shipper_id');
        $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);
        $order_number = $this->input->get('order_number');

        // vi tri trong kho hang
        $kho = "all";
        if ($kho == "all") {
            $name_kho = "Tổng LIL";
        } elseif ($kho == "lil") {
            $name_kho = "Kho LIL";
        } elseif ($kho == "AKL") {
            $name_kho = "Kho Lạnh";
        } else {
            $name_kho = "Cửa Hàng";
        }

        $date_time = $this->input->get('shipped_at');
        $ngaygiaohang = ($date_time == "") ? date('Y-m-d') : $date_time;
        $data = array();
        $data['order_number'] = $order_number;
        $data['date_time'] = $ngaygiaohang;
        $data['shipper_name'] = $shipper_name;
        $data['shipper_id'] = $shipper_id;
        $data['name_kho'] = $name_kho;
        $data['kho'] = $kho;
        $data['list_products'] = $this->m_voxy_package->get_all_product();//chi dung cho add product

        //infor product chỉnh sửa ở bảng infor xuất hàng
        $data['all_products'] = $this->data->xuathang_le($order_number);
        if ($data['all_products']['export2'] == null) {
            $data['thongbao'] = "ĐƠN HÀNG ĐÃ ĐƯỢC XUẤT RA KHỎI KHO";
        }
        $data['history_xuathang'] = json_decode($this->data->get_variants_le($order_number));
        $data['history_xuathang_list_product'] = json_decode($this->data->get_list_product_infor_checkhang_le($order_number));
        $this->session->set_userdata("search_string", "");
        $data["add_link"] = isset($data["add_link"]) ? $data["add_link"] : $this->url["add"];
        $data["get_link"] = isset($data["get_link"]) ? $data["get_link"] : $this->url["get"];
        $data["delete_list_link"] = isset($data["delete_list_link"]) ? $data["delete_list_link"] : site_url($this->url["delete"]);
        $data["ajax_data_link"] = isset($data["ajax_data_link"]) ? $data["ajax_data_link"] : site_url($this->name["class"] . "/ajax_list_data");
        $data["form_url"] = isset($data["form_url"]) ? $data["form_url"] : $data["ajax_data_link"];
        $data["form_conds"] = isset($data["form_conds"]) ? $data["form_conds"] : array();
        $data["title"] = $title = "Quản lý " . (isset($data["title"]) ? $data["title"] : $this->name["object"]);
        $viewFile = "base_manager/default_manager";

        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'table_xuathang_le.php')) {
            $viewFile = $this->name["view"] . '/' . 'table_xuathang_le';
        }
        $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        $head_page = $this->load->view($this->path_theme_view . 'base_manager/header_manager', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header_manager.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_manager', $data, true);
        }
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
            $viewFile = '/voxy_package_baocao_tonghop/search_pro';
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
            $viewFile = '/voxy_package_baocao_tonghop/search_pro_for_title';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    //in list san pham , trang index san pham
    public function export_product_excel()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package');

        $shipper_id = $this->input->post('shipper_id');
        //$laixe = $this->m_voxy_package_orders->get_name_shipper($shipper_id);
        $laixe = "";

        $date = $this->input->get('date_for_orders');
        $date_end = $this->input->get('date_for_orders_end');

        //$kho = $this->input->post('kho');
        $kho = "all";
        if ($kho == "all") {
            $name_kho = "Tổng LIL";
        } elseif ($kho == "lil") {
            $name_kho = "Kho LIL";
        } elseif ($kho == "AKL") {
            $name_kho = "Kho Lạnh";
        } else {
            $name_kho = "Cửa Hàng";
        }

        $sorting = $this->input->get('sorting');//location or category
        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location"){
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }

        $_all_products = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tong($date,$date_end, $laixe);//bang infor xuathang
        $_all_products_le = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_le($date,$date_end, $laixe); //bang infor xuathang le
        $_all_products_xuattaikho = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_taikho($date,$date_end); //bang transfer_outkho

        $all_products['result_catid'] = array_merge($_all_products['result_catid'], $_all_products_le['result_catid'], $_all_products_xuattaikho['result_catid']);
        ksort($all_products['result_catid']);
        $all_products['export2'] = array_merge($_all_products['export2'], $_all_products_le['export2'],$_all_products_xuattaikho['export2']);
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
                    if(isset($item['variant_id']) && isset($item2['variant_id'])){
                        if ( $item['variant_id'] == $item2['variant_id']) {
                            if(isset($item['quantity']) && isset($item2['quantity'])){
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
        } elseif($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        }else{//sl_xuat
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
        $all_products['export2'] = $export2;// sap xep lai san pham
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------
        //get số lượng đã xuất của các variant  , so sánh theo variant_id để biét đã xuất bao nhiêu sản phẩm
        $_history_xuathang_old = json_decode($this->m_voxy_package_xuathang->baocao_get_variants($date,$date_end)); //bang infor xuathang
        $_history_xuathang = array();
        if(isset($_history_xuathang_old) && isset($_history_xuathang_old) != null ){
            foreach ($_history_xuathang_old as $item){
                $_item = get_object_vars($item);
                $_history_xuathang[] = $_item;
            }
        }

        $_history_xuathang_le_old = json_decode($this->m_voxy_package_xuathang->baocao_get_variants_le_listkiem($date,$date_end)); // bang infor xuathang le
        $_history_xuathang_le = array();
        if(isset($_history_xuathang_le_old) && isset($_history_xuathang_le_old) != null ){
            foreach ($_history_xuathang_le_old as $item){
                $_item = get_object_vars($item);
                $_history_xuathang_le[] = $_item;
            }
        }

        $_history_xuathang_taikho = $_all_products_xuattaikho; // bang voxy_transfer_out_kho
        /* //chua xuat hang
         if ($_history_xuathang == null && $_history_xuathang_le == null) {
             $html_content = "<div style='font-family: DejaVu Sans;'>Bạn chưa xuất hàng nên list kiểm ở đây chưa có dữ liệu !</div>";
             $this->pdf->loadHtml($html_content);
             $this->pdf->render();
             $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
             $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
             $this->pdf->stream($laixe . "-" . $date . ".pdf", array("Attachment" => 0));
         }
         // end chua xuat hang*/
        //cho nay ghepsan pham, 3!= 6 truong hop
        if ($_history_xuathang_le != null && $_history_xuathang == null && $_history_xuathang_taikho["export2"] == null) {
            $history_xuathang = $_history_xuathang_le;
        } else if ($_history_xuathang_le == null && $_history_xuathang != null && $_history_xuathang_taikho["export2"] == null) {
            $history_xuathang = $_history_xuathang;
        } else if ($_history_xuathang_le == null && $_history_xuathang == null && $_history_xuathang_taikho["export2"] != null) {
            $history_xuathang = $_history_xuathang_taikho["export2"];
        }else if ($_history_xuathang_le != null && $_history_xuathang != null && $_history_xuathang_taikho["export2"] == null){
            $history_xuathang = array_merge($_history_xuathang_le,$_history_xuathang);
        }else if ($_history_xuathang_le != null && $_history_xuathang == null && $_history_xuathang_taikho["export2"] != null){
            $history_xuathang = array_merge($_history_xuathang_le,$_history_xuathang_taikho["export2"]);
        }else if ($_history_xuathang_le == null && $_history_xuathang != null && $_history_xuathang_taikho["export2"] != null){
            $history_xuathang = array_merge($_history_xuathang,$_history_xuathang_taikho["export2"]);
        } else{
            $history_xuathang = array_merge($_history_xuathang_le, $_history_xuathang, $_history_xuathang_taikho["export2"]);
        }
        $export2_history = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($history_xuathang as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($history_xuathang as $key2 => $item2) {
                if ($key2 > $key) {
                    if(isset($item['variant_id']) && isset($item['quantity'])){
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['quantity'] = $item['quantity'] + $item2['quantity'];
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
            }
            $export2_history[] = $item;
        }

        //remove nhung thang giong di
        foreach ($export2_history as $key => $item) {
            foreach ($chiso_remove as $key_reomove => $item_remove) {
                unset($export2_history[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }

        $history_xuathang = $export2_history;// sap xep lai san pham
//---------------------------------------------------------------------------------------
        //ksort tag theo khoa, krsort giam theo khoa hehe :D
        //ksort($export);

//Khởi tạo đối tượng
        $excel = new PHPExcel();
        //$excel->setDefaultFont('Time New Roman', 13);
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Báo cáo' . $date.'to'.$date_end);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);//barcode le
        //$excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
//Xét in đậm cho khoảng cột
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 13,
                'name'  => 'Time New Roman'
            ));
        $styleArray2 = array(
            'font'  => array(
                'size'  => 13,
                'name'  => 'Time New Roman'
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
        //$excel->getActiveSheet()->setCellValue('A2', 'Category');
        $excel->getActiveSheet()->setCellValue('A2', 'SKU');
        $excel->getActiveSheet()->setCellValue('B2', 'Tên');
        $excel->getActiveSheet()->setCellValue('C2', 'Trước');
        $excel->getActiveSheet()->setCellValue('D2', 'Xuất');
        $excel->getActiveSheet()->setCellValue('E2', 'Sau');//le
        $excel->getActiveSheet()->setCellValue('F2', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('G2', 'Giá vốn');
        $excel->getActiveSheet()->setCellValue('H2', 'Thành tiền');

        //$excel->getActiveSheet()->setCellValue('H2', 'Theo Xe');
        //$excel->getActiveSheet()->setCellValue('G1', 'Tổng tiền thêm 5 eu phi shipping: € '.$total_price);
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 3;
        $tongtien = 0;

//        foreach ($all_products['result_catid'] as $catid) {//category
//                if (!isset($catid['cat_id']) || $catid['cat_id'] == false) {
//                    $excel->getActiveSheet()->setCellValue('A' . $numRow, "No category");
//                } else {
//                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $this->m_voxy_category->get_cat_title($catid['cat_id']));
//                }
            //begin product to print
            foreach ($all_products['export2'] as $row) {//san pham
                //check product co thuoc san pham do khong thi moi in ra
                if (!isset($row['cat_id']) || $row['cat_id'] == false) {// dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu
                    $___sl_daxuat = 0;
                    if ($history_xuathang != null) {
                        foreach ($history_xuathang as $item_xuat) {
                            if (isset($item_xuat['variant_id']) && isset($row['variant_id'])) {
                                if ($item_xuat['variant_id'] == $row['variant_id']) {
                                    $___sl_daxuat = $item_xuat['quantity'];
                                } else {
                                    $___sl_daxuat = 0;
                                }
                            }
                        }
                    }
                    if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                        $row['variant_title'] = "no infor";
                    }
                    if (!isset($row['variant_id'])) {
                        $row['variant_id'] = 0;
                    }

                    //xu ly do dai cua sku
                    if (strlen($row['sku']) > 5) {
                        $row['sku'] = substr($row['sku'], 0, 5);
                    }
                    if(strlen($row['sku']) == 0){
                        $row['sku'] = "no_sku;";
                    }

                    if(!isset($row['title'])){
                        $title = "";
                    }else{
                        $title = $row['title'];
                    }
                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $row['sku']);//sku
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $title);//ten
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, 0);//truoc
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $___sl_daxuat);//sau
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, 0);//ton kho
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['variant_title']);//packung or kartong
                    $excel->getActiveSheet()->setCellValue('G' . $numRow, 0);//gia von
                    $excel->getActiveSheet()->setCellValue('H' . $numRow, 0);//thanh tien
                    $numRow++;
                } else {
                    //if ($catid['cat_id'] == $row['cat_id']) {
                        $sl_daxuat = 0;
                        if ($history_xuathang != null) {
                            foreach ($history_xuathang as $item_xuat) {
                                if(isset($item_xuat['variant_id'])){
                                    if ($item_xuat['variant_id'] == $row['variant_id']) {
                                        //$quantity_xuathang = $item_xuat['quantity'];
                                        //$data_da_xuat = $item_xuat->data_da_xuat;
                                        $sl_daxuat = $item_xuat['quantity'];
                                    }
                                }
                            }

                        }

                        if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                            $array_location = explode(',', $row['location']);
                            $row['location'] = '';
                            foreach ($array_location as $key => $loca) {
                                $row['location'] .= $loca . '<br>';
                            }
                        }

                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $quantity_in_ware_house = 0;
                        if ($check_variant1 == true) {
                            $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                        }

                        if ($check_variant2 == true) {
                            $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                        }

                        $quantity_before = $sl_daxuat + $quantity_in_ware_house;

                        //xu ly do dai cua sku
                        if (strlen($row['sku']) > 5) {
                            $row['sku'] = substr($row['sku'], 0, 5);
                        }

                        if(strlen($row['sku']) == 0){
                            $row['sku'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }

                    $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                    if ($check_variant1 == true) {
                        //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
                        //gia von la gia mua
                        if($id != false){
                            $giavon = $this->m_voxy_package->get_gia_mua_le($id);
                        }else{
                            $giavon = 0;
                        }

                    }
                    if ($check_variant2 == true) {
                        if($id != false){
                            $giavon = $this->m_voxy_package->get_gia_mua_si($id);
                        }else{
                            $giavon = 0;
                        }
                    }

                        $thanhtien = (double)$giavon * (int)$sl_daxuat;

                        $tongtien += $thanhtien;

                        $excel->getActiveSheet()->setCellValue('A' . $numRow, $row['sku']);
                        $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['title']);
                        $excel->getActiveSheet()->setCellValue('C' . $numRow, $quantity_before);
                        $excel->getActiveSheet()->setCellValue('D' . $numRow, $sl_daxuat);//le
                        $excel->getActiveSheet()->setCellValue('E' . $numRow, $quantity_in_ware_house);
                        $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['variant_title']);
                        $excel->getActiveSheet()->setCellValue('G' . $numRow, $giavon);//gia von
                        $excel->getActiveSheet()->setCellValue('H' . $numRow, $thanhtien);//thanh tien
                    //}
                    $numRow++;
                }
                //add style
                $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
                $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
                //$excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);

            }
        //}
        $excel->getActiveSheet()->setCellValue('C' . $numRow++, "Tong tien € : ".$tongtien);

// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=BC-' . $date . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }
}