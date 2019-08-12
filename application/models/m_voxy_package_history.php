<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package_history
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package_history extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_package_history';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = false;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'pack_code', 'value_old', 'value_new', 'action',

            'created_at', 'created_by'
        );
        $this->_rule        = Array();
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.pack_code'       => 'Package Code',
            'm.value_old'       => 'Giá trị cũ',
            'm.value_new'       => 'Giá trị mới',
            'm.action'          => 'Hành động',
            'au.full_name'      => 'Admin',
            'm.created_at'      => 'Thời gian',

        );
    }

    public function setting_select()
    {
        $this->db->select('m.*, au.user_name, au.full_name');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('admin_users AS au', 'au.id = m.created_by', 'LEFT');
    }

    /**
     * Ham lay du lieu hien thi admin
     * @param string $search_text
     * @param null $whereCondition
     * @param int $limit
     * @param int $post
     * @param null $order
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_table($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0) {
        $data = parent::get_list_table($search_text, $whereCondition, $limit, $post, $order);

        if (count($data)) {
            foreach ($data as $key => $value) {
                if (isset($value->value_old)) {
                    $data[$key]->value_old = $this->adapter_attr_value($value->value_old);
                }
                if (isset($value->value_new)) {
                    $data[$key]->value_new = $this->adapter_attr_value($value->value_new);
                }
            }
        }
        return $data;
    }

    /**
     * Ham chuyen json ra text de hien thi
     * @param $value
     * @return string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function adapter_attr_value($value) {
        $value_obj = json_decode($value);
        $response = '';

        if ($value_obj) {
            $value_obj = (is_object($value_obj) ? (array)$value_obj : (is_array($value_obj) ? $value_obj : array()));
            foreach ($value_obj as $key => $value){
                if(in_array($key, array('created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'))){
                    continue;
                }
                $response .= '<pre>' . $key .': ' . $value . '</pre>';
            }
        }
        return $response;
    }

    public function is_editable($where) {
        return false;
    }

    public function delete_by_id($where) {
        return false;
    }

}