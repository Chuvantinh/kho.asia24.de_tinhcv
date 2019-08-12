<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 06/14/2018
 * Time: 15:00
 *
 * @author chuvantinh1991@gmail.com
 */

class M_admin_tool_logs extends data_base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'admin_tool_logs';
        $this->_key_name            = 'id';
        $this->_exist_created_field = TRUE;
        $this->_exist_updated_field = TRUE;
        $this->_exist_deleted_field = FALSE;
        $this->_schema              = Array('id', 'group_function', 'params', 'response', 'curl_info', 'time_diff', 'created_by', 'created_at', 'updated_at', 'updated_by');
        $this->_rule        = Array(
            'id' => Array(
                'type' => 'hidden'
            ),
            'group_function' => Array(
                'type'      => 'text',
                'maxlength' => 100
            ),
            'params' => Array(
                'type' => 'text'
            ),
            'response' => Array(
                'type' => 'text'
            ),
            'time_diff' => Array(
                'type'      => 'text',
                'maxlength' => 20
            )
        );
        $this->_field_form  = Array();
        $this->_field_table = Array(
            'm.id'              => 'ID',
            'm.group_function'  => 'Group Log',
            'm.params'          => 'Params',
            'm.response'        => 'Curl Response',
            'm.curl_info'       => 'Curl Info',
            'm.error'           => 'Curl Error',
            'm.time_diff'       => 'Tổng thời gian xử lý',
            'admin_username'    => 'Người thực hiện',
            'm.created_at'      => 'Thời gian',
        );
    }

    public function setting_select()
    {
        $this->db->select("m.*, au.user_name AS admin_username");
        $this->db->from($this->_table_name . " AS m");
        $this->db->join('admin_users AS au', 'au.id = m.created_by', 'LEFT');

        if (isset($this->custom_conds["custom_where"]) && count($this->custom_conds["custom_where"])) {
            $custom_where = $this->custom_conds["custom_where"];
            if(isset($custom_where['is_error'])) {
                if($custom_where['is_error'] == 'YES') {
                    $this->db->where('LENGTH(m.error) > 0', NULL);
                } else if($custom_where['is_error'] == 'NO') {
                    $this->db->where('LENGTH(m.error) = 0', NULL);
                }
                unset($custom_where['is_error']);
            }
            if(isset($custom_where['used_admin'])) {
                if($custom_where['used_admin'] == 'ADMIN') {
                    $this->db->where('m.created_by > 0', NULL);
                } else if($custom_where['used_admin'] == 'CRONJOB') {
                    $this->db->where('m.created_by = -1', NULL);
                }
                unset($custom_where['used_admin']);
            }

            $this->db->where($custom_where);
        }
        if (isset($this->custom_conds["custom_like"]) && count($this->custom_conds["custom_like"])) {
            $custom_like = $this->custom_conds["custom_like"];

            $this->db->like($custom_like);
        }

        $this->db->order_by('m.id', 'DESC');
    }

    public function get_list_group_function()
    {
        return $this->db->select("m.group_function")
            ->from($this->_table_name . " AS m")
            ->group_by('m.group_function')
            ->get()
            ->result();
    }
}