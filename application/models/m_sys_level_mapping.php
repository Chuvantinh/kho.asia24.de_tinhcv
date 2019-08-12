<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_sys_level_mapping
 *
 * @author chuvantinh1991@gmail.com
 */
class M_sys_level_mapping extends data_base
{
    var $arr_status = array(
        '1' => 'ACTIVE',
        '0' => 'DEACTIVE'
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'sys_level_mapping';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'voxy_level', 'native_level', 'description', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        );

        $this->_rule = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'voxy_level'    => array(
                'type'          => 'select',
                'array_list'    => $this->get_list_voxy_level(),
            ),
            'native_level'  => array(
                'type'          => 'text',
                'maxlength'     => 10,
                'required'      => 'required',
                'unique'        => true
            ),
            'description'   => array(
                'type'          => 'textarea',
                'maxlength'     => 255,
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form = Array(
            'id'            => 'ID',
            'voxy_level'    => 'Voxy Level',
            'native_level'  => 'Native Level',
            'description'   => 'Mô tả',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.native_level'    => 'Native Level',
            'm.voxy_level'      => 'Voxy Level',
            'm.description'     => 'Mô tả',
            'm.status'          => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
    }

    public function get_list_voxy_level(){
        return array(
            1 => '1 - Super-Basic',
            2 => '2 - Basic',
            3 => '3 - Pre-Inter',
            4 => '4 - Inter',
            5 => '5 - Advance'
        );
    }

    /**
     * Ham kiem tra su ton tai cua mapping
     * @param int $voxy_level
     * @param string $native_level
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_exist_mapping($voxy_level = 0, $native_level = '', $id = 0){
        if(!$voxy_level || !$native_level){
            return false;
        }

        $this->setting_select();
        $this->db->where('m.voxy_level', $voxy_level);
        $this->db->where('m.native_level', $native_level);
        if($id){
            $this->db->where('m.id !=', $id);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->first_row();
        } else {
            return false;
        }
    }

    /**
     * Ham chuyen doi Native Level sang voxy Level
     * @param string $native_level
     * @return bool|null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_voxy_level($native_level = ''){
        $native_level = strtolower(trim($native_level));
        if(!$native_level){
            return NULL;
        }

        $this->setting_select();
        $this->db->where('m.native_level', $native_level);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $level_info = $query->first_row();
            return (isset($level_info->voxy_level) ? $level_info->voxy_level : FALSE);
        } else {
            return FALSE;
        }
    }

}