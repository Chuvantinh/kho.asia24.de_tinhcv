<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_units
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_units extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_units';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = false;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'name', 'total_lesson',

            'created_at', 'created_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'name'          => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
                'disabled'      => 'disabled'
            ),
            'total_lesson'      => array(
                'type'          => 'number',
                'maxlength'     => 5,
                'required'      => 'required',
                'disabled'      => 'disabled'
            ),
            'created_at'    => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'disabled'      => 'disabled'
            ),
            'created_by'    => array(
                'type'          => 'number',
                'maxlength'     => 11,
                'disabled'      => 'disabled'
            ),
        );
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.name'            => 'Tên Unit',
            'm.total_lesson'    => 'Số Lesson thuộc Unit',
            'm.created_at'      => 'Ngày tạo',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*' );
        $this->db->from($this->_table_name . ' AS m');
    }

    /**
     * Ham kiem tra su ton tai cua unit name
     *
     * @param string $unit_name     unit name
     * @return bool|null            null - tham so khong phu hop
     *                              int >0 - id lesson
     *                              0 - khong ton tai
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_unit_id_with_unit_name($unit_name = '')
    {
        if(!(is_string($unit_name) && trim($unit_name))){
            return NULL;
        }
        $unit_name = trim($unit_name);

        $this->db->select("m.*");
        $this->db->from($this->_table_name . " AS m");
        $this->db->where('m.name', $unit_name);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->first_row()->id;
        } else {
            return FALSE;
        }
    }
}