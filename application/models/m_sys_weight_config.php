<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_sys_weight_config
 *
 * @author chuvantinh1991@gmail.com
 */
class M_sys_weight_config extends data_base
{
    var $_diff_weight = 1000000000; // 1 ty

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'sys_weight_table';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = false;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'name', 'weight', 'connect', 'editable',

            'created_at', 'created_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'name'          => array(
                'type'          => 'text',
                'maxlength'     => 100,
                'required'      => 'required',
            ),
            'weight'        => array(
                'type'          => 'number',
                'min'           => 0,
                'max'           => '99999999999',
                'maxlength'     => 11,
                'required'      => 'required',
            ),
            'connect'          => array(
                'type'          => 'text',
                'minlength'     => 3,
                'maxlength'     => 20,
                'required'      => 'required',
            )
        );
        $this->_field_form  = Array(
            'id'            => 'ID',
            'name'          => 'Name',
            'weight'        => 'Cân nặng',
            'connect'       => 'Chuỗi nối',
        );
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.name'            => 'Tên đánh dấu',
            'm.weight'          => 'Cân nặng',
            'm.connect'         => 'Chuỗi nối',
            'm.created_at'      => 'Thời gian tạo',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->order_by('m.id', 'DESC');
    }

    /**
     * Kiem tra weight co hop le khong
     * @param int $weight
     * @return bool: TRUE - thoa man, FALSE - khong thoa man
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_weight($weight = 0){
        $weight = intval($weight);
        if(!$weight){
            return NULL;
        }
        $_diff_weight_minus = $weight - $this->_diff_weight + 1;
        $_diff_weight_minus = ($_diff_weight_minus > 0) ? $_diff_weight_minus : 0;
        $_diff_weight_plus  = $weight + $this->_diff_weight - 1;

        $this->setting_select();
        $this->db->where('m.weight >=', $_diff_weight_minus);
        $this->db->where('m.weight <=', $_diff_weight_plus);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Ham kiem tra hop len chuoi connect
     * @param string $connect
     * @return bool|null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_connect($connect = ''){
        if(!(is_string($connect) && trim($connect) != '')){
            return NULL;
        }

        $connect = trim($connect);
        $this->setting_select();
        $this->db->where('m.connect', $connect);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}