<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_transfer_out_kho extends data_base
{
    var $mark_as_complete = array(
        '0' => '- Lựa chọn giá trị -',
        'all' => 'All',
        'Cho' => 'Chờ',
        'Mot Phan' => 'Một Phần',
        'Hoan Thanh' => 'Đủ',
    );

    var $arr_status = array(
        '0' => '- Lựa chọn giá trị -',
        '1' => 'Xuất Kho',
        '2' => 'Chưa xuất kho',
    );

    var $arr_status2 = array(
        '1' => 'Xuất Kho',
        '2' => 'Chưa xuất kho',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'voxy_transfer_out_kho';
        $this->_key_name = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'name', 'adresse', 'date','product_variants', 'vendor','note',
            'tracking_nummer','tags','date_save',
            'created_at', 'created_by', 'updated_at', 'updated_by',
        );
        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),
            'name' => array(
                'type' => 'text',
                'maxlength' => 255,
                'required' => 'required',
            ),
            'date' => array(
                'type' => 'datetime',
                'maxlength' => 20,
                'required' => 'required',
            ),
            'products' => array(
                'type' => 'products',
                'required' => 'required',
            ),
            'vendor' => array(
                'type' => 'select',
                'target_model' => 'm_voxy_nha_cung_cap',
                'target_value' => 'id',
                'target_display' => 'name',
                'where_condition' => array(
                    'm.status' => 1,
                ),
            ),
            'laixe' => array(
                'type' => 'select',
                'target_model' => 'm_voxy_shippers',
                'target_value' => 'id',
                'target_display' => 'first_name',
            ),
            'note' => array(
                'type' => 'rich_editor',
            ),
            'tracking_nummer' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'tags' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'date_save' => array(
                'maxlength' => 255,
            ),
            'status' => array(
                'type' => 'select',
                'array_list' => $this->arr_status,
                'allow_null' => "true",
            ),
        );
        $this->_field_form = Array(
            'id' => 'ID',
            'name' => 'Khách hàng',
            'date' => 'Ngày xuất hàng',
            'products' => 'Chọn sản phẩm',
            //'vendor' => 'Nhà cung cấp',
            'laixe' => 'Lái xe',
            'note' => 'Ghi chú',
            //'tracking_nummer' =>'Khách hàng',
            'tags' => 'Tags',
            'status' => 'Trạng thái',
        );
        $this->_field_table = Array(
            'm.id' => 'ID',
            'm.name' => 'Khách hàng',
            'm.date' => 'Ngày Xuất',
            'm.status' => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        //$this->db->join('voxy_category AS cat', 'cat.cat_id = m.cat_id');

        if (isset($this->custom_conds["custom_where"]) && count($this->custom_conds["custom_where"]) > 0) {
            $custom_where = $this->custom_conds["custom_where"];
            $this->db->where($custom_where);
        }
        if (isset($this->custom_conds["custom_like"]) && count($this->custom_conds["custom_like"]) > 0) {
            $custom_like = $this->custom_conds["custom_like"];
            $this->db->like($custom_like);
        }
    }

    /**
     * Ham kiem tra su ton tai cua 1 barcode cua san pham
     * @param string $barcode
     * @param int $id
     * @return bool|null
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check_exist_barcode($barcode = '', $id = 0)
    {
        if (!((is_string($barcode) && trim($barcode) != '') || (intval($barcode) && $barcode))) {
            return NULL;
        }

        $barcode = trim($barcode);
        $this->setting_select();
        $this->db->where('m.barcode', $barcode);
        if ($id) {
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
     * Hàm thêm id_shopify vào bảng voxy_package với điều kiện nhập vào
     * @param Int| array $where Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$where</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$where</p>
     * @return  array list of id shopify
     */
    public function update_id_shopify($id, $id_shopify)
    {
        $data = array(
            'id_shopify' => $id_shopify,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    /**
     * Hàm xóa row với điều kiện nhập vào
     * @param Int|Array $where Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$list_id</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$list_id</p>
     * @return  Int     array of id_shopify
     */
    public function get_id_shopify($list_id)
    {
        $this->db->select('id_shopify');
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

    public function check_id_shopify($id)
    {
        $this->db->select('id');
        $this->db->from($this->_table_name);
        $this->db->where("id_shopify", $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->id;
            }
        } else {
            return false;
        }
    }

    public function get_name_from_id($id_shopify)
    {
        $this->db->select('title');
        $this->db->from($this->_table_name);
        $this->db->where("id_shopify", $id_shopify);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->title;
            }
        } else {
            return false;
        }
    }

    public function update_id_location($id, $id_location)
    {
        $data = array(
            'id_location' => $id_location,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_id_expriday($id, $expriday)
    {
        $data = array(
            'id_expriday' => $expriday,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function get_meta_location($id)
    {
        $this->db->select('id_location');
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->id_location;
            }
        } else {
            return false;
        }
    }

    public function get_meta_expri_day($id)
    {
        $this->db->select('id_expriday');
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->id_expriday;
            }
        } else {
            return false;
        }
    }

    public function update($where, $data)
    {
        if ($this->_exist_created_field) {
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
        if ($this->_exist_created_field) {
            $data['created_at'] = time();
            $data['created_by'] = $this->user_id;
        }
        $this->db->insert($this->_table_name, $data);
        return $this->db->insert_id();
    }

    public function get_product_expriday()
    {


    }

    public function get_categories($product_id)
    {
        $this->db->select('cat_id');
        $this->db->distinct();
        $this->db->from($this->_table_name);
        $this->db->where("id_shopify", $product_id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result_array() as $item) {
                return $item['cat_id'];
            }
        } else {
            return false;
        }
    }

    public function get_categories_of_product($id)
    {
        $this->db->select('cat_id');
        $this->db->distinct();
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result_array() as $item) {
                return $item['cat_id'];
            }
        } else {
            return false;
        }
    }

    public function get_id($barcode)
    {
        $this->db->select('m.id');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("m.barcode1 LIKE '%" . $barcode . "%' OR m.barcode2 LIKE '%" . $barcode . "%' ", null, false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->id;
            }
        } else {
            return false;
        }
    }

    public function getid_product_from_location($location)
    { //get id
        $this->db->select('m.id');
        $this->db->distinct();
        $this->db->from($this->_table_name . ' AS m');
        $this->db->like('m.location', $location);
        $query = $this->db->get();
        if ($query->result_array()) {
                return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_data_from_id($id)
    { // get data
        if(empty($id)){
            return false;
        }
        $id_string = implode(',', $id);
        $this->db->select('*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('m.id IN ( ' . $id_string . ' ) ', null, false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_all_product()
    {
        $this->db->select('*');
        $this->db->from('voxy_package');
        $query = $this->db->get();
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }



    public function get_sku($name){
        $this->db->select('sku2');
        $this->db->from('voxy_package');
        $this->db->like('location', $name);
        $query = $this->db->get();
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }
//dung nhung function duoi day
    public function get_products_selected($id)
    {
        $this->db->select('product_variants');
        $this->db->from('voxy_transfer_out_kho');
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->product_variants;
            }
        } else {
            return false;
        }
    }

    public function get_quantity($id_transfer){
        $this->db->select('product_variants');
        $this->db->from('voxy_transfer_out_kho');
        $this->db->where('id',$id_transfer);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->product_variants;
            }
        } else {
            return false;
        }
    }

    public function _get_status($id){
        $this->db->select('status');
        $this->db->from('voxy_transfer_out_kho');
        $this->db->where('id',$id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->status;
            }
        } else {
            return false;
        }
    }

    public function get_infor_theo_ngay($id, $ngay_giao_hang,$ngay_dat_hang)
    {
        $this->db->select('product_variants');
        $this->db->from('voxy_transfer_out_kho');

        if(isset($id['list_id']) && $id['list_id'] != ""){
            $this->db->where_in("id", $id['list_id']);
        }

        if($ngay_dat_hang != ""){
            $this->db->where("date >=", $ngay_dat_hang);
        }
        if($ngay_giao_hang != ""){
            $this->db->where("date <=", $ngay_giao_hang);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
                return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_list_table($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
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
        //xu ly cho find theo dieu kien
        if (is_array($whereCondition)) {

            if(isset($whereCondition["custom_where"]['m.ngay_dat_hang'])) {
                $ngaydathang = $whereCondition["custom_where"]["m.ngay_dat_hang"];
            }
            if(isset($whereCondition["custom_where"]['m.ngay_giao_hang'])){
                $ngaygiaohang = $whereCondition["custom_where"]['m.ngay_giao_hang'];
            }

            if(isset($ngaydathang)){
                $this->db->where("m.date >=",$ngaydathang);
            }

            if(isset($ngaygiaohang)){
                $this->db->where("m.date <=",$ngaygiaohang);
            }
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
}