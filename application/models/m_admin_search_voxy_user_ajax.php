<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 06/25/2018
 * Time: 10:14
 *
 * @author chuvantinh1991@gmail.com
 */

class M_admin_search_voxy_user_ajax extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = '';
        $this->_key_name            = 'id';
        $this->_exist_created_field = FALSE;
        $this->_exist_updated_field = FALSE;
        $this->_exist_deleted_field = FALSE;
        $this->_schema              = Array();
        $this->_rule                = Array();
        $this->_field_form          = Array();
        $this->_field_table         = Array();
    }

    public function setting_select()
    {
        // khong lam gi ca
    }

    public function get_voxy_user($str_search = '')
    {
        return $this->db->select('swt.name AS domain_name, vu.user_id AS voxy_id, vu.user_email AS voxy_email, vum.student_id AS native_id, vum.student_email AS native_email, vum.contact_id AS native_contact_id')
            ->from('voxy_user_mapping AS vum')
            ->join('voxy_users AS vu', 'vu.id = vum.vu_id')
            ->join('sys_weight_table AS swt', 'swt.id = vum.weight_id')
            ->where("(vum.student_id LIKE '%$str_search%' OR vu.user_id LIKE '%$str_search%' OR vum.contact_id LIKE '%$str_search%' OR vu.user_email LIKE '%$str_search%' OR vum.student_email LIKE '%$str_search%' )", NULL)
            ->order_by('vum.id', 'DESC')
            ->limit(10)
            ->get()
            ->result();
    }

}