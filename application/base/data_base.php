<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Lop chung class model
 * Class data_base
 *
 * @author chuvantinh1991@gmail.com
 */
abstract class data_base extends CI_Model
{
    /**
     * Mang luu bien session de tim kiem du lieu
     * @var array
     */
    var $custom_conds = array(
        'custom_where'  => array(),
        'custom_like'   => array(),
    );

    /** Mang du lieu trang thai cua table
     * @var array
     */
    var $arr_status = array(
        '1' => 'ACTIVE',
        '0' => 'DEACTIVE'
    );

    /**
     * @var Object -- Thông tin chung của user đã đăng nhập (chưa đăng nhập, $user_info = NULL)
     */
    var $USER               = null;

    /**
     * ID User dang nhap
     * @var int
     */
    var $user_id            = -1;

    /**
     * Tên bảng mà model đang tương tác
     * @var String
     */
    protected $_table_name = "";

    /**
     * Trường key của model đang tương tác <thuong la ID cua ban ghi>
     * @var String
     */
    protected $_key_name = "";

    /**
     * Mảng cấu trúc schema của table
     * @var Array
     */
    protected $_schema = Array();

    /**
     * Mảng cấu trúc luật các trường của table,
     * @var Array
     */
    protected $_rule = Array();

    /**
     * Chuoi convert tu mang rule ve string
     * @var string
     */
    protected $_string_rule = '';

    /**
     * Mảng nhãn hiển thị trong form, cũng là mảng tạo form
     * @var Array
     */
    protected $_field_form = Array();

    /**
     * Mảng các trường hiển thị trên bảng quản lý
     * @var Array
     */
    protected $_field_table = Array();

    /**
     * Bien de xac dinh bang du lieu co truong(created_at va created_by)
     * Nham tu cap nhat them thoi gian(int(11)) va id nguoi tao du lieu
     * @var bool : true - co, false - khong
     */
    protected $_exist_created_field = false;

    /**
     * Bien de xac dinh bang du lieu co truong(updated_at va updated_by)
     * Nham tu cap nhat them thoi gian(timestamp) va id nguoi cap nhat du lieu
     * @var bool : true - co, false - khong
     */
    protected $_exist_updated_field = false;

    /**
     * Bien de xac dinh bang du lieu co truong(deleted_at va deleted_by)
     * Nham xa dinh se xoa vat ly hay xoa logic
     * neu true -> xoa logic va cap nhat thoi gian(int(11) va id nguoi xoa logic
     * @var bool : true - xoa logic, false - xoa vat ly
     */
    protected $_exist_deleted_field = false;

    public function __construct()
    {
        parent::__construct();
        $this->setting_table();

        $this->load->library("session");
        if($this->session->userdata('USER')){
            $this->USER = $this->session->userdata('USER');
        }
        $this->user_id = (isset($this->USER->id) ? $this->USER->id : -1);
    }

    abstract function setting_table();

    /**
     * Hàm cài đặt các trường lấy ra trong câu truy vấn get_one và get_list
     */
    public function setting_select()
    {
        $this->db->select("m.*");
        $this->db->from($this->_table_name . " AS m");
    }

    /**
     * Hàm lấy thông tin để tạo form
     * @return Array Mảng các mảng chứa thông tin để tạo form
     */
    public function get_key_name()
    {
        return $this->_key_name;
    }

    /**
     * Hàm lấy thông tin để tạo form
     * @return Array Mảng các mảng chứa thông tin để tạo form
     */
    public function get_table_name()
    {
        return $this->_table_name;
    }

    /**
     * Hàm lấy thông tin về rule của các trường
     * @return Array Mảng các rule
     */
    public function get_rule()
    {
        return $this->_rule;
    }

    /**
     * Ham chuyen doi mang rule ve string
     * @param array $rule   mang chua cac rule
     * @return string       convert rule array -> string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_display_rule($rule = array())
    {
        $string = '';
        if ($rule && count($rule)) {
            foreach ($rule as $key => $value) {
                if (!is_array($value)) {
                    if(is_bool($value) && !$value) {
                        $value = '0';
                    }
                    $string .= $key . '=' . $value . ' ';
                }
            }
        }
        return $string;
    }

    /**
     * Hàm lấy thông tin schema của bảng
     * @return Array Mảng schema của bảng
     */
    public function get_schema()
    {
        return $this->_schema;
    }

    /**
     * Hàm lấy thông tin label của các trường
     * @return Array Mảng các label tương ứng với các trường trong Schema
     */
    public function get_field_form()
    {
        return $this->_field_form;
    }

    /**
     * Ham set lai field form, khi co su thay doi
     *
     * @param array $field_form     Mang field form can thay
     *
     * @author chuvantinh1991@gmail.com
     */
    public function set_field_form($field_form = array())
    {
        $this->_field_form = $field_form;
    }

    /**
     * Hàm lấy thông tin schema của bảng
     * @return Array Mảng schema của bảng
     */
    public function get_field_table()
    {
        return $this->_field_table;
    }

    /**
     * Hàm lấy thông tin để tạo form
     * @return Array Mảng các mảng chứa thông tin để tạo form
     */
    public function get_form()
    {
        $data = Array(
            'schema'        => $this->_schema,
            'rule'          => $this->_rule,
            'field_form'    => $this->_field_form,
            'field_table'   => $this->_field_table,
        );

        return $data;
    }

    /**
     * Hàm lấy thông tin của 1 row với điều kiện đầu vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là $key=$where[$key]</p>
     *                          <p>Nếu là Int thì điều kiện là $this->key=$where</p>
     * @param string    $type   Kiểu dữ liệu trả về: <b>'array'</b> hoặc <b>'object'</b>
     * @return Object   Object có cấu trúc tương tự $schema
     */
    public function get_one($where, $type = 'object')
    {
        $this->setting_select();
        if (is_array($where)) {
            $this->db->where($where);
        } else {
            $this->db->where("m." . $this->_key_name, $where);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            if ($type == 'array' || $type == 'object') {
                return $query->first_row($type);
            } else {
                return $query->first_row();
            }
        } else {
            return null;
        }
    }

    /**
     *
     * @param Int|Array     $where  Điều kiện dạng tùy biến int hoặc Array.
     *                              <p>Nếu là Array thì điều kiện là $key=$where[$key]</p>
     *                              <p>Nếu là Int thì điều kiện là $this->key=$where</p>
     * @param Int           $limit  Số item lấy ra (LIMIT trong SQL)
     * @param Int           $post   Vị trí bắt đầu lấy ra (POST trong SQL)
     * @param String        $order  Điều kiện dạng tùy biến String dạng 'title DESC, name ASC'
     * @param int           $total  Tong so ban ghi, bo qua limit
     * @return Array        Mảng các object tương tự $schema
     */
    public function get_list($where = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS NULL", false);
        $this->setting_select();
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        } else if (intval($where) > 0) {
            $this->db->where("m." . $this->_key_name, $where);
        }
        if ($limit) {
            $this->db->limit($limit, $post);
        }
        if ($order) {
            $this->db->order_by($order);
        }
        $query = $this->db->get();
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;
        // error_log(print_r($this->db->last_query(),true));
        return $query->result();
    }

    /**
     * Ham lay du lieu theo 1 danh sach co dieu kien
     * @param null $where
     * @param int $limit
     * @param int $post
     * @param null $order
     * @param int $total    tong so ban ghi, bo qua limit
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_in($where = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS NULL", false);
        $this->setting_select();
        if (is_array($where)) {
            $this->db->where_in("m." . $this->_key_name, $where);
        } else if (intval($where) > 0) {
            $this->db->where("m." . $this->_key_name, $where);
        }
        if ($limit) {
            $this->db->limit($limit, $post);
        }
        if ($order) {
            $this->db->order_by($order);
        }
        $query = $this->db->get();
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;
        return $query->result();
    }

    /**
     * Ham lay du lieu va tong so ban ghi thoa man dieu kien
     *
     * @param string $search_text   Từ khóa tìm kiếm
     * @param null $whereCondition  Mảng các trường cần tìm
     * @param int $limit            Số item lấy ra (LIMIT trong SQL)
     * @param int $post             Vị trí bắt đầu lấy ra (POST trong SQL)
     * @param null $order           Điều kiện dạng tùy biến String dạng 'title DESC, name ASC'
     * @param int $total            Tổng số bản ghi sau khi lấy được dữ liệu (loại bỏ limit trong sql)
     * @return Array                Mảng các object tương tự $schema
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_table($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS NULL", false);
        $this->setting_select();
        $where          = $this->_field_table;
        //$where          = $this->_field_search;
        $search_text    = trim($search_text);
        if (is_array($where) && strlen($search_text)) {
            $like_agr       = Array();
            $like_search    = $this->db->escape_like_str($search_text);
            foreach ($where as $key => $value) {
                $rule   = isset($this->_rule[$key]) ? $this->_rule[$key] : FALSE;
                $x_key  = $key;

                if ($rule) {
                    $temp       = $rule; // Tách các rule
                    $list_rule  = Array();
                    foreach ($temp as $rule_item) {
                        $rule_piece = explode("=", $rule_item);
                        if (sizeof($rule_piece) < 2) {
                            $list_rule[$rule_piece[0]] = NULL;
                        } else {
                            $list_rule[$rule_piece[0]] = $rule_piece[1];
                        }
                    }
                    if (isset($list_rule['type']) && $list_rule['type'] == "datetime") {
                        $x_key = " DATE_FORMAT(" . $x_key . ", '%d-%m-%Y %H:%i:%s')";
                    }
                    if (isset($list_rule['real_field'])) {/* Nếu rule có real_field thì lấy key ở real_fiel */
                        $x_key = $list_rule['real_field'];
                    }
                    if (isset($list_rule['disable_search'])) {
                        $x_key = FALSE;
                    }
                }
                if ($x_key !== FALSE) {
                    $like_agr[] = $x_key . " LIKE '%" . $like_search . "%'";
                }
            }
            if (count($like_agr)) {
                $this->db->where(" ( " . implode(" OR ", $like_agr) . " ) ", NULL, false);
            }
        }
        if (is_array($whereCondition)) {
            $this->db->where($whereCondition);
        } else if (intval($whereCondition) > 0) {
            $this->db->where("m." . $this->_key_name, $whereCondition);
        }
        if ($limit) {
            $this->db->limit($limit, $post);
        }
        if ($order && strlen($order)) {
            $this->db->order_by($order);
        } else {
            $this->db->order_by("m." . $this->_key_name, "DESC");
        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;

        return $query->result();
    }

    public function get_list_table_voxy_package($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS NULL", false);
        $this->setting_select();
        //$where          = $this->_field_table;
        $where          = $this->_field_search;
        $search_text    = trim($search_text);

        if (is_array($where) && strlen($search_text)) {
            $like_agr       = Array();
            $like_search    = $this->db->escape_like_str($search_text);

            foreach ($where as $key => $value) {
                $rule   = isset($this->_rule[$key]) ? $this->_rule[$key] : FALSE;
                $x_key  = $key;

                if ($rule) {
                    $temp       = $rule; // Tách các rule
                    $list_rule  = Array();
                    foreach ($temp as $rule_item) {
                        $rule_piece = explode("=", $rule_item);
                        if (sizeof($rule_piece) < 2) {
                            $list_rule[$rule_piece[0]] = NULL;
                        } else {
                            $list_rule[$rule_piece[0]] = $rule_piece[1];
                        }
                    }
                    if (isset($list_rule['type']) && $list_rule['type'] == "datetime") {
                        $x_key = " DATE_FORMAT(" . $x_key . ", '%d-%m-%Y %H:%i:%s')";
                    }
                    if (isset($list_rule['real_field'])) {/* Nếu rule có real_field thì lấy key ở real_fiel */
                        $x_key = $list_rule['real_field'];
                    }
                    if (isset($list_rule['disable_search'])) {
                        $x_key = FALSE;
                    }
                }
                if ($x_key !== FALSE) {
                    $like_agr[] = $x_key . " LIKE '%" . $like_search . "%'";
                }
            }
            if (count($like_agr)) {
                $this->db->where(" ( " . implode(" OR ", $like_agr) . " ) ", NULL, false);
            }
        }

        if (is_array($whereCondition)) {
            $this->db->where($whereCondition);
        } else if (intval($whereCondition) > 0) {
            $this->db->where("m." . $this->_key_name, $whereCondition);
        }

        if ($limit) {
            $this->db->limit($limit, $post);
        }

        if ($order && strlen($order)) {
            $this->db->order_by($order);
        } else {
            $this->db->order_by("m." . $this->_key_name, "DESC");
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;

        return $query->result();
    }

    //cai nay cho table order vi seach tieng viet sai ko được chuvantinh
    public function get_list_table_orders($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS NULL", false);
        $this->setting_select();
        $where          = $this->_field_table;
        $search_text    = trim($search_text);
        if (is_array($where) && strlen($search_text)) {
            $like_agr       = Array();
            $like_search    = $this->db->escape_like_str($search_text);
            foreach ($where as $key => $value) {
                $rule   = isset($this->_rule[$key]) ? $this->_rule[$key] : FALSE;
                $x_key  = $key;
                if ($rule) {
                    $temp       = $rule; // Tách các rule
                    $list_rule  = Array();
                    foreach ($temp as $rule_item) {
                        $rule_piece = explode("=", $rule_item);
                        if (sizeof($rule_piece) < 2) {
                            $list_rule[$rule_piece[0]] = NULL;
                        } else {
                            $list_rule[$rule_piece[0]] = $rule_piece[1];
                        }
                    }
                    if (isset($list_rule['type']) && $list_rule['type'] == "datetime") {
                        $x_key = " DATE_FORMAT(" . $x_key . ", '%d-%m-%Y %H:%i:%s')";
                    }
                    if (isset($list_rule['real_field'])) {/* Nếu rule có real_field thì lấy key ở real_fiel */
                        $x_key = $list_rule['real_field'];
                    }
                    if (isset($list_rule['disable_search'])) {
                        $x_key = FALSE;
                    }
                }
                if ($x_key !== FALSE) {
                    //$like_agr[] = $x_key . " REGEXP BINARY '^" . $like_search . "'";//day cho nay day, cai nay nhanh nhé bị sai, chữ  e bị biến đổi
                    $like_agr[] = $x_key . " LIKE CONCAT('%', CONVERT('".$like_search."', BINARY),'%') ";//day cho nay day
                }
            }
            if (count($like_agr)) {
                $this->db->where(" ( " . implode(" OR ", $like_agr) . " ) ", NULL, false);
            }
        }
        if (is_array($whereCondition)) {
            $this->db->where($whereCondition);
        } else if (intval($whereCondition) > 0) {
            $this->db->where("m." . $this->_key_name, $whereCondition);
        }
        if ($limit) {
            $this->db->limit($limit, $post);
        }
        if ($order && strlen($order)) {
            $this->db->order_by($order);
        } else {
            $this->db->order_by("m." . $this->_key_name, "DESC");
        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;

        return $query->result();
    }

    /**
     * Hàm lấy dữ liệu trong các select (liên kết 1-*)
     * @param String    $selectString  Chuỗi select
     * @param Array     $where  Mảng các trường cần tìm
     * @param Int       $limit  Số item lấy ra (LIMIT trong SQL)
     * @param Int       $post   Vị trí bắt đầu lấy ra (POST trong SQL)
     * @param String    $order  Điều kiện dạng tùy biến String dạng 'title DESC, name ASC'
     * @param int       $total  Tong so ban ghi, bo qua limit
     * @return Array    Mảng các object tương tự $schema
     */
    public function get_list_option($selectString = NULL, $where = Array(), $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS NULL", false);
        if ($selectString && strlen($selectString)) {
            $this->db->select($selectString);
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
        $this->db->from($this->_table_name . " AS m");
        $query = $this->db->get();
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;
        return $query->result();
    }

    /**
     * Hàm thêm một row
     * @param   Array   $data   Là mảng có cấu trúc tương tự $schema
     * @return  Int     Id của row vừa được thêm vào
     */
    public function add($data)
    {
        if($this->_exist_created_field){
            $data['created_at'] = time();
            $data['created_by'] = $this->user_id;
        }
        $this->db->insert($this->_table_name, $data);
        return $this->db->insert_id();
    }

    /**
     * Hàm thêm nhiều row cùng lúc trong 1 câu truy vấn
     * @param   Array   $data   Là MẢNG CÁC MẢNG có cấu trúc tương tự $schema
     * @return  Int     Số row được thêm vào
     */
    public function add_muti($data)
    {
        if($this->_exist_created_field){
            foreach ($data as $key => $one_data){
                $one_data['created_at'] = time();
                $one_data['created_by'] = $this->user_id;
                $data[$key] = $one_data;
            }
        }

        $this->db->insert_batch($this->_table_name, $data);
        return $this->db->affected_rows();
    }

    /**
     * Hàm Cập nhập thông tin một row với điều kiện và dữ liệu nhập vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là $key=$where[$key]</p>
     *                          <p>Nếu là Int thì điều kiện là $this->key=$where</p>
     * @param Array     $data   Dữ liệu update dạng mảng tương tự mảng $schema
     * @return Int      Số row được update
     */
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

    /**
     * Hàm Cập nhập thông tin một số row với điều kiện và dữ liệu nhập vào
     * @param $where
     * @param $data
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    public function update_list($where, $data)
    {
        if($this->_exist_created_field){
            $data['updated_at'] = date('Y-m-d H:i:s', time());
            $data['updated_by'] = $this->user_id;
        }

        if (is_array($where)) {
            $this->db->where_in($this->_key_name, $where);
        }
        if ($this->db->field_exists('editable', $this->_table_name)) {
            $this->db->where('editable', '1');
        }
        $this->db->update($this->_table_name, $data);
        return $this->db->affected_rows();
    }

    /**
     * Hàm xóa row với điều kiện nhập vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là $key=$where[$key]</p>
     *                          <p>Nếu là Int thì điều kiện là $this->key=$where</p>
     * @return  Int     Số row bị xóa
     */
    public function delete_by_custom($where)
    {
        if (is_array($where)) {
            $this->db->where($where);
        } else if (intval($where) > 0) {
            $this->db->where($this->_key_name, $where);
        } else {
            return false;
        }
        $this->db->delete($this->_table_name);
        return $this->db->affected_rows();
    }

    /**
     * Hàm xóa row với điều kiện nhập vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$where</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$where</p>
     * @return  Int     Số row bị xóa
     */
    public function delete_by_id($where)
    {
        if($this->_exist_deleted_field){
            $data['deleted_at'] = date('Y-m-d H:i:s', time());
            $data['deleted_by'] = $this->user_id;
            return $this->delete_logic_by_id($where, $data);
        } else {
            if (is_array($where)) {
                $this->db->where_in($this->_key_name, $where);
            } else if (intval($where) > 0) {
                $this->db->where($this->_key_name, $where);
            } else {
                $this->db->where($this->_key_name, json_encode($where));
            }
            if ($this->db->field_exists('editable', $this->_table_name)) {
                $this->db->where('editable', '1');
            }
            $this->db->delete($this->_table_name);
            return $this->db->affected_rows();
        }
    }

    /**
     * Hàm xóa logic row với điều kiện nhập vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$where</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$where</p>
     * @param array     $data   Mang du lieu can update
     * @return  Int     Số row bị xóa logic
     */
    public function delete_logic_by_id($list_id, $data){
        if (is_array($list_id)) {
            $this->db->where_in($this->_key_name, $list_id);
        } else if (intval($list_id) > 0) {
            $this->db->where($this->_key_name, $list_id);
        } else {
            $this->db->where($this->_key_name, json_encode($list_id));
        }
        if ($this->db->field_exists('editable', $this->_table_name)) {
            $this->db->where('editable', '1');
        }
        $this->db->update($this->_table_name, $data);
        return $this->db->affected_rows();
    }

    /**
     * Hàm kiểm tra xem có thể edit hay ko với điều kiện nhập vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$where</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$where</p>
     * @return  Int     1:có thể edit, 0: ko thể edit
     */
    public function is_editable($where)
    {
        if ($this->db->field_exists('editable', $this->_table_name)) {
            $this->db->select('editable');
            $this->db->where('editable', '1');
            if (is_array($where)) {
                $this->db->where($where);
            } else if (intval($where) > 0) {
                $this->db->where($this->_key_name, $where);
            }
            $this->db->from($this->_table_name);
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                return $result->first_row()->editable;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    /**
     * Hàm kiểm tra xem đã có trường nào có giá trị sắp thêm chưa
     * @param $key      trường cần kiểm tra
     * @param $value    Giá trị cần kiểm tra
     * @param int $id   ID cua ban ghi can check trung
     * @return bool     ID của row trùng hoặc false nếu không trùng
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_existed($key, $value, $id = 0)
    {
        $this->db->select($this->_key_name);
        $this->db->from($this->_table_name);
        $this->db->where($key, $value);
        $this->db->where($this->_key_name . ' !=', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $temp = $this->_key_name;
            return $query->first_row()->$temp;
        } else {
            return false;
        }
    }
}