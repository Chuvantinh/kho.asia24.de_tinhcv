<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 
 */
require_once APPPATH . "/third_party/PHPExcel.php";

class Excel extends PHPExcel {

    public function __construct() {
        parent::__construct();
    }

    public function read_contact($excel_path) {
        $data_return = array();
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($excel_path);
        if ($objPHPExcel) {
            $objWorksheet = $objPHPExcel->getActiveSheet();

            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'

            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

            $need_colum = array(
                "contact_id" => 0, //id
                "contact_name" => 1, //name
                "contact_gender" => 2, //gt
                "contact_phone" => 3, //phone
            );
            for ($row = 1; $row <= $highestRow; ++$row) {
                $data_row = array();
                if (intval($objWorksheet->getCellByColumnAndRow(0, $row)->getCalculatedValue()) && $objWorksheet->getCellByColumnAndRow(1, $row)->getCalculatedValue() != "") {
                    foreach ($need_colum as $col_name => $col) {
                        $data_row[$col_name] = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                        // Loai bo dau cach giua cac chu so dien thoai
                        if ($col_name == "contact_phone") {
                            $data_row[$col_name] = str_replace("+84", "0", $data_row[$col_name]);
							$data_row["contact_phone"] = str_replace("/", ";", $data_row["contact_phone"]);
                            $data_row[$col_name] = preg_replace("/[^\d;\/]+/", "", $data_row[$col_name]);
                            if ($data_row[$col_name][0] != "0") {
                                $data_row[$col_name] = "0" . $data_row[$col_name];
                            }
                        }
                    }
                    $data_return [] = $data_row;
                }
            }
        }
        return $data_return;
    }

}
