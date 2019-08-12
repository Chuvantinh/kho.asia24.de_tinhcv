<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_sys_system_config
 *
 * @author chuvantinh1991@gmail.com
 */
class M_sys_system_config extends data_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'sys_system_table';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'name', 'domain', 'weight_id', 'status',

            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'weight_id'        => array(
                'type'              => 'select',
                'target_model'      => 'm_sys_weight_config',
                'target_value'      => 'id',
                'target_display'    => 'name',
                'where_condition'   => array(),
            ),
            'name'          => array(
                'type'          => 'text',
                'maxlength'     => 100,
                'required'      => 'required',
            ),
            'domain'          => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form  = Array(
            'id'                => 'ID',
            'weight_id'         => 'Weight',
            'name'              => 'Name',
            'domain'            => 'Domain',
            'status'            => 'Trạng thái',
        );
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.name'            => 'Name',
            'm.domain'          => 'Domain',
            'w_name'            => 'Weight Name',
            'w_weight'          => 'Trọng số',
            'w_connect'         => 'Chuỗi nối Email',
            'm.status'          => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*, sys_weight.name AS w_name, sys_weight.weight AS w_weight, sys_weight.connect AS w_connect');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('sys_weight_table AS sys_weight', 'sys_weight.id = m.weight_id');
        $this->db->order_by('m.id', 'DESC');
    }

    /**
     * Kiem tra domain da tung ton tai chua
     * @param string $_domain
     * @return bool: TRUE - da ton tai, FALSE - chua ton tai, NULL - loi
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_exist_domain($_domain = ''){
        if(!is_string($_domain)){
            return NULL;
        }
        $_domain = trim($_domain);
        if(!$_domain){
            return NULL;
        }

        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('m.domain', $_domain);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}