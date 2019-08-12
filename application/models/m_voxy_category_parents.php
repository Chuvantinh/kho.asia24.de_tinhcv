<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_category_parents
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_category_parents extends data_base
{
    var $arr_status = array (
        '1' => 'Active',
        '0' => 'Deactive',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_category_parents';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id', 'title', 'status',
            'created_at', 'created_by', 'updated_at', 'updated_by',
        );
        $this->_rule        = Array(
            'id'                => array(
                'type'              => 'hidden'
            ),
            'cat_id'          => array(
                'type'              => 'hidden',
            ),
            'title'             => array(
                'type'              => 'text',
                'maxlength'         => 255,
                'required'          => 'required',
                'unique'        => true
            ),
            'status'            => array(
                'type'              => 'select',
                'array_list'        => $this->arr_status,
                'allow_null'        => "true",
            ),
            'created_at'        => array(
                'type'              => 'number',
                'maxlength'         => 11,
            ),
            'created_by'        => array(
                'type'              => 'number',
                'maxlength'         => 11,
            ),
            'updated_at'        => array(
                'type'              => 'datetime',
            ),
            'updated_by'        => array(
                'type'              => 'number',
                'maxlength'         => 11,
            )
        );
        $this->_field_form  = Array(
            'id'        => 'Role ID',
            'cat_id'    => 'Mã danh mục',
            'title'     => 'Tiêu đề',
            'status'    => 'Trạng thái',
        );
        $this->_field_table = Array(
            'm.id'          => 'ID',
            'm.title'       => 'Tiêu đề',
            'm.status'      => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->order_by('m.id', 'DESC');
    }

    /**
     * ham kiem tra su ton tai cua 1 cat_id
     * @param string $cat_id
     * @param int $id
     * @return bool|null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_exist_cat_id($cat_id = '', $id = 0){
        if(!((is_string($cat_id) && trim($cat_id) != '') || (intval($cat_id) && $cat_id))){
            return NULL;
        }

        $cat_id = trim($cat_id);
        $this->setting_select();
        $this->db->where('m.cat_id', $cat_id);
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
    public function get_category(){
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $query = $this->db->get();
        //error_log(print_r($this->db->last_query(),true));
        return $query->result_array();
    }


    public function get_cat_id($cat_id){
        $this->db->select('cat_id');
        $this->db->from($this->_table_name);
        $this->db->where("cat_id", $cat_id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->cat_id;
            }
        }else {
            return false;
        }
    }

    public function check_exist_title($titel = '', $id = 0){
        if(!((is_string($titel) && trim($titel) != '') || (intval($titel) && $titel))){
            return NULL;
        }
        $titel = trim($titel);
        $this->setting_select();
        $this->db->where('m.title', $titel);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->first_row();
        } else {
            return false;
        }
    }

    public function get_list_cat_id($list_id){
        $this->db->select('cat_id');
        $this->db->from($this->_table_name);
        if (is_array($list_id)) {
            $this->db->where_in($this->_key_name, $list_id);
        } else if (intval($list_id) > 0) {
            $this->db->where($this->_key_name, $list_id);
        } else {
            $this->db->where($this->_key_name, json_encode($list_id));
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update($where, $data)
    {
        if($this->_exist_created_field){
            $data['updated_at'] = date('Y-m-d H:i:s', time());
            $data['updated_by'] = $this->user_id;
        }

        if (is_array($where)) {
            $this->db->where($where);
        } else if (intval($where) > 0) {
            $this->db->where($this->_key_name, $where);
        } else if (strlen($where) > 0) {
            $this->db->where($this->_key_name, $where);
        } else {
            return false;
        }
        if ($this->db->field_exists('editable', $this->_table_name)) {
            $this->db->where('editable', '1');
        }
        $this->db->update($this->_table_name, $data);
        return $this->db->affected_rows();
    }

    public function add($data)
    {
        if($this->_exist_created_field){
            $data['created_at'] = time();
            $data['created_by'] = $this->user_id;
        }
        $this->db->insert($this->_table_name, $data);
        return $this->db->insert_id();
    }

    public function get_cat_title_parent($cat_parents){
        $this->db->select('title');
        $this->db->from($this->_table_name);
        $this->db->where("id", $cat_parents);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->title;
            }
        }else {
            return false;
        }
    }

}