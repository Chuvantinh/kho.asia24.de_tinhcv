<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_chamsockhachhang extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "voxy_chamsockhachhang",
            "view"      => "voxy_chamsockhachhang",
            "model"     => "m_voxy_chamsockhachhang",
            "object"    => "Chăm sóc khách hàng"
        );
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
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
            $record->custom_check = "<input type='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            if (isset($record->status) && isset($this->data->arr_status)) {
                $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }

            if (isset($record->products)) {
                $arr = $this->m_voxy_package->get_sku($record->name);
                $string = "";
                if(is_array($arr)) {
                    foreach ($arr as $item){
                        $string .= $item['sku2'].',';
                    }
                }
                $record->products =$string;
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

        if (!isset($data['name'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Titel SP  không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        //du lieu post lay dc tu form them
        $name = $data['name'];
        if ($this->data->check_title($name) == true){
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Tên đã tồn tại, mời nhập lại";
            echo json_encode($data_return);
            return FALSE;
        }
        //them data vao database
        $insert_id = $this->data->add($data);
        $data[$this->data->get_key_name()] = $insert_id;
        if ($insert_id) {
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
        $update = $this->data->update($id, $data);
        if ($update) {
            $data_return["key_name"]    = $this->data->get_key_name();
            $data_return["record"]      = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"]       = 1; /* state = 1 : insert thành công */
            $data_return["msg"]         = "Sửa bản ghi thành công !";
            $data_return["redirect"]    = isset($data_return['redirect']) ? $data_return['redirect'] : "";
            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"]   = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"]     = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    public function export_location_excel()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_location');
        $date = date('Y-m-d');

        $export = $this->m_location->get_all_location();
//Khởi tạo đối tượng
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Location ' . $date);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true);
//Tạo tiêu đề cho từng cột
//Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Ngày:' . $date);
        $excel->getActiveSheet()->setCellValue('A2', 'ID');
        $excel->getActiveSheet()->setCellValue('B2', 'Name');
        $excel->getActiveSheet()->setCellValue('C2', 'Description');
        $excel->getActiveSheet()->setCellValue('D2', 'Products');
// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
// dòng bắt đầu = 2
        $numRow = 3;
        if ($export != null) {
            foreach ($export as $row) {
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $row['id']);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['name']);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['description']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['products']);
                $numRow++;
            }
        }
// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
// ở đây mình lưu file dưới dạng excel2007
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=Location-' . $date . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }
}
?>