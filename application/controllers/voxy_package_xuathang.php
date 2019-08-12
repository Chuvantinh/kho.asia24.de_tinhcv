<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package_xuathang
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package_xuathang extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_package_xuathang",
            "view" => "voxy_package_xuathang",
            "model" => "m_voxy_package_xuathang",
            "object" => "Xuất Hàng"
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

        $this->load->model('m_voxy_package_xuathang');
        $data['shipper'] = $this->m_voxy_package_xuathang->get_all_shipper_id();
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

    protected function get_search_condition_new($data = array())
    {
        if (($data)) {
            $data = $this->input->get();
        }
        $where_data = array();
        $like_data = array();
        $list_field = array('ngay_dat_hang','ngay_giao_hang','laixe');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                switch ($value) {
                    case 'ngay_dat_hang':
                        if ($data['ngay_dat_hang'] != '') {
                            $where_data['m.ngaydathang'] = $data['ngay_dat_hang'];
                        }
                        break;
                    case 'ngay_giao_hang':
                        if ($data['ngay_giao_hang'] != '') {
                            $where_data['m.ngaygiaohang'] = $data['ngay_giao_hang'];
                        }
                        break;
                    case 'laixe':
                        if($data['laixe'] != ""){
                            $where_data['m.shipper_id'] = $data['laixe'];
                        }
                }
            }
        }

        $data_return = array(
            'custom_where' => $where_data,
            'custom_like' => $like_data
        );
        $this->session->set_userdata('arr_package_xuathang', json_encode($data_return));
        return $data_return;
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

        $data_get_ajax = $this->input->get();
        if ($data_get_ajax != false) {
            $data_get = $this->get_search_condition_new($data_get_ajax);
        } else {
            $json_conds = $this->session->userdata('arr_package_xuathang');
            $json_conds = json_decode($json_conds, TRUE);

//            if (isset($json_conds)) {
//                if (count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0) {
//                    $this->data->custom_conds = $this->get_search_condition_new();
//                } else {
//                    $this->data->custom_conds = $json_conds;
//                }
//            }
            if($json_conds){
                $data_get = $json_conds;
            }else{
                $data_get = "";
            }

        }
        //var_dump($this->session->userdata('arr_package_xuathang'));die;
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
        $record     = $this->m_voxy_package_xuathang->get_list_table_xuathangtaikho($search_string, $where_condition, $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->m_voxy_package_xuathang->get_list_table_xuathangtaikho($search_string, $where_condition, 0, 0, $order));
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

        $viewFile = 'voxy_package_xuathang/' . 'table';
        $content    = $this->load->view($viewFile, $data, true);

        if ($this->input->is_ajax_request()) {
            //$data_return["callback"]    = "get_manager_data_response";
            $data_return["state"]       = 1;
            $data_return["html"]        = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    public function ajax_list_data_xuathangtaikho($data = Array())
    {
        $this->load->model('m_voxy_package_xuathang');

        $data_get = $this->input->get();
        if ($data_get && is_array($data_get)) {
            $this->data->custom_conds = $this->get_search_condition($data_get);
        } else {
            $json_conds = $this->session->userdata('arr_package_xuathang');
            $json_conds = json_decode($json_conds, TRUE);

            if (isset($json_conds)) {
                if (count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0) {
                    $this->data->custom_conds = $this->get_search_condition();
                } else {
                    $this->data->custom_conds = $json_conds;
                }
            }
        }

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
        $record     = $this->m_voxy_package_xuathang->get_list_table_xuathangtaikho($search_string, $where_condition, $limit, $post, $order, $totalItem);

        if (isset($data['call_api']) && $data['call_api']) {
            // ko xu ly gi ca
        } else {
            // code de phong, hoi ngo ngan 1 chut
            if ($totalItem < 0) {
                $totalItem = count($this->m_voxy_package_xuathang->get_list_table_xuathangtaikho($search_string, $where_condition, 0, 0, $order));
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

        $viewFile = 'voxy_package_xuathang/' . 'table';
        $content    = $this->load->view($viewFile, $data, true);

        if ($this->input->is_ajax_request()) {
            //$data_return["callback"]    = "get_manager_data_response";
            $data_return["state"]       = 1;
            $data_return["html"]        = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    public function ajax_list_data_xuathang_tai_baocaotonghop($data = Array())
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
                $record->custom_action .= '<a target="_blank" style="margin-right: 4px;" class="btn-danger" href="' . base_url('htmltopdf/pdf_order') . "?order_number=" . $record->order_number . '" >PDF</a>';
                $record->custom_action .= '<a target="_blank" class="btn-info" href="' . base_url('voxy_package_xuathang/xuathang_le') . "?order_number=" . $record->order_number . '&shipper_id=' . $record->shipper_id . '&shipped_at=' . $record->shipped_at . '" >Xuất</a>';
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
        $this->load->model('m_voxy_package_xuathang');
        $list_id = $this->input->post('list_id');
        $id_order = array();
        if ($list_id != false) {
            foreach (get_object_vars(json_decode($list_id))['list_id'] as $item) {
                $id_order[] = $this->m_voxy_package_xuathang->get_id_order($item);

            }
        } else {
            $id_order = false;
        }

        //get order in shopify       per function shopify_get_orders

        $this->load->model("m_voxy_package_xuathang");
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
                    $_item2->location = $this->m_voxy_package_xuathang->get_location($item2['product_id']);
                    $_item2->expri_day = $this->m_voxy_package_xuathang->get_expriday($item2['product_id']);
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
                $data_add[$key2]['shipper_id'] = $this->m_voxy_package_xuathang->get_shipper_id($item['id']);
                $data_add[$key2]['shipper_name'] = $this->m_voxy_package_xuathang->get_name_shipper($data_add[$key2]['shipper_id']);
            }
            //check oder da ton tai, neu ton tai thi update , else add
            foreach ($data_add as $key => $item) {
                if ($this->m_voxy_package_xuathang->get_order_number($item['order_number']) == true) {
                    //nur update
                    $id = $this->m_voxy_package_xuathang->get_order_number($item['order_number']);
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
        $this->load->model("m_voxy_package_xuathang");
        $this->load->model("m_voxy_package_kunden");
        $this->load->model('m_voxy_connect_api_tinhcv');

        $data = $this->m_voxy_package_xuathang->get_order_from_mysql();

        //them data vao database
        $data_add = array();
        foreach ($data as $key2 => $item) {
            $data_add[$key2]['id_order'] = $item['order_name'];
            $data_add[$key2]['created_time'] = $item['created_at'];
            $data_add[$key2]['order_number'] = $item['local_order_id'];

            if (isset($item['customer_id'])) {
                $data_add[$key2]['customer'] = $this->m_voxy_package_kunden->get_default_address($item['customer_id']);
            }

            if (isset($item['customer_id'])) {
                $key_word_customer = $this->m_voxy_package_kunden->get_keyword($item['customer_id']);
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
            $item_local_order_new = str_replace(array("\r\n", "\r", "\n"),"",$item['local_order']);
            if(json_decode($item_local_order_new) != null){
                $tamthoi = get_object_vars(json_decode($item_local_order_new));
                foreach ( $tamthoi['line_items'] as $key => $_item2) {
                    $item3 = get_object_vars($_item2);
                    $_item2->location = $this->m_voxy_package_xuathang->get_location($item3['product_id']);
                    $array[] = $_item2;
                }
            }
            if($array){
                $data_add[$key2]['line_items'] = json_encode($array);
            }

            //get shipper_id for oder
            $data_add[$key2]['customer_id'] = (int)$item['customer_id'];
            $data_add[$key2]['key_word_customer'] = $key_word_customer;

            $data_add[$key2]['shipper_id'] = $item['shipper_id'];
            $data_add[$key2]['shipped_at'] = $item['shipped_at'];
            $data_add[$key2]['shipper_name'] = $this->m_voxy_package_xuathang->get_name_shipper($data_add[$key2]['shipper_id']);
        }

        //check oder da ton tai, neu ton tai thi update , else add
        foreach ($data_add as $key => $item) {
            if ($this->m_voxy_package_xuathang->get_order_number($item['order_number']) == true) {
                //nur update
                $id = $this->m_voxy_package_xuathang->get_order_number($item['order_number']);
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
            $this->load->model('m_voxy_package_xuathang');
            foreach (json_decode($data_obj['line_items']) as $key => $item) {
                $_item_id = get_object_vars($item)['id'];
                //$item->expri_day = $this->m_voxy_package_xuathang->get_expriday($_item_id);
                //$item->location = $this->m_voxy_package_xuathang->get_location($_item_id);
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
        $this->load->model('m_voxy_package_xuathang');
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }
        if (isset($_GET["total_price"])) {
            //5 eu tien phi shipping
            $total_price = $_GET["total_price"] + 5;
        }
        $data = $this->m_voxy_package_xuathang->get_order($order_number);
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
        $this->load->model('m_voxy_package_xuathang');
        if (isset($_GET["date"])) {
            $date = $_GET["date"];
        } else {
            $date = date("Y-m-d");
        }

        $result = $this->m_voxy_package_xuathang->get_data_pdf($date);

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


    // xuat hang don tong voxy_package_xuathang/xuathang
    public function xuathang()
    {
        // theo tai xe nao
        $shipper_id = $this->input->post('shipper_id');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package');
        $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);
        // vi tri trong kho hang
        $kho = $this->input->post('kho');
        $sorting = $this->input->post('sorting');

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
        //$data['list_products'] = $this->m_voxy_package->get_all_product();//chi dung cho add product
//infor product chỉnh sửa ở bảng infor xuất hàng
        $list_id_to_nhathang = $this->input->post('list_id_to_nhathang');

        if($list_id_to_nhathang != ""){
            $list_id_to_nhathang = get_object_vars(json_decode($list_id_to_nhathang))['list_id'];
        }

        $data['all_products'] = $this->data->xuathang($ngayxuathang, $shipper_id, $sorting, $list_id_to_nhathang);//tat ca cac san pham trong don hang

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
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_transfer');
        $list_id = $this->input->post('list_id');//de update inventory
        $list_order = $this->input->post('list_order');//list order for check_hang to 1
        $list_order = explode(",", $list_order);

        //begin update to the orders
        $list_replace = $this->input->post('list_replace');//thay the san pham trong don hang

        if($list_replace){
            foreach ($list_replace as $replace){
                //step1 : get don hang nay, line_items, so luong van vay, gia van the, so sanh theo variant_id_ product _id., title
                $_line_items = $this->m_voxy_package_orders->get_line_items($replace['data_order_number']);
                $_line_items = json_decode($_line_items);
                $line_items = array();
                $id_order = $this->m_voxy_package_orders->get_id_order_number($replace['data_order_number']);

                foreach ($_line_items as $item){
                    $item = get_object_vars($item);

                    //step 2: //get information of san pham moi de them vao
                    $variant_id_new = $replace['variant_id'];
                    $id = $this->m_voxy_package->get_id_from_variant($variant_id_new);

                    $information_new = $this->m_voxy_package->get_all_infor($id);

                    $check_variant1 = $this->m_voxy_package->check_variant1($variant_id_new);
                    $check_variant2 = $this->m_voxy_package->check_variant2($variant_id_new);

                    if ($check_variant1 == true) {
                        $infor = $information_new[0];
                        $sku = $infor['sku1'];
                        $quantity = $item['quantity'];//old
                        $price = $item['price'];//old
                        $title = $infor['title'];
                        $product_id = $infor['id_shopify'];
                        $variant_id = $infor['variant1_id'];
                        $variant_title = $infor['option1'];
                        $item_note = "";
                        $hangve = 0;
                        $hanghong = 0;
                        $hangthieu = 0;
                        $hangthem = 0;
                        $refund = 1;
                    }
                    if ($check_variant2 == true) {
                        $infor = $information_new[0];
                        $sku = $infor['sku2'];
                        $quantity = $item['quantity'];//old
                        $price = $item['price'];//old
                        $title = $infor['title'];
                        $product_id = $infor['id_shopify'];
                        $variant_id = $infor['variant2_id'];
                        $variant_title = $infor['option2'];
                        $item_note = "";
                        $hangve = 0;
                        $hanghong = 0;
                        $hangthieu = 0;
                        $hangthem = 0;
                        $refund = 1;
                    }

                    //step 3: update lai line_items, update lai is_edittable
                    if($replace['data_variant_old'] != 0){
                        if($replace['data_variant_old'] == $item['variant_id']){
                            $item['sku'] = $sku;
                            $item['quantity'] = $quantity;//old
                            $item['price'] = $price;//old
                            $item['title'] = $title;
                            $item['product_id'] = $product_id;
                            $item['variant_id'] = $variant_id;
                            $item['variant_title'] = $variant_title;
                            $item['item_note'] = $item_note;
                            $item['hangve'] = $hangve;
                            $item['hanghong'] = $hanghong;
                            $item['hangthieu'] = $hangthieu;
                            $item['hangthem'] = $hangthem;
                            $item['refund'] = $refund;
                        }
                    }else if($replace['data_product_id_old'] != 0 && $replace['data_product_id_old'] == $item['product_id']){//so sanh theo product id
                        $item['sku'] = $sku;
                        $item['quantity'] = $quantity;//old
                        $item['price'] = $price;//old
                        $item['title'] = $title;
                        $item['product_id'] = $product_id;
                        $item['variant_id'] = $variant_id;
                        $item['variant_title'] = $variant_title;
                        $item['item_note'] = $item_note;
                        $item['hangve'] = $hangve;
                        $item['hanghong'] = $hanghong;
                        $item['hangthieu'] = $hangthieu;
                        $item['hangthem'] = $hangthem;
                        $item['refund'] = $refund;
                    }else{//so sanh theo title
                        if($replace['data_title'] != ""){
                           if($replace['data_title'] == $item['title']){
                               $item['sku'] = $sku;
                               $item['quantity'] = $quantity;//old
                               $item['price'] = $price;//old
                               $item['title'] = $title;
                               $item['product_id'] = $product_id;
                               $item['variant_id'] = $variant_id;
                               $item['variant_title'] = $variant_title;
                               $item['item_note'] = $item_note;
                               $item['hangve'] = $hangve;
                               $item['hanghong'] = $hanghong;
                               $item['hangthieu'] = $hangthieu;
                               $item['hangthem'] = $hangthem;
                               $item['refund'] = $refund;
                           }
                        }
                    }
                    $line_items[] = $item;
                }
            //step 4: update lai co so du lieu vs oder number
               $data_add_database['line_items'] = json_encode($line_items);
               $data_add_database['edit_kho'] = 1;
               $this->m_voxy_package_orders->update($id_order, $data_add_database);
            }
        }
        //end update to the orders


        $date_time_lan1 = $this->input->post('date');

        //update check_xuat hang o list tong
        if (isset($list_order) && is_array($list_order)) {
            foreach ($list_order as $order) {
                $data_update_check_xuathang = array(
                    "check_xuathang" => 1,
                );
                $this->m_voxy_package_orders->update_checked_xuathang($order, $data_update_check_xuathang);//add checked in talbe orders

                //update tu dong mac dinh la thanh toan lan 1 ok luon, de ko phai vao tung don hang chinh so tien da thu lan 1
                //don nao ma no thi la vao sua sau  , nhu thang shopify vay
                // so tien se dc lay trong m_voxy_package_orders - update_thanhtoan_lan1_xuathang
                $this->m_voxy_package_orders->update_thanhtoan_lan1_xuathang($order, $date_time_lan1);//add checked in talbe orders
            }
        }

        if ($list_id) {
            $data_nhaphang = array();
            foreach ($list_id as $item) {

                if ($item['quantity'] == "") {
                    $item['quantity'] = 0;
                }

                $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                $check_variant1_id = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2_id = $this->m_voxy_package->check_variant2($item['variant_id']);

                if($item['nk'] === "true"){//nk= true -> ngoai kho , them vao nhap hang, con van tru ben duoi
                    $data_nhaphang[] = $item;
                    //plus
                    if ($check_variant1_id == true) {
                        $this->m_voxy_package->update_plus_inventory1($item['quantity'], $id);//in DB
                    }

                    if ($check_variant2_id == true) {
                        $this->m_voxy_package->update_plus_inventory2($item['quantity'], $id);//in DB
                    }
                }
                //minus
                if(isset($item['variant_id'])){
                    if ($check_variant1_id == true) {
                        $this->m_voxy_package->update_minus_inventory1($item['quantity'], $id);//in DB
                    }

                    if ($check_variant2_id == true) {
                        $this->m_voxy_package->update_minus_inventory2($item['quantity'], $id);//in DB
                    }
                }
            }
        }

        if($data_nhaphang){
            $data_add_nhaphang = array();
            $i = 0;
            foreach ($data_nhaphang as $key => $item){
                $i++;
                $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

                $item['sl_nhap'] = $item['quantity'];

                if ($check_variant1 == true) {
                    //GET SKU,get don vi ,gia ban, thanh tien.
                    $data_get = $this->m_voxy_package->get_all_infor($id);
                    if($data_get){
                        foreach ($data_get as $item2){
                            $item['cat_id'] = $item2['cat_id'];
                            $item['product_id'] = $item2['id_shopify'];
                            $item['sl_kho'] = $item2['inventory_quantity1'];
                            $item['variant_title'] = $item2['option1'];
                            $item['location'] = $item2['location'];
                            $item['title'] = $item2['title'];
                            $item['sku'] = $item2['sku1'];
                            $item['giabannew'] = "";
                            $item['gianhapnew'] = "";
                            $item['thanhtien'] = 0;
                            $item['average_price'] = "";
                        }
                    }
                }
                if ($check_variant2 == true) {
                    $data_get = $this->m_voxy_package->get_all_infor($id);
                    if($data_get){
                        foreach ($data_get as $item2){
                            $item['cat_id'] = $item2['cat_id'];
                            $item['product_id'] = $item2['id_shopify'];
                            $item['sl_kho'] = $item2['inventory_quantity2'];
                            $item['variant_title'] = $item2['option2'];
                            $item['location'] = $item2['location'];
                            $item['title'] = $item2['title'];
                            $item['sku'] = $item2['sku1'];
                            $item['giabannew'] = "";
                            $item['gianhapnew'] = "";
                            $item['thanhtien'] = 0;
                            $item['average_price'] = "";
                        }
                    }
                }

                $data_add_nhaphang[$i] = $item;
            }
        }

        if(isset($data_add_nhaphang)){
            $add_nhaphang_end = array();
            $add_nhaphang_end['product_variants']    = json_encode($data_add_nhaphang);
            $add_nhaphang_end['name'] = "Lay them";
            $add_nhaphang_end['date'] = date("Y-m-d");
            $add_nhaphang_end['vendor'] = 55;//them_cuahang
            $add_nhaphang_end['total_price'] = 0;//them_cuahang
            $add_nhaphang_end['date_save'] = date("m-d-Y H:i:s");
            $add_nhaphang_end['status'] = 1;
            $add_nhaphang_end['mswt'] = 2;
            $this->m_voxy_transfer->add($add_nhaphang_end);
        }//end nhap hang cho cac san pham ngoai kho

        $list_products = $this->input->post('list_products');//them vao history

        $date = $this->input->post('date');//them vao history
        $laixe = $this->input->post('laixe');//them vao history

        $data_history = array();

        $data_history['date'] = $date;
        $data_history['laixe'] = $laixe;
        $data_history['list_products'] = json_encode($list_products);
        $data_history['variants'] = json_encode($list_id);


        if ($list_id) {//khi co san pham dc check box thi moi add vao history
            $id_history = $this->m_voxy_package_xuathang->check_update($data_history['date'], $data_history['laixe']);
            if ($id_history != false) {
                $variants_in_history = $this->m_voxy_package_xuathang->get_variants($data_history['date'], $data_history['laixe']);
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

//                            if ($item['quantity_need'] == $item['quantity']) {
//                                $item['data_da_xuat'] = "ja";
//                            } else {
//                                $item['data_da_xuat'] = "nein";
//                            }
                        }
                    }
//                    if ($item['quantity_need'] == $item['quantity']) {
//                        $item['data_da_xuat'] = "ja";
//                    } else {
//                        $item['data_da_xuat'] = "nein";
//                    }
                    $list_id_new[] = $item;
                }

                $data_history_update['variants'] = json_encode($list_id_new);
                $data_history_update['list_products'] = json_encode($list_products);
                $this->m_voxy_package_xuathang->update_infor_xuathang($data_history_update, $id_history);//nur update
            } else {
                $list_id_new = array();
                foreach ($list_id as $item) {
//                    if ($item['quantity_need'] == $item['quantity']) {
//                        $item['data_da_xuat'] = "ja";
//                    } else {
//                        $item['data_da_xuat'] = "nein";
//                    }
                    $list_id_new[] = $item;
                }
                $data_history['variants'] = json_encode($list_id_new);
                $this->m_voxy_package_xuathang->add_infor_xuathang($data_history);//add new history
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
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_transfer');
        $list_id = $this->input->post('list_id');//de update inventory

        //begin update to the orders
        $list_replace = $this->input->post('list_replace');//thay the san pham trong don hang

        if($list_replace){
            foreach ($list_replace as $replace){
                //step1 : get don hang nay, line_items, so luong van vay, gia van the, so sanh theo variant_id_ product _id., title
                $_line_items = $this->m_voxy_package_orders->get_line_items($replace['data_order_number']);
                $_line_items = json_decode($_line_items);
                $line_items = array();
                $id_order = $this->m_voxy_package_orders->get_id_order_number($replace['data_order_number']);

                foreach ($_line_items as $item){
                    $item = get_object_vars($item);

                    //step 2: //get information of san pham moi de them vao
                    $variant_id_new = $replace['variant_id'];
                    $id = $this->m_voxy_package->get_id_from_variant($variant_id_new);

                    $information_new = $this->m_voxy_package->get_all_infor($id);

                    $check_variant1 = $this->m_voxy_package->check_variant1($variant_id_new);
                    $check_variant2 = $this->m_voxy_package->check_variant2($variant_id_new);

                    if ($check_variant1 == true) {
                        $infor = $information_new[0];
                        $sku = $infor['sku1'];
                        $quantity = $item['quantity'];//old
                        $price = $item['price'];//old
                        $title = $infor['title'];
                        $product_id = $infor['id_shopify'];
                        $variant_id = $infor['variant1_id'];
                        $variant_title = $infor['option1'];
                        $item_note = "";
                        $hangve = 0;
                        $hanghong = 0;
                        $hangthieu = 0;
                        $hangthem = 0;
                        $refund = 1;
                    }
                    if ($check_variant2 == true) {
                        $infor = $information_new[0];
                        $sku = $infor['sku2'];
                        $quantity = $item['quantity'];//old
                        $price = $item['price'];//old
                        $title = $infor['title'];
                        $product_id = $infor['id_shopify'];
                        $variant_id = $infor['variant2_id'];
                        $variant_title = $infor['option2'];
                        $item_note = "";
                        $hangve = 0;
                        $hanghong = 0;
                        $hangthieu = 0;
                        $hangthem = 0;
                        $refund = 1;
                    }

                    //step 3: update lai line_items, update lai is_edittable
                    if($replace['data_variant_old'] != 0){
                        if($replace['data_variant_old'] == $item['variant_id']){
                            $item['sku'] = $sku;
                            $item['quantity'] = $quantity;//old
                            $item['price'] = $price;//old
                            $item['title'] = $title;
                            $item['product_id'] = $product_id;
                            $item['variant_id'] = $variant_id;
                            $item['variant_title'] = $variant_title;
                            $item['item_note'] = $item_note;
                            $item['hangve'] = $hangve;
                            $item['hanghong'] = $hanghong;
                            $item['hangthieu'] = $hangthieu;
                            $item['hangthem'] = $hangthem;
                            $item['refund'] = $refund;
                        }
                    }else if($replace['data_product_id_old'] != 0 && $replace['data_product_id_old'] == $item['product_id']){//so sanh theo product id
                        $item['sku'] = $sku;
                        $item['quantity'] = $quantity;//old
                        $item['price'] = $price;//old
                        $item['title'] = $title;
                        $item['product_id'] = $product_id;
                        $item['variant_id'] = $variant_id;
                        $item['variant_title'] = $variant_title;
                        $item['item_note'] = $item_note;
                        $item['hangve'] = $hangve;
                        $item['hanghong'] = $hanghong;
                        $item['hangthieu'] = $hangthieu;
                        $item['hangthem'] = $hangthem;
                        $item['refund'] = $refund;
                    }else{//so sanh theo title
                        if($replace['data_title'] != ""){
                            if($replace['data_title'] == $item['title']){
                                $item['sku'] = $sku;
                                $item['quantity'] = $quantity;//old
                                $item['price'] = $price;//old
                                $item['title'] = $title;
                                $item['product_id'] = $product_id;
                                $item['variant_id'] = $variant_id;
                                $item['variant_title'] = $variant_title;
                                $item['item_note'] = $item_note;
                                $item['hangve'] = $hangve;
                                $item['hanghong'] = $hanghong;
                                $item['hangthieu'] = $hangthieu;
                                $item['hangthem'] = $hangthem;
                                $item['refund'] = $refund;
                            }
                        }
                    }
                    $line_items[] = $item;
                }
                //step 4: update lai co so du lieu vs oder number
                $data_add_database['line_items'] = json_encode($line_items);
                $data_add_database['edit_kho'] = 1;
                $this->m_voxy_package_orders->update($id_order, $data_add_database);
            }
        }
        //end update to the orders

        if ($list_id) {
            $data_nhaphang = array();
            foreach ($list_id as $item) {

                if ($item['quantity'] == "") {
                    $item['quantity'] = 0;
                }

                $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                $check_variant1_id = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2_id = $this->m_voxy_package->check_variant2($item['variant_id']);

                if($item['nk'] === "true"){//nk= true -> ngoai kho , them vao nhap hang, con van tru ben duoi
                    $data_nhaphang[] = $item;
                    //plus
                    if ($check_variant1_id == true) {
                        $this->m_voxy_package->update_plus_inventory1($item['quantity'], $id);//in DB
                    }

                    if ($check_variant2_id == true) {
                        $this->m_voxy_package->update_plus_inventory2($item['quantity'], $id);//in DB
                    }
                }
                //minus
                if ($check_variant1_id == true) {
                    $this->m_voxy_package->update_minus_inventory1($item['quantity'], $id);//in DB
                }

                if ($check_variant2_id == true) {
                    $this->m_voxy_package->update_minus_inventory2($item['quantity'], $id);//in DB
                }

            }
        }

        if($data_nhaphang){
            $data_add_nhaphang = array();
            $i = 0;
            foreach ($data_nhaphang as $key => $item){
                $i++;
                $id = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

                $item['sl_nhap'] = $item['quantity'];

                if ($check_variant1 == true) {
                    //GET SKU,get don vi ,gia ban, thanh tien.
                    $data_get = $this->m_voxy_package->get_all_infor($id);
                    if($data_get){
                        foreach ($data_get as $item2){
                            $item['cat_id'] = $item2['cat_id'];
                            $item['product_id'] = $item2['id_shopify'];
                            $item['sl_kho'] = $item2['inventory_quantity1'];
                            $item['variant_title'] = $item2['option1'];
                            $item['location'] = $item2['location'];
                            $item['title'] = $item2['title'];
                            $item['sku'] = $item2['sku1'];
                            $item['giabannew'] = "";
                            $item['gianhapnew'] = "";
                            $item['thanhtien'] = 0;
                            $item['average_price'] = "";
                        }
                    }
                }
                if ($check_variant2 == true) {
                    $data_get = $this->m_voxy_package->get_all_infor($id);
                    if($data_get){
                        foreach ($data_get as $item2){
                            $item['cat_id'] = $item2['cat_id'];
                            $item['product_id'] = $item2['id_shopify'];
                            $item['sl_kho'] = $item2['inventory_quantity2'];
                            $item['variant_title'] = $item2['option2'];
                            $item['location'] = $item2['location'];
                            $item['title'] = $item2['title'];
                            $item['sku'] = $item2['sku1'];
                            $item['giabannew'] = "";
                            $item['gianhapnew'] = "";
                            $item['thanhtien'] = 0;
                            $item['average_price'] = "";
                        }
                    }
                }

                $data_add_nhaphang[$i] = $item;
            }
        }

        if(isset($data_add_nhaphang)){
            $add_nhaphang_end = array();
            $add_nhaphang_end['product_variants']    = json_encode($data_add_nhaphang);
            $add_nhaphang_end['name'] = "Lay them";
            $add_nhaphang_end['date'] = date("Y-m-d");
            $add_nhaphang_end['vendor'] = 55;//them_cuahang
            $add_nhaphang_end['total_price'] = 0;//them_cuahang
            $add_nhaphang_end['date_save'] = date("m-d-Y H:i:s");
            $add_nhaphang_end['status'] = 1;
            $add_nhaphang_end['mswt'] = 2;
            $this->m_voxy_transfer->add($add_nhaphang_end);
        }//end nhap hang cho cac san pham ngoai kho

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

        if ($list_id) {
            $id_history = $this->m_voxy_package_xuathang->check_update_le($order_number);

            if ($id_history != false) {
                $variants_in_history = $this->m_voxy_package_xuathang->get_variants_le($order_number);
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
                            //COMMENT DATE 24.05
//                            if ($item['quantity_need'] == $item['quantity']) {
//                                $item['data_da_xuat'] = "ja";
//                            } else {
//                                $item['data_da_xuat'] = "nein";
//                            }
                        }
                    }
                    //COMMENT DATE 24.05
//                    if ($item['quantity_need'] == $item['quantity']) {
//                        $item['data_da_xuat'] = "ja";
//                    } else {
//                        $item['data_da_xuat'] = "nein";
//                    }

                    $list_id_new[] = $item;
                }

                $data_history_update['variants'] = json_encode($list_id_new);
                $data_history_update['list_products'] = json_encode($list_products);
                $this->m_voxy_package_xuathang->update_infor_xuathang_le($data_history_update, $id_history);//nur update
                $order_id = $this->m_voxy_package_orders->get_order_number($order_number);//get id of order
                $data_update_check_xuathang = array(
                    "check_xuathang" => 1,
                );
                $this->m_voxy_package_orders->update_checked_xuathang($order_id, $data_update_check_xuathang);//add checked in talbe orders
            } else {
                $list_id_new = array();
                foreach ($list_id as $item) {
                    //COMMENT DATE 24.05
//                    if ($item['quantity_need'] == $item['quantity']) {
//                        $item['data_da_xuat'] = "ja";
//                    } else {
//                        $item['data_da_xuat'] = "nein";
//                    }
                    $list_id_new[] = $item;
                }

                $data_history['variants'] = json_encode($list_id_new);
                $this->m_voxy_package_xuathang->add_infor_xuathang_le($data_history);//add new history
                $order_id = $this->m_voxy_package_orders->get_order_number($order_number);//get id of order
                $data_update_check_xuathang = array(
                    "check_xuathang" => 1,
                );
                $this->m_voxy_package_orders->update_checked_xuathang($order_id, $data_update_check_xuathang);//add checked in talbe orders


                $date_time_lan1 = $this->input->post('date');
                //update tu dong mac dinh la thanh toan lan 1 ok luon, de ko phai vao tung don hang chinh so tien da thu lan 1
                //don nao ma no thi la vao sua sau  , nhu thang shopify vay
                // so tien se dc lay trong m_voxy_package_orders - update_thanhtoan_lan1_xuathang
                $this->m_voxy_package_orders->update_thanhtoan_lan1_xuathang($order_id, $date_time_lan1);//add checked in talbe orders
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
        $sorting = $this->input->get('sorting');

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
        $data['all_products'] = $this->data->xuathang_le($order_number, $sorting);

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
            $viewFile = '/voxy_package_xuathang/search_pro';
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
            $viewFile = '/voxy_package_xuathang/search_pro_for_title';
            $content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
            $data_return["state"] = 1;
            $data_return["msg"] = "Ok";
            $data_return["html"] = $content;
            echo json_encode($data_return);
            return TRUE;
        }
    }

    //check product is existed in the order or no
    // if true => return false; else oke
    public function check_exist_product_in_order(){
        $this->load->model('m_voxy_package_orders');
        $product_id = $this->input->post('product_id');
        $variant_id = $this->input->post('variant_id');
        $title = $this->input->post('title');
        $data_order_number = $this->input->post('data_order_number');

        $check = $this->m_voxy_package_orders->check_exist_product_in_order($product_id, $variant_id, $title,$data_order_number);

        if($check == true){
            $data_return = array();
            $data_return['status'] = true;
            echo  json_encode($data_return);die;
        }else{
            $data_return = array();
            $data_return['status'] = false;
            echo  json_encode($data_return);die;
        }
    }

}