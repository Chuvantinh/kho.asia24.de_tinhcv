<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Quan ly danh sach tai khoan voxy
 * Class Voxy_users
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_users extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            'class'     => 'voxy_users',
            'view'      => 'voxy_users',
            'model'     => 'm_voxy_users',
            'object'    => 'Tài khoản Voxy'
        );
    }

    public function index()
    {
        $this->manager();
    }

    protected function manager($data = Array())
    {
        $data['link_export_excel'] = site_url($this->name['class'] . "/export_excel");
        parent::manager($data);
    }

    public function ajax_list_data($data = Array())
    {
        parent::ajax_list_data($data);
    }

    public function export_excel()
    {
        // vvyuht start tranh loi
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('apc.cache_by_default', 0);

        if ($this->session->userdata('voxy_accounts_search_data') != null)
        {
            $data["form_conds"] = json_decode($this->session->userdata('voxy_accounts_search_data'), true);
            $this->data->where_data = $data["form_conds"];
        } else
        {
            $this->data->where_data = $this->get_search_condition();
        }
        $dateJoinedStart = (isset($this->data->where_data['custom_where']['date_joined >=']) ? date('m/d/Y', $this->data->where_data['custom_where']['date_joined >=']) : '');
        $dateJoinedEnd = (isset($this->data->where_data['custom_where']['date_joined <=']) ? date('m/d/Y', $this->data->where_data['custom_where']['date_joined <=']) : '');

        $dataHeader = Array(
            'stt' => "STT", 'user_id' => "Voxy ID", 'first_name' => "Học viên", 'email' => "Email", 'phone' => "Số điện thoại", "date_joined" => "Kích hoạt", "expiration_date" => "Hết hạn", 'level' => 'Level', 'native_language' => 'Lang', 'time_modified' => "Cập nhật lần cuối"
        );
        // tao temp cho tung dong du lieu can insert
        $rowTemp = new stdClass();
        $tempArrKey = array_keys($dataHeader);
        foreach ($tempArrKey as $keyArr)
        {
            $rowTemp->$keyArr = '';
        }

        $listData = $this->data->get_list(null, null, 0, "m.date_joined DESC");
        $dataExport = array();
        if (is_array($listData) && count($listData))
        {
            $mappingLevelVoxy = $this->mappingLevelVoxy();
            $stt = 1;
            foreach ($listData as $key => $oneData)
            {
                $dataTemp = clone $rowTemp;
                $dataTemp->stt = $stt;
                $dataTemp->user_id = isset($oneData->user_id) ? $oneData->user_id : '';
                $dataTemp->first_name = isset($oneData->first_name) ? $oneData->first_name : '';
                $dataTemp->email = isset($oneData->email) ? $oneData->email : '';
                $dataTemp->phone = isset($oneData->phone) ? $oneData->phone : '';
                $dataTemp->date_joined = (isset($oneData->date_joined) && $oneData->date_joined) ? date('m/d/Y', $oneData->date_joined) : '';
                $dataTemp->expiration_date = (isset($oneData->expiration_date) && $oneData->expiration_date) ? date('m/d/Y', $oneData->expiration_date) : '';
                $dataTemp->level = (isset($oneData->level) && array_key_exists($oneData->level, $mappingLevelVoxy)) ? $mappingLevelVoxy[$oneData->level] : '';
                $dataTemp->native_language = isset($oneData->native_language) ? $oneData->native_language : '';
                $dataTemp->time_modified = isset($oneData->time_modified) ? $oneData->time_modified : '';
                $dataExport[] = $dataTemp;
                $stt++;
            }
        }

        $this->load->library('excel');
        $sheet = new PHPExcel ();
        //style cho header cua excel: Danh sach hoc vien voxy
        $styleHeader = array(
            'font' => array(
                'bold' => true, 'color' => array('rgb' => '000000'), 'size' => 14, 'name' => 'Times New Roman'
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
            ),
        );
        //style cho header2 cua excel: Từ ngày …. tháng…năm... đến ngày…….  tháng……..  năm…..
        $styleSubHeader1 = array(
            'font' => array(
                'italic' => true, 'color' => array('rgb' => '000000'), 'size' => 10, 'name' => 'Times New Roman'
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
            ),
        );
        $styleSubHeader2 = array(
            'font' => array(
                'italic' => true, 'color' => array('rgb' => '000000'), 'size' => 10, 'name' => 'Times New Roman'
            )
        );

        //style cho row dau tien cua bang
        $styleHeaderRow = array(
            'font' => array(
                'bold' => true, 'color' => array('rgb' => '000000'), 'size' => 10, 'name' => 'Times New Roman'
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true
            ), 'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ), 'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR, 'rotation' => 90, 'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ), 'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                ),
            ),
        );

        // Set column width
        $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $sheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $sheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);

        $sheet->getProperties()->setTitle('Danh sach hoc vien Voxy')->setDescription('Danh sach hoc vien Voxy');
        $sheet->setActiveSheetIndex(0);

        // row 1
        $sheet->getActiveSheet()->setCellValue('A1', "Ngày xuất: " . date('Y-m-d H:i:s', time()));
        $sheet->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleSubHeader2);

        // row 3
        $sheet->getActiveSheet()->mergeCells('C3:G3');
        $sheet->getActiveSheet()->setCellValue('C3', "Danh Sách Học Viên Voxy");
        $sheet->getActiveSheet()->getStyle('C3')->applyFromArray($styleHeader);

        //row 4
        $sheet->getActiveSheet()->mergeCells('C4:G4');
        $sheet->getActiveSheet()->setCellValue('C4', "Kích hoạt từ ngày " . ($dateJoinedStart ? $dateJoinedStart : '... ') . "đến ngày " . ($dateJoinedEnd ? $dateJoinedEnd : '... .'));

        //row 4->6
        $sheet->getActiveSheet()->getStyle('A4:J6')->applyFromArray($styleSubHeader1);

        //row 7
        $rowHeader = 7;
        $colHeader = 0;
        foreach ($dataHeader as $keyHeader => $headerName)
        {
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($colHeader, $rowHeader, $headerName);
            $sheet->getActiveSheet()->getStyleByColumnAndRow($colHeader, $rowHeader)->applyFromArray($styleHeaderRow);
            $colHeader++;
        }
        // row 8 bat dau du lieu hoc vien
        $row = 8;
        $dataExportObject = array();
        foreach ($dataExport as $key => $value)
        {
            $dataExportObject[$key] = get_object_vars($value);
            $row++;
        }
        unset($dataExport);
        $sheet->setActiveSheetIndex(0)->fromArray($dataExportObject, 99999999999, 'A8');
        //End-code
        //die('asdasd');
        $sheet_writer = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="voxy_account_' . date('mdY', time()) . '.csv"');
        header('Cache-Control: max-age=0');
        $sheet_writer->save('php://output');
    }

    /**
     * Hàm thêm cột vào bản ghi trước khi đưa ra bảng quản lý
     * Mặc định hàm này sẽ thêm 2 cột là cột chứa 3 nút (thêm, sửa xóa) và cột "input"
     * @param Array $record Mảng chứa các bản ghi
     * @return type
     */
    protected function _add_colum_action($record)
    {
        $form = $this->data->get_form();
        $dataReturn = Array();
        $dataReturn["schema"]   = $form["schema"];
        $dataReturn["rule"]     = $form["rule"];
        $dataReturn["colum"]    = $form["field_table"];

        /* Thêm cột action */
        $dataReturn["colum"]["custom_action"] = "Action";
        /* Thêm cột check */
        // $dataReturn["colum"]["custom_check"] = "<input type='checkbox' class='e_check_all' />";

        $record = $this->_process_data_table($record);
        $dataReturn["record"] = $record;
        return $dataReturn;
    }

    /**
     * Trường xử lý bản ghi trước khi hiển thị ra bảng
     * @param Array|Object $record
     * @return Array|Object
     */
    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $form       = $this->data->get_form();
        $key_table  = $this->data->get_key_name();
        /* Tùy biến dữ liệu các cột */
        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            $record->custom_action = '<div class="action">';
            $record->custom_action .= '<a class="detail e_ajax_link icon16 i-eye-3 " per="1" href="' . site_url($this->url["view"] . $record->$key_table) . '" title="Xem"></a>';
            if (!isset($record->editable) || (isset($record->editable) && $record->editable)) {
                $record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
                // $record->custom_action .= '<a class="delete e_ajax_confirm e_ajax_link icon16 i-remove" per="1" href="' . site_url($this->url["delete"] . $record->$key_table) . '" title="Xóa"></a>';
            }
            $record->custom_action .= '</div>';
            $record->custom_check = "<input type='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            foreach ($form["field_table"] as $keyColum => $valueColum) {
                if (isset($form["rule"][$keyColum])) {
                    if (isset($form["rule"][$keyColum]['type']) && $form["rule"][$keyColum]['type'] == 'checkbox') {
                        if ($record->$keyColum) {
                            $record->$keyColum = "<input type='checkbox' name='" . $keyColum . "' disabled='disabled' checked='checked' />";
                        } else {
                            $record->$keyColum = "<input type='checkbox' name='" . $keyColum . "' disabled='disabled' />";
                        }
                    } elseif (isset($form["rule"][$keyColum]['type']) && $form["rule"][$keyColum]['type'] == 'file') {
                        $record->$keyColum = "<div class='center'><img src='" . $record->$keyColum . "' /></div>";
                    } elseif (isset($form["rule"][$keyColum]['type']) && ($form["rule"][$keyColum]['type'] == 'datetime' || $form["rule"][$keyColum]['type'] == 'date')) {
                        $temp = strtotime($record->$keyColum);
                        if ($form["rule"][$keyColum]['type'] == 'datetime') {
                            $record->$keyColum = date("d-m-Y H:i:s", $temp);
                        } else {
                            $record->$keyColum = date("d-m-Y", $temp);
                        }
                    }
                }
            }
            if(isset($record->status) && isset($this->data->arr_status)){
                $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }
            if(isset($record->created_at) && intval($record->created_at)){
                $record->created_at = date('d-m-Y H:i:s', $record->created_at);
            }
        }
        return $record;
    }
}