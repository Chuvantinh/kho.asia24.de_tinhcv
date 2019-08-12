<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HtmltoPDF extends home_base
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_htmltopdf');
        $this->load->library('pdf');//dompdf
    }

    // https://quanlykho.eu/htmltopdf  phieu nhat hang
    public function index()
    {
        // theo tai xe nao
        //var_dump($this->input->post());die;
        $shipper_id = $this->input->post('shipper_id');
        $category_id = $this->input->post('category_id');//nhung thang ma dc chọn theo category
        $list_id_to_nhathang = $this->input->post('list_id_to_nhathang');
        if ($list_id_to_nhathang != "") {
            $list_id_to_nhathang = get_object_vars(json_decode($list_id_to_nhathang))['list_id'];
        }

        $this->load->model('m_voxy_package_orders');
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
        $sorting = $this->input->post('sorting');
        if ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Category";
        }
        //$date_time = $this->input->post('date_for_orders');
        //$ngay_dat_hang = ($date_time == "") ? "" : $date_time;

        $date_liefer = $this->input->post('date_liefer');
        $ngay_chuyen_hang = ($date_liefer == "") ? "" : $date_liefer;
//                        <div class="datum" style="float: left; width: 30%; text-align: right;">
//                            <span style="font-family: DejaVu Sans">Ngày đặt: ' . $ngay_dat_hang . '</span>
//                        </div>

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 50%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $shipper_name . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 50%; text-align: right;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: <b>' . $ngay_chuyen_hang . '</b></span>
                        </div>
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >Phiếu Nhặt Hàng</h4>';
        $allready = false;
        $html_content .= $this->m_htmltopdf->pdf_day($category_id, $list_id_to_nhathang, $ngay_chuyen_hang, $kho, $shipper_id, $allready, $sorting);
        $html_content .= '
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style="clear: left;margin-top: 70px;"></p>
            <div style="font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;">
                <hr style="width: 200px;margin: 0; padding: 0;">
                <span style="text-align: center">Người Nhặt Hàng</span>
            </div> 
            <div style="font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;">
                <hr style="width: 200px;margin: 0; padding: 0;">
                <span style="text-align: center">Quản Lý Kho</span>
            </div> 
            <div style="font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;">
                <hr style="width: 200px;margin: 0; padding: 0;">
                <span style="text-align: center">Tài Xế</span>
            </div> 
        ';
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($ngay_chuyen_hang . "-" . $shipper_name . ".pdf", array("Attachment" => 0));
    }

    // https://quanlykho.eu/htmltopdf  phieu nhat hang da xong , da dc nhat
    public function print_order_allready()
    {
        // theo tai xe nao
        $shipper_id = $this->input->post('shipper_id');
        $category_id = $this->input->post('category_id');//nhung thang ma dc chọn theo category
        $list_id_to_nhathang = $this->input->post('list_id_to_nhathang');
        if ($list_id_to_nhathang != "") {
            $list_id_to_nhathang = get_object_vars(json_decode($list_id_to_nhathang))['list_id'];
        }
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_htmltopdf');
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
        $sorting = $this->input->post('sorting');//location or category
        if ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Category";
        }
        //$date_time = $this->input->post('date_for_orders');
        //$ngay_dat_hang = ($date_time == "") ? "" : $date_time;

        $date_liefer = $this->input->post('date_liefer');
        $ngay_chuyen_hang = ($date_liefer == "") ? "" : $date_liefer;

//        <div class="datum" style="float: left; width: 30%; text-align: right;">
//                            <span style="font-family: DejaVu Sans">Ngày đặt: ' . $ngay_dat_hang . '</span>
//                        </div>

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 50%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $shipper_name . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 50%; text-align: right;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: <b>' . $ngay_chuyen_hang . '</b></span>
                        </div>
                        
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >Phiếu Nhặt Hàng</h4>';
        $allready = true;
        $html_content .= $this->m_htmltopdf->pdf_day($category_id, $list_id_to_nhathang, $ngay_chuyen_hang, $kho, $shipper_id, $allready, $sorting);
        $html_content .= '
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style="clear: left;margin-top: 70px;"></p>
            <div style="font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;">
                <hr style="width: 200px;margin: 0; padding: 0;">
                <span style="text-align: center">Người Nhặt Hàng</span>
            </div> 
            <div style="font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;">
                <hr style="width: 200px;margin: 0; padding: 0;">
                <span style="text-align: center">Quản Lý Kho</span>
            </div> 
            <div style="font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;">
                <hr style="width: 200px;margin: 0; padding: 0;">
                <span style="text-align: center">Tài Xế</span>
            </div> 
        ';
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($ngay_chuyen_hang . "-" . $shipper_name . ".pdf", array("Attachment" => 0));
    }

    // print thong tin tien  theo ngay giao hang va lai xe, tab xuat hang,button ubersicht
    public function print_money_day()
    {
        $this->load->model('m_voxy_package_orders');

        $shipper_id = $this->input->post('shipper_id');
        if ($shipper_id == false) {
            $shipper_id = "";
        }

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
        $date_liefer = $this->input->post('date_liefer');
        $ngay_chuyen_hang = ($date_liefer == "") ? date('Y-m-d') : $date_liefer;

        $your_string = $ngay_chuyen_hang;
        $date_print = date("d-m-Y", strtotime($your_string));

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 60%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $shipper_name . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: left;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: <b>' . $date_print . '</b></span>
                        </div>
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" > Übersicht - Thông tin đơn hàng và khách hàng </h4>';
        $html_content .= $this->m_htmltopdf->print_money_day($ngay_chuyen_hang, $shipper_id);
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        //$this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($ngay_chuyen_hang . ".pdf", array("Attachment" => 0));
    }

    //history of the report "Ubersicht"
    public function print_money_day_history()
    {
        $this->load->model('m_voxy_package_orders');

        $shipper_id = $this->input->post('shipper_id');
        if ($shipper_id == false) {
            $shipper_id = "";
        }

        $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);

        $date_liefer = $this->input->post('date_liefer');
        $ngay_chuyen_hang = ($date_liefer == "") ? date('Y-m-d') : $date_liefer;

        $your_string = $ngay_chuyen_hang;
        $date_print = date("d-m-Y", strtotime($your_string));

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 60%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $shipper_name . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: left;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: <b>' . $date_print . '</b></span>
                        </div>
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" > Übersicht - Lịch sử</h4>';
        $html_content .= $this->m_htmltopdf->print_money_day_history($ngay_chuyen_hang, $shipper_id);
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        //$this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($ngay_chuyen_hang . ".pdf", array("Attachment" => 0));
    }

    public function print_money_day_excel()
    {
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_chiphi_laixe');
        require_once APPPATH . "/third_party/PHPExcel.php";

        $shipper_id = $this->input->post('shipper_id');
        if ($shipper_id == false) {
            $shipper_id = "";
        }
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
        $date_liefer = $this->input->post('date_liefer');
        $ngay_chuyen_hang = ($date_liefer == "") ? date('Y-m-d') : $date_liefer;

        //Khởi tạo đối tượng
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Übersicht_' . $ngay_chuyen_hang);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
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

        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FF0000'),
                'size' => 13,
                'name' => 'Calibri'
            ));


        $excel->getActiveSheet()->setCellValue('B1', 'Übersicht-Thông tin đơn hàng và khách');
        $excel->getActiveSheet()->setCellValue('A2', 'Ngày:' . $ngay_chuyen_hang);
        $excel->getActiveSheet()->setCellValue('B2', 'Lái xe:' . $shipper_name);

        $excel->getActiveSheet()->setCellValue('A3', 'Order');
        $excel->getActiveSheet()->setCellValue('B3', 'Khách hàng');
        $excel->getActiveSheet()->setCellValue('C3', 'Doanh Thu');
        $excel->getActiveSheet()->setCellValue('D3', 'Doanh Thu Thực');
        $excel->getActiveSheet()->setCellValue('E3', 'Đã thu');
        $excel->getActiveSheet()->setCellValue('F3', 'Còn nợ');
        $excel->getActiveSheet()->setCellValue('G3', 'Ghi chú');

        $data = $this->m_htmltopdf->print_money_day_excel($ngay_chuyen_hang, $shipper_id);

        $total_price = 0;
        $tongtien_thu = 0;
        $tongtien_no = 0;

        $tong_tongtienno = 0;
        $tong_tongtienthu = 0;
        $numRow = 4;

        $list_kunden = array();
        $list_order_number = array();
        $list_time_lan1 = array();

        $tongtien_before = 0;
        foreach ($data as $item) {

            $list_kunden[] = $item['customer_id'];
            $list_order_number[] = $item['order_number'];
            if ($ngay_chuyen_hang != "") {
                $list_time_lan1[] = $ngay_chuyen_hang;
            }
            $tongtien_before += $item['total_price_before'];
            $total_price += $item['total_price'];

            $tongtien_thu = (float)$item['thanhtoan_lan1'] + (float)$item['thanhtoan_lan2'] + (float)$item['thanhtoan_lan3'] + (float)$item['thanhtoan_lan4'] + (float)$item['thanhtoan_lan5'];
            $tongtien_no = $item['tongtien_no'];
            if ($tongtien_thu == 0) {
                $tongtien_thu_print = "";
            } else {
                $tongtien_thu_print = $tongtien_thu;
            }

            $tong_tongtienthu += $tongtien_thu;
            $tong_tongtienno += $tongtien_no;

            if ($tongtien_no == 0) {
                $tongtien_no_print = "";
            } else {
                $tongtien_no_print = $tongtien_no;
            }

            $json_customer = get_object_vars(json_decode($item['customer']));

            if (isset($item['key_word_customer']) && $item['key_word_customer'] != "") {
                $key_word = $item['key_word_customer'];
            } else {
                $key_word = null;
            }

            //get id khachhang
            $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($item['customer_id']);

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

            if ($key_word != null) {
                $customer_name = $key_word;
            } else {
                $customer_name = $frist_name . " " . $last_name;
            }

            if ($json_customer) {
                if (isset($json_customer['phone'])) {
                    if (strlen($json_customer['phone']) > 11) {
                        $array_phone = explode("/", $json_customer['phone']);
                        $phone = "";
                        foreach ($array_phone as $key => $_phone) {
                            $phone .= $_phone . "<br>";
                        }
                    }
                }
                $adresse = $json_customer['address1'] . " " . $json_customer['zip'] . " " . $json_customer['city'];
            }

            $excel->getActiveSheet()->setCellValue('A' . $numRow, $item['order_number']);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $id_khachang . "-" . $customer_name);
            $excel->getActiveSheet()->setCellValue('C' . $numRow, number_format($item['total_price_before'], 2));
            $excel->getActiveSheet()->setCellValue('C' . $numRow, number_format($item['total_price'], 2));
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $tongtien_thu_print);
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $tongtien_no_print);
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $item['note']);
            $numRow++;
        }

        $list_kunden = array_unique($list_kunden);
        $list_time_lan1 = array_unique($list_time_lan1);

        if ($list_time_lan1) {

            $data_lastime = $this->m_voxy_package_orders->get_lastime_pay($list_kunden, $list_time_lan1, $list_order_number, $shipper_id);
        }

        if (isset($data_lastime) && $data_lastime != false) {
            $lastime_total_price = 0;
            $lastime_total_dathu = 0;
            $lastime_total_no = 0;
            foreach ($data_lastime as $item) {

                if (isset($item['key_word_customer']) && $item['key_word_customer'] != "") {
                    $key_word = $item['key_word_customer'];
                } else {
                    $key_word = null;
                }

                //get id khachhang
                $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($item['customer_id']);

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

                if ($key_word != null) {
                    $customer_name = $key_word;
                } else {
                    $customer_name = $frist_name . " " . $last_name;
                }


                if ($item['thanhtoan_lan1'] == 0 || $item['thanhtoan_lan1'] == null) {
                    $thanhtoan_lan1_print = " ";
                } else {
                    $thanhtoan_lan1_print = number_format($item['thanhtoan_lan1'], 2) . " €";
                }

                if ($item['thanhtoan_lan2'] == 0 || $item['thanhtoan_lan2'] == null) {
                    $thanhtoan_lan2_print = " ";
                } else {
                    $thanhtoan_lan2_print = number_format($item['thanhtoan_lan2'], 2) . " €";
                }

                if ($item['thanhtoan_lan3'] == 0 || $item['thanhtoan_lan3'] == null) {
                    $thanhtoan_lan3_print = " ";
                } else {
                    $thanhtoan_lan3_print = number_format($item['thanhtoan_lan3'], 2) . " €";
                }

                if ($item['thanhtoan_lan4'] == 0 || $item['thanhtoan_lan4'] == null) {
                    $thanhtoan_lan4_print = " ";
                } else {
                    $thanhtoan_lan4_print = number_format($item['thanhtoan_lan4'], 2) . " €";
                }

                if ($item['thanhtoan_lan5'] == 0 || $item['thanhtoan_lan5'] == null) {
                    $thanhtoan_lan5_print = " ";
                } else {
                    $thanhtoan_lan5_print = number_format($item['thanhtoan_lan5'], 2) . " €";
                }


                if ($item['tongtien_no'] == 0 || $item['tongtien_no'] == null) {
                    $tongtien_no_lan1_print = " ";
                } else {
                    $tongtien_no_lan1_print = $item['tongtien_no'];
                }

                $lastime_total_price += $item['total_price'];
                $lastime_total_no += $item['tongtien_no'];

                if ($item['time_lan1'] == $ngay_chuyen_hang) {
                    $lastime_total_dathu += $item['thanhtoan_lan1'];

                    $excel->getActiveSheet()->getStyle('A' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('B' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('C' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('D' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('E' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray);

                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $item['order_number']);
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $id_khachang . "-" . $customer_name);
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $thanhtoan_lan1_print);
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $item['shipped_at']);
                    $numRow++;

                }

                if ($item['time_lan2'] == $ngay_chuyen_hang) {
                    $lastime_total_dathu += $item['thanhtoan_lan2'];

                    $excel->getActiveSheet()->getStyle('A' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('B' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('C' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('D' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('E' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray);

                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $item['order_number']);
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $id_khachang . "-" . $customer_name);
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $thanhtoan_lan2_print);
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $item['shipped_at']);
                    $numRow++;

                }

                if ($item['time_lan3'] == $ngay_chuyen_hang) {
                    $lastime_total_dathu += $item['thanhtoan_lan3'];

                    $excel->getActiveSheet()->getStyle('A' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('B' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('C' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('D' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('E' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray);

                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $item['order_number']);
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $id_khachang . "-" . $customer_name);
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $thanhtoan_lan3_print);
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $item['shipped_at']);
                    $numRow++;

                }

                if ($item['time_lan4'] == $ngay_chuyen_hang) {
                    $lastime_total_dathu += $item['thanhtoan_lan4'];

                    $excel->getActiveSheet()->getStyle('A' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('B' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('C' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('D' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('E' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray);

                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $item['order_number']);
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $id_khachang . "-" . $customer_name);
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $thanhtoan_lan4_print);
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $item['shipped_at']);
                    $numRow++;

                }

                if ($item['time_lan5'] == $ngay_chuyen_hang) {
                    $lastime_total_dathu += $item['thanhtoan_lan5'];

                    $excel->getActiveSheet()->getStyle('A' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('B' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('C' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('D' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('E' . $numRow)->applyFromArray($styleArray);
                    $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray);

                    $excel->getActiveSheet()->setCellValue('A' . $numRow, $item['order_number']);
                    $excel->getActiveSheet()->setCellValue('B' . $numRow, $id_khachang . "-" . $customer_name);
                    $excel->getActiveSheet()->setCellValue('C' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('D' . $numRow, $thanhtoan_lan5_print);
                    $excel->getActiveSheet()->setCellValue('E' . $numRow, "");
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, $item['shipped_at']);
                    $numRow++;

                }

            }
        }
        if (!isset($lastime_total_price)) {
            $lastime_total_price = 0;
        }

        if (!isset($lastime_total_dathu)) {
            $lastime_total_dathu = 0;
        }

        if (!isset($lastime_total_no)) {
            $lastime_total_no = 0;
        }

        $numRow_new = $numRow + 1;

        //them chi phi lai xe vào table
        if ($shipper_id) {
            $data_chiphi_laixe = $this->m_voxy_chiphi_laixe->get_chiphilaixe($ngay_chuyen_hang, $shipper_id);
            if (isset($data_chiphi_laixe)) {
                $chi_phi_lai_xe = $data_chiphi_laixe[0]['tongchiphi'];
                $excel->getActiveSheet()->setCellValue('A' . $numRow, "");
                $excel->getActiveSheet()->setCellValue('B' . $numRow, "Trừ chi phí lái xe");
                $excel->getActiveSheet()->setCellValue('C' . $numRow, "");
                $excel->getActiveSheet()->setCellValue('D' . $numRow, number_format($data_chiphi_laixe[0]['tongchiphi'], 2));
                $excel->getActiveSheet()->setCellValue('E' . $numRow, "");
                $excel->getActiveSheet()->setCellValue('F' . $numRow, "");
            } else {
                $chi_phi_lai_xe = 0;
            }
        }
        //end them chi phi lai xe vào table
        $numRow_new = $numRow_new + 1;
        $excel->getActiveSheet()->setCellValue('B' . $numRow_new, "Tổng cộng");// +$lastime_total_price
        $excel->getActiveSheet()->setCellValue('C' . $numRow_new, number_format($total_price_before, 2));// +$lastime_total_price
        $excel->getActiveSheet()->setCellValue('D' . $numRow_new, number_format($total_price, 2));// +$lastime_total_price
        $excel->getActiveSheet()->setCellValue('E' . $numRow_new, number_format($tong_tongtienthu + $lastime_total_dathu - $chi_phi_lai_xe, 2));
        $excel->getActiveSheet()->setCellValue('F' . $numRow_new, number_format($tong_tongtienno, 2));

        $numRow_new++;

        $excel->getActiveSheet()->setCellValue('B' . $numRow_new, "Chi tiết mặt hàng đã thay đổi");

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('ubersicht.xlsx');
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=ubersicht_' . $ngay_chuyen_hang . ".xlsx");
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }


    //click PDF in le, tab xuat hang
    public function pdf_order()
    {
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }
        if (isset($_GET["total_price"])) {
            //5 eu tien phi shipping
            $total_price = $_GET["total_price"] + 5;
        }

        $infor_kunden = $this->m_htmltopdf->get_infor_kunden($order_number);
        $ngaydathang = "";
        $shipped_at = "";
        foreach ($infor_kunden as $item) {
            $_json_customer = json_decode($item['customer']);
            $ngaydathang = $item['created_time'];
            $shipped_at = $item['shipped_at'];
        }
        $date = date_create($ngaydathang);
        $ngaydathang = date_format($date, 'd-m-Y');

        $date_shipped_at = date_create($shipped_at);
        $shipped_at = date_format($date_shipped_at, 'd-m-Y');

        $json_customer = get_object_vars($_json_customer);
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

        $name = $frist_name . " " . $last_name;
        $firma = (isset($json_customer['company']) && ($json_customer['company'] != "")) ? $json_customer['company'] : "";
        $phone = $json_customer['phone'];
        $adresse = $json_customer['address1'] . " " . $json_customer['city'] . " " . $json_customer['zip'];

        $this->load->model('m_voxy_package_orders');
        $shipper_name = $this->m_voxy_package_orders->name_shipper($order_number);
        $shipper_phone = $this->m_voxy_package_orders->phone_shipper($shipper_name);

        $html_content = '<head>
                            <div class="diachi" style="font-size:13px;float: left; width: 50%">
                               <br>
                                <span style="width: 33%;font-family: DejaVu Sans; text-transform: uppercase;">' . $name . '</span><br>
                                ';
        if ($firma != "") {
            $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $firma . '</span><br>';
        }
        $html_content .= '    <span style="width: 33%;font-family: DejaVu Sans">' . $adresse . '</span><br>
                              <span style="width: 33%;font-family: DejaVu Sans">' . $phone . '</span>
                            </div>
                        
                            <div class="datum" style="float: left; width: 50%; text-align: right;font-size: 13px">
                            <span style="font-family: DejaVu Sans">LIL GmbH - HerbergStraße 131,13595 Berlin</span><br>
                                <span style="font-family: DejaVu Sans">Fahrer: <b>' . $shipper_name . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Phone: <b>' . $shipper_phone . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Auftragsnummer: <b>' . $order_number . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Bestelldatum: <b>' . $ngaydathang . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Lieferdatum: <b>' . $shipped_at . '</b></span><br>
                            </div>
                        </head><br>';

        $html_content .= '<h3 align="center" style="clear:left; font-family: DejaVu Sans" >Lieferschein</h3><br>';
        $html_content .= '<p>Vielen Dank für Ihre Bestellung. Wir liefern Ihnen wie vereinbart folgende Waren:</p>';

        $html_content .= $this->m_htmltopdf->pdf_order($order_number);
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
        $this->pdf->getCanvas()->page_text(72, 18, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
        $this->pdf->stream($order_number . ".pdf", array("Attachment" => 0));
    }

    //Inlist giao hang cung 1 luc , tab xuat hang
    public function pdf_list_giaohang()
    {
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_htmltopdf');

        $shipper_id = $this->input->post('shipper_id');
        //$shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);

        $date_liefer = $this->input->post('date_liefer');
        $ngay_chuyen_hang = ($date_liefer == "") ? date('Y-m-d') : $date_liefer;

        $data_list_order = $this->m_htmltopdf->get_list_order($ngay_chuyen_hang, $shipper_id);
        if ($data_list_order != false) {
            $html_content = "";
            foreach ($data_list_order as $key => $item_order) {
                $order_number = $item_order['order_number'];
                $shipped_at = $item_order['shipped_at'];

                $ngaydathang = $item_order['created_time'];
                $date = date_create($ngaydathang);

                $ngaydathang = date_format($date, 'Y-m-d');
                $shipper_name = $item_order['shipper_name'];
                $shipper_phone = $this->m_voxy_package_orders->phone_shipper($shipper_name);
                $infor_kunden = get_object_vars(json_decode($item_order["customer"]));
                if (isset($infor_kunden['d_first_name'])) {
                    $frist_name = $infor_kunden['d_first_name'];
                } elseif (isset($infor_kunden['first_name'])) {
                    $frist_name = $infor_kunden['first_name'];
                } else {
                    $frist_name = "";
                }

                if (isset($infor_kunden['d_last_name'])) {
                    $last_name = $infor_kunden['d_last_name'];
                } elseif (isset($infor_kunden['last_name'])) {
                    $last_name = $infor_kunden['last_name'];
                } else {
                    $last_name = "";
                }

                if ($infor_kunden) {
                    $name = $frist_name . " " . $last_name;
                }

                $firma = ((isset($infor_kunden['company'])) && $infor_kunden['company'] != "") ? $infor_kunden['company'] : "";
                if (isset($infor_kunden['phone'])) {
                    $phone = $infor_kunden['phone'];
                } else {
                    $phone = "";
                }

                $adresse = $infor_kunden['address1'] . " " . $infor_kunden['zip'] . " " . $infor_kunden['city'];

                $html_content .= '<head>
                            <div class="diachi" style="font-size:13px;float: left; width: 50%">
                            <br>
                                        <span style="width: 33%;font-family: DejaVu Sans; text-transform: uppercase;">' . $name . '</span><br>
                                        ';

                if ($firma != "") {
                    $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $firma . '</span><br>';
                }
                $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $adresse . '</span><br>
                                      <span style="width: 33%;font-family: DejaVu Sans">' . $phone . '</span>
                            </div>
                        
                            <div class="datum" style="float: left; width: 50%; text-align: right;font-size: 13px">
                               <span style="font-family: DejaVu Sans">LIL GmbH - HerbergStraße 131,13595 Berlin</span><br>
                                <span style="font-family: DejaVu Sans">Fahrer: <b>' . $shipper_name . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Phone: <b>' . $shipper_phone . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Auftragsnummer: <b>' . $order_number . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Bestelldatum: <b>' . $ngaydathang . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Lieferdatum: <b>' . $shipped_at . '</b></span><br>
                            </div>
                        </head><br>';

                $html_content .= '<h3 align="center" style="clear:left; font-family: DejaVu Sans" >Lieferschein</h3><br>';
                $html_content .= '<p>Vielen Dank für Ihre Bestellung. Wir liefern Ihnen wie vereinbart folgende Waren:</p>';

                $html_content .= $this->m_htmltopdf->pdf_order($item_order['order_number']);
                $html_content .= '<style>
                                   .page-break{
                                        page-break-after: always;
                                   }
                                </style>';
                $html_content .= '<div class="page-break"></div>';
            }
        } else {
            $html_content = "Không có dữ liệu, xem lại ngày giao hàng bitte";
        }
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
        $this->pdf->stream("list_" . $ngay_chuyen_hang . ".pdf", array("Attachment" => 0));
    }

    //phieu nhat hang ,tab nhat hang
    public function pdf_order_nhathang()
    {
        if (isset($_GET["order_number"])) {
            $order_number = $_GET["order_number"];
        }
        if (isset($_GET["total_price"])) {
            //5 eu tien phi shipping
            $total_price = $_GET["total_price"] + 5;
        }

        $_infor_kunden = $this->m_htmltopdf->get_infor_kunden($order_number);

        $ngaydathang = "";
        $shipped_at = "";
        foreach ($_infor_kunden as $item) {
            //if(isset($item['customer']) and $item['customer'] != 0){
            $infor_kunden = get_object_vars(json_decode($item['customer']));
            // }

            $ngaydathang = $item['created_time'];
            $shipped_at = $item['shipped_at'];
        }

        $date = date_create($ngaydathang);
        $ngaydathang = date_format($date, 'd-m-Y');

        $date_shipped_at = date_create($shipped_at);
        $shipped_at = date_format($date_shipped_at, 'd-m-Y');

        if (isset($infor_kunden['d_first_name'])) {
            $frist_name = $infor_kunden['d_first_name'];
        } elseif (isset($infor_kunden['first_name'])) {
            $frist_name = $infor_kunden['first_name'];
        } else {
            $frist_name = "";
        }

        if (isset($infor_kunden['d_last_name'])) {
            $last_name = $infor_kunden['d_last_name'];
        } elseif (isset($infor_kunden['last_name'])) {
            $last_name = $infor_kunden['last_name'];
        } else {
            $last_name = "";
        }

        if (isset($infor_kunden)) {
            $name = $frist_name . " " . $last_name;
        }

        if (isset($infor_kunden)) {
            $firma = ((isset($infor_kunden['company'])) && $infor_kunden['company'] != "") ? $infor_kunden['company'] : "";
            $phone = isset($infor_kunden['phone']) ? $infor_kunden['phone'] : "";
            $adresse = $infor_kunden['address1'] . "<br>" . $infor_kunden['zip'] . " " . $infor_kunden['city'];
        } else {
            $firma = "";
            $phone = "";
            $adresse = "";
        }

        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package');
        $shipper_name = $this->m_voxy_package_orders->name_shipper($order_number);
        //get so dien thoai shipper
        $shipper_phone = $this->m_voxy_package_orders->phone_shipper($shipper_name);
        $html_content = '<head>
                            <div class="diachi" style="float: left; width: 50%; font-size: 13px;">
                               <br> <span style="width: 33%;font-family: DejaVu Sans; text-transform: uppercase;">' . $name . '</span><br>
                                ';
        if ($firma != "") {
            $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $firma . '</span><br>';
        }
        $html_content .= '    <span style="width: 33%;font-family: DejaVu Sans">' . $adresse . '</span><br>
                              <span style="width: 33%;font-family: DejaVu Sans">' . $phone . '</span>
                            </div>
                        
                            <div class="datum" style="float: left; width: 50%; text-align: right;font-size: 13px">
                                <span style="font-family: DejaVu Sans">LIL GmbH - HerbergStraße 131,13595 Berlin</span><br>
                                <span style="font-family: DejaVu Sans">Fahrer: <b>' . $shipper_name . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Phone: <b>' . $shipper_phone . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Auftragsnummer: <b>' . $order_number . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Bestelldatum: <b>' . $ngaydathang . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Lieferdatum: <b>' . $shipped_at . '</b></span><br>
                            </div>
                        </head><br>';

        $html_content .= '<h3 align="center" style="clear:left; font-family: DejaVu Sans" >Lieferschein</h3><br>';
        $html_content .= '<p>Vielen Dank für Ihre Bestellung. Wir liefern Ihnen wie vereinbart folgende Waren:</p>';

        //html_content .= '<br><p style="font-family: DejaVu Sans; clear: left;">Kính Chào Quý Khách,</p>';
        //$html_content .= '<p style="font-family: DejaVu Sans">Cảm ơn quý khách đã hợp tác. Đơn hàng của quý khách bao gồm những mặt hàng sau: </p>';

        $html_content .= $this->m_htmltopdf->pdf_order($order_number);
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
        $this->pdf->getCanvas()->page_text(35, 18, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
        $this->pdf->stream($order_number . ".pdf", array("Attachment" => 0));
    }

    public function export_product()
    {
        $shipper_id = $this->input->post('shipper_id');
        $this->load->model('m_voxy_package_orders');
        $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);

        $html_content = '<h3 align="center" style="font-family: DejaVu Sans" >Phiếu Xuất Kho LIL</h3>
                        <span style="font-family: DejaVu Sans">Người xuất kho : ' . $this->USER->user_name . '</span>
                        <span style="font-family: DejaVu Sans">Ngày: ' . date('d-m-Y H:i:s') . '</span>
                         <span style="font-family: DejaVu Sans">Người lái xe : ' . $shipper_name . '</span>
                        ';
        $date_time = date('d-m-Y');
        $html_content .= $this->m_htmltopdf->pdf_product();
        $html_content .= '
            <br>
            <br>
            <br>
            <div style="font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;">Người Nhặt Hàng</div> 
            <div style="font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;">Quản Lý Kho</div> 
            <div style="font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;">Tài Xế</div> 
        ';
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
        $this->pdf->stream("Product-" . $date_time . ".pdf", array("Attachment" => 0));
    }

    //xuat don hang tong theo liefer datum , tab xuathang
    public function phieuxuathang()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');

        $laixe = $this->input->post('laixe');
        $shipper_id = $this->input->post('shipper_id');
        $date = $this->input->post('date');
        $kho = $this->input->post('kho');
        $name_kho = $this->input->post('name_kho');

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 60%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $laixe . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: ' . $date . '</span>
                        </div>
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >Phiếu Xuất Hàng ' . $name_kho . '</h4>';

        //$all_products = $this->m_voxy_package_xuathang->xuathang($date, $shipper_id);
        $_all_products = json_decode($this->input->post("list_products"));
        $all_products = array();
        foreach ($_all_products as $item) {
            $all_products[] = get_object_vars($item);
        }

        $_list_cat_id = json_decode($this->input->post("list_cat_id"));
        $list_cat_id = array();

        foreach ($_list_cat_id as $item) {
            $list_cat_id[] = $item;
        }

        $_array_note_products = json_decode($this->input->post("data_note"));
        $array_note_products = array();
        foreach ($_array_note_products as $item) {
            $array_note_products[] = get_object_vars($item);
        }
        $history_xuathang = json_decode($this->m_voxy_package_xuathang->get_variants($date, $laixe));
        //$all_products['result_catid'] = json_decode($this->input->post('result_catid'));
        //$all_products['array_note_products'] = json_decode($this->input->post('data_note'));

        $html_content .= " 
<div class='products'>
    <div class='pro_th'>
        <span style='display: none;width: 10%;float: left;text-align: center;text-align: left !important;'>Variant ID</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 70%;float: left;text-align: center'>Tên</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left'>SL</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left'><b>SL Xuất</b></span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center'>Đơn Vị</span>
    </div>
    <div class='pro_body'> ";
        $id = -1;
        foreach ($list_cat_id as $catid) {//category
            if ($kho == 'AKL') {
                if ($catid == '91459649625') {
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                }
            } elseif ($kho == 'lil') {
                foreach ($all_products as $item2) {
                    if ($catid === $item2['cat_id']) {
                        if (strpos($item2['location'], 'AH') !== false) {
                            $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                            break;
                        }
                    }
                }
            } elseif ($kho == 'cua_hang') {//trong cua hang
                if ($catid == false) {
                    $html_content .= "<b>No Category</b>";
                } else {
                    foreach ($all_products as $item5) {
                        if ($catid === $item5['cat_id']) {
                            if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                            } else {
                                $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                                break;
                            }
                        }
                    }
                }
            } else {
                if ($catid == false) {
                    $html_content .= "<b>No Category</b>";
                } else {
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                }
            }
            foreach ($all_products as $row) {//san pham
                //check product co thuoc san pham do khong thi moi in ra
                if ($catid == $row['cat_id']) {

                    //$data_da_xuat = 'nein';
                    //$quantity_xuathang = 0;
                    $sl_daxuat = 0;
                    if ($history_xuathang != null) {
                        foreach ($history_xuathang as $item_xuat) {
                            if ($item_xuat->variant_id == $row['variant_id']) {
                                //$quantity_xuathang = $item_xuat['quantity'];
                                //$data_da_xuat = $item_xuat->data_da_xuat;
                                $sl_daxuat = $item_xuat->quantity;
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

                    if ($kho == 'all') { // in tat ca k phan biet
                        $id++;
                        $value_note = '';
                        foreach ($array_note_products as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'>$value_note</span>";
                        }
                        $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";

                    } elseif ($kho == 'lil') {
                        if (strpos($row['location'], 'AH') !== false) {
                            $id++;
                            $value_note = '';
                            foreach ($all_products['array_note_products'] as $item_note) {
                                if ($item_note['title'] === $row['title']) {
                                    $value_note .= $item_note['item_note_value'];
                                }
                            }
                            $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                            if ($value_note != '') {
                                $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                            }
                            $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                        }
                    } elseif ($kho == 'AKL') {
                        if (strpos($row['location'], 'AKL') !== false) {
                            $id++;
                            $value_note = '';
                            foreach ($all_products['array_note_products'] as $item_note) {
                                if ($item_note['title'] === $row['title']) {
                                    $value_note .= $item_note['item_note_value'];
                                }
                            }
                            $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                            if ($value_note != '') {
                                $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                            }
                            $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                        }
                    } elseif ($kho == 'cua_hang') {
                        if ($row['location'] == false) {
                            $id++;
                            $value_note = '';
                            foreach ($all_products['array_note_products'] as $item_note) {
                                if ($item_note['title'] === $row['title']) {
                                    $value_note .= $item_note['item_note_value'];
                                }
                            }

                            $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                            if ($value_note != '') {
                                $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                            }
                            $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                        }
                    }
                    //end check in ra trong kho nao
                }
            }
        }
        $html_content .= "
                                </div>
</div>
                                ";
        $html_content .= "
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style='clear: left;margin-top: 70px;'></p>
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Người Xuất Hàng</span>
            </div> 
            <div style='font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Quản Lý Kho</span>
            </div> 
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Tài Xế</span>
            </div> 
        ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($laixe . "-" . $date . ".pdf", array("Attachment" => 0));
    }

    public function phieuxuathang_le()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $order_number = $this->input->post('order_number');
        $laixe = $this->input->post('laixe');
        $shipper_id = $this->input->post('shipper_id');
        $date = $this->input->post('date');
        $kho = $this->input->post('kho');
        $name_kho = $this->input->post('name_kho');

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 60%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $laixe . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: ' . $date . '</span>
                        </div>
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >Phiếu Xuất Hàng Lẻ - Order ' . $order_number . '</h4>';

        $all_products = $this->m_voxy_package_xuathang->xuathang_le($order_number);
        $history_xuathang = json_decode($this->m_voxy_package_xuathang->get_variants_le($order_number));
        //$all_products['result_catid'] = json_decode($this->input->post('result_catid'));
        //$all_products['array_note_products'] = json_decode($this->input->post('data_note'));

        $html_content .= " 
<div class='products'>
    <div class='pro_th'>
        <span style='display: none;width: 10%;float: left;text-align: center;text-align: left !important;'>Variant ID</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 70%;float: left;text-align: center'>Tên</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left'>SL</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left'><b>SL Xuất</b></span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center'>Đơn Vị</span>
    </div>
    <div class='pro_body'> ";
        $id = -1;
        foreach ($all_products['result_catid'] as $catid) {//category
            if ($kho == 'AKL') {
                if ($catid == '91459649625') {
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                }
            } elseif ($kho == 'lil') {
                foreach ($all_products['export2'] as $item2) {
                    if ($catid === $item2['cat_id']) {
                        if (strpos($item2['location'], 'AH') !== false) {
                            $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                            break;
                        }
                    }
                }
            } elseif ($kho == 'cua_hang') {//trong cua hang
                if ($catid == false) {
                    $html_content .= "<b>No Category</b>";
                } else {
                    foreach ($all_products['export2'] as $item5) {
                        if ($catid === $item5['cat_id']) {
                            if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                            } else {
                                $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                                break;
                            }
                        }
                    }
                }
            } else {
                if ($catid == false) {
                    $html_content .= "<b>No Category</b>";
                } else {
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid) . "</p>";
                }
            }
            foreach ($all_products['export2'] as $row) {//san pham
                //check product co thuoc san pham do khong thi moi in ra
                if ($catid == $row['cat_id']) {

                    //$data_da_xuat = 'nein';
                    //$quantity_xuathang = 0;
                    $sl_daxuat = 0;
                    if ($history_xuathang != null) {
                        foreach ($history_xuathang as $item_xuat) {
                            if ($item_xuat->variant_id == $row['variant_id']) {
                                //$quantity_xuathang = $item_xuat['quantity'];
                                //$data_da_xuat = $item_xuat->data_da_xuat;
                                $sl_daxuat = $item_xuat->quantity;
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

                    if ($kho == 'all') { // in tat ca k phan biet
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'>$value_note</span>";
                        }
                        $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";

                    } elseif ($kho == 'lil') {
                        if (strpos($row['location'], 'AH') !== false) {
                            $id++;
                            $value_note = '';
                            foreach ($all_products['array_note_products'] as $item_note) {
                                if ($item_note['title'] === $row['title']) {
                                    $value_note .= $item_note['item_note_value'];
                                }
                            }
                            $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                            if ($value_note != '') {
                                $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                            }
                            $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                        }
                    } elseif ($kho == 'AKL') {
                        if (strpos($row['location'], 'AKL') !== false) {
                            $id++;
                            $value_note = '';
                            foreach ($all_products['array_note_products'] as $item_note) {
                                if ($item_note['title'] === $row['title']) {
                                    $value_note .= $item_note['item_note_value'];
                                }
                            }
                            $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                            if ($value_note != '') {
                                $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                            }
                            $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                        }
                    } elseif ($kho == 'cua_hang') {
                        if ($row['location'] == false) {
                            $id++;
                            $value_note = '';
                            foreach ($all_products['array_note_products'] as $item_note) {
                                if ($item_note['title'] === $row['title']) {
                                    $value_note .= $item_note['item_note_value'];
                                }
                            }

                            $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                            if ($value_note != '') {
                                $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                            }
                            $html_content .= "
                    </div>
                    <div style='width: 5%;height: auto;float: left' class='quantity-" . $id . "'>" . $row['quantity'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                        }
                    }
                    //end check in ra trong kho nao
                }
            }
        }
        $html_content .= "
                                </div>
</div>
                                ";
        $html_content .= "
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style='clear: left;margin-top: 70px;'></p>
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Người Xuất Hàng</span>
            </div> 
            <div style='font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Quản Lý Kho</span>
            </div> 
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Tài Xế</span>
            </div> 
        ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($laixe . "-" . $date . ".pdf", array("Attachment" => 0));
    }

    //xuat don hang tong theo liefer datum , tab xuathang
    public function pdf_list_kiemhang()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');

        $shipper_id = $this->input->post('shipper_id');
        $laixe = $this->m_voxy_package_orders->get_name_shipper($shipper_id);

        $date = $this->input->post('date_for_orders');

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

        $sorting = $this->input->post('sorting');//location or category
        if ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Category";
        }

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 30%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $laixe . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%">
                            <span style="font-family: DejaVu Sans">Ngày xuất: <b>' . date('Y-m-d') . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: ' . $date . '</span>
                        </div>
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO XUẤT KHO ' . $name_kho . ' Theo ' . $xuattheo . '</h4>';

        $_all_products = $this->m_voxy_package_xuathang->xuathang_listkiem($date, $laixe);//bang infor xuathang
        $_all_products_le = $this->m_voxy_package_xuathang->xuathang_listkiem_le($date, $laixe); //bang infor xuathang le

        $all_products['result_catid'] = array_merge($_all_products['result_catid'], $_all_products_le['result_catid']);
        $all_products['export2'] = array_merge($_all_products['export2'], $_all_products_le['export2']);
        $all_products['array_note_products'] = array_merge($_all_products['array_note_products'], $_all_products_le['array_note_products']);

        //loai bo nhung thang giong nhau tang quantity len and variant id
        //$all_products['result_catid'] = array_unique($all_products['result_catid']);//loai bo cate giong nhau

        ksort($all_products['result_catid']);//sap xep lai category lan nua, sau khi gop 2 mang category

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($all_products['export2'] as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($all_products['export2'] as $key2 => $item2) {
                if ($key2 > $key) {
                    if (isset($item['variant_id']) && isset($item2['variant_id'])) {
                        if ($item['variant_id'] == $item2['variant_id']) {
                            $item['quantity'] = (int)$item['quantity'] + (int)$item2['quantity'];
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
        } else {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        }

        $all_products['export2'] = $export2;// sap xep lai san pham

        $_history_xuathang = json_decode($this->m_voxy_package_xuathang->get_variants($date, $laixe)); //bang infor xuathang
        $_history_xuathang_le = json_decode($this->m_voxy_package_xuathang->get_variants_le_listkiem($date, $laixe)); // bang infor xuathang le
        //chua xuat hang
        if ($_history_xuathang == null && $_history_xuathang_le == null) {
            $html_content = "<div style='font-family: DejaVu Sans;'>Bạn chưa xuất hàng nên list kiểm ở đây chưa có dữ liệu. </div>";
            $this->pdf->loadHtml($html_content);
            $this->pdf->render();
            $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
            $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
            $this->pdf->stream($laixe . "-" . $date . ".pdf", array("Attachment" => 0));
        }
        // end chua xuat hang

        $history_xuathang = array();
        if ($_history_xuathang_le != null && $_history_xuathang == null) {
            $___history_xuathang = $_history_xuathang_le;
        } else if ($_history_xuathang_le == null && $_history_xuathang != null) {
            $___history_xuathang = $_history_xuathang;
        } else if ($_history_xuathang_le != null && $_history_xuathang != null) {
            $___history_xuathang = array_merge($_history_xuathang, $_history_xuathang_le);
        } else {

        }
        foreach ($___history_xuathang as $item) {
            $history_xuathang[] = get_object_vars($item);
        }

        $export2_history = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($history_xuathang as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($history_xuathang as $key2 => $item2) {
                if ($key2 > $key) {
                    if ($item['variant_id'] == $item2['variant_id']) {
                        $item['quantity'] = $item['quantity'] + $item2['quantity'];
                        $chiso_remove[$key2] = $key2;//index of same product and then remove it
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


//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: center'>Giá vốn</span>
//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: center'>Tiền</span>

        $html_content .= "
<div class='products'>
    <div class='pro_th'>
        <span style='display: none;width: 10%;float: left;text-align: center;text-align: left !important;'>Variant ID</span>
        
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 70%;float: left;text-align: center'>Tên</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: left;'>Đơn vị</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center'>Xuất</span>
        
    </div>
    <div class='pro_body'> ";
        $id = -1;
        $tongtien = 0;
        $k = 0;
        if ($sorting == "category") {
            foreach ($all_products['result_catid'] as $catid) {//category
                if ($kho == 'AKL') {
                    if ($catid['cat_id'] == '91459649625') {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $catid['title'] . "</p>";
                    }
                } elseif ($kho == 'lil') {
                    foreach ($all_products['export2'] as $item2) {
                        if ($catid['cat_id'] === $item2['cat_id']) {
                            if (strpos($item2['location'], 'AH') !== false) {
                                $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $catid['title'] . "</p>";
                                break;
                            }
                        }
                    }
                } elseif ($kho == 'cua_hang') {//trong cua hang
                    if ($catid['cat_id'] == false) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        foreach ($all_products['export2'] as $item5) {
                            if ($catid['cat_id'] === $item5['cat_id']) {
                                if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                                } else {
                                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $catid['title'] . "</p>";
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    if (!isset($catid['title'])) {
                        $catid['title'] = null;
                    }
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $catid['title'] . "</p>";
                }
                $stt = 0;
                foreach ($all_products['export2'] as $row) {//san pham
                    //check product co thuoc san pham do khong thi moi in ra
                    // dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu
                    // if (!isset($row['cat_id'])) {
                    if ($row['cat_id'] == "loaibo chuvantinh") {
                        $___sl_daxuat = 0;

                        if ($history_xuathang != null) {
                            foreach ($history_xuathang as $item_xuat) {
                                if (isset($item_xuat['variant_id']) && isset($row['variant_id'])) {
                                    if ($item_xuat['variant_id'] == $row['variant_id']) {
                                        //$quantity_xuathang = $item_xuat['quantity'];
                                        //$data_da_xuat = $item_xuat->data_da_xuat;
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

                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . (isset($row['variant_id']) && $row['variant_id'] != null) ? $row['variant_id'] : "" . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>0</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$___sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>0</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
                        ";
                    } else {
                        if ($catid['cat_id'] == $row['cat_id']) {
                            //$data_da_xuat = 'nein';
                            //$quantity_xuathang = 0;

                            $sl_daxuat = $row['quantity'];
//                            if ($history_xuathang != null) {
//                                foreach ($history_xuathang as $item_xuat) {
//                                    if ($item_xuat['variant_id'] == $row['variant_id']) {
//                                        //$quantity_xuathang = $item_xuat['quantity'];
//                                        //$data_da_xuat = $item_xuat->data_da_xuat;
//                                        $sl_daxuat = $item_xuat['quantity'];
//                                    }
//                                }
//                            }

                            if (isset($row['location'])) {
                                if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                                    $array_location = explode(',', $row['location']);
                                    $row['location'] = '';
                                    foreach ($array_location as $key => $loca) {
                                        $row['location'] .= $loca . '<br>';
                                    }
                                }
                            } else {
                                $row['location'] = "";
                            }

                            if (isset($row['variant_id'])) {
                                $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                            } else {
                                $check_variant1 = false;
                                $check_variant2 = false;

                            }


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

                            if (isset($row['variant_id']) && $row['variant_id'] != "") {
                                $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                            } else {
                                $idnew = false;
                            }

                            if ($check_variant1 == true) {
                                //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
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

                            if (!isset($giavon)) {
                                $giavon = 0;
                            }
                            $thanhtien = (double)$giavon * (int)$sl_daxuat;

                            $tongtien += $thanhtien;

                            /*<div style='width: 7%;height: auto;float: left' class='giavon'>$giavon</div>
                <div style='width: 7%;height: auto;float: left' class='thanhtien'>$thanhtien</div>*/

                            if ($kho == 'all') { // in tat ca k phan biet
                                $stt++;
                                $id++;
                                $k++;
                                if (!isset($row['variant_id'])) {
                                    $row['variant_id'] = 0;
                                    $row['variant_title'] = "";
                                }

                                if (!isset($row['variant_title'])) {
                                    $row['variant_title'] = "";
                                }
                                $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                
                <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='s'>" . $k . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
            </div>
        ";

                            } elseif ($kho == 'lil') {
                                if (strpos($row['location'], 'AH') !== false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                    $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                                }
                            } elseif ($kho == 'AKL') {
                                if (strpos($row['location'], 'AKL') !== false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                    $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                                }
                            } elseif ($kho == 'cua_hang') {
                                if ($row['location'] == false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }

                                    $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                                }
                            }
                            //end check in ra trong kho nao
                        }
                    }
                }
            }
        } else {//in theo location
            foreach ($all_products['export2'] as $row) {//san pham
                //$data_da_xuat = 'nein';
                //$quantity_xuathang = 0;
                $sl_daxuat = 0;
                if (!isset($row['variant_id'])) {
                    $row['variant_id'] = 0;
                }

                //xu ly do dai cua sku
                if (strlen($row['sku']) > 5) {
                    $row['sku'] = substr($row['sku'], 0, 5);
                }

                if ($history_xuathang != null) {
                    foreach ($history_xuathang as $item_xuat) {
                        if ($item_xuat['variant_id'] == $row['variant_id']) {
                            //$quantity_xuathang = $item_xuat['quantity'];
                            //$data_da_xuat = $item_xuat->data_da_xuat;
                            $sl_daxuat = $item_xuat['quantity'];
                        }
                    }
                }

                if (!isset($row['location'])) {
                    $row['location'] = "";
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

                if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                    $row['variant_title'] = "no infor";
                }
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
                $thanhtien = (double)$giavon * (int)$sl_daxuat;

                $tongtien += $thanhtien;


                if ($kho == 'all') { // in tat ca k phan biet
                    $id++;
                    $k++;
                    $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                
                <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='stt'>" . $k . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                
                
            </div>
        ";

//                    <div style='width: 7%;height: auto;float: left' class='giavon'>$giavon</div>
//                <div style='width: 7%;height: auto;float: left;color: red;' class='thanhtien'>$thanhtien</div>

                } elseif ($kho == 'lil') {
                    if (strpos($row['location'], 'AH') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                    }
                } elseif ($kho == 'AKL') {
                    if (strpos($row['location'], 'AKL') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                    }
                } elseif ($kho == 'cua_hang') {
                    if ($row['location'] == false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }

                        $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                    }
                }
                //end check in ra trong kho nao
            }
        }

        $tongtien = number_format($tongtien, 2);

//        $html_content .= "
//            <br>
//            <br>
//            <p style='margin-right: 30px;float:right;font-family: DejaVu Sans'>Tổng tiền: $tongtien €</p>";


        $html_content .= "
                                </div>
</div>
                                ";
        $html_content .= "
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style='clear: left;margin-top: 70px;'></p>
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Người Xuất Hàng</span>
            </div> 
            <div style='font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Quản Lý Kho</span>
            </div> 
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Tài Xế</span>
            </div> 
        ";
        $this->pdf->loadHtml($html_content);
        //var_dump($html_content);die;
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($laixe . "-" . $date . ".pdf", array("Attachment" => 0));
    }

    //sau khi xuat hang , se co 1 tab moi hien thi, ra nhung cai da xuat hang
    public function phieuxuathangnew()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');

        $laixe = $this->input->post('laixe');
        $shipper_id = $this->input->post('shipper_id');
        $date = $this->input->post('date');
        $kho = $this->input->post('kho');
        $name_kho = $this->input->post('name_kho');
        $sorting = $this->input->post('sorting');

        $html_content = '<head>
                        <div class="fahrer" style="float: left; width: 60%">
                            <span style="font-family: DejaVu Sans">Lái xe: <b>' . $laixe . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;">
                            <span style="font-family: DejaVu Sans">Ngày giao hàng: ' . $date . '</span>
                        </div>
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >Phiếu Xuất Hàng ' . $name_kho . '</h4>';

        //$all_products = $this->m_voxy_package_xuathang->xuathang($date, $shipper_id);
        $_all_products = json_decode($this->input->post("list_products"));

        $all_products = array();
        foreach ($_all_products as $item) {
            $all_products['export2'][] = get_object_vars($item);
        }

        $_list_cat_id = json_decode($this->input->post("list_cat_id"));
        $list_cat_id = array();
        foreach ($_list_cat_id as $item) {
            $list_cat_id[] = get_object_vars($item);
        }

        $_array_note_products = json_decode($this->input->post("data_note"));
        $array_note_products = array();
        foreach ($_array_note_products as $item) {
            $array_note_products[] = get_object_vars($item);
        }

        $_data_xuathang = json_decode($this->input->post("data_xuathang"));

        $data_xuathang = array();
        foreach ($_data_xuathang as $item) {
            $data_xuathang[] = get_object_vars($item);
        }

        $all_products['result_catid'] = $list_cat_id;
        $all_products['array_note_products'] = $array_note_products;

        $html_content .= "
<div class='products'>
    <div class='pro_th'>
        <span style='display: none;width: 10%;float: left;text-align: center;text-align: left !important;'>Variant ID</span>
        <span style='font-family: DejaVu Sans;width: 8%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 65%;float: left;text-align: center'>Tên</span>
        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left'>Trưóc</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;'>xuất </span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: right'> Kho</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: right'>Đơn Vị</span>
    </div>
    <div class='pro_body'> ";
        $id = -1;
        if ($sorting == "category") {
            foreach ($all_products['result_catid'] as $catid) {//category
                if ($kho == 'AKL') {
                    if ($catid['cat_id'] == '91459649625') {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                    }
                } elseif ($kho == 'lil') {
                    foreach ($all_products['export2'] as $item2) {
                        if ($catid['cat_id'] === $item2['cat_id']) {
                            if (strpos($item2['location'], 'AH') !== false) {
                                $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                break;
                            }
                        }
                    }
                } elseif ($kho == 'cua_hang') {//trong cua hang
                    if ($catid['cat_id'] == false) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        foreach ($all_products['export2'] as $item5) {
                            if ($catid['cat_id'] === $item5['cat_id']) {
                                if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                                } else {
                                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    if (!isset($catid['cat_id'])) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                    }
                }
                foreach ($all_products['export2'] as $row) {//san pham
                    //check product co thuoc san pham do khong thi moi in ra
                    // dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu
                    if (!isset($row['cat_id'])) {

                        $___sl_daxuat = 0;
                        if ($data_xuathang != null) {
                            foreach ($data_xuathang as $item_xuat) {
                                if ($item_xuat['variant_id'] == $row['variant_id']) {
                                    //$quantity_xuathang = $item_xuat['quantity'];
                                    //$data_da_xuat = $item_xuat->data_da_xuat;
                                    $___sl_daxuat = $item_xuat['quantity'];
                                } else {
                                    $___sl_daxuat = 0;
                                }
                            }
                        }
                        if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                            $row['variant_title'] = "no infor";
                        }

                        //xu ly do dai cua sku
                        if (strlen($row['sku']) > 5) {
                            $row['sku'] = substr($row['sku'], 0, 5);
                        }

                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . isset($row['variant_id']) ? $row['variant_id'] : null . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>0</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$___sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>0</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
                        ";
                    } else {
                        if ($catid['cat_id'] == $row['cat_id']) {
                            //$data_da_xuat = 'nein';
                            //$quantity_xuathang = 0;

                            $sl_daxuat = 0;
                            if ($data_xuathang != null) {
                                foreach ($data_xuathang as $item_xuat) {
                                    if ($item_xuat['variant_id'] == $row['variant_id']) {
                                        //$quantity_xuathang = $item_xuat['quantity'];
                                        //$data_da_xuat = $item_xuat->data_da_xuat;
                                        $sl_daxuat = $item_xuat['quantity'];
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

                            //xu ly do dai cua sku
                            if (strlen($row['sku']) > 5) {
                                $row['sku'] = substr($row['sku'], 0, 5);
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

                            $quantity_before = (int)$sl_daxuat + (int)$quantity_in_ware_house;

                            if ($kho == 'all') { // in tat ca k phan biet
                                $id++;
                                $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 3%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 7%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";

                            } elseif ($kho == 'lil') {
                                if (strpos($row['location'], 'AH') !== false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                    $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                                }
                            } elseif ($kho == 'AKL') {
                                if (strpos($row['location'], 'AKL') !== false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                    $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                                }
                            } elseif ($kho == 'cua_hang') {
                                if ($row['location'] == false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }

                                    $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                                }
                            }
                            //end check in ra trong kho nao
                        }
                    }
                }
            }
        } else {//in theo location
            foreach ($all_products['export2'] as $row) {//san pham
                //$data_da_xuat = 'nein';
                //$quantity_xuathang = 0;
                $sl_daxuat = 0;
                if ($data_xuathang != null) {
                    foreach ($data_xuathang as $item_xuat) {
                        if ($item_xuat['variant_id'] == $row['variant_id']) {
                            //$quantity_xuathang = $item_xuat['quantity'];
                            //$data_da_xuat = $item_xuat->data_da_xuat;
                            $sl_daxuat = $item_xuat['quantity'];
                        }
                    }
                }

                if (!isset($row['location'])) {
                    $row['location'] = "";
                }

                //xu ly do dai cua sku
                if (strlen($row['sku']) > 5) {
                    $row['sku'] = substr($row['sku'], 0, 5);
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

                $quantity_before = (int)$sl_daxuat + (int)$quantity_in_ware_house;

                if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                    $row['variant_title'] = "no infor";
                }

                if ($kho == 'all') { // in tat ca k phan biet
                    $id++;
                    $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 7%;height: auto;float: left; text-align: left; padding-left:3px;' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;text-align: right;padding-right:5px;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";

                } elseif ($kho == 'lil') {
                    if (strpos($row['location'], 'AH') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                    }
                } elseif ($kho == 'AKL') {
                    if (strpos($row['location'], 'AKL') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                    }
                } elseif ($kho == 'cua_hang') {
                    if ($row['location'] == false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }

                        $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                    }
                }
                //end check in ra trong kho nao
            }
        }
        $html_content .= "
                                </div>
</div>
                                ";
        $html_content .= "
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style='clear: left;margin-top: 70px;'></p>
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Người Xuất Hàng</span>
            </div> 
            <div style='font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Quản Lý Kho</span>
            </div> 
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Tài Xế</span>
            </div> 
        ";
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($laixe . "-" . $date . ".pdf", array("Attachment" => 0));
    }

    //xuathang tong, tab bao cao xuathang
    public function baocao_xuathang()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');

        $shipper_id = $this->input->post('shipper_id');
        //$laixe = $this->m_voxy_package_orders->get_name_shipper($shipper_id);
        $laixe = "";

        $date = $this->input->post('date_for_orders');
        $date_end = $this->input->post('date_for_orders_end');

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

        $sorting = $this->input->post('sorting');//location or category
        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }

        $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ ngày: <b>' . $date . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến ngày: <b>' . $date_end . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Sắp xếp: ' . $xuattheo . '</span>
                        </div>
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO XUẤT KHO ' . $name_kho . '</h4>';

        $_all_products = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tong($date, $date_end, $laixe);//bang infor xuathang
        //var_dump($_all_products);die;
        $_all_products_le = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_le($date, $date_end, $laixe); //bang infor xuathang le
        $_all_products_xuattaikho = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_taikho($date, $date_end); //bang transfer_outkho

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
                if (isset($row['title'])) {
                    $band[$key] = $row['title'];
                    $auflage[$key] = $row['sku'];
                }
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['quantity'])) {
                    $row['quantity'] = 0;
                }
                $wek[$key] = $row['quantity'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }
        $all_products['export2'] = $export2;// sap xep lai san pham
//--------------------------------------------------------------------------------------------------------------------------------------
        //get số lượng đã xuất của các variant  , so sánh theo variant_id để biét đã xuất bao nhiêu sản phẩm
        $_history_xuathang_old = json_decode($this->m_voxy_package_xuathang->baocao_get_variants($date, $date_end)); //bang infor xuathang
        $_history_xuathang = array();
        if (isset($_history_xuathang_old) && isset($_history_xuathang_old) != null) {
            foreach ($_history_xuathang_old as $item) {
                $_item = get_object_vars($item);
                $_history_xuathang[] = $_item;
            }
        }

        $_history_xuathang_le_old = json_decode($this->m_voxy_package_xuathang->baocao_get_variants_le_listkiem($date, $date_end)); // bang infor xuathang le
        $_history_xuathang_le = array();
        if (isset($_history_xuathang_le_old) && isset($_history_xuathang_le_old) != null) {
            foreach ($_history_xuathang_le_old as $item) {
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
        } else if ($_history_xuathang_le != null && $_history_xuathang != null && $_history_xuathang_taikho["export2"] == null) {
            $history_xuathang = array_merge($_history_xuathang_le, $_history_xuathang);
        } else if ($_history_xuathang_le != null && $_history_xuathang == null && $_history_xuathang_taikho["export2"] != null) {
            $history_xuathang = array_merge($_history_xuathang_le, $_history_xuathang_taikho["export2"]);
        } else if ($_history_xuathang_le == null && $_history_xuathang != null && $_history_xuathang_taikho["export2"] != null) {
            $history_xuathang = array_merge($_history_xuathang, $_history_xuathang_taikho["export2"]);
        } else {
            $history_xuathang = array_merge($_history_xuathang_le, $_history_xuathang, $_history_xuathang_taikho["export2"]);
        }

        $export2_history = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($history_xuathang as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($history_xuathang as $key2 => $item2) {
                if ($key2 > $key) {
                    if (isset($item['variant_id']) && isset($item['quantity'])) {
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
//--------------------------------------------------------------------------------------------------------------------------------------
//        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Sau</span>
//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Trưóc</span>
        $tongtien = 0;

        $html_content .= "
<div class='products' style='font-family: Times New Roman; font-size: 14px'>
    <div class='pro_th'>
        <span style='display: none;width: 10%;float: left;text-align: center;text-align: left !important;'>Variant ID</span>
        
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 55%;float: left;text-align: center'>Mặt Hàng</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center'>Đơn Vị</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Xuất</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center'>Giá Vốn</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center'>Tiền</span>
    </div>
    <div class='pro_body'> ";
        $id = -1;
        if ($sorting == "category") {
            foreach ($all_products['result_catid'] as $catid) {//category
                if ($kho == 'AKL') {
                    if ($catid['cat_id'] == '91459649625') {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                    }
                } elseif ($kho == 'lil') {
                    foreach ($all_products['export2'] as $item2) {
                        if ($catid['cat_id'] === $item2['cat_id']) {
                            if (strpos($item2['location'], 'AH') !== false) {
                                $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                break;
                            }
                        }
                    }
                } elseif ($kho == 'cua_hang') {//trong cua hang
                    if ($catid['cat_id'] == false) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        foreach ($all_products['export2'] as $item5) {
                            if ($catid['cat_id'] === $item5['cat_id']) {
                                if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                                } else {
                                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                    break;
                                }
                            }
                        }
                    }
                } else {
//                    if (!isset($catid['cat_id'])) {
//                        $html_content .= "<b>No Category</b>";
//                    } else {
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $catid['title'] . "</p>";
                    //$html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                    //  }
                }
                //begin product to print
                $j = 0;
                foreach ($all_products['export2'] as $row) {//san pham
                    //check product co thuoc san pham do khong thi moi in ra
                    //if (!isset($row['cat_id'])) {// dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu
                    //if ($row['cat_id'] == "chuvantinh") {// dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu
                    if ($row['cat_id'] == "loaibo chuvantinh") {// dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu
                        $___sl_daxuat = 0;
                        if ($history_xuathang != null) {
                            foreach ($history_xuathang as $item_xuat) {
                                if (isset($item_xuat['variant_id']) && isset($row['variant_id'])) {
                                    if ($item_xuat['variant_id'] == $row['variant_id']) {
                                        //$quantity_xuathang = $item_xuat['quantity'];
                                        //$data_da_xuat = $item_xuat->data_da_xuat;
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
                        if (strlen($row['sku']) == 0) {
                            $row['sku'] = "no_sku;";
                        }

                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . (isset($row['variant_id']) && $row['variant_id'] != null) ? $row['variant_id'] : "" . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>0</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$___sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>0</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
                        ";
                    } else {
                        if ($catid['cat_id'] == $row['cat_id']) { // cho nay day em oi
                            $j++;
                            $sl_daxuat = $row['quantity'];

                            //deo hieu sao cho nay lai sai, phai kiem tra lai thoi
//                            if ($history_xuathang != null) {
//                                foreach ($history_xuathang as $item_xuat) {
//                                    if(isset($item_xuat['variant_id'])){
//                                        if ($item_xuat['variant_id'] == $row['variant_id']) {
//                                            //$quantity_xuathang = $item_xuat['quantity'];
//                                            //$data_da_xuat = $item_xuat->data_da_xuat;
//                                            $sl_daxuat = $item_xuat['quantity'];
//                                        }
//                                    }
//                                }
//
//                            }

                            if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                                $array_location = explode(',', $row['location']);
                                $row['location'] = '';
                                foreach ($array_location as $key => $loca) {
                                    $row['location'] .= $loca . '<br>';
                                }
                            }

                            if (isset($row['variant_id']) && $row['variant_id'] != "") {
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
                            }


                            //xu ly do dai cua sku
                            if (strlen($row['sku']) > 5) {
                                $row['sku'] = substr($row['sku'], 0, 5);
                            }

                            if (strlen($row['sku']) == 0) {
                                $row['sku'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                            }

                            //                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
//                            <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>

                            if (isset($row['variant_id']) || $row['variant_id'] != "") {
                                $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                            } else {
                                $idnew = false;
                            }

                            $giavon = 0;
                            if ($check_variant1 == true) {
                                //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
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

                            $thanhtien = (double)$giavon * (int)$sl_daxuat;

                            $tongtien += $thanhtien;

                            if ($kho == 'all') { // in tat ca k phan biet
                                $id++;
                                $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                
                <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='id'>" . $j . "</div>
                <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 55%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                <div style='width: 5%;height: auto;float: left;text-align: center' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: center;' class='giavon'>" . $giavon . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: center;' class='tien'>" . $thanhtien . "</div>
            </div>
        ";

                            } elseif ($kho == 'lil') {
                                if (strpos($row['location'], 'AH') !== false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                    $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                                }
                            } elseif ($kho == 'AKL') {
                                if (strpos($row['location'], 'AKL') !== false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                    $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                                }
                            } elseif ($kho == 'cua_hang') {
                                if ($row['location'] == false) {
                                    $id++;
                                    $value_note = '';
                                    foreach ($all_products['array_note_products'] as $item_note) {
                                        if ($item_note['title'] === $row['title']) {
                                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }

                                    $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                                    if ($value_note != '') {
                                        $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                                    }
                                    $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                                }
                            }
                            //end check in ra trong kho nao
                        }
                    }
                }
            }
        } else {//in theo location
//--------------------------------------------------------------------------------------------------------------------------------------
            $k = 0;
            foreach ($all_products['export2'] as $row) {//san pham
                //$data_da_xuat = 'nein';
                //$quantity_xuathang = 0;
                $sl_daxuat = $row['quantity'];

                if (!isset($row['variant_id'])) {
                    $row['variant_id'] = 0;
                }

                //xu ly do dai cua sku
                if (strlen($row['sku']) > 5) {
                    $row['sku'] = substr($row['sku'], 0, 5);
                }

//                if ($history_xuathang != null) {
//                    foreach ($history_xuathang as $item_xuat) {
//                        if ($item_xuat['variant_id'] == $row['variant_id']) {
//                            //$quantity_xuathang = $item_xuat['quantity'];
//                            //$data_da_xuat = $item_xuat->data_da_xuat;
//                            $sl_daxuat = $item_xuat['quantity'];
//                        }
//                    }
//                }

                if (!isset($row['location'])) {
                    $row['location'] = "";
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

                $quantity_before = (int)$sl_daxuat + (int)$quantity_in_ware_house;

                if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                    $row['variant_title'] = "no infor";
                }

                if (isset($row['title'])) {
                    $title = $row['title'];
                } else {
                    $title = "";
                }
                if (isset($row['variant_id']) && $row['variant_id'] != "") {
                    $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                } else {
                    $idnew = false;
                }

                if ($check_variant1 == true) {
                    //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
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

                $thanhtien = (double)$giavon * (int)$sl_daxuat;

                $tongtien += $thanhtien;

//                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
//                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>

                if ($kho == 'all') {// in tat ca k phan biet
                    $k++;
                    $id++;
                    $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 5%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                
                <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='$k'>" . $k . "</div>
                <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='width: 55%;font-family:DejaVu Sans;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $title . "</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                <div style='width: 5%;height: auto;float: left;text-align: center;' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 10%;height: auto;float: left;text-align: center;' class='giavon'>$giavon</div>
                <div style='width: 10%;height: auto;float: left;text-align: center;' class='thanhtien'>$thanhtien</div>
            </div>
        ";

                } elseif ($kho == 'lil') {
                    if (strpos($row['location'], 'AH') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= " 
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                    }
                } elseif ($kho == 'AKL') {
                    if (strpos($row['location'], 'AKL') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= "
                <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
          ";
                    }
                } elseif ($kho == 'cua_hang') {
                    if ($row['location'] == false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }

                        $html_content .= "
            <div class='infomation' style='width:100%;clear: left'>
                <div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id_$id;'>" . $row['variant_id'] . "</div>
                <div style='width: 8%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                <div style='font-family:DejaVu Sans;width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . " ";
                        if ($value_note != '') {
                            $html_content .= "<br><span style='text-transform: uppercase'><b>NOTE --></b>.$value_note.</span>";
                        }
                        $html_content .= "
                    </div>
                <div style='width: 7%;height: auto;float: left' class='quantity-" . $id . "'>$quantity_before</div>
                <div style='width: 5%;height: auto;float: left' class='sl-daxuat-$id'>$sl_daxuat</div>
                <div style='width: 5%;height: auto;float: left;color: red;' class='sl-daxuat-$id'>$quantity_in_ware_house</div>
                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
            </div>
        ";
                    }
                }
                //end check in ra trong kho nao
            }
        }

        $tongtien = number_format($tongtien, 2);

        $html_content .= "
                    <br>
                    <br>
                    <p style='margin-right: 30px;float:right;font-family: DejaVu Sans'>Tổng tiền: $tongtien €</p>";

        $html_content .= "
                                </div>
</div>
                                ";
        $html_content .= "
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style='clear: left;margin-top: 70px;'></p>
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Người Xuất Hàng</span>
            </div> 
            <div style='font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Quản Lý Kho</span>
            </div> 
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Tài Xế</span>
            </div> 
        ";

        //var_dump($html_content);die;

        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($laixe . "-" . $date . ".pdf", array("Attachment" => 0));
    }

    //nur load ajax
    public function baocao_taikho()
    {
        $this->load->model('m_voxy_transfer_out_kho');
        $list_id = $this->input->post('list_id');
        $ngay_dat_hang = $this->input->post('ngay_dat_hang');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');

        $data_return = array();
        if ($list_id != false) {
            $data_return['list_id'] = $list_id;
            if ($ngay_giao_hang != false) {
                //$data_return['giao_hang'] = $ngay_giao_hang;
            }

            if ($ngay_dat_hang != false) {
                //$data_return['dat_hang'] = $ngay_dat_hang;
            }
        } else {
            $data_return['status'] = 0;//ko thanh cong
        }

        echo json_encode($data_return);
        return true;
    }

    public function baocao_xuathang_taikho_pdf()
    {
        $this->load->model('m_voxy_transfer_out_kho');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_orders');

        $list_id = $this->input->post('list_id');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');
        if ($ngay_giao_hang == false) {
            $ngay_giao_hang = "";
        }

        $ngay_dat_hang = $this->input->post('ngay_dat_hang');
        if ($ngay_dat_hang == false) {
            $ngay_dat_hang = "";
        }

        if ($list_id && $list_id != false) {
            $data = $this->m_voxy_transfer_out_kho->get_infor_theo_ngay(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang);
        } else {
            $data = $this->m_voxy_transfer_out_kho->get_infor_theo_ngay("", $ngay_giao_hang, $ngay_dat_hang);//set list id  = ""
        }

        $export = array();//xu ly all product sang array
        if ($data) {
            foreach ($data as $item) {

                $_item = get_object_vars(json_decode($item['product_variants']));
                foreach ($_item as $item_con) {
                    $item_con->quantity = $item_con->sl_nhap;
                    $export[] = get_object_vars($item_con);
                }
            }
        }

        //in ra pdf below------------------------------------------------------------------------------------
        $kho = "all";
        $sorting = "category";//location or category
        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }

        $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ Ngày: <b>' . $ngay_dat_hang . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến Ngày: <b>' . $ngay_giao_hang . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Sắp xếp: danh mục</span>
                        </div>
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO XUẤT HÀNG TẠI KHO</h4>';


        $export2 = array();
        $chiso_remove = array();
        foreach ($export as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($export as $key2 => $item2) {
                if ($key2 > $key) {
                    if ($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] && $item['variant_id'] == $item2['variant_id']) {
                        if (!isset($item['quantity'])) {
                            $item['quantity'] = 0;
                        }
                        $item['quantity'] = (int)$item['quantity'] + (int)$item2['quantity'];
                        $chiso_remove[$key2] = $key2;//index of same product and then remove it
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
        $all_products['export2'] = $export2;// sap xep lai san pham

        //loc category
        $arr_cat_id = array();
        foreach ($export2 as $item) {
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);

            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
        }

        // step 2: sort tang dan
        ksort($arr_cat_id);
        $all_products['result_catid'] = $arr_cat_id;
//--------------------------------------------------------------------------------------------------------------------------------------

//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Giá Vốn</span>
//          <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Thành tiền</span>

        $html_content .= "
<div class='products' style='font-family: DejaVu Sans; font-size: 15px'>
    <div class='pro_th'>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 70%;float: left;text-align: center'>Mặt Hàng</span>
         <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center'>Đơn Vị</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Xuất</span>
    </div>
    <div class='pro_body'> ";
        $id = -1;
        $tongtien = 0;
        $k = 0;
        if ($sorting == "category") {
            foreach ($all_products['result_catid'] as $catid) {//category
                if ($kho == 'AKL') {
                    if ($catid['cat_id'] == '91459649625') {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                    }
                } elseif ($kho == 'lil') {
                    foreach ($all_products['export2'] as $item2) {
                        if ($catid['cat_id'] === $item2['cat_id']) {
                            if (strpos($item2['location'], 'AH') !== false) {
                                $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                break;
                            }
                        }
                    }
                } elseif ($kho == 'cua_hang') {//trong cua hang
                    if ($catid['cat_id'] == false) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        foreach ($all_products['export2'] as $item5) {
                            if ($catid['cat_id'] === $item5['cat_id']) {
                                if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                                } else {
                                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    if (!isset($catid['cat_id'])) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $catid['title'] . "</p>";
                    }
                }
                $id_new = 0;
                //begin product to print
                foreach ($all_products['export2'] as $row) {//san pham
                    //check product co thuoc san pham do khong thi moi in ra
                    if (!isset($row['cat_id'])) {// dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu


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
                        if (strlen($row['sku']) == 0) {
                            $row['sku'] = "no_sku;";
                        }

                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
                        ";
                    } else {
                        if ($catid['cat_id'] == $row['cat_id']) {
                            if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                                $array_location = explode(',', $row['location']);
                                $row['location'] = '';
                                foreach ($array_location as $key => $loca) {
                                    $row['location'] .= $loca . '<br>';
                                }
                            }

                            //xu ly do dai cua sku
                            if (strlen($row['sku']) > 5) {
                                $row['sku'] = substr($row['sku'], 0, 5);
                            }

                            if (strlen($row['sku']) == 0) {
                                $row['sku'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                            }


                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);

                            if (isset($row['variant_id']) && $row['variant_id'] != "") {
                                $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                            } else {
                                $idnew = false;
                            }

                            if ($check_variant1 == true) {
                                //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
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

                            $thanhtien = (double)$giavon * (int)$row['sl_nhap'];

                            $tongtien += $thanhtien;

                            if ($kho == 'all') { // in tat ca k phan biet
//                                <div style='width: 7%;height: auto;float: left'>".$giavon."</div>
//                            <div style='width: 7%;height: auto;float: left'>".$thanhtien."</div>

                                $id++;
                                $k++;
                                $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='id'>" . $k . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            
                        </div>
                        ";
                            } elseif ($kho == 'lil') {
                                if (strpos($row['location'], 'AH') !== false) {
                                    $id++;
                                    $html_content .= " 
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                                }
                            } elseif ($kho == 'AKL') {
                                if (strpos($row['location'], 'AKL') !== false) {
                                    $id++;

                                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
          ";
                                }
                            } elseif ($kho == 'cua_hang') {
                                if ($row['location'] == false) {
                                    $id++;

                                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                                }
                            }
                            //end check in ra trong kho nao
                        }
                    }
                }
            }
        } else {//in theo location
//--------------------------------------------------------------------------------------------------------------------------------------
            foreach ($all_products['export2'] as $row) {//san pham
                //$data_da_xuat = 'nein';
                //$quantity_xuathang = 0;
                $sl_daxuat = 0;
                if (!isset($row['variant_id'])) {
                    $row['variant_id'] = 0;
                }

                //xu ly do dai cua sku
                if (strlen($row['sku']) > 5) {
                    $row['sku'] = substr($row['sku'], 0, 5);
                }

                if (!isset($row['location'])) {
                    $row['location'] = "";
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

                if (isset($row['variant_id']) && $row['variant_id'] != "") {
                    $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                } else {
                    $idnew = false;
                }

                if ($check_variant1 == true) {
                    //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
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
                $thanhtien = (double)$giavon * (int)$row['sl_nhap'];

                $tongtien += $thanhtien;


                if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                    $row['variant_title'] = "no infor";
                }

                if ($kho == 'all') { // in tat ca k phan biet
                    $id++;
                    $k++;
                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='id'>" . $k . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                            <div style='width: 5%;height: auto;float: left;text-align: center'>" . $row['sl_nhap'] . "</div>
                            
                        </div>
        ";

//                    <div style='width: 5%;height: auto;float: left;text-align: center'>".$giavon."</div>
//                            <div style='width: 5%;height: auto;float: left;text-align: center'>".$thanhtien."</div>
                } elseif ($kho == 'lil') {
                    if (strpos($row['location'], 'AH') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= " 
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                    }
                } elseif ($kho == 'AKL') {
                    if (strpos($row['location'], 'AKL') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
          ";
                    }
                } elseif ($kho == 'cua_hang') {
                    if ($row['location'] == false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }

                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                    }
                }
                //end check in ra trong kho nao
            }
        }
        $html_content .= "
                                </div>
</div>
                                ";

        $tongtien = number_format($tongtien, 2);

//        $html_content .= "
//                    <br>
//                    <br>
//                    <p style='margin-right: 30px;float:right;font-family: DejaVu Sans'>Tổng tiền: $tongtien €</p>";

        $html_content .= "
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p style='clear: left;margin-top: 70px;'></p>
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px; text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Người Xuất Hàng</span>
            </div> 
            <div style='font-family: DejaVu Sans ; width: 33%; float:left; font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Quản Lý Kho</span>
            </div> 
            <div style='font-family: DejaVu Sans; width: 33%; float:left;font-size: 12px;text-align: center;'>
                <hr style='width: 200px;margin: 0; padding: 0;'>
                <span style='text-align: center'>Tài Xế</span>
            </div> 
        ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream("Ngay-" . $ngay_dat_hang . "to" . $ngay_giao_hang . ".pdf", array("Attachment" => 0));
    }

    //nhap kho
    public function baocao_nhapkho_ajax()
    {
        $this->load->model('m_voxy_transfer');
        $list_id = $this->input->post('list_id');
        $ngay_dat_hang = $this->input->post('ngay_dat_hang');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');

        $data_return = array();
        if ($list_id != false) {
            $data_return['list_id'] = $list_id;
            if ($ngay_giao_hang != false) {
                //$data_return['giao_hang'] = $ngay_giao_hang;
            }

            if ($ngay_dat_hang != false) {
                //$data_return['dat_hang'] = $ngay_dat_hang;
            }
        } else {
            $data_return['status'] = 0;//ko thanh cong
        }

        echo json_encode($data_return);
        return true;
    }

    //chi dung cho nhan vien.admin below
    public function baocao_nhaphang_taikho_pdf()
    {
        $this->load->model('m_voxy_transfer');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');

        $list_id = $this->input->post('list_id');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');
        if ($list_id && $list_id != false) {
            $ngay_giao_hang = "";
            $ngay_dat_hang = "";
        } else {
            if ($ngay_giao_hang == false) {
                $ngay_giao_hang = date('Y-m-d');
            }

            $ngay_dat_hang = $this->input->post('ngay_dat_hang');
            if ($ngay_dat_hang == false) {
                $ngay_dat_hang = date('Y-m-d');
            }
        }

        $vendor = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($vendor);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }

        if ($list_id && $list_id != false) {
            $vendor_name = "";
            $data = $this->m_voxy_transfer->get_infor_theo_ngay(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang, $vendor);
        } else {
            $data = $this->m_voxy_transfer->get_infor_theo_ngay("", $ngay_giao_hang, $ngay_dat_hang, $vendor);//set list id  = ""
        }

        if ($data == "" || $data == null) {
            echo "KHông có dữ liệu, bitte chọn nhà cung cấp khác ";
            die;
        }

        $export = array();//xu ly all product sang array
        if ($data) {
            foreach ($data as $item) {

                $_item = get_object_vars(json_decode($item['product_variants']));
                foreach ($_item as $item_con) {
                    $item_con->quantity = $item_con->sl_nhap;
                    $export[] = get_object_vars($item_con);
                }
            }
        }

        //in ra pdf below------------------------------------------------------------------------------------
        $kho = "all";
        $sorting = "category";//location or category
        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }

        $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ Ngày: <b>' . $ngay_dat_hang . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến Ngày: <b>' . $ngay_giao_hang . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Nhà cung cấp: ' . $vendor_name . '</span>
                        </div>
                       
                        </head><br>';
        if ($list_id && $list_id != false) {
            $list_id_print = json_decode($list_id)->list_id;
            $list_id_print = implode(",", $list_id_print);
            $html_content .= "<span>ID: " . $list_id_print . "</span>";
        }
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO NHẬP HÀNG TẠI KHO</h4>';

        $export2 = array();
        $chiso_remove = array();
        foreach ($export as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($export as $key2 => $item2) {
                if ($key2 > $key) {
                    if ($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] && $item['variant_id'] == $item2['variant_id']) {
                        if (!isset($item['quantity'])) {
                            $item['quantity'] = 0;
                        }
                        $item['quantity'] = (int)$item['quantity'] + (int)$item2['quantity'];
                        $chiso_remove[$key2] = $key2;//index of same product and then remove it
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
        $all_products['export2'] = $export2;// sap xep lai san pham

        //loc category
        $arr_cat_id = array();
        foreach ($export2 as $item) {
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);

            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
        }

        // step 2: sort tang dan
        ksort($arr_cat_id);
        $all_products['result_catid'] = $arr_cat_id;
//--------------------------------------------------------------------------------------------------------------------------------------
//        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;font-size: 13px;'>Giá mua</span>
//        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: right;font-size: 13px;'>Tổng vốn</span>
        $html_content .= "
<div class='products' style='font-family: DejaVu Sans; font-size: 15px'>
    <div class='pro_th'>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 70%;float: left;text-align: center'>Tên</span>
       <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: left'>Đơn Vị</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Nhập</span>
    </div>
    <div class='pro_body'> ";
        $id = 0;
        $tongtien = 0;

        if ($sorting == "category") {
            foreach ($all_products['result_catid'] as $catid) {//category

                if ($kho == 'AKL') {
                    if ($catid['cat_id'] == '91459649625') {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                    }
                } elseif ($kho == 'lil') {
                    foreach ($all_products['export2'] as $item2) {
                        if ($catid['cat_id'] === $item2['cat_id']) {
                            if (strpos($item2['location'], 'AH') !== false) {
                                $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                break;
                            }
                        }
                    }
                } elseif ($kho == 'cua_hang') {//trong cua hang
                    if ($catid['cat_id'] == false) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        foreach ($all_products['export2'] as $item5) {
                            if ($catid['cat_id'] === $item5['cat_id']) {
                                if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                                } else {
                                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    if (!isset($catid['cat_id'])) {
                        $html_content .= "<b>No Category</b>";
                    } else {
                        $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                    }
                }
                //begin product to print
                foreach ($all_products['export2'] as $row) {//san pham
                    //check product co thuoc san pham do khong thi moi in ra
                    if (!isset($row['cat_id'])) {// dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu

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
                        if (strlen($row['sku']) == 0) {
                            $row['sku'] = "no_sku;";
                        }

                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
                        ";
                    } else {
                        if ($catid['cat_id'] == $row['cat_id']) {
                            if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                                $array_location = explode(',', $row['location']);
                                $row['location'] = '';
                                foreach ($array_location as $key => $loca) {
                                    $row['location'] .= $loca . '<br>';
                                }
                            }

                            //xu ly do dai cua sku
                            if (strlen($row['sku']) > 5) {
                                $row['sku'] = substr($row['sku'], 0, 5);
                            }

                            if (strlen($row['sku']) == 0) {
                                $row['sku'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                            }

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
                            $thanhtien = (double)$giavon * (int)$row['sl_nhap'];

                            $tongtien += $thanhtien;


                            if ($kho == 'all') { // in tat ca k phan biet

//                                <div style='width: 5%;height: auto;float: left; text-align: center'>".$giavon."</div>
//                            <div style='width: 10%;height: auto;float: left; text-align: right'>".$thanhtien."</div>

                                $id++;
                                $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $id . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                            <div style='width: 5%;height: auto;float: left; text-align: left'>" . $row['sl_nhap'] . "</div>
                            
                            
                        </div>
                        ";
                            } elseif ($kho == 'lil') {
                                if (strpos($row['location'], 'AH') !== false) {
                                    $id++;
                                    $html_content .= " 
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                                }
                            } elseif ($kho == 'AKL') {
                                if (strpos($row['location'], 'AKL') !== false) {
                                    $id++;

                                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
          ";
                                }
                            } elseif ($kho == 'cua_hang') {
                                if ($row['location'] == false) {
                                    $id++;

                                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                                }
                            }
                            //end check in ra trong kho nao
                        }
                    }
                }
            }
        } else {//in theo location
//--------------------------------------------------------------------------------------------------------------------------------------
            foreach ($all_products['export2'] as $row) {//san pham
                //$data_da_xuat = 'nein';
                //$quantity_xuathang = 0;
                $sl_daxuat = 0;
                if (!isset($row['variant_id'])) {
                    $row['variant_id'] = 0;
                }

                //xu ly do dai cua sku
                if (strlen($row['sku']) > 5) {
                    $row['sku'] = substr($row['sku'], 0, 5);
                }

                if (!isset($row['location'])) {
                    $row['location'] = "";
                }

                if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                    $array_location = explode(',', $row['location']);
                    $row['location'] = '';
                    foreach ($array_location as $key => $loca) {
                        $row['location'] .= $loca . '<br>';
                    }
                }

//                $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
//                $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
//                $quantity_in_ware_house = 0;
//                if ($check_variant1 == true) {
//                    $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
//                }
//
//                if ($check_variant2 == true) {
//                    $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
//                }
//
//                $quantity_before = $sl_daxuat + $quantity_in_ware_house;

                if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                    $row['variant_title'] = "no infor";
                }

                if ($kho == 'all') { // in tat ca k phan biet
//                    <div style='width: 5%;height: auto;float: left'>".$giavon."</div>
//                            <div style='width: 10%;height: auto;float: left'>".$thanhtien."
                    $id++;
                    $html_content .= "
                        <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $id . "</div>
                            <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 60%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: right !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            
                            </div>
        ";

                } elseif ($kho == 'lil') {
                    if (strpos($row['location'], 'AH') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= " 
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                    }
                } elseif ($kho == 'AKL') {
                    if (strpos($row['location'], 'AKL') !== false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
          ";
                    }
                } elseif ($kho == 'cua_hang') {
                    if ($row['location'] == false) {
                        $id++;
                        $value_note = '';
                        foreach ($all_products['array_note_products'] as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                            }
                        }

                        $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['sl_nhap'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                    }
                }
                //end check in ra trong kho nao
            }
        }

        $tongtien = number_format($tongtien, 2);

        /*$html_content .= "
            <br>
            <br>
            <p style='margin-right: 30px;float:right;font-family: DejaVu Sans'>Tổng tiền: <b>$tongtien </b> €</p>";*/


        $html_content .= "
                                </div>
</div>
                                ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream("Ngay-" . $ngay_dat_hang . "to" . $ngay_giao_hang . ".pdf", array("Attachment" => 0));
    }


    public function baocao_nhaphang_taikho_pdf_admin()
    {
        $this->load->model('m_voxy_transfer');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');

        $list_id = $this->input->post('list_id');

        $ngay_giao_hang = $this->input->post('ngay_giao_hang');
        if ($list_id && $list_id != false) {
            $ngay_giao_hang = "";
            $ngay_dat_hang = "";
        } else {
            if ($ngay_giao_hang == false) {
                $ngay_giao_hang = date('Y-m-d');
            }

            $ngay_dat_hang = $this->input->post('ngay_dat_hang');
            if ($ngay_dat_hang == false) {
                $ngay_dat_hang = date('Y-m-d');
            }
        }

        $vendor = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($vendor);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }

        if ($list_id && $list_id != false) {
            $vendor_name = "";
            $data = $this->m_voxy_transfer->get_infor_theo_ngay(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang, $vendor);
        } else {
            $data = $this->m_voxy_transfer->get_infor_theo_ngay("", $ngay_giao_hang, $ngay_dat_hang, $vendor);//set list id  = ""
        }

        if ($data == "" || $data == null) {
            echo "KHông có dữ liệu, bitte chọn ngày tháng khác ";
            die;
        }

        $export = array();//xu ly all product sang array
        if ($data) {
            foreach ($data as $item) {
                $_item = get_object_vars(json_decode($item['product_variants']));
                foreach ($_item as $item_con) {
                    $item_con->quantity = $item_con->sl_nhap;
                    $export[] = get_object_vars($item_con);
                }
            }
        }

        //in ra pdf below------------------------------------------------------------------------------------
        $kho = "all";
        $sorting = "category";//location or category
        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }


        $export2 = array();
        $chiso_remove = array();

        foreach ($export as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            if(! isset($item['title'] )){
                $item['gianhapnew'] = 0;
                $item['sku'] = "";
                $item['title'] = "";
                $item['variant_title'] = "";
                $item['product_id'] = "";
            }
            foreach ($export as $key2 => $item2) {
                if ($key2 > $key) {
                    if(! isset($item2['title'] )){
                        $item2['gianhapnew'] = 0;
                        $item2['sku'] = "";
                        $item2['title'] = "";
                        $item2['variant_title'] = "";
                        $item2['product_id'] = "";
                    }
                    if ($item['variant_id'] == $item2['variant_id'] && $item['gianhapnew'] == $item2['gianhapnew']) {
                        if (!isset($item['sl_nhap'])) {
                            $item['sl_nhap'] = 0;
                        }
                        $item['sl_nhap'] = $item['sl_nhap'] + $item2['sl_nhap'];
                        $chiso_remove[$key2] = $key2;//index of same product and then remove it
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
        $all_products['export2'] = $export2;// sap xep lai san pham

        //loc category
        $arr_cat_id = array();
        foreach ($export2 as $item) {
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);

            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
        }

        // step 2: sort tang dan
        ksort($arr_cat_id);
        $all_products['result_catid'] = $arr_cat_id;
//--------------------------------------------------------------------------------------------------------------------------------------

        //export to excel file
        require_once APPPATH . "/third_party/PHPExcel.php";


        if ($list_id && $list_id != false) {
            $list_id_print = json_decode($list_id)->list_id;
            $list_id_print = implode(",", $list_id_print);
        }else{
            $list_id_print = "";
        }
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
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
        $styleArray8 = array(
            'font' => array(
                'size' => 15,
                'name' => 'Time New Roman',
            ));

        $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:P3')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A4:P4')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A5:P5')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A6:P6')->getFont()->setBold(true);
        //$excel->getActiveSheet()->getStyle('A8:P8')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('C1', "Báo cáo Nhập Hàng");
        $excel->getActiveSheet()->setCellValue('C2', "Từ ngày:" . $ngay_dat_hang);
        $excel->getActiveSheet()->setCellValue('C3', "Đến ngày:" . $ngay_giao_hang);
        $excel->getActiveSheet()->setCellValue('C4', "Nhà cung cấp:" . $vendor_name);
        $excel->getActiveSheet()->setCellValue('C5', "ID:" . $list_id_print);

        $excel->getActiveSheet()->setCellValue('A6', 'STT');
        $excel->getActiveSheet()->setCellValue('B6', 'SKU ');
        $excel->getActiveSheet()->setCellValue('C6', 'Tên sản phẩm');
        $excel->getActiveSheet()->setCellValue('D6', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('E6', 'Số lượng');
        $excel->getActiveSheet()->setCellValue('F6', 'Giá nhập');
        $excel->getActiveSheet()->setCellValue('G6', 'Thành tiền');
       // $excel->getActiveSheet()->setCellValue('H6', 'Ghi chú');

        $id = 0;
        $numRow = 6;
        $tongtien = 0;
        foreach ($all_products['export2'] as $row) {//san pham
            $numRow++;
                    //xu ly do dai cua sku
                    if (strlen($row['sku']) > 5) {
                        $row['sku'] = substr($row['sku'], 0, 5);
                    }

                    if (strlen($row['sku']) == 0) {
                        $row['sku'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }

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

                    $gianhapnew = (isset($row['gianhapnew']) ? $row['gianhapnew'] : 0);
                    $thanhtien = (double)$gianhapnew * (double)$row['sl_nhap'];
                    $tongtien += $thanhtien;

                        $id++;
            $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['variant_title']);
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_nhap']);
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['gianhapnew']);
            $excel->getActiveSheet()->setCellValue('G' . $numRow, $thanhtien);
           // $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['note']);

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

        $tongtien = number_format($tongtien, 2);
        $numRow++;
        $excel->getActiveSheet()->setCellValue('F' . $numRow, "Tổng cộng");
        //$excel->getActiveSheet()->setCellValue('F' . $numRow,number_format($tong_total_price,2));
        $excel->getActiveSheet()->setCellValue('G' . $numRow, $tongtien);

        $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('G' . $numRow)->applyFromArray($styleArray2);

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_nhaphang.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    //tab bao cao xuat hang, button theo khach hang
    public function baocao_theo_khachhang()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');

        $shipper_id = $this->input->post('shipper_id');
        if ($shipper_id != false) {
            $laixe = array();
            foreach ($shipper_id as $item) {
                $laixe[] = $this->m_voxy_package_orders->get_name_shipper((int)$item);
            }
            $laixe_print = implode(",", $laixe);
        } else {
            $shipper_id = "";
            $laixe_print = "Tất cả";
        }


        $date = $this->input->post('date_for_orders');
        $date_end = $this->input->post('date_for_orders_end');

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

        $sorting = $this->input->post('sorting');//location or category

        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }

        $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ ngày: <b>' . $date . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến ngày: <b>' . $date_end . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Lái xe: ' . $laixe_print . '</span>
                        </div>
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO THEO TOUR</h4>';

        $all_tour = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tour($date, $date_end, $shipper_id);//bang voxy_package_orders
//--------------------------------------------------------------------------------------------------------------------------------------
        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same tour
        //var_dump($all_tour);die;
        foreach ($all_tour as $key => $item) {
            foreach ($all_tour as $key2 => $item2) {
                if ($key2 > $key) {
                    if ((int)$item['shipper_id'] == (int)$item2['shipper_id']) {
                        if (isset($item['total_price']) && isset($item2['total_price'])) {
                            $item['total_price'] = (double)$item['total_price'] + (double)$item2['total_price'];
                            $item['order_number'] = $item['order_number'] . "," . $item2['order_number'];
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
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                if (isset($row['title'])) {
                    $band[$key] = $row['title'];
                    $auflage[$key] = $row['sku'];
                }
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                $wek[$key] = $row['total_price'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }
        $all_products['export2'] = $export2;// sap xep lai san pham
//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
//        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Sau</span>
//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Trưóc</span>
        $tongtien = 0;

        $html_content .= "
<div class='products' style='font-family: Times New Roman; font-size: 14px'>
    <div class='pro_th'>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 20%;float: left;text-align: center;text-align: left !important;'>Khách Hàng</span>
        <span style='font-family: DejaVu Sans;width: 25%;float: left;text-align: center'>Giá Trị Đơn Hàng</span>
        <span style='font-family: DejaVu Sans;width: 50%;float: left;text-align: center'>Ghi Chú</span>
    </div>
    <div class='pro_body'> ";
        $id = 0;
        $tongtien = 0;
        foreach ($all_products['export2'] as $row) {
            $tongtien += $row['total_price'];
            $row['total_price'] = number_format($row['total_price'], 2);

            $arr = explode(",", $row['order_number']);
            $order_number = "";
            $j = 1;
            foreach ($arr as $item) {
                $order_number .= $item . " ";
                if ($j % 8 == 0) {
                    $j = 0;
                    $order_number .= "<br>";
                }
                $j++;
            }

            if ($kho == 'all') {// in tat ca k phan biet
                $id++;
                $html_content .= "
                <div class='infomation' style='width:100%;clear: left;'>
                    <div style='width: 5%;height: auto;float: left;text-align: left !important;'>" . $id . "</div>
                    <div style='width: 20%;font-family:DejaVu Sans;height: auto;float: left;text-align: left !important;'>" . $row['shipper_name'] . "</div>
                    <div style='width: 25%;font-family:DejaVu Sans;height: auto;float: left; text-align: left'>" . $row['total_price'] . "</div>
                    <div style='width: 50%;height: auto;float: left;text-align: left !important;'>" . $row['note'] . " " . "</div>
                </div>
                <br>
                        ";
            }

        }
        $tongtien = number_format($tongtien, 2);

        $html_content .= "
                    <br>
                    <br>
                    <p style='margin-left: 180px;float:left;font-family: DejaVu Sans'>Tổng tiền: <b>$tongtien</b> €</p>";

        $html_content .= "
                                </div>
</div>
                                ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($date . ".pdf", array("Attachment" => 0));
    }

    //tab bao cao nhap hang theo nha cung cap, transfer
    public function baocao_theo_nhaccungcap()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');
        $this->load->model('m_voxy_package');

        $date_end = $this->input->post('ngay_giao_hang');
        if ($date_end == false) {
            $date_end = date("Y-m-d");
        }

        $date = $this->input->post('ngay_dat_hang');
        if ($date == false) {
            $date = date("Y-m-d");
        }

        $nhacc = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($nhacc);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }
        $kho = "all";
        $sorting = "sl_xuat";//theo so tien cua nha cung cap di

        $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ ngày: <b>' . $date . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến ngày: <b>' . $date_end . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Nhà cung cấp: ' . $vendor_name . '</span>
                        </div>
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO TỔNG HỢP NHẬP HÀNG THEO NHÀ CUNG CẤP</h4>';

        $all_tour = $this->m_voxy_package_xuathang->baocao_nhaphang_nhacungcap($date, $date_end, $nhacc);//bang voxy_package_orders

        if ($all_tour == null || $all_tour == "") {
            echo "KHÔNG CÓ DỮ LIỆU , XIN CHỌN LẠI NGÀY VÀ NHÀ CUNG CẤP";
            die;
        }
//--------------------------------------------------------------------------------------------------------------------------------------
        $all_tour2 = array();
        $tongtien = 0;
        foreach ($all_tour as $key => $item) {
            //var_dump(get_object_vars(json_decode($item['product_variants'])));die;
            $thanhtien = 0;
            foreach (get_object_vars(json_decode($item['product_variants'])) as $row) {
                $row = get_object_vars($row);
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

                if (!isset($giavon)) {
                    $giavon = 0;
                }

                //update code date 27.05 vi co update them nhap gia
                if (isset($row['gianhap_new']) && $row['gianhap_new'] != "") {
                    $giavon = $row['gianhap_new'];
                }

                $thanhtien += (double)$giavon * (double)$row['sl_nhap'];
                $item['total_price'] = $thanhtien;
            }

            $tongtien += $thanhtien;
            $all_tour2[] = $item;
        }
        //var_dump($thanhtien);die;

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same tour
        foreach ($all_tour2 as $key => $item) {
            foreach ($all_tour2 as $key2 => $item2) {
                if ($key2 > $key) {
                    if ((int)$item['vendor'] == (int)$item2['vendor']) {
                        if (isset($item['total_price']) && isset($item2['total_price'])) {
                            $item['total_price'] = (double)$item['total_price'] + (double)$item2['total_price'];
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
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                if (isset($row['title'])) {
                    $band[$key] = $row['title'];
                    $auflage[$key] = $row['sku'];
                }
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                $wek[$key] = $row['total_price'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }
        $all_products['export2'] = $export2;// sap xep lai san pham
//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
//        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Sau</span>
//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Trưóc</span>
        $html_content .= "
<div class='products' style='font-family: Times New Roman; font-size: 14px'>
    <div class='pro_th'>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 50%;float: left;text-align: center;text-align: left !important;'>Nhà cung cấp</span>
        <span style='font-family: DejaVu Sans;width: 25%;float: left;text-align: center'>Giá Trị Đơn Hàng</span>
        <span style='font-family: DejaVu Sans;width: 20%;float: left;text-align: center'>Ghi chú</span>
    </div>
    
    <div class='pro_body'> ";
        $id = 0;
        foreach ($all_products['export2'] as $row) {
            $row['total_price'] = number_format($row['total_price'], 2);

            $vendor = $this->m_voxy_nha_cung_cap->get_title($row['vendor']);
            if ($kho == 'all') {// in tat ca k phan biet
                $id++;
                $html_content .= "
                <div class='infomation' style='width:100%;clear: left;'>
                    <div style='width: 5%;height: auto;float: left;text-align: left !important;'>" . $id . "</div>
                    <div style='width: 50%;font-family:DejaVu Sans;height: auto;float: left;text-align: left !important;'>" . $vendor . "</div>
                    <div style='width: 25%;font-family:DejaVu Sans;height: auto;float: left; text-align: left'>" . $row['total_price'] . "</div>
                    <div style='width: 20%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['note'] . "</div>
                </div>
                <br>
                        ";
            }

        }
        $tongtien = number_format($tongtien, 2);

        $html_content .= "
                    <br>
                    <br>
                    <p style='margin-left: 400px;float:left;font-family: DejaVu Sans'>Tổng tiền: <b>$tongtien</b> €</p>";
        $html_content .= "
                                </div>
</div>
                                ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($date . "-" . $date_end . ".pdf", array("Attachment" => 0));
    }

    public function baocao_hangve_sanpham()
    {
        require_once APPPATH . "/third_party/PHPExcel.php";
        $this->load->model('m_voxy_transfer');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');

        $list_id = $this->input->post('list_id');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');
        if ($ngay_giao_hang == false) {
            $ngay_giao_hang = date("Y-m-d");
        }

        $ngay_dat_hang = $this->input->post('ngay_dat_hang');
        if ($ngay_dat_hang == false) {
            $ngay_dat_hang = date("Y-m-d");
        }

        $vendor = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($vendor);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }

        //not used with the manufacturer
//        if ($list_id && $list_id != false) {
//            $data = $this->m_voxy_transfer->get_infor_theo_ngay_hangve(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang, $vendor);
//        } else {
//
//        }

        $data = $this->m_voxy_package_orders->get_infor_theo_ngay_hangve($ngay_giao_hang, $ngay_dat_hang);//set list id  = ""

        if ($data == "" || $data == null) {
            echo "KHông có dữ liệu, bitte chọn nhà cung cấp khác ";
            die;
        }

        //in ra pdf below------------------------------------------------------------------------------------
        $kho = "all";
        $sorting = "category";//location or category
        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }

        $export2 = array();
        foreach ($data as $item) {
            //var_dump($item);die;
            $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
            $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
            if ($check_variant1 == true) {
                //GET SKU,get don vi ,gia ban, thanh tien.
                $data_get = $this->m_voxy_package->get_all_infor($idnew);
                foreach ($data_get as $item2) {
                    $item['sku'] = $item2['sku1'];
                    $item['title'] = $item2['title'];
                    $item['variant_title'] = $item2['option1'];
                    $item['product_id'] = $item2['id_shopify'];
                    $item['cat_id'] = $item2['cat_id'];
                    $item['location'] = $item2['location'];
                }
                if ($idnew != false) {
                    $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                } else {
                    $giavon = 0;
                }

                $item['quantity'] = $item['sl_nhap'];
            }
            if ($check_variant2 == true) {
                $data_get = $this->m_voxy_package->get_all_infor($idnew);
                foreach ($data_get as $item2) {
                    $item['sku'] = $item2['sku2'];
                    $item['title'] = $item2['title'];
                    $item['variant_title'] = $item2['option2'];
                    $item['product_id'] = $item2['id_shopify'];
                    $item['cat_id'] = $item2['cat_id'];
                    $item['location'] = $item2['location'];
                }
                $item['quantity'] = $item['sl_nhap'];
                if ($idnew != false) {
                    $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                } else {
                    $giavon = 0;
                }
            }
            $export2[] = $item;
        }

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php

        if ($sorting == "location") {
            $wek = array();
            foreach ($export2 as $key => $row) {
                if (!isset($row['shipper_name'])) {
                    $row['shipper_name'] = "";
                }
                $wek[$key] = $row['shipper_name'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_ASC, $export2);
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan

            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['shipper_name'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'shipper_name');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
//                if (!isset($row['quantity'])) {
//                    $row['quantity'] = "";
//                }
                $wek[$key] = $row['shipper_name'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }

        $all_products['export2'] = $export2;// sap xep lai san pham

        //loc category
        $arr_cat_id = array();
        foreach ($export2 as $item) {
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);

            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
        }

        // step 2: sort tang dan
        ksort($arr_cat_id);
        $all_products['result_catid'] = $arr_cat_id;
//--------------------------------------------------------------------------------------------------------------------------------------

        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
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
        $styleArray8 = array(
            'font' => array(
                'size' => 15,
                'name' => 'Time New Roman',
            ),
            'alignment' => array(
                'horizontal' => 'right'
            )
        );

        $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:P3')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('B1', "Báo cáo hàng trả về");

        $new_ngaydathang = date("d-m-Y", strtotime($ngay_dat_hang));
        $new_ngaygiaohang = date("d-m-Y", strtotime($ngay_giao_hang));

        $excel->getActiveSheet()->setCellValue('A2', "Từ ngày:" . $new_ngaydathang);
        $excel->getActiveSheet()->setCellValue('B2', "Đến ngày:" . $new_ngaygiaohang);

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'SKU');
        $excel->getActiveSheet()->setCellValue('C3', 'Title');
        $excel->getActiveSheet()->setCellValue('D3', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('E3', 'Số lượng');
        $excel->getActiveSheet()->setCellValue('F3', 'Giá bán €');
        $excel->getActiveSheet()->setCellValue('G3', 'Thành tiền €');
        $excel->getActiveSheet()->setCellValue('H3', 'Lý Do');
        $excel->getActiveSheet()->setCellValue('I3', 'Lái xe');
        $excel->getActiveSheet()->setCellValue('J3', 'Đơn hàng');
        $excel->getActiveSheet()->setCellValue('K3', 'Ngày');


        $id = 0;
        $tongtien = 0;
        $numRow = 4;
        foreach ($all_products['export2'] as $row) {//san pham
            $id++;
            if (!isset($row['variant_title'])) {
                $row['variant_title'] = "#";
            }

            if (!isset($row['note'])) {
                $row['note'] = "#";
            }

            $shipped_at = date("d-m-Y", strtotime($row['shipped_at']));

            $tongtien += $row['thanhtien'];

            $price_sell = round(($row['thanhtien'] / $row['sl_nhap']),2);

            $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['variant_title']);
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_nhap']);
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $price_sell);
            $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['thanhtien']);
            $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['note']);
            $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['shipper_name']);
            $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['order_number']);
            $excel->getActiveSheet()->setCellValue('K' . $numRow, $shipped_at);
            $numRow++;
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
        }

        $tongtien = number_format($tongtien, 2);

        $numRow = $numRow + 1;
        $excel->getActiveSheet()->setCellValue('G' . $numRow, $tongtien);
        $excel->getActiveSheet()->getStyle('G' . $numRow)->applyFromArray($styleArray8);

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_hangve.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function baocao_hangve_nhaccungcap()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');
        $this->load->model('m_voxy_package');

        $date_end = $this->input->post('ngay_giao_hang');
        if ($date_end == false) {
            $date_end = date("Y-m-d");
        }

        $date = $this->input->post('ngay_dat_hang');
        if ($date == false) {
            $date = date("Y-m-d");
        }
        $nhacc = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($nhacc);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }
        $kho = "all";
        $sorting = "sl_xuat";//theo so tien cua nha cung cap di

        $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ ngày: <b>' . $date . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến ngày: <b>' . $date_end . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Nhà cung cấp:  ' . $vendor_name . '</span>
                        </div>
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO TỔNG HỢP HÀNG TRẢ VỀ THEO NHÀ CUNG CẤP</h4>';

        $all_tour = $this->m_voxy_package_xuathang->baocao_nhaphang_nhacungcap_hangve($date, $date_end, $nhacc);
        if ($all_tour == "" || $all_tour == null) {
            echo "KHông có dữ liệu, bitte chọn nhà cung cấp khác ";
            die;
        }

//--------------------------------------------------------------------------------------------------------------------------------------
        $all_tour2 = array();
        $tongtien = 0;
        foreach ($all_tour as $key => $item) {
            //var_dump(get_object_vars(json_decode($item['product_variants'])));die;
            $thanhtien = 0;
            foreach (get_object_vars(json_decode($item['product_variants'])) as $row) {
                $row = get_object_vars($row);
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

                if (!isset($giavon)) {
                    $giavon = 0;
                }
                $thanhtien += (double)$giavon * (double)$row['sl_nhap'];
                $item['total_price'] = $thanhtien;
            }

            $tongtien += $thanhtien;
            $all_tour2[] = $item;
        }
        //var_dump($thanhtien);die;

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same tour
        foreach ($all_tour2 as $key => $item) {
            foreach ($all_tour2 as $key2 => $item2) {
                if ($key2 > $key) {
                    if ((int)$item['vendor'] == (int)$item2['vendor']) {
                        if (isset($item['total_price']) && isset($item2['total_price'])) {
                            $item['total_price'] = (double)$item['total_price'] + (double)$item2['total_price'];
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
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                if (isset($row['title'])) {
                    $band[$key] = $row['title'];
                    $auflage[$key] = $row['sku'];
                }
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                $wek[$key] = $row['total_price'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }
        $all_products['export2'] = $export2;// sap xep lai san pham
//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
//        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Sau</span>
//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Trưóc</span>
        $html_content .= "
<div class='products' style='font-family: Times New Roman; font-size: 14px'>
    <div class='pro_th'>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 50%;float: left;text-align: center;text-align: left !important;'>Nhà cung cấp</span>
        <span style='font-family: DejaVu Sans;width: 25%;float: left;text-align: center'>Giá Trị Đơn Hàng</span>
        <span style='font-family: DejaVu Sans;width: 20%;float: left;text-align: center'>Ghi chú</span>
    </div>
    
    <div class='pro_body'> ";
        $id = 0;
        foreach ($all_products['export2'] as $row) {
            $row['total_price'] = number_format($row['total_price'], 2);

            $vendor = $this->m_voxy_nha_cung_cap->get_title($row['vendor']);
            if ($kho == 'all') {// in tat ca k phan biet
                $id++;
                $html_content .= "
                <div class='infomation' style='width:100%;clear: left;'>
                    <div style='width: 5%;height: auto;float: left;text-align: left !important;'>" . $id . "</div>
                    <div style='width: 50%;font-family:DejaVu Sans;height: auto;float: left;text-align: left !important;'>" . $vendor . "</div>
                    <div style='width: 25%;font-family:DejaVu Sans;height: auto;float: left; text-align: left'>" . $row['total_price'] . "</div>
                    <div style='width: 20%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['note'] . "</div>
                </div>
                <br>
                        ";
            }

        }
        $tongtien = number_format($tongtien, 2);

        $html_content .= "
                    <br>
                    <br>
                    <p style='margin-left: 400px;float:left;font-family: DejaVu Sans'>Tổng tiền: <b>$tongtien</b> €</p>";
        $html_content .= "
                                </div>
</div>
                                ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($date . "-" . $date_end . ".pdf", array("Attachment" => 0));
    }

    //hang hong bao cao
    public function baocao_hanghong_sanpham()
    {
        $this->load->model('m_voxy_transfer');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');

        $list_id = $this->input->post('list_id');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');
        if ($ngay_giao_hang == false) {
            $ngay_giao_hang = date("Y-m-d");
        }

        $ngay_dat_hang = $this->input->post('ngay_dat_hang');
        if ($ngay_dat_hang == false) {
            $ngay_dat_hang = date("Y-m-d");
        }

        $vendor = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($vendor);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }

//        if ($list_id && $list_id != false) {
//            $data = $this->m_voxy_transfer->get_infor_theo_ngay_hanghong(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang, $vendor);
//        } else {
//            //set list id  = ""
//        }

        $data = $this->m_voxy_package_orders->get_infor_theo_ngay_hanghong($ngay_giao_hang, $ngay_dat_hang);

        if ($data == "" || $data == null) {
            echo "KHông có dữ liệu, bitte chọn nhà cung cấp khác ";
            die;
        }

        $export2 = array();
        foreach ($data as $item) {
            $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
            $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
            if ($check_variant1 == true) {
                //GET SKU,get don vi ,gia ban, thanh tien.
                $data_get = $this->m_voxy_package->get_all_infor($idnew);
                foreach ($data_get as $item2) {
                    $item['sku'] = $item2['sku2'];
                    $item['title'] = $item2['title'];
                    $item['variant_title'] = $item2['option2'];
                    $item['product_id'] = $item2['id_shopify'];
                    $item['cat_id'] = $item2['cat_id'];
                    $item['location'] = $item2['location'];
                }
                if ($idnew != false) {
                    $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                } else {
                    $giavon = 0;
                }

                $item['quantity'] = $item['sl_nhap'];
            }
            if ($check_variant2 == true) {
                $data_get = $this->m_voxy_package->get_all_infor($idnew);
                foreach ($data_get as $item2) {
                    $item['sku'] = $item2['sku2'];
                    $item['title'] = $item2['title'];
                    $item['variant_title'] = $item2['option2'];
                    $item['product_id'] = $item2['id_shopify'];
                    $item['cat_id'] = $item2['cat_id'];
                    $item['location'] = $item2['location'];
                }
                $item['quantity'] = $item['sl_nhap'];
                if ($idnew != false) {
                    $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                } else {
                    $giavon = 0;
                }
            }
            $export2[] = $item;
        }

//print to excel below------------------------------------------------------------------------------------
        require_once APPPATH . "/third_party/PHPExcel.php";

        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
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
        $styleArray8 = array(
            'font' => array(
                'size' => 15,
                'name' => 'Time New Roman',
            ),
            'alignment' => array(
                'horizontal' => 'right'
            )
        );

        $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:P3')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('B1', "Báo cáo hàng thêm");

        $new_ngay_dat_hang = date("d-m-Y", strtotime($ngay_dat_hang));
        $new_ngay_giao_hang = date("d-m-Y", strtotime($ngay_giao_hang));
        $excel->getActiveSheet()->setCellValue('A2', "Từ ngày:" . $new_ngay_dat_hang);
        $excel->getActiveSheet()->setCellValue('B2', "Đến ngày:" . $new_ngay_giao_hang);

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'SKU');
        $excel->getActiveSheet()->setCellValue('C3', 'Tên sản phẩm');
        $excel->getActiveSheet()->setCellValue('D3', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('E3', 'Số lượng');
        $excel->getActiveSheet()->setCellValue('F3', 'Giá bán €');
        $excel->getActiveSheet()->setCellValue('G3', 'Thành tiền €');
        $excel->getActiveSheet()->setCellValue('H3', 'Ghi chú');
        $excel->getActiveSheet()->setCellValue('I3', 'Lái xe');
        $excel->getActiveSheet()->setCellValue('J3', 'Đơn hàng');
        $excel->getActiveSheet()->setCellValue('K3', 'Ngày');


        $id = 0;
        $tongtien = 0;
        $numRow = 4;
        foreach ($export2 as $row) {//san pham
            $id++;
            if (!isset($row['variant_title'])) {
                $row['variant_title'] = "#";
            }

            if (!isset($row['note'])) {
                $row['note'] = "#";
            }
            $shipped_at = date("d-m-Y", strtotime($row['shipped_at']));

            $tongtien += $row['thanhtien'];

            $price_sell = round(($row['thanhtien'] / $row['sl_nhap']),2);

            $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['variant_title']);
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_nhap']);
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $price_sell);
            $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['thanhtien']);
            $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['note']);
            $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['shipper_name']);
            $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['order_number']);
            $excel->getActiveSheet()->setCellValue('K' . $numRow, $shipped_at);
            $numRow++;
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
        }

        $tongtien = number_format($tongtien, 2);

        $numRow = $numRow + 1;
        $excel->getActiveSheet()->setCellValue('G' . $numRow, $tongtien);
        $excel->getActiveSheet()->getStyle('G' . $numRow)->applyFromArray($styleArray8);

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_hanghong.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function baocao_hanghong_nhaccungcap()
    {
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');
        $this->load->model('m_voxy_package');

        $date_end = $this->input->post('ngay_giao_hang');
        if ($date_end == false) {
            $date_end = "";
        }

        $date = $this->input->post('ngay_dat_hang');
        if ($date == false) {
            $date = "";
        }
        $nhacc = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($nhacc);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }
        $kho = "all";
        $sorting = "sl_xuat";//theo so tien cua nha cung cap di

        $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ ngày: <b>' . $date . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến ngày: <b>' . $date_end . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Nhà cung cấp: ' . $vendor_name . '</span>
                        </div>
                        
                        </head><br>';
        $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO TỔNG HỢP HÀNG HỎNG THEO NHÀ CUNG CẤP</h4>';

        $all_tour = $this->m_voxy_package_xuathang->baocao_nhaphang_nhacungcap_hanghong($date, $date_end, $nhacc);

        if ($all_tour == "" || $all_tour == null) {
            echo "KHông có dữ liệu, bitte chọn nhà cung cấp khác ";
            die;
        }
//--------------------------------------------------------------------------------------------------------------------------------------
        $all_tour2 = array();
        $tongtien = 0;
        foreach ($all_tour as $key => $item) {
            //var_dump(get_object_vars(json_decode($item['product_variants'])));die;
            $thanhtien = 0;
            foreach (get_object_vars(json_decode($item['product_variants'])) as $row) {
                $row = get_object_vars($row);
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

                if (!isset($giavon)) {
                    $giavon = 0;
                }
                $thanhtien += (double)$giavon * (double)$row['sl_nhap'];
                $item['total_price'] = $thanhtien;
            }

            $tongtien += $thanhtien;
            $all_tour2[] = $item;
        }
        //var_dump($thanhtien);die;

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same tour
        foreach ($all_tour2 as $key => $item) {
            foreach ($all_tour2 as $key2 => $item2) {
                if ($key2 > $key) {
                    if ((int)$item['vendor'] == (int)$item2['vendor']) {
                        if (isset($item['total_price']) && isset($item2['total_price'])) {
                            $item['total_price'] = (double)$item['total_price'] + (double)$item2['total_price'];
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
        } elseif ($sorting == "category") {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                if (isset($row['title'])) {
                    $band[$key] = $row['title'];
                    $auflage[$key] = $row['sku'];
                }
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        } else {//sl_xuat
            $wek = array();
            foreach ($export2 as $key => $row) {
                $wek[$key] = $row['total_price'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);
        }
        $all_products['export2'] = $export2;// sap xep lai san pham
//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
//        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Sau</span>
//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Trưóc</span>
        $html_content .= "
<div class='products' style='font-family: Times New Roman; font-size: 14px'>
    <div class='pro_th'>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 50%;float: left;text-align: center;text-align: left !important;'>Nhà cung cấp</span>
        <span style='font-family: DejaVu Sans;width: 25%;float: left;text-align: center'>Giá Trị Đơn Hàng</span>
        <span style='font-family: DejaVu Sans;width: 20%;float: left;text-align: center'>Ghi chú</span>
    </div>
    
    <div class='pro_body'> ";
        $id = 0;
        foreach ($all_products['export2'] as $row) {
            $row['total_price'] = number_format($row['total_price'], 2);

            $vendor = $this->m_voxy_nha_cung_cap->get_title($row['vendor']);
            if ($kho == 'all') {// in tat ca k phan biet
                $id++;
                $html_content .= "
                <div class='infomation' style='width:100%;clear: left;'>
                    <div style='width: 5%;height: auto;float: left;text-align: left !important;'>" . $id . "</div>
                    <div style='width: 50%;font-family:DejaVu Sans;height: auto;float: left;text-align: left !important;'>" . $vendor . "</div>
                    <div style='width: 25%;font-family:DejaVu Sans;height: auto;float: left; text-align: left'>" . $row['total_price'] . "</div>
                    <div style='width: 20%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['note'] . "</div>
                </div>
                <br>
                        ";
            }

        }
        $tongtien = number_format($tongtien, 2);

        $html_content .= "
                    <br>
                    <br>
                    <p style='margin-left: 400px;float:left;font-family: DejaVu Sans'>Tổng tiền: <b>$tongtien</b> €</p>";
        $html_content .= "
                                </div>
</div>
                                ";
        //var_dump($html_content);die;
        $this->pdf->loadHtml($html_content);
        $this->pdf->render();
        $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
        $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        $this->pdf->stream($date . "-" . $date_end . ".pdf", array("Attachment" => 0));
    }

    public function baocao_hangthem_sanpham()
    {
        $this->load->model('m_voxy_transfer');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');

        $list_id = $this->input->post('list_id');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');
        if ($ngay_giao_hang == false) {
            $ngay_giao_hang = date("Y-m-d");
        }

        $ngay_dat_hang = $this->input->post('ngay_dat_hang');
        if ($ngay_dat_hang == false) {
            $ngay_dat_hang = date("Y-m-d");
        }

        $vendor = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($vendor);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }

//        if ($list_id && $list_id != false) {
//            $data = $this->m_voxy_transfer->get_infor_theo_ngay_hanghong(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang, $vendor);
//        } else {
//            //set list id  = ""
//        }

        $data = $this->m_voxy_package_orders->get_infor_theo_ngay_hangthem($ngay_giao_hang, $ngay_dat_hang);

        if ($data == "" || $data == null) {
            echo "KHông có dữ liệu, bitte chọn nhà cung cấp khác ";
            die;
        }

        $export2 = array();
        foreach ($data as $item) {
            $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
            $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
            if ($check_variant1 == true) {
                //GET SKU,get don vi ,gia ban, thanh tien.
                $data_get = $this->m_voxy_package->get_all_infor($idnew);
                foreach ($data_get as $item2) {
                    $item['sku'] = $item2['sku2'];
                    $item['title'] = $item2['title'];
                    $item['variant_title'] = $item2['option2'];
                    $item['product_id'] = $item2['id_shopify'];
                    $item['cat_id'] = $item2['cat_id'];
                    $item['location'] = $item2['location'];
                }
                if ($idnew != false) {
                    $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                } else {
                    $giavon = 0;
                }

                $item['quantity'] = $item['sl_nhap'];
            }
            if ($check_variant2 == true) {
                $data_get = $this->m_voxy_package->get_all_infor($idnew);
                foreach ($data_get as $item2) {
                    $item['sku'] = $item2['sku2'];
                    $item['title'] = $item2['title'];
                    $item['variant_title'] = $item2['option2'];
                    $item['product_id'] = $item2['id_shopify'];
                    $item['cat_id'] = $item2['cat_id'];
                    $item['location'] = $item2['location'];
                }
                $item['quantity'] = $item['sl_nhap'];
                if ($idnew != false) {
                    $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                } else {
                    $giavon = 0;
                }
            }
            $export2[] = $item;
        }

//print to excel below------------------------------------------------------------------------------------
        require_once APPPATH . "/third_party/PHPExcel.php";

        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
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
        $styleArray8 = array(
            'font' => array(
                'size' => 15,
                'name' => 'Time New Roman',
            ),
            'alignment' => array(
                'horizontal' => 'right'
            )
        );

        $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:P3')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('B1', "Báo cáo hàng trả về");
        $new_ngay_dat_hang = date("d-m-Y", strtotime($ngay_dat_hang));
        $new_ngay_giao_hang = date("d-m-Y", strtotime($ngay_giao_hang));
        $excel->getActiveSheet()->setCellValue('A2', "Từ ngày:" . $new_ngay_dat_hang);
        $excel->getActiveSheet()->setCellValue('B2', "Đến ngày:" . $new_ngay_giao_hang);

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'SKU');
        $excel->getActiveSheet()->setCellValue('C3', 'Title');
        $excel->getActiveSheet()->setCellValue('D3', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('E3', 'Số lượng');
        $excel->getActiveSheet()->setCellValue('F3', 'Gía bán');
        $excel->getActiveSheet()->setCellValue('G3', 'Thành tiền');
        $excel->getActiveSheet()->setCellValue('H3', 'Lý Do');
        $excel->getActiveSheet()->setCellValue('I3', 'Lái xe');
        $excel->getActiveSheet()->setCellValue('J3', 'Đơn hàng');
        $excel->getActiveSheet()->setCellValue('K3', 'Ngày');


        $id = 0;
        $tongtien = 0;
        $numRow = 4;
        foreach ($export2 as $row) {//san pham
            $id++;
            if (!isset($row['variant_title'])) {
                $row['variant_title'] = "#";
            }

            if (!isset($row['note'])) {
                $row['note'] = "#";
            }

            $shipped_at = date("d-m-Y", strtotime($row['shipped_at']));

            $tongtien += $row['thanhtien'];
            $price_sell = round(($row['thanhtien'] / $row['sl_nhap']),2);

            $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['variant_title']);
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['sl_nhap']);
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $price_sell);
            $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['thanhtien']);
            $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['note']);
            $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['shipper_name']);
            $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['order_number']);
            $excel->getActiveSheet()->setCellValue('K' . $numRow, $shipped_at);
            $numRow++;
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
        }

        $tongtien = number_format($tongtien, 2);

        $numRow = $numRow + 1;
        $excel->getActiveSheet()->setCellValue('G' . $numRow, $tongtien);
        $excel->getActiveSheet()->getStyle('G' . $numRow)->applyFromArray($styleArray8);

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_hangthem.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    }

    public function baocao_hangthieu_sanpham()
    {
        $this->load->model('m_voxy_transfer');
        $this->load->model('m_voxy_package_xuathang');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_nha_cung_cap');

        require_once APPPATH . "/third_party/PHPExcel.php";

        $list_id = $this->input->post('list_id');
        $ngay_giao_hang = $this->input->post('ngay_giao_hang');
        if ($ngay_giao_hang == false) {
            $ngay_giao_hang = date("Y-m-d");
        }

        $ngay_dat_hang = $this->input->post('ngay_dat_hang');
        if ($ngay_dat_hang == false) {
            $ngay_dat_hang = date("Y-m-d");
        }

        $vendor = $this->input->post('vendor');
        $vendor_name = $this->m_voxy_nha_cung_cap->get_title($vendor);
        if ($vendor_name == false) {
            $vendor_name = "tất cả";
        }

//        if ($list_id && $list_id != false) {
//            $data = $this->m_voxy_transfer->get_infor_theo_ngay_hanghong(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang, $vendor);
//        } else {
//
//        }

        $data = $this->m_voxy_package_orders->get_infor_theo_ngay_hangthieu($ngay_giao_hang, $ngay_dat_hang);//set list id  = ""

        if ($data == "" || $data == null) {
            echo "KHông có dữ liệu, bitte chọn nhà cung cấp khác ";
            die;
        }

        $export2 = array();
        foreach ($data as $item) {
            $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
            $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
            if ($idnew) {
                if ($check_variant1 == true) {
                    //GET SKU,get don vi ,gia ban, thanh tien.
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2) {
                        $item['sku'] = $item2['sku1'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option1'];
                        $item['product_id'] = $item2['id_shopify'];
                        $item['cat_id'] = $item2['cat_id'];
                        $item['location'] = $item2['location'];
                    }
                    if ($idnew != false) {
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    } else {
                        $giavon = 0;
                    }

                    $item['quantity'] = $item['sl_nhap'];
                }
                if ($check_variant2 == true) {
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2) {
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                        $item['product_id'] = $item2['id_shopify'];
                        $item['cat_id'] = $item2['cat_id'];
                        $item['location'] = $item2['location'];
                    }
                    $item['quantity'] = $item['sl_nhap'];
                    if ($idnew != false) {
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    } else {
                        $giavon = 0;
                    }
                }
                $export2[] = $item;
            }
        }

        //in ra pdf below------------------------------------------------------------------------------------
        $kho = "all";
        $sorting = "category";//location or category
        if ($sorting == "sl_xuat") {
            $xuattheo = "Số lượng xuất";
        } elseif ($sorting == "sl_sau") {
            $xuattheo = "Số lượng trong kho";
        } elseif ($sorting == "location") {
            $xuattheo = "Vị Trí";
        } else {
            $xuattheo = "Danh mục";
        }

        if ($list_id && $list_id != false) {
            $list_id_print = json_decode($list_id)->list_id;
            $list_id_print = implode(",", $list_id_print);
        }


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
        $all_products['export2'] = $export2;// sap xep lai san pham

        //loc category
        $arr_cat_id = array();
        foreach ($export2 as $item) {
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);

            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
        }

        // step 2: sort tang dan
        ksort($arr_cat_id);
        $all_products['result_catid'] = $arr_cat_id;
//--------------------------------------------------------------------------------------------------------------------------------------

        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
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

        $style_center = array(
            'font' => array(
                'size' => 12,
                'name' => 'Time New Roman',
         'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            ));

        $styleArray8 = array(
            'font' => array(
                'size' => 15,
                'name' => 'Time New Roman',
            ),
            'alignment' => array(
                'horizontal' => 'right'
            )
        );

        $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:P3')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A4:P4')->getFont()->setBold(true);
        //$excel->getActiveSheet()->getStyle('A8:P8')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('C1', "Báo cáo hàng thiếu");
        $excel->getActiveSheet()->setCellValue('C2', "Từ ngày:" .  date("d-m-Y", strtotime($ngay_dat_hang)));
        $excel->getActiveSheet()->setCellValue('C3', "Đến ngày:" . date("d-m-Y", strtotime($ngay_giao_hang)));

        $excel->getActiveSheet()->setCellValue('A4', 'STT');
        $excel->getActiveSheet()->setCellValue('B4', 'SKU');
        $excel->getActiveSheet()->setCellValue('C4', 'Tên sản phẩm');
        $excel->getActiveSheet()->setCellValue('D4', 'Đơn vị');
        $excel->getActiveSheet()->setCellValue('E4', 'Số lượng');
        $excel->getActiveSheet()->setCellValue('F4', 'Giá bán €');
        $excel->getActiveSheet()->setCellValue('G4', 'Thành tiền €');
        $excel->getActiveSheet()->setCellValue('H4', 'Ghi chú');
        $excel->getActiveSheet()->setCellValue('I3', 'Lái xe');
        $excel->getActiveSheet()->setCellValue('J3', 'Đơn hàng');
        $excel->getActiveSheet()->setCellValue('K3', 'Ngày');


        $id = 0;
        $numRow = 4;
        $tongtien = 0;

        foreach ($all_products['export2'] as $row) {//san pham
            $numRow++;
            //xu ly do dai cua sku
            if (strlen($row['sku']) > 5) {
                $row['sku'] = substr($row['sku'], 0, 5);
            }

            if (strlen($row['sku']) == 0) {
                $row['sku'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }

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
            $thanhtien = (double)$giavon * (int)$row['sl_nhap'];

            $tongtien += $thanhtien;
            $shipped_at = date("d-m-Y", strtotime($row['shipped_at']));
            $price_sell = round(($row['thanhtien'] / $row['sl_nhap']),2);
            $id++;
            $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['variant_title']);
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['quantity']);
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $price_sell);
            $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['thanhtien']);
            $excel->getActiveSheet()->setCellValue('H' . $numRow, $row['note']);
            $excel->getActiveSheet()->setCellValue('I' . $numRow, $row['shipper_name']);
            $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['order_number']);
            $excel->getActiveSheet()->setCellValue('K' . $numRow, $shipped_at);

            $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('B')->applyFromArray($style_center);
            $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray2);
        }

        //end check in ra trong kho nao

        $tongtien = number_format($tongtien, 2);

        $numRow = $numRow + 1;
        $excel->getActiveSheet()->setCellValue('G' . $numRow, $tongtien);
        $excel->getActiveSheet()->getStyle('G' . $numRow)->applyFromArray($styleArray8);

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_hangthieu.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');

}


//tab bao cao doanh số
public
function baocao_xuathang_tonghop()
{
    $this->load->model('m_voxy_package_xuathang');
    $this->load->model('m_voxy_category');
    $this->load->model('m_voxy_package');
    $this->load->model('m_voxy_package_orders');
    $this->load->model('m_voxy_shippers');

    $shipper_id = $this->input->post('shipper_id');
    if ($shipper_id != false) {
        $laixe2 = array();
        foreach ($shipper_id as $item) {
            $laixe2[] = $this->m_voxy_package_orders->get_name_shipper((int)$item);
        }
        $laixe_print = implode(",", $laixe2);
    } else {
        $shipper_id = "";
        $laixe_print = "Tất cả";
    }

    $shipper_are_id = $this->input->post('shipper_are_id');

    if ($shipper_are_id == false) {
        $shipper_are_name = "all";
    } else {
        $shipper_are_name = implode(",", $this->m_voxy_package_xuathang->get_namne_ship_area($shipper_are_id));
    }

    $date = $this->input->post('date_for_orders');
    $date_end = $this->input->post('date_for_orders_end');
//                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
//                            <span style="font-family: DejaVu Sans">Lái xe: '.$laixe_print.'</span>
//                        </div>
    $html_content = '
                        <div style="width: 100%;">
                            <div class="date-xuat" style="float: left; width: 50%;font-size: 13px;">
                                <span style="font-family: DejaVu Sans">Từ ngày: <b>' . $date . '</b></span>
                            </div>
                        
                            <div class="date-xuat" style="float: left; width: 50%;font-size: 13px;text-align: right">
                                <span style="font-family: DejaVu Sans">Đến ngày: <b>' . $date_end . '</b></span>
                            </div>
                        </div>
                        <p>Vùng: ' . $shipper_are_name . '</p>
                        <br>';
    $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO KINH DOANH</h4>';

    $all_tour = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tour_tonghop($date, $date_end, $shipper_id, $shipper_are_id);//bang voxy_package_orders
    if ($all_tour == "") {
        echo "Không có dữ liệu đặt hàng vào ngày này, vui lòng kiểm tra lại thông tin";
        die;
    }

    //hang tra ve
    $hangtrave = $this->m_voxy_package_xuathang->xuathang_baocao_hangve_tonghop($date, $date_end, $shipper_id, $shipper_are_id);//voxy_package_orders
    //hang hong
    $hanghong = $this->m_voxy_package_xuathang->xuathang_baocao_hanghong_tonghop($date, $date_end, $shipper_id, $shipper_are_id);//voxy_package_orders
    //---------------------------------------------------
    $hangthieu = $this->m_voxy_package_xuathang->xuathang_baocao_hangthieu_tonghop($date, $date_end, $shipper_id, $shipper_are_id);//voxy_package_orders
    $hangthem = $this->m_voxy_package_xuathang->xuathang_baocao_hangthem_tonghop($date, $date_end, $shipper_id, $shipper_are_id);//voxy_package_orders
    //3 bien export,exporthanghong,exporthangve la du lieu sau cung sau khi xu ly

    if ($all_tour) {
        $array_theo_tour = array();

        foreach ($all_tour as $key => $item2) {
            $line_items = json_decode($item2['line_items']);
            $tongvon_trong1donhang = 0;
            foreach ($line_items as $item) {
                $item = get_object_vars($item);
                if (isset($item['variant_id'])) {
                    $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                    if ($idnew) {
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
                        $tongvon_trong1donhang += $giavon * $item['quantity'];
                    }
                }
            }
            $item2['tongtien_von'] = $tongvon_trong1donhang;
            $array_theo_tour[] = $item2;
        }
    }

    //step 2: cong total price and total von
    $chiso_remove = array();
    $export2 = array();

    foreach ($array_theo_tour as $key => $item2) {
        foreach ($array_theo_tour as $key2 => $item3) {
            if ($key2 > $key) {
                if ($item2['shipper_id'] == $item3['shipper_id']) {//cong tong theo laixe
                    $item2['total_price'] = $item2['total_price'] + $item3['total_price'];
                    $item2['tongtien_von'] = $item2['tongtien_von'] + $item3['tongtien_von'];
                    $chiso_remove[$key2] = $key2;//index of same product and then remove it
                }
            }
        }
        $export2[] = $item2;
    }

    foreach ($export2 as $key => $item) {
        foreach ($chiso_remove as $key_reomove => $item_remove) {
            unset($export2[$item_remove]);
            unset($chiso_remove[$key_reomove]);
        }
    }

    //---------------------------------------------------------

    if ($hangtrave != null || $hangtrave != "") {
        $array_hangtrave = array();
        foreach ($hangtrave as $key => $item2) {
            $item2['tongtien_von'] = 0;
            if ($item2['hangve'] != "") {
                $tongtien_trong1laixe = 0;
                foreach (json_decode($item2['hangve']) as $row) {
                    $row = get_object_vars($row);
                    if ($row['variant_id'] != null || $row['variant_id'] != "" && isset($row['variant_id'])) {
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        if ($idnew) {
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

                            $item2['tongtien_von'] += (double)$giavon * (int)$row['sl_nhap'];
                            $tongtien_trong1laixe += $row['thanhtien'];
                        }
                    }
                }
                $item2['thanhtien'] = $tongtien_trong1laixe;
                $array_hangtrave[] = $item2;
            }
        }

        //step 2: cong total price and total von
        $chiso_remove_hangtrave = array();
        $export_hangtrave = array();

        foreach ($array_hangtrave as $key => $item2) {
            foreach ($array_hangtrave as $key2 => $item3) {
                if ($key2 > $key) {
                    if ($item2['shipper_id'] == $item3['shipper_id']) {//cong tong theo laixe
                        $item2['tongtien_von'] += $item2['tongtien_von'] + $item3['tongtien_von'];
                        $item2['thanhtien'] = $item2['thanhtien'] + $item3['thanhtien'];
                        $chiso_remove_hangtrave[$key2] = $key2;//index of same product and then remove it
                    }
                }
            }
            $export_hangtrave[] = $item2;
        }

        foreach ($export_hangtrave as $key => $item) {
            foreach ($chiso_remove_hangtrave as $key_reomove => $item_remove) {
                unset($export_hangtrave[$item_remove]);
                unset($chiso_remove_hangtrave[$key_reomove]);
            }
        }
//            $tongtienhangtrave = 0;
//            foreach ($export_hangtrave as $key => $item) {
//                $tongtienhangtrave += $item['tongtien_von'];
//            }
    }
    //---------------------------------------------------------

    //---------------------------------------------------------


    //---------------------------------------------------------
    if ($hanghong != null || $hanghong != "") {
        $array_hanghong = array();
        foreach ($hanghong as $key => $item2) {
            $item2['tongtien_von'] = 0;
            if ($item2['hanghong'] != "") {
                $tongtien_trong1laixe = 0;
                foreach (json_decode($item2['hanghong']) as $row) {
                    $row = get_object_vars($row);
                    if ($row['variant_id'] != null || $row['variant_id'] != "" && isset($row['variant_id'])) {
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        if ($idnew) {
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

                            $item2['tongtien_von'] += (double)$giavon * (int)$row['sl_nhap'];
                            $tongtien_trong1laixe += $row['thanhtien'];
                        }
                    }
                }
                $item2['thanhtien'] = $tongtien_trong1laixe;
                $array_hanghong[] = $item2;
            }
        }

        //step 2: cong total price and total von
        $chiso_remove_hanghong = array();
        $export_hanghong = array();

        foreach ($array_hanghong as $key => $item2) {
            foreach ($array_hanghong as $key2 => $item3) {
                if ($key2 > $key) {
                    if ($item2['shipper_id'] == $item3['shipper_id']) {//cong tong theo laixe
                        $item2['tongtien_von'] += $item2['tongtien_von'] + $item3['tongtien_von'];
                        $item2['thanhtien'] = $item2['thanhtien'] + $item3['thanhtien'];
                        $chiso_remove_hanghong[$key2] = $key2;//index of same product and then remove it
                    }
                }
            }
            $export_hanghong[] = $item2;
        }

        foreach ($export_hanghong as $key => $item) {
            foreach ($chiso_remove_hanghong as $key_reomove => $item_remove) {
                unset($export_hanghong[$item_remove]);
                unset($chiso_remove_hanghong[$key_reomove]);
            }
        }
//            $tongtienhanghong = 0;
//            foreach ($export_hanghong as $key => $item) {
//                $tongtienhanghong += $item['tongtien_von'];
//            }
    }
    //---------------------------------------------------------
    if ($hangthieu != null || $hangthieu != "") {
        $array_hangthieu = array();

        foreach ($hangthieu as $key => $item2) {
            $item2['tongtien_von'] = 0;
            if ($item2['hangthieu'] != "") {
                $tongtien_trong1laixe = 0;
                foreach (json_decode($item2['hangthieu']) as $row) {
                    $row = get_object_vars($row);
                    if ($row['variant_id'] != null || $row['variant_id'] != "" && isset($row['variant_id'])) {
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        if ($idnew) {
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
                        }

                        $item2['tongtien_von'] += (double)$giavon * (int)$row['sl_nhap'];
                        $tongtien_trong1laixe += $row['thanhtien'];
                    }
                }
                $item2['thanhtien'] = $tongtien_trong1laixe;
                $array_hangthieu[] = $item2;
            }
        }

        //step 2: cong total price and total von
        $chiso_remove_hangthieu = array();
        $export_hangthieu = array();

        foreach ($array_hangthieu as $key => $item2) {
            foreach ($array_hangthieu as $key2 => $item3) {
                if ($key2 > $key) {
                    if ($item2['shipper_id'] == $item3['shipper_id']) {//cong tong theo laixe
                        $item2['tongtien_von'] = $item2['tongtien_von'] + $item3['tongtien_von'];
                        $item2['thanhtien'] = $item2['thanhtien'] + $item3['thanhtien'];
                        $chiso_remove_hangthieu[$key2] = $key2;//index of same product and then remove it
                    }
                }
            }
            $export_hangthieu[] = $item2;
        }

        foreach ($export_hangthieu as $key => $item) {
            foreach ($chiso_remove_hangthieu as $key_reomove => $item_remove) {
                unset($export_hangthieu[$item_remove]);
                unset($chiso_remove_hangthieu[$key_reomove]);
            }
        }

    }
    //---------------------------------------------------------

    //---------------------------------------------------------
    if ($hangthem != null || $hangthem != "") {
        $array_hangthem = array();
        foreach ($hangthem as $key => $item2) {
            $item2['tongtien_von'] = 0;
            if ($item2['hangthem'] != "") {
                $tongtien_trong1laixe = 0;
                foreach (json_decode($item2['hangthem']) as $row) {
                    $row = get_object_vars($row);
                    if ($row['variant_id'] != null || $row['variant_id'] != "" && isset($row['variant_id'])) {
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        if ($idnew) {
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
                        }

                        $item2['tongtien_von'] += (double)$giavon * (int)$row['sl_nhap'];
                        $tongtien_trong1laixe += $row['thanhtien'];

                    }
                }
                $item2['thanhtien'] = $tongtien_trong1laixe;
                $array_hangthem[] = $item2;
            }
        }

        //step 2: cong total price and total von
        $chiso_remove_hangthem = array();
        $export_hangthem = array();

        foreach ($array_hangthem as $key => $item2) {
            foreach ($array_hangthem as $key2 => $item3) {
                if ($key2 > $key) {
                    if ($item2['shipper_id'] == $item3['shipper_id']) {//cong tong theo laixe
                        $item2['tongtien_von'] = $item2['tongtien_von'] + $item3['tongtien_von'];
                        $item2['thanhtien'] = $item2['thanhtien'] + $item3['thanhtien'];
                        $chiso_remove_hangthem[$key2] = $key2;//index of same product and then remove it
                    }
                }
            }
            $export_hangthem[] = $item2;
        }

        foreach ($export_hangthem as $key => $item) {
            foreach ($chiso_remove_hangthem as $key_reomove => $item_remove) {
                unset($export_hangthem[$item_remove]);
                unset($chiso_remove_hangthem[$key_reomove]);
            }
        }
    }
    //---------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------

    // Hole eine Liste von Spalten
    // http://php.net/manual/de/function.array-multisort.php

//        $wek = array();
//        foreach ($array_theo_tour as $key => $row) {
//            $wek[$key] = $row['doanhthu'];
//        }
    // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
    //array_multisort($wek, SORT_DESC, $array_theo_tour);

    // $all_products['export2'] = $array_theo_tour;// sap xep lai san pham
//--------------------------------------------------------------------------------------------------------------------------------------
    $html_content .= '<table width="100%" style="page-break-inside: auto; border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
<thead style="border: 1px solid;">
    <tr>
                <th style="border: 1px solid;">Nr</th>
                <th style="border: 1px solid;">Tour</th>
                <th style="width:50%;border: 1px solid;">Doanh thu</th>
                <th style="border: 1px solid;">H.về</th>
                <th style="border: 1px solid;">H.hỏng</th>
                <th style="border: 1px solid;">H.thiếu</th>
                <th style="border: 1px solid;">H.thêm</th>
                <th style="border: 1px solid;">D/S thuần</th>
                <th style="border: 1px solid;">Vốn</th>
                <th style="border: 1px solid;">Lợi nhuận gộp</th>
                <th style="border: 1px solid;">Lợi nhuận %</th>
            </tr>
</thead>       
';
    $html_content .= '<tbody style="border: 1px solid;">';

    if ($export2 != null || $export2 != "") {

        $tongdoanhthu = 0;
        $tongtiendoanhthuthuan = 0;
        $tongtienvon = 0;
        $tongtienloinhuangop = 0;

        $id = 0;
        $tongtienhanghong = 0;
        $tongtienhangtrave = 0;
        $tongtienhangthieu = 0;
        $tongtienhangthem = 0;

        $tongtienhanghong_von = 0;
        $tongtienhangtrave_von = 0;
        $tongtienhangthieu_von = 0;
        $tongtienhangthem_von = 0;

        foreach ($export2 as $row) {

            $id++;

            if (isset($export_hangtrave)) {
                foreach ($export_hangtrave as $trave) {
                    if ($trave['shipper_id'] == $row['shipper_id']) {
                        $tongtienhangtrave_von += $trave['tongtien_von'];
                        $tongtienhangtrave += $trave['thanhtien'];
                        $row['thanhtien_trave'] = $trave['thanhtien'];
                    }

                }
            }

            if (isset($export_hanghong)) {
                foreach ($export_hanghong as $hong) {
                    if ($hong['shipper_id'] == $row['shipper_id']) {
                        $tongtienhanghong_von += $hong['tongtien_von'];
                        $tongtienhanghong += $hong['thanhtien'];
                        $row['thanhtien_hong'] = $hong['thanhtien'];
                    }

                }
            }

            if (isset($export_hangthieu)) {
                foreach ($export_hangthieu as $thieu) {
                    if ($thieu['shipper_id'] == $row['shipper_id']) {
                        $tongtienhangthieu_von += $thieu['tongtien_von'];
                        $tongtienhangthieu += $thieu['thanhtien'];
                        $row['thanhtien_thieu'] = $thieu['thanhtien'];
                    }

                }
            }

            if (isset($export_hangthem)) {
                foreach ($export_hangthem as $them) {
                    if ($them['shipper_id'] == $row['shipper_id']) {
                        $tongtienhangthem_von += $them['tongtien_von'];
                        $tongtienhangthem += $them['thanhtien'];

                        $row['thanhtien_them'] = $them['thanhtien'];
                    }

                }
            }

            //tong
            $tongdoanhthu += $row['total_price'];

            $row['tongtien_von'] = $row['tongtien_von'] + $tongtienhangthem_von - $tongtienhangtrave_von - $tongtienhanghong_von - $tongtienhanghong_von;

            $tongtienvon += $row['tongtien_von'];

            $tongtiendoanhthuthuan = $tongdoanhthu - $tongtienhangtrave - $tongtienhanghong - $tongtienhangthieu + $tongtienhangthem;

            $tongtienloinhuangop = $tongtiendoanhthuthuan - $tongtienvon;

            if ($tongtiendoanhthuthuan == 0) {
                $tongtien_loinhuan_phantram = 0;
            } else {
                $tongtien_loinhuan_phantram = number_format(($tongtienloinhuangop / $tongtiendoanhthuthuan) * 100, 1);
            }
            //tong

            // $doanhthuthuan_theotour = $row['total_price'] - $tongtienhangtrave - $tongtienhanghong - $tongtienhangthieu + $tongtienhangthem;
            $doanhthuthuan_theotour = $row['total_price'];
            $loinhuangop = $doanhthuthuan_theotour - $row['tongtien_von'];

            if ($doanhthuthuan_theotour == 0) {
                $loinhuan_phantram = 0;
            } else {
                $loinhuan_phantram = number_format(($loinhuangop / $doanhthuthuan_theotour) * 100, 1);
            }

            if (!isset($row['thanhtien_trave'])) {
                $row['thanhtien_trave'] = 0;
            }

            if (!isset($row['thanhtien_hong'])) {
                $row['thanhtien_hong'] = 0;
            }
            if (!isset($row['thanhtien_thieu'])) {
                $row['thanhtien_thieu'] = 0;
            }
            if (!isset($row['thanhtien_them'])) {
                $row['thanhtien_them'] = 0;
            }

            $html_content .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $id . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">' . $row['shipper_name'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;">' . number_format($row['total_price'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($row['thanhtien_trave'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($row['thanhtien_hong'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($row['thanhtien_thieu'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($row['thanhtien_them'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($doanhthuthuan_theotour, 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($row['tongtien_von'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($loinhuangop, 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $loinhuan_phantram . '</td>
			</tr>
			';
        }

        $end_doanhthu = number_format($tongdoanhthu, 2);
        $end_trave = number_format($tongtienhangtrave, 2);
        $end_hong = number_format($tongtienhanghong, 2);
        $end_thieu = number_format($tongtienhangthieu, 2);
        $end_them = number_format($tongtienhangthem, 2);
        $end_doanhthu_thuan = number_format($tongtiendoanhthuthuan, 2);
        $end_tongtienvon = number_format($tongtienvon, 2);
        $end_tongtienloinhuangop = number_format($tongtienloinhuangop, 2);
        $end_tongtien_loinhuan_phantram = number_format($tongtien_loinhuan_phantram, 2);
        $html_content .= "
                    <tr>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>Tổng</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_doanhthu</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_trave</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_hong</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_thieu</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_them</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_doanhthu_thuan</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_tongtienvon</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_tongtienloinhuangop</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$end_tongtien_loinhuan_phantram</b></td>
                    </tr>
                    <br>
        ";
    } else {
        $html_content .= "Không có dữ liệu hiển thị";
    }

    $html_content .= '</tbody>';
    $html_content .= '</table><br>';
    //var_dump($html_content);die;
    $this->pdf->loadHtml($html_content);
    $this->pdf->render();
    $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
    $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
    $this->pdf->stream("BC-kinhdoanh-" . $date . ".pdf", array("Attachment" => 0));
}

public function baocao_xuathang_tonghop_theodonhang()
{
    $this->load->model('m_voxy_package_xuathang');
    $this->load->model('m_voxy_category');
    $this->load->model('m_voxy_package');
    $this->load->model('m_voxy_package_orders');
    $this->load->model('m_voxy_shippers');

    $shipper_id = $this->input->post('shipper_id');
    if ($shipper_id == false) {
        $shipper_id = $this->m_voxy_package_orders->get_all_shipper();
    }


    $shipper_are_id = $this->input->post('shipper_are_id');
    if ($shipper_are_id == false) {
        $shipper_are_name = "all";
    } else {
        $shipper_are_name = implode(",", $this->m_voxy_package_xuathang->get_namne_ship_area($shipper_are_id));
    }

    //var_dump($shipper_id);die;
    $date = $this->input->post('date_for_orders');
    $date_end = $this->input->post('date_for_orders_end');

    if ($date == "") {
        echo "Vui lòng kiểm tra thông tin ngày tháng ,lái xe";
        die;
    }
    $html_content = '
                        <div style="width: 100%;">
                            <div class="date-xuat" style="float: left; width: 50%;font-size: 13px;">
                                <span style="font-family: DejaVu Sans">Từ ngày: <b>' . $date . '</b></span>
                            </div>
                        
                            <div class="date-xuat" style="float: left; width: 50%;font-size: 13px;text-align: right">
                                <span style="font-family: DejaVu Sans">Đến ngày: <b>' . $date_end . '</b></span>
                            </div>
                        </div>
                        <div>
                        <p>Vùng: ' . $shipper_are_name . '</p>
</div>
                        <br>';
    $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO KINH DOANH</h4>';

    $html_content .= '<table width="100%" style="border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
            <thead style="border: 1px solid;">
                <tr>
                            <th style="border: 1px solid;">STT</th>
                            <th style="border: 1px solid;">Tour</th>
                            <th style="border: 1px solid;">Đơn hàng</th>
                            <th style="border: 1px solid;">Hàng về</th>
                            <th style="border: 1px solid;">Hàng thiếu</th>
                            <th style="border: 1px solid;">Hàng hỏng</th>
                            <th style="border: 1px solid;">Hàng thêm</th>
                            <th style="border: 1px solid;">Vốn</th>
                            <th style="border: 1px solid;">Doanh Thu</th>
                            <th style="border: 1px solid;">Lợi nhuận gộp</th>
                            <th style="border: 1px solid;">Lợi nhuận %</th>
                        </tr>
            </thead>         
            ';
    $html_content .= '<tbody style="border: 1px solid;">';

    foreach ($shipper_id as $ship) {
        $one_tour = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_theotour_tonghop_new($date, $date_end, $ship, $shipper_are_id);//bang voxy_package_orders
        if ($one_tour && $one_tour != false) {
            $html_content .= $this->table_baocao_tonghop($one_tour);
        }
    }

    $html_content .= '</tbody>';
    $html_content .= '</table>';

    $this->pdf->loadHtml($html_content);

    $this->pdf->render();
    $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
    $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
    $this->pdf->stream("list_" . $date . "to" . $date_end . ".pdf", array("Attachment" => 0));

//may be thieu cai xuat hang tại kho , chưa tính cái giá đó nhỉ
}

public
function table_baocao_tonghop($export2)
{
    if ($export2 != null && $export2 != "" && $export2 != false) {
        $html_content = "";
        $id = 0;
        $tongtienvon = 0;
        $tongdoanhthu = 0;
        $tongloinhuangop = 0;
        //tong ben duoi cua moi lai xe
        $tongtien_hangve = 0;
        $tongtien_hanghong = 0;
        $tongtien_hangthieu = 0;
        $tongtien_hangthem = 0;
        $tongtien_hangve_theodon = 0;
        $tongtien_hanghong_theodon = 0;
        $tongtien_hangthieu_theodon = 0;
        $tongtien_hangthem_theodon = 0;

        foreach ($export2 as $row) {
            $id++;

            //them cac loai tra ve, thieu , hong, them
            if ($row['hangve'] != null) {
                $hangve = json_decode($row['hangve']);
                $tongtien_hangve_theodon = 0;
                $hangve_giavon = 0;
                foreach ($hangve as $item) {
                    $item = get_object_vars($item);
                    $tongtien_hangve_theodon += $item['thanhtien'];

                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                    if ($idnew)
                        $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

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

                    $hangve_giavon += $giavon * $item['sl_nhap'];
                }
                $tongtien_hangve += $tongtien_hangve_theodon;
            } else {
                $tongtien_hangve_theodon = 0;
                $hangve_giavon = 0;
            }

            if ($row['hanghong'] != null) {
                $hanghong = json_decode($row['hanghong']);
                $tongtien_hanghong_theodon = 0;
                $hanghong_giavon = 0;
                foreach ($hanghong as $item) {
                    $item = get_object_vars($item);
                    $tongtien_hanghong_theodon += $item['thanhtien'];

                    $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
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

                    $hanghong_giavon += $giavon * $item['sl_nhap'];

                }
                $tongtien_hanghong += $tongtien_hanghong_theodon;
            } else {
                $tongtien_hanghong_theodon = 0;
                $hanghong_giavon = 0;
            }

            if ($row['hangthieu'] != null) {
                $hangthieu = json_decode($row['hangthieu']);
                $tongtien_hangthieu_theodon = 0;
                $hangthieu_giavon = 0;
                foreach ($hangthieu as $item) {
                    $item = get_object_vars($item);
                    $tongtien_hangthieu_theodon += $item['thanhtien'];
                    $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
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

                    $hangthieu_giavon += $giavon * $item['sl_nhap'];
                }
                $tongtien_hangthieu += $tongtien_hangthieu_theodon;
            } else {
                $tongtien_hangthieu_theodon = 0;
                $hangthieu_giavon = 0;
            }


            if ($row['hangthem'] != null) {
                $hangthem = json_decode($row['hangthem']);
                $tongtien_hangthem_theodon = 0;
                $hangthem_giavon = 0;
                foreach ($hangthem as $item) {
                    $item = get_object_vars($item);
                    $tongtien_hangthem_theodon += $item['thanhtien'];

                    $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
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

                    $hangthem_giavon += $giavon * $item['sl_nhap'];
                }
                $tongtien_hangthem += $tongtien_hangthem_theodon;
            } else {
                $tongtien_hangthem_theodon = 0;
                $hangthem_giavon = 0;
            }
            //end them cac loai


            //$row['tongtien_von'] = $row['total_price'] - $this->m_voxy_shippers->get_order_profit($row['order_number']);// theo anh vinh ko dung

            if ($row['line_items']) {
                $giavon_theotungdonhang = 0;
                foreach (json_decode($row['line_items']) as $line) {
                    $giavon = 0;
                    $line = get_object_vars($line);
                    if (isset($line['variant_id']) || $line['variant_id'] != "") {
                        $check_variant1 = $this->m_voxy_package->check_variant1($line['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($line['variant_id']);
                        $idnew_line = $this->m_voxy_package->get_id_from_variant($line['variant_id']);

                        if ($check_variant1 == true) {

                            //gia von la gia mua
                            if ($idnew_line != false) {
                                $giavon = $this->m_voxy_package->get_gia_mua_le($idnew_line);
                            } else {
                                $giavon = 0;
                            }

                        }

                        if ($check_variant2 == true) {
                            if ($idnew_line != false) {
                                $giavon = $this->m_voxy_package->get_gia_mua_si($idnew_line);
                            } else {
                                $giavon = 0;
                            }
                        }
                    }

                    $giavon_theotungdonhang += $giavon * $line['quantity'];
                }
            }

            $row['tongtien_von'] = $giavon_theotungdonhang;

            //$row['total_price'] = $row['total_price'] - $tongtien_hangve_theodon - $tongtien_hangthieu_theodon - $tongtien_hanghong_theodon + $tongtien_hangthem_theodon;
            if (!isset($hangve_giavon)) {
                $hangve_giavon = 0;
            }
            if (!isset($hanghong_giavon)) {
                $hanghong_giavon = 0;
            }
            if (!isset($hangthieu_giavon)) {
                $hangthieu_giavon = 0;
            }
            if (!isset($hangthem_giavon)) {
                $hangthem_giavon = 0;
            }
            $row['tongtien_von'] = $row['tongtien_von'] - $hangve_giavon - $hanghong_giavon - $hangthieu_giavon + $hangthem_giavon;

            $tongdoanhthu += $row['total_price'];
            $tongtienvon += $row['tongtien_von'];

            $tongloinhuangop += ($row['total_price'] - $row['tongtien_von']);
            if ($tongdoanhthu != 0) {
                $tongloinhuan_phantram = number_format(($tongloinhuangop / $tongdoanhthu) * 100, 1);
            } else {
                $tongloinhuan_phantram = 0;
            }

            $loinhuangop = $row['total_price'] - $row['tongtien_von'];
            if ($row['total_price'] != 0) {
                $loinhuan_phantram = number_format(($loinhuangop / $row['total_price']) * 100, 1);
            } else {
                $loinhuan_phantram = 0;
            }

            if ($tongtien_hangve_theodon == 0) {
                $tongtien_hangve_theodon_print = "";
            } else {
                $tongtien_hangve_theodon_print = $tongtien_hangve_theodon;
            }

            if ($tongtien_hangthieu_theodon == 0) {
                $tongtien_hangthieu_theodon_print = "";
            } else {
                $tongtien_hangthieu_theodon_print = $tongtien_hangthieu_theodon;
            }

            if ($tongtien_hanghong_theodon == 0) {
                $tongtien_hanghong_theodon_print = "";
            } else {
                $tongtien_hanghong_theodon_print = $tongtien_hanghong_theodon;
            }

            if ($tongtien_hangthem_theodon == 0) {
                $tongtien_hangthem_theodon_print = "";
            } else {
                $tongtien_hangthem_theodon_print = $tongtien_hangthem_theodon;
            }


            $html_content .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $id . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">' . $row['shipper_name'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">' . $row['order_number'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $tongtien_hangve_theodon_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $tongtien_hangthieu_theodon_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $tongtien_hanghong_theodon_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $tongtien_hangthem_theodon_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($row['tongtien_von'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($row['total_price'], 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . number_format($loinhuangop, 2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $loinhuan_phantram . '</td>
			</tr>
			';
        }

        $html_content .= '
            <tr>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>Tổng</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"></td> 
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . number_format($tongtien_hangve, 2) . '</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . number_format($tongtien_hangthieu, 2) . '</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . number_format($tongtien_hanghong, 2) . '</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . number_format($tongtien_hangthem, 2) . '</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . number_format($tongtienvon, 2) . '</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . number_format($tongdoanhthu, 2) . '</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . number_format($tongloinhuangop, 2) . '</b></td>
               <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . $tongloinhuan_phantram . '</b></td>
            </tr>
            ';

        return $html_content;
    } else {
        $html_content = "";
        return $html_content;
    }

}

//click PDF in bao cao kinh doanh
public
function pdf_order_kinhdoanh()
{
    if (isset($_GET["order_number"])) {
        $order_number = $_GET["order_number"];
    }

    if (isset($_GET["total_price"])) {
        //5 eu tien phi shipping
        $total_price = $_GET["total_price"] + 5;
    }

    $infor_kunden = $this->m_htmltopdf->get_infor_kunden($order_number);
    $ngaydathang = "";
    $shipped_at = "";
    foreach ($infor_kunden as $item) {
        $_json_customer = json_decode($item['customer']);
        $ngaydathang = $item['created_time'];
        $shipped_at = $item['shipped_at'];
    }
    $date = date_create($ngaydathang);
    $ngaydathang = date_format($date, 'd-m-Y');

    $date_shipped_at = date_create($shipped_at);
    $shipped_at = date_format($date_shipped_at, 'd-m-Y');

    $json_customer = get_object_vars($_json_customer);
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

    $name = $frist_name . " " . $last_name;
    $firma = (isset($json_customer['company']) && ($json_customer['company'] != "")) ? $json_customer['company'] : "";
    if (isset($json_customer['phone'])) {
        $phone = $json_customer['phone'];
    } else {
        $phone = "";
    }

    $adresse = $json_customer['address1'] . " " . $json_customer['city'] . " " . $json_customer['zip'];

    $this->load->model('m_voxy_package_orders');
    $shipper_name = $this->m_voxy_package_orders->name_shipper($order_number);
    $shipper_phone = $this->m_voxy_package_orders->phone_shipper($shipper_name);

    $html_content = '<head>
                            <div class="diachi" style="font-size:13px;float: left; width: 50%">
                               <br>
                                <span style="width: 33%;font-family: DejaVu Sans; text-transform: uppercase;">' . $name . '</span><br>
                                ';
    if ($firma != "") {
        $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $firma . '</span><br>';
    }
    $html_content .= '    <span style="width: 33%;font-family: DejaVu Sans">' . $adresse . '</span><br>
                              <span style="width: 33%;font-family: DejaVu Sans">' . $phone . '</span>
                            </div>
                        
                            <div class="datum" style="float: left; width: 50%; text-align: right;font-size: 13px">
                            <span style="font-family: DejaVu Sans">LIL GmbH - HerbergStraße 131,13595 Berlin</span><br>
                                <span style="font-family: DejaVu Sans">Fahrer: <b>' . $shipper_name . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Phone: <b>' . $shipper_phone . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Auftragsnummer: <b>' . $order_number . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Bestelldatum: <b>' . $ngaydathang . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Lieferdatum: <b>' . $shipped_at . '</b></span><br>
                            </div>
                        </head><br>';

    $html_content .= '<h3 align="center" style="clear:left; font-family: DejaVu Sans" >Báo cáo kinh doanh theo đơn hàng</h3>';

    $html_content .= $this->m_htmltopdf->pdf_order_kinhdoanh($order_number);
    //var_dump($html_content);die;
    $this->pdf->loadHtml($html_content);
    $this->pdf->setPaper('A4', 'landscape');
    $this->pdf->render();
    $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
    $this->pdf->getCanvas()->page_text(72, 18, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
    $this->pdf->stream($order_number . ".pdf", array("Attachment" => 0));
}

public
function baocao_hangtralai_taikho_pdf()
{
    $this->load->model('m_voxy_hangtralai_nhacc');
    $this->load->model('m_voxy_package_xuathang');
    $this->load->model('m_voxy_category');
    $this->load->model('m_voxy_package');
    $this->load->model('m_voxy_package_orders');

    $list_id = $this->input->post('list_id');
    $ngay_giao_hang = $this->input->post('ngay_giao_hang');
    if ($ngay_giao_hang == false) {
        $ngay_giao_hang = "";
    }

    $ngay_dat_hang = $this->input->post('ngay_dat_hang');
    if ($ngay_dat_hang == false) {
        $ngay_dat_hang = "";
    }

    if ($list_id && $list_id != false) {
        $data = $this->m_voxy_hangtralai_nhacc->get_infor_theo_ngay(get_object_vars(json_decode($list_id)), $ngay_giao_hang, $ngay_dat_hang);
    } else {
        $data = $this->m_voxy_hangtralai_nhacc->get_infor_theo_ngay("", $ngay_giao_hang, $ngay_dat_hang);//set list id  = ""
    }

    $export = array();//xu ly all product sang array
    if ($data) {
        foreach ($data as $item) {

            $_item = get_object_vars(json_decode($item['product_variants']));
            foreach ($_item as $item_con) {
                $export[] = get_object_vars($item_con);
            }
        }
    }

    //in ra pdf below------------------------------------------------------------------------------------
    $kho = "all";
    $sorting = "category";//location or category
    if ($sorting == "sl_xuat") {
        $xuattheo = "Số lượng xuất";
    } elseif ($sorting == "sl_sau") {
        $xuattheo = "Số lượng trong kho";
    } elseif ($sorting == "location") {
        $xuattheo = "Vị Trí";
    } else {
        $xuattheo = "Danh mục";
    }

    $html_content = '<head>
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Từ Ngày: <b>' . $ngay_dat_hang . '</b></span>
                        </div>
                        
                        <div class="date-xuat" style="float: left; width: 30%;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Đến Ngày: <b>' . $ngay_giao_hang . '</b></span>
                        </div>
                        
                        <div class="datum" style="float: left; width: 40%; text-align: right;font-size: 13px;">
                            <span style="font-family: DejaVu Sans">Sắp xếp: danh mục</span>
                        </div>
                        
                        </head><br>';
    $html_content .= '<h4 align="center" style="font-family: DejaVu Sans" >BÁO CÁO XUẤT HÀNG TẠI KHO</h4>';


    $export2 = array();
    $chiso_remove = array();
    foreach ($export as $key => $item) {
        // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
        foreach ($export as $key2 => $item2) {
            if ($key2 > $key) {
                if ($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] && $item['variant_id'] == $item2['variant_id']) {
                    if (!isset($item['quantity'])) {
                        $item['quantity'] = 0;
                    }
                    $item['quantity'] = (int)$item['quantity'] + (int)$item2['quantity'];
                    $chiso_remove[$key2] = $key2;//index of same product and then remove it
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
    $all_products['export2'] = $export2;// sap xep lai san pham

    //loc category
    $arr_cat_id = array();
    foreach ($export2 as $item) {
        $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
        $cat_title = $this->m_voxy_category->get_cat_title($cat_id);

        $arr_cat_id[$cat_title]['title'] = $cat_title;
        $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
    }

    // step 2: sort tang dan
    ksort($arr_cat_id);
    $all_products['result_catid'] = $arr_cat_id;
//--------------------------------------------------------------------------------------------------------------------------------------

//        <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Giá Vốn</span>
//          <span style='font-family: DejaVu Sans;width: 7%;float: left;text-align: left;font-size: 13px;'>Thành tiền</span>

    $html_content .= "
<div class='products' style='font-family: DejaVu Sans; font-size: 15px'>
    <div class='pro_th'>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: center;text-align: left !important;'>STT</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center;text-align: left !important;'>SKU</span>
        <span style='font-family: DejaVu Sans;width: 65%;float: left;text-align: center'>Mặt Hàng</span>
        <span style='font-family: DejaVu Sans;width: 10%;float: left;text-align: center'>Đơn Vị</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Trả lại</span>
        <span style='font-family: DejaVu Sans;width: 5%;float: left;text-align: left;font-size: 13px;'>Thành Tiền</span>
    </div>
    
    <div class='pro_body'> ";
    $id = -1;
    $tongtien = 0;
    $k = 0;
    if ($sorting == "category") {
        foreach ($all_products['result_catid'] as $catid) {//category
            if ($kho == 'AKL') {
                if ($catid['cat_id'] == '91459649625') {
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                }
            } elseif ($kho == 'lil') {
                foreach ($all_products['export2'] as $item2) {
                    if ($catid['cat_id'] === $item2['cat_id']) {
                        if (strpos($item2['location'], 'AH') !== false) {
                            $html_content .= " <p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                            break;
                        }
                    }
                }
            } elseif ($kho == 'cua_hang') {//trong cua hang
                if ($catid['cat_id'] == false) {
                    $html_content .= "<b>No Category</b>";
                } else {
                    foreach ($all_products['export2'] as $item5) {
                        if ($catid['cat_id'] === $item5['cat_id']) {
                            if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                            } else {
                                $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $this->m_voxy_category->get_cat_title($catid['cat_id']) . "</p>";
                                break;
                            }
                        }
                    }
                }
            } else {
                if (!isset($catid['cat_id'])) {
                    $html_content .= "<b>No Category</b>";
                } else {
                    $html_content .= "<p style='font-family: DejaVu Sans;text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;'>" . $catid['title'] . "</p>";
                }
            }
            $id_new = 0;
            //begin product to print
            foreach ($all_products['export2'] as $row) {//san pham
                //check product co thuoc san pham do khong thi moi in ra
                if (!isset($row['cat_id'])) {// dooi voi san pham khong co category thi ko in ra, ko no bi loi moi cho chu


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
                    if (strlen($row['sku']) == 0) {
                        $row['sku'] = "no_sku;";
                    }

                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
                        ";
                } else {
                    if ($catid['cat_id'] == $row['cat_id']) {
                        if (strlen($row['location']) > 11) {//xu ly chuoi location overlengt 12
                            $array_location = explode(',', $row['location']);
                            $row['location'] = '';
                            foreach ($array_location as $key => $loca) {
                                $row['location'] .= $loca . '<br>';
                            }
                        }

                        //xu ly do dai cua sku
                        if (strlen($row['sku']) > 5) {
                            $row['sku'] = substr($row['sku'], 0, 5);
                        }

                        if (strlen($row['sku']) == 0) {
                            $row['sku'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }


                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);

                        if (isset($row['variant_id']) && $row['variant_id'] != "") {
                            $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        } else {
                            $idnew = false;
                        }

//                            if ($check_variant1 == true) {
//                                //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
//                                //gia von la gia mua
//                                if($idnew != false){
//                                    $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
//                                }else{
//                                    $giavon = 0;
//                                }
//                            }
//
//                            if ($check_variant2 == true) {
//                                if($idnew != false){
//                                    $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
//                                }else{
//                                    $giavon = 0;
//                                }
//                            }


                        $thanhtien = $row['thanhtien'];

                        $tongtien += $thanhtien;

                        if ($kho == 'all') { // in tat ca k phan biet
//                                <div style='width: 7%;height: auto;float: left'>".$giavon."</div>
//                            <div style='width: 7%;height: auto;float: left'>".$thanhtien."</div>

                            $id++;
                            $k++;
                            $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 5%;height: auto;float: left;text-align: left !important;' class='id'>" . $k . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['thanhtien'] . "</div>
                        </div>
                        ";
                        } elseif ($kho == 'lil') {
                            if (strpos($row['location'], 'AH') !== false) {
                                $id++;
                                $html_content .= " 
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                            }
                        } elseif ($kho == 'AKL') {
                            if (strpos($row['location'], 'AKL') !== false) {
                                $id++;

                                $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
          ";
                            }
                        } elseif ($kho == 'cua_hang') {
                            if ($row['location'] == false) {
                                $id++;

                                $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                            }
                        }
                        //end check in ra trong kho nao
                    }
                }
            }
        }
    } else {//in theo location
//--------------------------------------------------------------------------------------------------------------------------------------
        foreach ($all_products['export2'] as $row) {//san pham
            //$data_da_xuat = 'nein';
            //$quantity_xuathang = 0;
            $sl_daxuat = 0;
            if (!isset($row['variant_id'])) {
                $row['variant_id'] = 0;
            }

            //xu ly do dai cua sku
            if (strlen($row['sku']) > 5) {
                $row['sku'] = substr($row['sku'], 0, 5);
            }

            if (!isset($row['location'])) {
                $row['location'] = "";
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

            if (isset($row['variant_id']) && $row['variant_id'] != "") {
                $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
            } else {
                $idnew = false;
            }

            if ($check_variant1 == true) {
                //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
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
            $thanhtien = (double)$giavon * (int)$row['sl_nhap'];

            $tongtien += $thanhtien;


            if (!isset($row['variant_title']) || $row['variant_title'] == "") {
                $row['variant_title'] = "no infor";
            }

            if ($kho == 'all') { // in tat ca k phan biet
                $id++;
                $k++;
                $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='id'>" . $k . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 70%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                            <div style='width: 5%;height: auto;float: left;text-align: center'>" . $row['quantity'] . "</div>
                            
                        </div>
        ";

//                    <div style='width: 5%;height: auto;float: left;text-align: center'>".$giavon."</div>
//                            <div style='width: 5%;height: auto;float: left;text-align: center'>".$thanhtien."</div>
            } elseif ($kho == 'lil') {
                if (strpos($row['location'], 'AH') !== false) {
                    $id++;
                    $value_note = '';
                    foreach ($all_products['array_note_products'] as $item_note) {
                        if ($item_note['title'] === $row['title']) {
                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                        }
                    }
                    $html_content .= " 
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                }
            } elseif ($kho == 'AKL') {
                if (strpos($row['location'], 'AKL') !== false) {
                    $id++;
                    $value_note = '';
                    foreach ($all_products['array_note_products'] as $item_note) {
                        if ($item_note['title'] === $row['title']) {
                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                        }
                    }
                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
          ";
                }
            } elseif ($kho == 'cua_hang') {
                if ($row['location'] == false) {
                    $id++;
                    $value_note = '';
                    foreach ($all_products['array_note_products'] as $item_note) {
                        if ($item_note['title'] === $row['title']) {
                            $value_note .= "<br>" . $item_note['item_note_value'] . "<br>";
                        }
                    }

                    $html_content .= "
                        <div class='infomation' style='width:100%;clear: left'>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku-$id'>" . $row['sku'] . "</div>
                            <div style='width: 65%;height: auto;float: left; text-align: left' class='title-. $id; .'>" . $row['title'] . "</div>
                            <div style='width: 10%;height: auto;float: left'>" . $row['sl_kho'] . "</div>
                            <div style='width: 5%;height: auto;float: left'>" . $row['quantity'] . "</div>
                            <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title-$id'>" . $row['variant_title'] . "</div>
                        </div>
        ";
                }
            }
            //end check in ra trong kho nao
        }
    }
    $html_content .= "
                                </div>
</div>
                                ";

    $tongtien = number_format($tongtien, 2);

    $html_content .= "
                    <br>
                    <br>
                    <p style='margin-right: 20px;float:right;font-family: DejaVu Sans'>Tổng tiền: $tongtien €</p>";

//        $html_content .= "
//            <br>
//            <br>
//            <br>
//            <br>
//            <br>
//            <br>
//            <p style='clear: left;margin-top: 70px;'></p>
//            <div style='font-family: DejaVu Sans; width: 50%; float:left;font-size: 12px; text-align: center;'>
//                <hr style='width: 200px;margin: 0; padding: 0;'>
//                <span style='text-align: center'>Người Tạo Báo Cáo</span>
//            </div>
//            <div style='font-family: DejaVu Sans ; width: 50%; float:left; font-size: 12px;text-align: center;'>
//                <hr style='width: 200px;margin: 0; padding: 0;'>
//                <span style='text-align: center'>Quản Lý Kho</span>
//            </div>
//        ";
    //var_dump($html_content);die;
    $this->pdf->loadHtml($html_content);
    $this->pdf->render();
    $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
    $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
    $this->pdf->stream("Ngay-" . $ngay_dat_hang . "to" . $ngay_giao_hang . ".pdf", array("Attachment" => 0));
}

public
function baocao_lieferschein_all()
{
    $this->load->model('m_htmltopdf');
    $this->load->model('m_voxy_package_orders');
    $_list_id = $this->input->post('input-hidden-all-liferschein');

    if ($_list_id == "") {
        //var_dump("bạn phải chọn các đơn hàng trước sau đó mới in lieferschein.Danke");die;
        //get all id là oke
        $tungay = $this->input->post('date_for_orders');
        $denngay = $this->input->post('date_for_orders_end');
        $shipper_id = $this->input->post('shipper_id');//array
        $shipper_are_id = $this->input->post('shipper_are_id');//array
        $list_id = $this->m_voxy_package_orders->get_list_id($tungay, $denngay, $shipper_id, $shipper_are_id);
    } else {
        $list_id = get_object_vars(json_decode($_list_id))['list_id'];//array mit id
    }

    //var_dump($list_id);die;

    if (!$list_id) {
        var_dump("Không có dữ liệu, xin chọn lại ngày tháng, tour ");
        die;
    }

    $html_content = "";

    foreach ($list_id as $id) {
        $order_number = $this->m_voxy_package_orders->get_order_number_from_id($id);
        $infor_kunden = $this->m_htmltopdf->get_infor_kunden($order_number);
        $ngaydathang = "";
        $shipped_at = "";
        foreach ($infor_kunden as $item) {
            $_json_customer = json_decode($item['customer']);
            $ngaydathang = $item['created_time'];
            $shipped_at = $item['shipped_at'];
        }
        $date = date_create($ngaydathang);
        $ngaydathang = date_format($date, 'd-m-Y');

        $date_shipped_at = date_create($shipped_at);
        $shipped_at = date_format($date_shipped_at, 'd-m-Y');

        $json_customer = get_object_vars($_json_customer);
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

        $name = $frist_name . " " . $last_name;
        $firma = (isset($json_customer['company']) && ($json_customer['company'] != "")) ? $json_customer['company'] : "";
        if (isset($json_customer['phone'])) {
            $phone = $json_customer['phone'];
        } else {
            $phone = "";
        }

        $adresse = $json_customer['address1'] . " " . $json_customer['city'] . " " . $json_customer['zip'];

        $this->load->model('m_voxy_package_orders');
        $shipper_name = $this->m_voxy_package_orders->name_shipper($order_number);
        $shipper_phone = $this->m_voxy_package_orders->phone_shipper($shipper_name);

        $html_content .= '<head>
                            <div class="diachi" style="font-size:13px;float: left; width: 50%">
                               <br>
                                <span style="width: 33%;font-family: DejaVu Sans; text-transform: uppercase;">' . $name . '</span><br>
                                ';
        if ($firma != "") {
            $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $firma . '</span><br>';
        }
        $html_content .= '    <span style="width: 33%;font-family: DejaVu Sans">' . $adresse . '</span><br>
                              <span style="width: 33%;font-family: DejaVu Sans">' . $phone . '</span>
                            </div>
                        
                            <div class="datum" style="float: left; width: 50%; text-align: right;font-size: 13px">
                            <span style="font-family: DejaVu Sans">LIL GmbH - HerbergStraße 131,13595 Berlin</span><br>
                                <span style="font-family: DejaVu Sans">Fahrer: <b>' . $shipper_name . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Phone: <b>' . $shipper_phone . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Auftragsnummer: <b>' . $order_number . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Bestelldatum: <b>' . $ngaydathang . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Lieferdatum: <b>' . $shipped_at . '</b></span><br>
                            </div>
                        </head><br>';

        $html_content .= '<h3 align="center" style="clear:left; font-family: DejaVu Sans" >Báo cáo kinh doanh theo đơn hàng</h3>';
        $html_content .= $this->m_htmltopdf->pdf_order_kinhdoanh($order_number);
        $html_content .= '<style>
                                   .page-break{
                                        page-break-after: always;
                                   }
                                </style>';
        $html_content .= '<div class="page-break"></div>';
    }

    $this->pdf->loadHtml($html_content);
    $this->pdf->setPaper('A4', 'landscape');
    $this->pdf->render();
    $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
    $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
    $this->pdf->stream("list_lieferschein.pdf", array("Attachment" => 0));

    //file_put_contents('xml/list_lieferschein.pdf', $this->pdf->output());

//        $files = glob("../pdf");
//        foreach($files as $file) include_once($file);
//        $output = $this->pdf->output();
//        file_put_contents('Lieferschein.pdf', $output);

}

public
function baocao_lieferschein_all_excel()
{
    $this->load->model('m_htmltopdf');
    $this->load->model('m_voxy_package_orders');
    $_list_id = $this->input->post('input-hidden-all-liferschein');

    if ($_list_id == "") {
        //var_dump("bạn phải chọn các đơn hàng trước sau đó mới in lieferschein.Danke");die;
        //get all id là oke
        $tungay = $this->input->post('date_for_orders');
        $denngay = $this->input->post('date_for_orders_end');
        $shipper_id = $this->input->post('shipper_id');//array
        $shipper_are_id = $this->input->post('shipper_are_id');//array
        $list_id = $this->m_voxy_package_orders->get_list_id($tungay, $denngay, $shipper_id, $shipper_are_id);
    } else {
        $list_id = get_object_vars(json_decode($_list_id))['list_id'];//array mit id
    }

    //var_dump($list_id);die;

    if (!$list_id) {
        var_dump("Không có dữ liệu, xin chọn lại ngày tháng, tour ");
        die;
    }

    $html_content = "";

    foreach ($list_id as $id) {
        $order_number = $this->m_voxy_package_orders->get_order_number_from_id($id);
        $infor_kunden = $this->m_htmltopdf->get_infor_kunden($order_number);
        $ngaydathang = "";
        $shipped_at = "";
        foreach ($infor_kunden as $item) {
            $_json_customer = json_decode($item['customer']);
            $ngaydathang = $item['created_time'];
            $shipped_at = $item['shipped_at'];
        }
        $date = date_create($ngaydathang);
        $ngaydathang = date_format($date, 'd-m-Y');

        $date_shipped_at = date_create($shipped_at);
        $shipped_at = date_format($date_shipped_at, 'd-m-Y');

        $json_customer = get_object_vars($_json_customer);
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

        $name = $frist_name . " " . $last_name;
        $firma = (isset($json_customer['company']) && ($json_customer['company'] != "")) ? $json_customer['company'] : "";
        if (isset($json_customer['phone'])) {
            $phone = $json_customer['phone'];
        } else {
            $phone = "";
        }

        $adresse = $json_customer['address1'] . " " . $json_customer['city'] . " " . $json_customer['zip'];

        $this->load->model('m_voxy_package_orders');
        $shipper_name = $this->m_voxy_package_orders->name_shipper($order_number);
        $shipper_phone = $this->m_voxy_package_orders->phone_shipper($shipper_name);

        $html_content .= '<head>
                            <div class="diachi" style="font-size:13px;float: left; width: 50%">
                               <br>
                                <span style="width: 33%;font-family: DejaVu Sans; text-transform: uppercase;">' . $name . '</span><br>
                                ';
        if ($firma != "") {
            $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $firma . '</span><br>';
        }
        $html_content .= '    <span style="width: 33%;font-family: DejaVu Sans">' . $adresse . '</span><br>
                              <span style="width: 33%;font-family: DejaVu Sans">' . $phone . '</span>
                            </div>
                        
                            <div class="datum" style="float: left; width: 50%; text-align: right;font-size: 13px">
                            <span style="font-family: DejaVu Sans">LIL GmbH - HerbergStraße 131,13595 Berlin</span><br>
                                <span style="font-family: DejaVu Sans">Fahrer: <b>' . $shipper_name . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Phone: <b>' . $shipper_phone . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Auftragsnummer: <b>' . $order_number . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Bestelldatum: <b>' . $ngaydathang . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Lieferdatum: <b>' . $shipped_at . '</b></span><br>
                            </div>
                        </head><br>';

        $html_content .= '<h3 align="center" style="clear:left; font-family: DejaVu Sans" >Báo cáo kinh doanh theo đơn hàng</h3>';
        $html_content .= $this->m_htmltopdf->pdf_order_kinhdoanh($order_number);
        $html_content .= '<style>
                                   .page-break{
                                        page-break-after: always;
                                   }
                                </style>';
        $html_content .= '<div class="page-break"></div>';
    }

    $this->pdf->loadHtml($html_content);
    $this->pdf->setPaper('A4', 'landscape');
    $this->pdf->render();
    $font = $this->pdf->getFontMetrics()->get_font("helvetica", "bold");
    $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
    $this->pdf->stream("list_lieferschein.pdf", array("Attachment" => 0));
}

public
function pdf_order_kinhdoanh_excel()
{
    require_once APPPATH . "/third_party/PHPExcel.php";
    if (isset($_GET["order_number"])) {
        $order_number = $_GET["order_number"];
    }
    if (isset($_GET["total_price"])) {
        //5 eu tien phi shipping
        $total_price = $_GET["total_price"] + 5;
    }

    $infor_kunden = $this->m_htmltopdf->get_infor_kunden($order_number);
    $ngaydathang = "";
    $shipped_at = "";
    foreach ($infor_kunden as $item) {
        $_json_customer = json_decode($item['customer']);
        $ngaydathang = $item['created_time'];
        $shipped_at = $item['shipped_at'];
    }
    $date = date_create($ngaydathang);
    $ngaydathang = date_format($date, 'd-m-Y');

    $date_shipped_at = date_create($shipped_at);
    $shipped_at = date_format($date_shipped_at, 'd-m-Y');

    $json_customer = get_object_vars($_json_customer);
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

    $name = $frist_name . " " . $last_name;
    $firma = (isset($json_customer['company']) && ($json_customer['company'] != "")) ? $json_customer['company'] : "";
    if (isset($json_customer['phone'])) {
        $phone = $json_customer['phone'];
    } else {
        $phone = "";
    }

    $adresse = $json_customer['address1'] . " " . $json_customer['city'] . " " . $json_customer['zip'];

    $this->load->model('m_voxy_package_orders');
    $shipper_name = $this->m_voxy_package_orders->name_shipper($order_number);
    $shipper_phone = $this->m_voxy_package_orders->phone_shipper($shipper_name);

    $html_content = '<head>
                            <div class="diachi" style="font-size:13px;float: left; width: 50%">
                               <br>
                                <span style="width: 33%;font-family: DejaVu Sans; text-transform: uppercase;">' . $name . '</span><br>
                                ';
    if ($firma != "") {
        $html_content .= '<span style="width: 33%;font-family: DejaVu Sans">' . $firma . '</span><br>';
    }
    $html_content .= '    <span style="width: 33%;font-family: DejaVu Sans">' . $adresse . '</span><br>
                              <span style="width: 33%;font-family: DejaVu Sans">' . $phone . '</span>
                            </div>
                        
                            <div class="datum" style="float: left; width: 50%; text-align: right;font-size: 13px">
                            <span style="font-family: DejaVu Sans">LIL GmbH - HerbergStraße 131,13595 Berlin</span><br>
                                <span style="font-family: DejaVu Sans">Fahrer: <b>' . $shipper_name . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Phone: <b>' . $shipper_phone . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Auftragsnummer: <b>' . $order_number . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Bestelldatum: <b>' . $ngaydathang . '</b></span><br>
                                <span style="font-family: DejaVu Sans">Lieferdatum: <b>' . $shipped_at . '</b></span><br>
                            </div>
                        </head><br>';

    $html_content .= '<h3 align="center" style="clear:left; font-family: DejaVu Sans" >Báo cáo kinh doanh theo đơn hàng</h3>';

    $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
    $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
    $excel->getActiveSheet()->setTitle('Thông tin đơn hàng số ' . $order_number);
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
    $styleArray8 = array(
        'font' => array(
            'size' => 15,
            'name' => 'Time New Roman',
        ));

    $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
    //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
    $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray8);
    $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
    $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
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
    $excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
    $excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
    $excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
    $excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
//Xét in đậm cho khoảng cột
    $excel->getActiveSheet()->getStyle('A7:P7')->getFont()->setBold(true);
    $excel->getActiveSheet()->getStyle('A8:P8')->getFont()->setBold(true);
    if ($firma != "" && isset($firma)) {
        $firma_print = $firma;
    } else {
        $firma_print = "";
    }

    $excel->getActiveSheet()->setCellValue('A1', $name);
    $excel->getActiveSheet()->setCellValue('A2', $firma_print);
    $excel->getActiveSheet()->setCellValue('A3', $adresse);
    $excel->getActiveSheet()->setCellValue('A4', $phone);

    $excel->getActiveSheet()->setCellValue('C1', "LIL GmbH - HerbergStraße 131,13595 Berlin");
    $excel->getActiveSheet()->setCellValue('C2', "Fahrer:" . $shipper_name);
    $excel->getActiveSheet()->setCellValue('C3', "Phone:" . $shipper_phone);
    $excel->getActiveSheet()->setCellValue('C4', "Auftragsnummer:" . $order_number);
    $excel->getActiveSheet()->setCellValue('C5', "Bestelldatum:" . $ngaydathang);
    $excel->getActiveSheet()->setCellValue('C6', "Lieferdatum:" . $shipped_at);

    $excel->getActiveSheet()->setCellValue('C7', "Báo cáo kinh doanh theo đơn hàng");

    $excel->getActiveSheet()->setCellValue('A8', 'STT');
    $excel->getActiveSheet()->setCellValue('B8', 'SKU');
    $excel->getActiveSheet()->setCellValue('C8', 'Tên SP');
    $excel->getActiveSheet()->setCellValue('D8', 'Số lượng');
    $excel->getActiveSheet()->setCellValue('E8', 'Hàng về');
    $excel->getActiveSheet()->setCellValue('F8', 'Hàng thiếu');
    $excel->getActiveSheet()->setCellValue('G8', 'Hàng hỏng');
    $excel->getActiveSheet()->setCellValue('H8', 'Hàng thêm');
    $excel->getActiveSheet()->setCellValue('I8', 'SL cuối');
    $excel->getActiveSheet()->setCellValue('J8', 'Đơn vị');
    $excel->getActiveSheet()->setCellValue('K8', 'Giá vốn');
    $excel->getActiveSheet()->setCellValue('L8', 'Giá bán');
    $excel->getActiveSheet()->setCellValue('M8', 'Vốn');
    $excel->getActiveSheet()->setCellValue('N8', 'Doanh thu');
    $excel->getActiveSheet()->setCellValue('O8', 'Lợi nhuận gộp');
    $excel->getActiveSheet()->setCellValue('P8', 'Lợi nhuận %');

    $_export = $this->m_htmltopdf->pdf_order_kinhdoanh_excel($order_number);

    $id = 0;
    $numRow = 9;
    $total_price = 0;
    $netto = 0;
    $tongloinhuan = 0;
    $tongvon = 0;

    foreach ($_export as $row) {
        $id++;
        if (!isset($row['hangve'])) {
            $row['hangve'] = 0;
            $hangve_print = "";
        } else {
            $hangve_print = $row['hangve'];
        }
        if ($hangve_print == 0) {
            $hangve_print = "";
        }

        if (!isset($row['hanghong'])) {
            $row['hanghong'] = 0;
            $hanghong_print = "";
        } else {
            $hanghong_print = $row['hanghong'];
        }
        if ($hanghong_print == 0) {
            $hanghong_print = "";
        }

        if (!isset($row['hangthieu'])) {
            $row['hangthieu'] = 0;
            $hangthieu_print = "";
        } else {
            $hangthieu_print = $row['hangthieu'];
        }
        if ($hangthieu_print == 0) {
            $hangthieu_print = "";
        }

        if (!isset($row['hangthem'])) {
            $row['hangthem'] = 0;
            $hangthem_print = "";
        } else {
            $hangthem_print = $row['hangthem'];
        }
        if ($hangthem_print == 0) {
            $hangthem_print = "";
        }

        $sl_cuoicung = $row['quantity'] - $row['hangve'] - $row['hanghong'] - $row['hangthieu'] + $row['hangthem'];

        $total_price += $row['price'] * $sl_cuoicung;
        $gesamt = $row['price'] * $sl_cuoicung;

        $mwst = 7;
        if (isset($row['sku'])) {
            $mwst = $this->m_voxy_package->get_mwst($row['sku']);
        }
        if ($mwst == false) {
            $mwst = 7;
        }
        $netto += $gesamt / (($mwst / 100) + 1);

        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);

        if (isset($row['variant_id']) && $row['variant_id'] != "") {
            $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
        } else {
            $idnew = false;
        }

        if ($check_variant1 == true) {
            //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
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

        if (!isset($giavon)) {
            $giavon = 0;
        }

        $tongvon += (double)$giavon * (double)$sl_cuoicung;
        $loinhuan = ($row['price'] - (double)$giavon) * (double)$sl_cuoicung;//doanhthu
        $tongloinhuan += $loinhuan;

        if ($loinhuan == 0 || $row['price'] == 0) {
            $phantram = 0;
        } else {
            $phantram = number_format(($row['price'] - $giavon) * 100 / $row['price'], 1);
        }

        $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
        $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['sku']);
        $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['title']);
        $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['quantity']);
        $excel->getActiveSheet()->setCellValue('E' . $numRow, $hangve_print);
        $excel->getActiveSheet()->setCellValue('F' . $numRow, $hangthieu_print);
        $excel->getActiveSheet()->setCellValue('G' . $numRow, $hanghong_print);
        $excel->getActiveSheet()->setCellValue('H' . $numRow, $hangthem_print);
        $excel->getActiveSheet()->setCellValue('I' . $numRow, $sl_cuoicung);
        $excel->getActiveSheet()->setCellValue('J' . $numRow, $row['variant_title']);
        $excel->getActiveSheet()->setCellValue('K' . $numRow, $giavon);
        $excel->getActiveSheet()->setCellValue('L' . $numRow, $row['price']);
        $excel->getActiveSheet()->setCellValue('M' . $numRow, $giavon * $sl_cuoicung);//tongvon
        $excel->getActiveSheet()->setCellValue('N' . $numRow, $row['price'] * $sl_cuoicung);//doanhthu
        $excel->getActiveSheet()->setCellValue('O' . $numRow, $loinhuan);//loinhuan
        $excel->getActiveSheet()->setCellValue('P' . $numRow, $phantram);//%
        $numRow++;
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
        $excel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray2);
    }

    if ($tongloinhuan == 0) {
        $tong_phantam = 0;
    } else {
        if ($total_price == 0) {
            $tong_phantam = 0;
        } else {
            $tong_phantam = number_format(($tongloinhuan / $total_price) * 100, 1);
        }
    }
    $excel->getActiveSheet()->setCellValue('M' . $numRow, number_format($tongvon, 2));
    $excel->getActiveSheet()->setCellValue('N' . $numRow, number_format($total_price, 2));
    $excel->getActiveSheet()->setCellValue('O' . $numRow, number_format($tongloinhuan, 2));
    $excel->getActiveSheet()->setCellValue('P' . $numRow, number_format($tong_phantam, 2));
    $excel->getActiveSheet()->getStyle('M' . $numRow)->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('N' . $numRow)->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('O' . $numRow)->applyFromArray($styleArray2);
    $excel->getActiveSheet()->getStyle('P' . $numRow)->applyFromArray($styleArray2);

    PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=' . $order_number . ".xlsx");
    PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');

//        $filename = 'test.xlsx';
//        file_put_contents($filename,PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx'));
//        $excel->save($filename);
}

//bao cao tien no ubersicht , theo nhieu ngay
public function print_pdf_tienno()
{
    $this->load->model('m_voxy_package_orders');
    $this->load->model('m_htmltopdf');

    // theo tai xe nao
    $shipper_id = $this->input->post('shipper_id');
    $ship_are_id = $this->input->post('shipper_are_id');

    $list_id_to_tienno = $this->input->post('list_id_to_nhathang');

    if ($list_id_to_tienno != "") {
        $list_id_to_tienno = get_object_vars(json_decode($list_id_to_tienno))['list_id'];
    }

    $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);

    $date_for_orders = $this->input->post('date_for_orders');
    $ngay_dat_hang = ($date_for_orders == "") ? "" : $date_for_orders;

    $date_for_orders_end = $this->input->post('date_for_orders_end');
    $ngay_chuyen_hang = ($date_for_orders_end == "") ? "" : $date_for_orders_end;

    $html_content = $this->m_htmltopdf->pdf_day_tienno($list_id_to_tienno, $ngay_dat_hang, $ngay_chuyen_hang, $shipper_id, $ship_are_id);

    //var_dump($html_content);die;
    $this->pdf->loadHtml($html_content);
    $this->pdf->setPaper('A4', 'landscape');
    $this->pdf->render();
    $font = $this->pdf->getFontMetrics()->get_font("helvetica", "");
    $this->pdf->getCanvas()->page_text(72, 18, "Trang: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
    $this->pdf->stream($ngay_chuyen_hang . "-" . $shipper_name . ".pdf", array("Attachment" => 0));
}

public function print_excel_tienno()
{
    $this->load->model('m_voxy_package_orders');
    $this->load->model('m_htmltopdf');

    require_once APPPATH . "/third_party/PHPExcel.php";

    // theo tai xe nao
    $shipper_id = $this->input->post('shipper_id');
    $ship_are_id = $this->input->post('shipper_are_id');

    $list_id_to_tienno = $this->input->post('list_id_to_nhathang');

    if ($list_id_to_tienno != "") {
        $list_id_to_tienno = get_object_vars(json_decode($list_id_to_tienno))['list_id'];
    }

    $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);

    $date_for_orders = $this->input->post('date_for_orders');
    $ngay_dat_hang = ($date_for_orders == "") ? "" : $date_for_orders;

    $date_for_orders_end = $this->input->post('date_for_orders_end');
    $ngay_chuyen_hang = ($date_for_orders_end == "") ? "" : $date_for_orders_end;

    $data = $this->m_htmltopdf->pdf_day_excel_tienno($list_id_to_tienno, $ngay_dat_hang, $ngay_chuyen_hang, $shipper_id, $ship_are_id);//data_excel

    if ($data != "") {
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
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
        $styleArray8 = array(
            'font' => array(
                'size' => 15,
                'name' => 'Time New Roman',
            ));

        $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
        //$excel->getActiveSheet()->getStyle('A8:P8')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('C1', "Báo cáo Nợ");
        $excel->getActiveSheet()->setCellValue('C2', "Từ ngày:" . $ngay_dat_hang);
        $excel->getActiveSheet()->setCellValue('D2', "Đến ngày:" . $ngay_chuyen_hang);

        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'Ngày giao ');
        $excel->getActiveSheet()->setCellValue('C3', 'Tài xế');
        $excel->getActiveSheet()->setCellValue('D3', 'Đơn hàng');
        $excel->getActiveSheet()->setCellValue('E3', 'Khách hàng');
//            $excel->getActiveSheet()->setCellValue('F3', 'Tổng tiền');
//            $excel->getActiveSheet()->setCellValue('G3', 'TT.Lần 1');
//            $excel->getActiveSheet()->setCellValue('H3', 'TT.Lần 2');
//            $excel->getActiveSheet()->setCellValue('I3', 'TT.Lần 3');
//            $excel->getActiveSheet()->setCellValue('J3', 'TT.Lần 4');
//            $excel->getActiveSheet()->setCellValue('K3', 'TT.Lần 5');
        $excel->getActiveSheet()->setCellValue('F3', 'Tổng nợ');
        $excel->getActiveSheet()->setCellValue('G3', 'Ghi chú');

        $id = 0;
        $numRow = 4;
        $tong_total_price = 0;
        $tong_tienno = 0;
        foreach ($data as $row) {
            if ($row['tongtien_no'] != 0) {
                $id++;
                $tong_total_price += $row['total_price'];
                $tong_tienno += $row['tongtien_no'];

                if ($row['customer']) {
                    $json_customer = get_object_vars(json_decode($row['customer']));
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
                    $customer = $frist_name . " " . $last_name;
                }

                if ($customer == " ") {
                    $customer = $row['key_word_customer'];
                }

                if ($row['thanhtoan_lan1'] == 0 || $row['thanhtoan_lan1'] == "") {
                    $thanhtoan_lan1_print = "";
                } else {
                    $thanhtoan_lan1_print = $row['thanhtoan_lan1'];
                }

                if ($row['thanhtoan_lan2'] == 0 || $row['thanhtoan_lan2'] == "") {
                    $thanhtoan_lan2_print = "";
                } else {
                    $thanhtoan_lan2_print = $row['thanhtoan_lan2'];
                }

                if ($row['thanhtoan_lan3'] == 0 || $row['thanhtoan_lan3'] == "") {
                    $thanhtoan_lan3_print = "";
                } else {
                    $thanhtoan_lan3_print = $row['thanhtoan_lan3'];
                }

                if ($row['thanhtoan_lan4'] == 0 || $row['thanhtoan_lan4'] == "") {
                    $thanhtoan_lan4_print = "";
                } else {
                    $thanhtoan_lan4_print = $row['thanhtoan_lan4'];
                }

                if ($row['thanhtoan_lan5'] == 0 || $row['thanhtoan_lan5'] == "") {
                    $thanhtoan_lan5_print = "";
                } else {
                    $thanhtoan_lan5_print = $row['thanhtoan_lan1'];
                }

                if ($row['tongtien_no'] == "") {
                    $row['tongtien_no'] = 0;
                }

                $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row['shipped_at']);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row['shipper_name']);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['order_number']);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $customer);
                //$excel->getActiveSheet()->setCellValue('F' . $numRow, $row['total_price']);
//                    $excel->getActiveSheet()->setCellValue('G' . $numRow, $thanhtoan_lan1_print);
//                    $excel->getActiveSheet()->setCellValue('H' . $numRow, $thanhtoan_lan2_print);
//                    $excel->getActiveSheet()->setCellValue('I' . $numRow, $thanhtoan_lan3_print);
//                    $excel->getActiveSheet()->setCellValue('J' . $numRow, $thanhtoan_lan4_print);
//                    $excel->getActiveSheet()->setCellValue('K' . $numRow, $thanhtoan_lan5_print);
                $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['tongtien_no']);
                $excel->getActiveSheet()->setCellValue('G' . $numRow, $row['note']);
                $numRow++;
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
        }
        $excel->getActiveSheet()->setCellValue('E' . $numRow, "Tổng cộng");
        //$excel->getActiveSheet()->setCellValue('F' . $numRow,number_format($tong_total_price,2));
        $excel->getActiveSheet()->setCellValue('F' . $numRow, number_format($tong_tienno, 2));

        $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('L' . $numRow)->applyFromArray($styleArray2);

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_tienno.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');

    } else {
        die('Không có dữ liệu , xin chọn lại ngày tháng hoặc đơn hàng');
    }

}


public
function print_excel_tienno_kunden()
{
    $this->load->model('m_voxy_package_orders');
    $this->load->model('m_voxy_package_kunden');
    $this->load->model('m_voxy_package_baocao_tonghop');
    $this->load->model('m_htmltopdf');

    require_once APPPATH . "/third_party/PHPExcel.php";

    // theo tai xe nao
    $shipper_id = $this->input->post('shipper_id');
    $ship_are_id = $this->input->post('shipper_are_id');

    if ($ship_are_id) {
        $ship_are_name = $this->m_voxy_package_baocao_tonghop->get_name_shipper_area_id($ship_are_id);
    }

    if (!isset($ship_are_name)) {
        $ship_are_name = "";
    }


    $list_id_to_tienno = $this->input->post('list_id_to_nhathang');

    if ($list_id_to_tienno != "") {
        $list_id_to_tienno = get_object_vars(json_decode($list_id_to_tienno))['list_id'];
    }

    $shipper_name = $this->m_voxy_package_orders->get_name_shipper($shipper_id);

    $date_for_orders = $this->input->post('date_for_orders');
    $ngay_dat_hang = ($date_for_orders == "") ? "" : $date_for_orders;

    $date_for_orders_end = $this->input->post('date_for_orders_end');
    $ngay_chuyen_hang = ($date_for_orders_end == "") ? "" : $date_for_orders_end;

    $data_raw = $this->m_htmltopdf->pdf_day_excel_tienno($list_id_to_tienno, $ngay_dat_hang, $ngay_chuyen_hang, $shipper_id, $ship_are_id);//data_excel
    if ($data_raw == "") {
        die('Không có dữ liệu vào ngày này, xin mời chọn lại hoặc chọn orders bên dưới');
    }
    $data = array();

    $array_remove = array();

    foreach ($data_raw as $key1 => $item1) {
        foreach ($data_raw as $key2 => $item2) {
            if ($key2 > $key1) {
                if ($item1['customer_id'] == $item2['customer_id']) {
                    $item1['total_price'] = (double)$item1['total_price'] + (double)$item2['total_price'];
                    $tongthanhtoan_item1 = (double)$item1['thanhtoan_lan1'] + (double)$item1['thanhtoan_lan2'] + (double)$item1['thanhtoan_lan3'] + (double)$item1['thanhtoan_lan4'] + (double)$item1['thanhtoan_lan5'];
                    $tongthanhtoan_item2 = (double)$item2['thanhtoan_lan1'] + (double)$item2['thanhtoan_lan2'] + (double)$item2['thanhtoan_lan3'] + (double)$item2['thanhtoan_lan4'] + (double)$item2['thanhtoan_lan5'];
                    $item1['tong_da_thu'] = $tongthanhtoan_item1 + $tongthanhtoan_item2;
                    $item1['tong_con_no'] = $item1['total_price'] - $item1['tong_da_thu'];
                    $array_remove[$key2] = $key2;
                }
            } else {
                $tongthanhtoan_item1 = (double)$item1['thanhtoan_lan1'] + (double)$item1['thanhtoan_lan2'] + (double)$item1['thanhtoan_lan3'] + (double)$item1['thanhtoan_lan4'] + (double)$item1['thanhtoan_lan5'];

                $item1['tong_da_thu'] = $tongthanhtoan_item1;

                $item1['tong_con_no'] = $item1['total_price'] - $tongthanhtoan_item1;

            }
        }
        $data[] = $item1;
    }

    foreach ($data as $row) {
        foreach ($array_remove as $key_reomove => $remo) {
            unset($data[$remo]);
            unset($array_remove[$key_reomove]);
        }
    }

    if ($data != "") {
        $excel = new PHPExcel();
//Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
//Tạo tiêu đề cho trang. (có thể không cần)
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
        $styleArray8 = array(
            'font' => array(
                'size' => 15,
                'name' => 'Time New Roman',
            ),
            'alignment' => array(
                'horizontal' => 'right'
            )
        );

        $excel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray);
        $excel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A5:P5')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A6:P6')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A7:P7')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray2);
        $excel->getActiveSheet()->getStyle('A9:P9')->applyFromArray($styleArray2);

//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
//Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
        //$excel->getActiveSheet()->getStyle('A8:P8')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('B1', "Báo cáo Tiền nợ theo đơn hàng");

        $excel->getActiveSheet()->setCellValue('A2', "Từ ngày:" . $date_for_orders);
        $excel->getActiveSheet()->setCellValue('B2', "Đến ngày:" . $date_for_orders_end);
        $excel->getActiveSheet()->setCellValue('C2', "Fahrer:" . $shipper_name);
        $excel->getActiveSheet()->setCellValue('D2', "Tour:" . $ship_are_name);


        $excel->getActiveSheet()->setCellValue('A3', 'STT');
        $excel->getActiveSheet()->setCellValue('B3', 'Tên');
        $excel->getActiveSheet()->setCellValue('C3', 'ID');
        $excel->getActiveSheet()->setCellValue('D3', 'Tổng tiền');
        $excel->getActiveSheet()->setCellValue('E3', 'Đã Thu');
        $excel->getActiveSheet()->setCellValue('F3', 'Còn nợ');

        $id = 0;
        $numRow = 4;
        $tong_total_price = 0;
        $tong_tienno = 0;
        $tong_dathu = 0;
        foreach ($data as $row) {
            $id++;

            $tong_total_price += $row['total_price'];
            $tong_tienno += $row['tong_con_no'];
            $tong_dathu += $row['tong_da_thu'];

            if ($row['customer']) {
                $json_customer = get_object_vars(json_decode($row['customer']));
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
                $customer = $frist_name . " " . $last_name;
            }

            if ($row['key_word_customer'] != "") {
                $customer = $row['key_word_customer'];
            }

            $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($row['customer_id']);

            if ($row['tong_da_thu'] == 0) {
                $row['tong_da_thu'] = "";
            }

            if ($row['tong_con_no'] == 0) {
                $row['tong_con_no'] = "";
            }

            $excel->getActiveSheet()->setCellValue('A' . $numRow, $id);
            $excel->getActiveSheet()->setCellValue('B' . $numRow, $customer);
            $excel->getActiveSheet()->setCellValue('C' . $numRow, $id_khachang);
            $excel->getActiveSheet()->setCellValue('D' . $numRow, $row['total_price']);
            $excel->getActiveSheet()->setCellValue('E' . $numRow, $row['tong_da_thu']);
            $excel->getActiveSheet()->setCellValue('F' . $numRow, $row['tong_con_no']);
            $numRow++;
            $excel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray2);
            $excel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray2);
        }
        $numRow = $numRow + 1;
        $excel->getActiveSheet()->setCellValue('D' . $numRow, number_format($tong_total_price, 2));
        $excel->getActiveSheet()->setCellValue('E' . $numRow, number_format($tong_dathu, 2));
        $excel->getActiveSheet()->setCellValue('F' . $numRow, number_format($tong_tienno, 2));

        $excel->getActiveSheet()->getStyle('D' . $numRow)->applyFromArray($styleArray8);
        $excel->getActiveSheet()->getStyle('E' . $numRow)->applyFromArray($styleArray8);
        $excel->getActiveSheet()->getStyle('F' . $numRow)->applyFromArray($styleArray8);

        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_tienno.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
    } else {
        die('Không có dữ liệu , xin chọn lại ngày tháng hoặc đơn hàng');
    }
}
}

?>
