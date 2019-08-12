<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_admin_funtions
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_functions extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_functions';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = true;
        $this->_schema      = Array(
            'id', 'name', 'controller', 'action', 'status', 'description', 'editable',

            'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'name'          => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
                'unique'        => true
            ),
            'controller'    => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
            ),
            'action'        => array(
                'type'          => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
            ),
            'description'   => array(
                'type'          => 'textarea',
                'maxlength'     => 500,
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form  = Array(
            'id'            => 'Function ID',
            'name'          => 'Tên Function',
            'controller'    => 'Controller',
            'action'        => 'Action',
            'description'   => 'Mô tả',
            'status'        => 'Trạng thái'
        );
        $this->_field_table = Array(
            'm.id'          => 'Role ID',
            'm.name'        => 'Nhóm quyền',
            'm.controller'  => 'Controller',
            'm.action'      => 'Action',
            'm.description' => 'Mô tả',
            'm.status'      => 'Trạng thái',
            'm.created_at'  => 'Thời gian tạo'
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL);
    }

    /**
     * @param int $role_id
     * @return null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_action_by_role_id($role_id = 0)
    {
        if(!$role_id){
            return null;
        }

        return $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->join('admin_role_function AS rf', 'm.id = rf.function_id')
            ->join('admin_roles AS role', 'role.id = rf.role_id')
            ->where('role.id', $role_id)
            ->where('role.status', 1) // role chua suspend
            ->where('m.status', 1)  // function chua suspend
            ->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL)
            ->get()
            ->result();
    }

    /**
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_full_list_action()
    {
        return $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->where('m.status', 1) // function chua suspend
            ->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL) // function chua xoa
            ->get()
            ->result();
    }

    /**
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_action_by_role_admin()
    {
        return $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->where('m.id', 1) // id = 1 full tat ca cac quyen
            ->get()
            ->result();
    }

    /**
     * @param $user_id
     * @return null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_role_id_by_user_id($user_id)
    {
        if(!$user_id){
            return null;
        }
        $arr_role = $this->db->select('role.id')
            ->from($this->_table_name . ' AS m')
            ->join('admin_role_function AS rf', 'm.id = rf.function_id')
            ->join('admin_roles AS role', 'role.id = rf.role_id')
            ->join('admin_user_role AS ur', 'ur.role_id = role.id')
            ->where('ur.user_id', $user_id)
            ->where('role.status', 1)
            ->where('m.status', 1)
            ->where('(m.deleted_at IS NULL OR m.deleted_at = "")', NULL)
            ->get()
            ->result();
        if (is_array($arr_role)) {
            return $arr_role[0]->id;
        }
        return null;
    }

    /**
     * @param null $controller
     * @return null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_id_action_by_controller($controller = null)
    {
        if (!$controller) {
            return null;
        }
        return $this->db->select('m.id')
            ->from($this->_table_name . ' AS m')
            ->where('m.controller', $controller)
            ->get()
            ->result();
    }

    /**
     * @param $controller
     * @param $method
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_exist_by_controller_action($controller, $method)
    {
        return $this->db->select('m.*')
            ->from($this->_table_name . ' AS m')
            ->where('m.controller', $controller) // function chua suspend
            ->where('m.action', $method) // function chua suspend
            ->get()
            ->num_rows();
    }
}