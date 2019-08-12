<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package_history
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package_history extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class"     => "voxy_package_history",
            "view"      => "voxy_package_history",
            "model"     => "m_voxy_package_history",
            "object"    => "Package History"
        );
    }

    /**
     * Hàm thêm cột vào bản ghi trước khi đưa ra bảng quản lý
     * Mặc định hàm này sẽ thêm 2 cột là cột chứa 3 nút (thêm, sửa xóa) và cột "input"
     * @param Array $record Mảng chứa các bản ghi
     * @return type
     */
    protected function _add_colum_action($record)
    {
        $form                   = $this->data->get_form();
        $dataReturn             = Array();
        $dataReturn["schema"]   = $form["schema"];
        $dataReturn["rule"]     = $form["rule"];
        $dataReturn["colum"]    = $form["field_table"];
        $record                 = $this->_process_data_table($record);
        $dataReturn["record"] = $record;
        return $dataReturn;
    }

    /**
     * Tuy bien du lieu hien thi
     * @param array|Object $record
     * @return array|Object
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }

        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            if(isset($record->created_at) && intval($record->created_at)){
                $record->created_at = date('d-m-Y H:i', intval($record->created_at));
            }
        }
        return $record;
    }
}