<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class M_admin_accounts extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = "admin_users";
        $this->_key_name            = "id";
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = true;
        $this->_schema      = Array(
            'id', 'user_name', 'full_name', 'password', 'email', 'phone', 'remember_token', 'status', 'editable',

            'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'
        );
        $this->_rule        = Array(
            'id'                => array(
                'type'              => 'hidden'
            ),
            'role_id'           => array(
                'type'              => 'select',
                'allow_null'        => false,
                'target_model'      => 'm_admin_roles',
                'target_value'      => 'id',
                'target_display'    => 'name',
                'where_condition'   => array(
                    'm.status'          => 1,
                ),
            ),
            'user_name'         => array(
                'type'              => 'text',
                'maxlength'         => 255,
                'required'          => 'required',
                'unique'            => true
            ),
            'full_name'         => array(
                'type'              => 'text',
                'maxlength'         => 255,
                'required'          => 'required'
            ),
            'password'          => array(
                'type'              => 'password',
                'maxlength'         => 255,
                'minlength'         => 6,
                'required'          => 'required'
            ),
            '_password'         => array(
                'type'              => 'password',
                'maxlength'         => 255,
                'minlength'         => 6,
                'required'          => 'required',
                'recheck'           => 'password'
            ),
            'email'             => array(
                'type'              => 'text',
                'maxlength'         => 255,
                'required'          => 'required',
                'is_email'          => 1,
                'allow_null'        => false
            ),
            'phone'             => array(
                'type'              => 'text',
                'maxlength'         => 30
            ),
            'status'            => array(
                'type'              => 'select',
                'array_list'        => $this->arr_status,
                'allow_null'        => "true",
            )
        );
        $this->_field_form  = Array(
            'id'        => 'Admin ID',
            'user_name' => 'Tên đăng nhập',
            'full_name' => 'Tên hiển thị',
            'password'  => 'Mật khẩu',
            '_password' => 'Nhập lại mật khẩu',
            'email'     => 'Email',
            'phone'     => 'Số điện thoại',
            'role_id'   => 'Phân quyền',
        );
        $this->_field_table = Array(
            'm.id'          => 'Admin ID',
            'm.user_name'   => 'Tên đăng nhập',
            'm.full_name'   => 'Tên hiển thị',
            'role_name'     => 'Quyền tài khoản',
            'role_status'   => 'Trạng thái quyền',
            'm.email'       => 'Email',
            'm.phone'       => 'Số điện thoại',
            'm.status'      => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*, role.id AS role_id, role.name AS role_name, role.status AS role_status');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('admin_user_role AS ur', 'ur.user_id = m.id', 'left');
        $this->db->join('admin_roles AS role', 'role.id = ur.role_id', 'left');
        $this->db->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL); // user chua xoa

        if (isset($this->custom_conds["custom_where"]) && count($this->custom_conds["custom_where"])) {
            $custom_where = $this->custom_conds["custom_where"];

            $this->db->where($custom_where);
        }
        if (isset($this->custom_conds["custom_like"]) && count($this->custom_conds["custom_like"])) {
            $custom_like = $this->custom_conds["custom_like"];

            $this->db->like($custom_like);
        }
    }

    /**
     * Ham lay danh sach cac permission cua 1 user
     * @param int $user_id
     * @return null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_user_role_data($user_id = 0){
        if(!$user_id){
            return null;
        }
        $this->db->select('m.id, m.user_name, m.email, role.name AS role_name, func.name AS func_name, func.controller AS func_controller, func.action AS func_action');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('admin_user_role AS ur', 'ur.user_id = m.id', 'left');
        $this->db->join('admin_roles AS role', 'role.id = ur.role_id', 'left');
        $this->db->join('admin_role_function AS rf', 'rf.role_id = role.id');
        $this->db->join('admin_functions AS func', 'func.id = rf.function_id');
        $this->db->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL); // user chua xoa
        $this->db->where('m.id', $user_id); // User ID
        $this->db->where('m.status', 1); // user actived
        $this->db->where('role.status', 1); // role chua suspend
        $this->db->where('func.status', 1); // function chua suspend
        $this->db->where('(func.deleted_at IS NULL OR func.deleted_at = "")', NULL); // function chua xoa
        $query = $this->db->get();
        // error_log(print_r($this->db->last_query(),true));
        return $query->result();
    }
	
	public function get_list_option($select_string = NULL, $where = Array(), $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
		if ($select_string && strlen($select_string)) {
            $this->db->select($select_string);
        } else {
            return Array();
        }
		if (is_array($where)) {
            $this->db->where($where);
        } else if (intval($where) > 0) {
            $this->db->where($this->_key_name, $where);
        }
        if ($limit) {
            $this->db->limit($limit, $post);
        }
        if ($order) {
            $this->db->order_by($order);
        }
		$this->db->join('admin_roles AS role', 'role.id = m.role_id', 'left');
		$this->db->where('role.value >=', (isset($this->USER->role_value) ? $this->USER->role_value : 0));
        $this->db->from($this->_table_name . " AS m");
        $query = $this->db->get();
        $query = $this->db->get();
        return $query->result();
		
	}

    /**
     * Ham kiem tra trong DB co ton tai User de dang nhap hay khong
     * @param string $user_name
     * @param string $password
     * @return null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_login($user_name = '', $password = '')
    {
        if(!$user_name || !$password){
            return NULL;
        }

        $this->setting_select();
        $this->db->where('m.user_name', $user_name);
        $this->db->where('m.password', $password);
        $this->db->where('m.status', 1); // user actived
        $this->db->where('role.status', 1); // role chua suspend
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->first_row();
        } else {
            return NULL;
        }
    }
}