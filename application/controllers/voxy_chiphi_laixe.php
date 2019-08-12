<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package_orders
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_chiphi_laixe extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_chiphi_laixe",
            "view" => "voxy_chiphi_laixe",
            "model" => "m_voxy_chiphi_laixe",
            "object" => "Chi phí lái xe"
        );
    }


    /**
     * @param array $data Mang du lieu truyen ra view
     *
     * @author chuvantinh1991@gmail.com
     */
    public function manager($data = array())
    {
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_baocao_tonghop');

        $json_conds = $this->session->userdata('arr_package_search');
        $json_conds = json_decode($json_conds, TRUE);

        $data['form_conds'] = (array)$json_conds;
        $data['category'] = $this->m_voxy_category->get_category();

        $data['list_status'] = $this->data->arr_status;

        $this->load->model('m_voxy_chiphi_laixe');
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
        $list_field = array('tungay', 'denngay', 'laixe', 'ship_areas');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                //$data[$value] = trim($data[$value]);
                switch ($value) {
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
                        if ($data['laixe'] != "") {
                            $where_data['m.laixe'] = $data['laixe'];
                        }

                    case 'ship_areas':
                        if ($data['ship_areas'] != "") {
                            $where_data['m.ship_areas'] = $data['ship_areas'];
                        }
                }
            }
        }

        $data_return = array(
            'custom_where' => $where_data,
            'custom_like' => $like_data
        );
        $this->session->set_userdata('voxy_chiphi_laixe_sessions', json_encode($data_return));
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

        $data_post = $this->input->post();
        //var_dump($data_post);die;

        if ($data_post && is_array($data_post)) {
            $this->data->custom_conds = $this->get_search_condition($data_post);
        } else {
            $json_conds = $this->session->userdata('voxy_chiphi_laixe_sessions');
            $json_conds = json_decode($json_conds, TRUE);

            if (isset($json_conds)) {
                if (count($json_conds['custom_where']) == 0 && count($json_conds['custom_like']) == 0) {
                    $this->data->custom_conds = $this->get_search_condition();
                } else {
                    $this->data->custom_conds = $json_conds;
                }
            }
        }
    //var_dump($this->session->userdata('voxy_chiphi_laixe_sessions'));die;
        //var_dump($this->data->custom_conds);die;
        parent::ajax_list_data($data);
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
            $record->custom_action = '<div class="action"><a class="detail e_ajax_link icon16 i-eye-3 " per="1" href="' . site_url($this->url["view"] . $record->$key_table) . '" title="Xem"></a>';
            if (!isset($record->editable) || (isset($record->editable) && $record->editable)) {
                $record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
                $record->custom_action .= '<a class="delete e_ajax_confirm e_ajax_link icon16 i-remove" per="1" href="' . site_url($this->url["delete"] . $record->$key_table) . '" title="Xóa"></a></div>';
            }

            $record->custom_check = "<input type='checkbox' style='width:20px;' class='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            if ($record->tongchiphi) {
                $record->tongchiphi = "€" . number_format($record->tongchiphi, 2);
            }

            if (isset($record->shipped_at)) {
                $date = date_create($record->shipped_at);
                $record->shipped_at = date_format($date, 'd/m/Y');
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

        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        $tongtien = $data['tienxang'] + $data['tienthuexe'] + $data['khauhaoxe'] + $data['chiphikhac'];
        $data['tongchiphi'] = $tongtien;

        //du lieu post lay dc# tu form them
        $insert_id = $this->data->add($data);

        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data;
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            if($data['status'] == 1){
                //1 tralai 2. luu tam
                $data_return["msg"] = "Lưu thành công";

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

    public function get()
    {
        $this->load->model("m_voxy_package_xuathang");
        $this->load->model("m_voxy_package_kunden");
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_chiphi_laixe');

        $list_id = $this->input->post('list_id');

        if ($list_id != false) {
            foreach (get_object_vars(json_decode($list_id))['list_id'] as $item) {
                $id_order = $this->m_voxy_chiphi_laixe->get_order_number_from_id($item);
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
                $item_local_order_new = str_replace(array("rn", "r", "n", "t", "v", "e", "\\", "u005E", "u002C", "u007C", "u0027"), "", $item['local_order']);
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

                    $check_edit_kho = $this->m_voxy_chiphi_laixe->check_edit_kho($id);

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



    public function edit_save($id = 0, $data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }
        $tongtien = $data['tienxang'] + $data['tienthuexe'] + $data['khauhaoxe'] + $data['chiphikhac'];
        $data['tongchiphi'] = $tongtien;

        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        $update = $this->data->update($id, $data);
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

    public function excel()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_chiphi_laixe');
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }
        $data = $this->m_voxy_chiphi_laixe->get_order($order_number);
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
            $band[$key] = $row['title'];
            $auflage[$key] = $row['id'];
        }
        $band = array_column($_export, 'title');
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
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:M2')->getFont()->setBold(true);
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
        $excel->getActiveSheet()->setCellValue('B2', 'Số Lượng');
        $excel->getActiveSheet()->setCellValue('C2', 'Gia Ban €');
        $excel->getActiveSheet()->setCellValue('D2', 'Hàng về');
        $excel->getActiveSheet()->setCellValue('E2', 'Hàng thiếu');
        $excel->getActiveSheet()->setCellValue('F2', 'Hàng hỏng');
        $excel->getActiveSheet()->setCellValue('G2', 'Hàng thêm');
        $excel->getActiveSheet()->setCellValue('H2', 'SL cuối');
        $excel->getActiveSheet()->setCellValue('I2', 'Thành tiền €');
//        $excel->getActiveSheet()->setCellValue('E2', 'Vị trí');
//        $excel->getActiveSheet()->setCellValue('F2', 'Ngày hết hạn');
        //$excel->getActiveSheet()->setCellValue('G2', 'Tai xe');
        //$excel->getActiveSheet()->setCellValue('G1', 'Tổng tiền thêm 5 eu phi shipping: € '.$total_price);
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2

        //$_data = json_decode($data);
        $numRow = 3;
        $total_price = 0;
        if ($_export != null) {
            foreach ($_export as $row) {
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
                $total_price += $thanhtien;
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $row['title']);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['quantity']);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['price']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['hangve']);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['hangthieu']);
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['hanghong']);
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['hangthem']);
                $excel->getActiveSheet()->setCellValue('H' . $numRow, $sl_cuoicung);
                $excel->getActiveSheet()->setCellValue('I' . $numRow, $thanhtien);
//                $excel->getActiveSheet()->setCellValue('E' . $numRow,$row['location']);
//                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['expri_day']);
                //$excel->getActiveSheet()->setCellValue('G' . $numRow, "dressen");
                $numRow++;
            }
            $excel->getActiveSheet()->setCellValue('I' . $numRow++, "Tong tien € : " . number_format($total_price, 2));
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
        $this->load->model('m_voxy_chiphi_laixe');
        if (isset($_GET["date"])) {
            $date = $_GET["date"];
        } else {
            $date = date("Y-m-d");
        }

        $result = $this->m_voxy_chiphi_laixe->get_data_pdf($date);

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
            $viewFile = '/voxy_chiphi_laixe/search_pro';
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
            $viewFile = '/voxy_chiphi_laixe/search_pro_for_title';
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
}