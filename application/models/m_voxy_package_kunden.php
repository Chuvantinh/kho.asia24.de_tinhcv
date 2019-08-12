<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package_kunden extends data_base
{
    var $arr_status = array(
        '0' => 'Active',
        '1' => 'Deactive',
    );

    var $data_sort_debt = array(
            'debt_short' => 'Nợ Gối',
            'debt_long' => 'Nợ Tháng',
        );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'voxy_package_kunden';
        //$this->_key_name = 'id';
        $this->_key_name = 'conno';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'id_customer', 'addresses', 'default_address',
            'email', 'first_name', 'last_name', 'last_order_id', 'last_order_name',
            'metafield', 'multipass_identifier', 'note', 'orders_count',
            'phone', 'state','debt', 'tags', 'total_spent', 'verified_email',
            'created_at', 'created_by', 'updated_at', 'updated_by', 'status',
        );
        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),
            'id_customer' => array(
                'type' => 'hidden'
            ),
            'addresses' => array(
                'type' => 'addresses',
                'allow_null' => "true",
            ),
            'default_address' => array(
                'type' => 'default_address',
                'allow_null' => "true",
            ),
            'email' => array(
                'type' => 'text',
                'maxlength' => 50,
            ),
            'first_name' => array(
                'type' => 'text',
                'maxlength' => 50,
                'required' => 'required',
            ),
            'last_name' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'last_order_id' => array(
                'type' => 'int',
                'maxlength' => 11,
            ),
            'last_order_name' => array(
                'type' => 'int',
                'maxlength' => 11,
            ),
            'metafield' => array(
                'type' => 'metafield',
                'allow_null' => true,
            ),
            'multipass_identifier' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'note' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'orders_count' => array(
                'type' => 'int',
            ),
            'state' => array(
                'type' => 'select',
                'array_list' => $this->arr_status,
                'allow_null' => "true",
            ),

            'debt' => array(
                'type' => 'select',
                'array_list' => $this->data_sort_debt,
                'allow_null' => "true",
            ),

            'tags' => array(
                'type' => 'tags',
                'allow_null' => true,
            ),
            'total_spent' => array(
                'type' => 'int',
            ),
            'verified_email' => array(
                'type' => 'select',
                'array_list' => $this->arr_status,
                'allow_null' => "true",
            ),
            'created_at' => array(
                'type' => 'number',
                'maxlength' => 11,
            ),
            'created_by' => array(
                'type' => 'number',
                'maxlength' => 11,
            ),
            'updated_at' => array(
                'type' => 'datetime',
            ),
            'updated_by' => array(
                'type' => 'number',
                'maxlength' => 11,
            )
        );
        // form khi click them
        $this->_field_form = Array(
            'id' => 'ID',
            'id_customer' => 'ID Khách hàng',
            'first_name' => 'Tên',
            'last_name' => 'Họ',
            'default_address' => 'Địa chỉ mặc định',
            'email' => 'Email',
            'multipass_identifier' => 'Nick name',
            'note' => 'Ghi Chú',
            'state' => 'Trạng thái',
            'debt' => 'Nợ Gối / Tháng',
        );
        //table hien thi du lieu san pham
        $this->_field_table = Array(
            'm.id' => 'ID',
            'm.first_name' => 'Tên',
            //'m.last_name' => 'Họ',
            //'m.default_address' => 'Địa chỉ',
            //'m.email' => 'Email',
            'm.multipass_identifier' => 'Nick Name',
            'm.soluong' => 'Số lượng',
            'm.tongtien' => 'Tổng tiền',
            'm.conno' => 'Còn nợ',
            //'m.note' => 'Ghi Chú',
            //'m.state' => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        //$this->db->order_by('m.id', 'DESC');
    }

    public function update_id_customer($id, $id_customer){
        $data = array(
            'id_customer' => $id_customer,
        );
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    /**
     * Hàm xóa row với điều kiện nhập vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$list_id</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$list_id</p>
     * @return  Int     array of id_shopify
     */
    public function get_id_customer($list_id){
        $this->db->select('id_customer');
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

    //check xem co id customer nay trong database hay chua
    public function get_id_customer2($id_customer){
        $this->db->select('id_customer');
        $this->db->from($this->_table_name);
        $this->db->where("id_customer", $id_customer);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id_customer;
            }
        }else {
            return false;
        }
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

    public function get_default_address($custommer_id){
        $this->db->select('default_address');
        $this->db->from($this->_table_name);
        $this->db->where("id_customer", $custommer_id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->default_address;
            }
        }else {
            return false;
        }
    }

    public function get_keyword($customer_id){
        $this->db->select('multipass_identifier');
        $this->db->from($this->_table_name);
        $this->db->where("id_customer", $customer_id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->multipass_identifier;
            }
        }else {
            return false;
        }
    }

    public function get_id_khachhang($customer_id){
        $this->db->select('id');
        $this->db->from($this->_table_name);
        $this->db->where("id_customer", $customer_id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id;
            }
        }else {
            return false;
        }
    }

    public function get_all_kunden()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->order_by('m.id', 'ASC');
        $query = $this->db->get();
        if ($query->result_array()){
                return $query->result_array();
        }else {
            return false;
        }
    }

    public function get_all_order($customer_id, $date_liefer, $date_liefer_end){
        $this->db->select('customer_id');
        $this->db->select('order_number');
        $this->db->select('total_price');
        $this->db->select('tongtien_no');
        $this->db->select('shipped_at');

        $this->db->from('voxy_package_orders AS m');
        $this->db->order_by('m.id', 'ASC');
        $this->db->where('customer_id',$customer_id);
        $this->db->where('tongtien_no !=',0);
        $this->db->where('tongtien_no is not null',null,false);

        if($date_liefer != ""){
            $this->db->where('shipped_at >=',$date_liefer);
        }
        if($date_liefer_end != ""){
            $this->db->where('shipped_at <=',$date_liefer_end);
        }

        $query = $this->db->get();
            //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function get_all_order_all($customer_id, $date_liefer, $date_liefer_end){
        $this->db->select('customer_id');
        $this->db->select('order_number');
        $this->db->select('total_price');
        $this->db->select('tongtien_no');
        $this->db->select('shipped_at');

        $this->db->from('voxy_package_orders AS m');
        $this->db->order_by('m.id', 'ASC');
        $this->db->where('customer_id',$customer_id);
        $this->db->where('tongtien_no !=',0);
        $this->db->where('tongtien_no is not null',null,false);

        if($date_liefer != ""){
            $this->db->where('shipped_at >=',$date_liefer);
        }
        if($date_liefer_end != ""){
            $this->db->where('shipped_at <=',$date_liefer_end);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            //add sum of same custommer
            $data = array();
            $chiso_remove = array();
            foreach ($query->result_array() as $key => $item2){
                foreach ($query->result_array() as $key2 => $item3){
                    if ($key2 > $key) {
                        if ($item2['customer_id'] == $item3['customer_id']) {//cong tong theo laixe
                            $item2['total_price'] = $item2['total_price'] + $item3['total_price'];
                            $item2['tongtien_no'] = $item2['tongtien_no'] + $item3['tongtien_no'];
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
                $data[] = $item2;
            }

            foreach ($data as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($data[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }
            return $data;
        }else {
            return false;
        }
    }

    public function count_all_order($customer_id){
        $this->db->select('order_number');
        $this->db->from('voxy_package_orders AS m');
        $this->db->order_by('m.id', 'ASC');
        $this->db->where('customer_id',$customer_id);

        $query = $this->db->get();
        if ($query->result_array()){
            return $query->num_rows();
        }else {
            return false;
        }
    }

    public function total_all_order($customer_id){
        $this->db->select('total_price');
        $this->db->from('voxy_package_orders AS m');
        $this->db->order_by('m.id', 'ASC');
        $this->db->where('customer_id',$customer_id);

        $query = $this->db->get();
        if ($query->result_array()){
            $total_tien = 0;
            foreach ($query->result_array() as $item){
                $total_tien += $item['total_price'];
            }
            return $total_tien;
        }else {
            return false;
        }
    }

    public function total_all_conno($customer_id){
        $this->db->select('tongtien_no');
        $this->db->from('voxy_package_orders AS m');
        $this->db->order_by('m.id', 'ASC');
        $this->db->where('customer_id',$customer_id);

        $query = $this->db->get();
        if ($query->result_array()){
            $total_tien = 0;
            foreach ($query->result_array() as $item){
                $total_tien += $item['tongtien_no'];
            }
            return $total_tien;
        }else {
            return false;
        }
    }
    //chuc nang ghi no
    public function get_all_id_customer() {
        $this->db->select('id_customer');
        $this->db->select('id');
        $this->db->from('voxy_package_kunden AS m');
        $this->db->order_by('m.id', 'ASC');

        $query = $this->db->get();
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function update_total_price($id,$total_price){
        $data = array(
            'tongtien' => $total_price,
        );
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_total_con_no($id,$conno){
        $data = array(
            'conno' => $conno,
        );
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_soluong($id,$soluong){
        $data = array(
            'soluong' => $soluong,
        );
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function get_all_infor_no($list_id,$date_liefer, $date_lifer_end){
        $this->db->select('id, id_customer,first_name, last_name, multipass_identifier','soluong','tongtien','conno');
        $this->db->from('voxy_package_kunden AS m');
        $this->db->order_by('m.id', 'DESC');
        $this->db->where_in('id',$list_id);

        $query = $this->db->get();
        if ($query->result_array()){
            $arr = array();
            foreach ($query->result_array() as $item){
                 $item['infor'] = $this->get_all_order($item['id_customer'],$date_liefer,$date_lifer_end);
                 $arr[] = $item;
            }
            return $arr;
        }else {
            return false;
        }
    }

    public function get_all_infor_no_all($list_id,$date_liefer, $date_lifer_end){
        $this->db->select('id, id_customer,first_name, last_name, multipass_identifier','soluong','tongtien','conno');
        $this->db->from('voxy_package_kunden AS m');
        $this->db->order_by('m.id', 'DESC');
        $this->db->where_in('id',$list_id);

        $query = $this->db->get();
        if ($query->result_array()){
            $arr = array();
            foreach ($query->result_array() as $item){
                $item['infor'] = $this->get_all_order_all($item['id_customer'],$date_liefer,$date_lifer_end);
                $arr[] = $item;
            }
            return $arr;
        }else {
            return false;
        }
    }

    // end chuc nang ghi no
    public function get_list_table($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS NULL", false);
        $this->setting_select();
        $this->db->distinct();

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
        //var_dump($this->db->last_query());die;

        //xu ly cho find theo dieu kien
        if ($this->session->userdata('where_data') != false){

            $this->db->join('voxy_package_orders as orders ', 'orders.customer_id = m.id_customer','inner');

            $where_data = $this->session->userdata('where_data');

            if(isset($where_data['date_liefer'])){
                $date_liefer = $where_data['date_liefer'];
                $this->db->where('orders.shipped_at >=',$date_liefer);
            }

            if(isset($where_data['date_liefer_end'])){
                $date_liefer_end = $where_data['date_liefer_end'];
                $this->db->where('orders.shipped_at <=',$date_liefer_end);
            }

            if(isset($where_data['data_shipper_id'])){
                $data_shipper_id = $where_data['data_shipper_id'];
                $this->db->where_in('orders.shipper_id',$data_shipper_id);
            }

            if(isset($where_data['data_shipper_are_id'])){
                $data_shipper_are_id = $where_data['data_shipper_are_id'];
                $this->db->where_in('orders.ship_area_id',$data_shipper_are_id);
            }
            if(isset($where_data['data_sort_debt'])){
                $data_sort_debt = $where_data['data_sort_debt'];

                if($data_sort_debt == "debt"){
                    $this->db->where('orders.tongtien_no != ', 0);
                } else if($data_sort_debt == "debt_short"){
                    $this->db->where('orders.tongtien_no != ', 0);
                    $this->db->where('m.debt',$data_sort_debt);
                }else if($data_sort_debt == "debt_long"){//debt_long
                    $this->db->where('orders.tongtien_no != ', 0);
                    $this->db->where('m.debt',$data_sort_debt);
                }else{

                }
            }
            //$this->db->where('orders.tongtien_no != ', 0);

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