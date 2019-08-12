<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package_orders
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package_orders extends data_base
{
    var $arr_status = array(
        '0' => 'Hiện',
        '1' => 'Ẩn',
    );
    var $financial_status = array(
        'default' => 'any',
        'authorized' => 'authorized',
        'pending' => 'pending',
        'paid' => 'paid',
        'partially_paid' => 'partially_paid',
        'refunded' => 'refunded',
        'voided' => 'voided',
        'partially_refunded' => 'partially_refunded',
        'unpaid' => 'unpaid',
    );
    var $fulfillment_status = array(
        'default' => 'any',
        'shipped' => 'shipped',
        'partial' => 'pending',
        'unshipped' => 'unshipped',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'voxy_package_orders';
        $this->_key_name = 'id';
        //$this->_key_name = 'shipper_id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'id_order', 'order_number', 'customer', 'financial_status',
            'fulfillment_status', 'total_price', 'line_items',
            'note', 'shipping_address', 'billing_address',
            'created_time', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status',
            'tongtien_no'
        );
        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),
            'id_order' => array(
                'type' => 'hidden'
            ),
            'order_number' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
            'custommer' => array(
                'type' => 'text',
            ),
            'financial_status' => array(
                'type' => 'select',
                'array_list' => $this->financial_status,
            ),
            'fulfillment_status' => array(
                'type' => 'select',
                'array_list' => $this->fulfillment_status,
            ),
            'total_price' => array(
                'type' => 'text',
                'maxlength' => 255,
                'disabled' => 'disabled',
            ),
            'line_items' => array(
                'type' => 'line_items',
                'maxlength' => 2555,
                'required' => 'required',
            ),
            'note' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'shipping_address' => array(
                'type' => 'shipping_address',
                'maxlength' => 2552,
                'required' => 'required',
            ),
            'billing_address' => array(
                'type' => 'billing_address',
                'maxlength' => 2552,
                'required' => 'required',
            ),
            'created_time' => array(
                'type' => 'datetime',
                'maxlength' => 100,
            ),
            'created_at' => array(
                'type' => 'datetime',
                'maxlength' => 22,
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
            ),
            'status' => array(
                'type' => 'text',
                'maxlength' => 11,
            )
        );
        // form khi click them
        $this->_field_form = Array(
            'order_number' => 'Order',
            'id_order' => 'ID Order',
            'note' => 'Ghi chú',
//            'shipping_address' => 'Địa chỉ giao hàng',
//            'billing_address' => 'Địa chỉ thanh toán',
            'line_items' => 'List Sản phẩm',
        );
        //table hien thi du lieu san pham
        $this->_field_table = Array(
            'm.order_number' => 'STT',
            //'m.created_time' => 'Ngày đặt hàng',
            'm.shipped_at' => 'Ngày giao hàng',
            'm.shipper_name' => 'Tài xế',
            //'m.time_fulfillments' => 'FULL_F..Time',
            //'m.time_fulfillments_update' => 'FT days',
            //'m.time_refund' => 'Thời gian trả',
            //'m.refund_days' => 'Ngày trả',
            //'m.time_paid' => 'Thời gian trả tiền',
            'm.key_word_customer' => 'Khách Hàng',
            //'m.financial_status' => 'Trạng thái Tiền',
            //'m.fulfillment_status' => 'Trạng thái gửi hàng',
            'm.total_price_before' => 'Doanh Thu',
            'm.total_price' => 'DT Thực',
            'm.tongtien_no' => 'Nợ',
            'm.note' => 'Ghi Chú',
            //'m.status' => 'Trạng thái'
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('m.status != ', 'red');
        //$this->db->order_by('m.order_number', 'DESC');
    }

    public function update_id_custommer($id, $id_order){
        $data = array(
            'id_order' => $id_order,
        );
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_id_check_nhat_hang($id){
        $data = array(
            'check_nhat_hang' => 1,
        );
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function check_edit_kho($id){
        $this->db->select('edit_kho');
        $this->db->from($this->_table_name);
        $this->db->where('id',$id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->edit_kho;
            }
        } else {
            return false;
        }
    }

    /**
     * Hàm xóa row với điều kiện nhập vào
     * @param Int|Array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$list_id</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$list_id</p>
     * @return  Int     array of id_shopify
     */
    public function get_order_number($id){
        $this->db->select('id');
        $this->db->from($this->_table_name);
        $this->db->where("order_number", $id);
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

    public function get_id_order($id){
        $this->db->select('id_order');
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id_order;
            }
        }else {
            return false;
        }
    }

    public function get_order_number_from_id($id){
        $this->db->select('order_number');
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->order_number;
            }
        }else {
            return false;
        }
    }

    public function get_expriday($id_shopify){
        $this->db->select('expri_day');
        $this->db->from('voxy_package');
        $this->db->where("id_shopify", $id_shopify);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->expri_day;
            }
        }else {
            return false;
        }
    }

    public function get_location($id_shopify){
        $this->db->select('location');
        $this->db->from('voxy_package');
        $this->db->where("id_shopify", $id_shopify);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->location;
            }
        }else {
            return false;
        }
    }

    public function get_order($oder_number){
        $this->db->select('line_items');
        $this->db->select('customer_id');
        //$this->db->select('id');
        $this->db->from('voxy_package_orders');
        if(is_array($oder_number)){
            $this->db->where_in("order_number", $oder_number);
        }else{
            $this->db->where("order_number", $oder_number);
        }

        $query = $this->db->get();
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function get_id_order_number($order_number){
        $this->db->select('id');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $order_number);
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

    public function get_data_pdf($order_day){
        if ($order_day == false){
            $order_day  = date("Y-m-d");
        }
        $this->db->select('line_items');
        $this->db->from('voxy_package_orders');
        //$this->db->where("created_time", $order_day);
        $this->db->like('created_time', $order_day);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function get_infor_vs_laixe($date,$date_end)
    {
        $this->db->select('shipper_name');
        $this->db->select('shipper_id');
        $this->db->select('total_price');
        $this->db->from('voxy_package_orders');
        $this->db->order_by('total_price', 'asc');
        $this->db->where("shipped_at <=", $date);
        $this->db->where("shipped_at >=", $date_end);

        $this->db->where("status != ","red");//da nhap kho
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;

        if ($query->result_array()) {
            $export2 = array();
            $chiso_remove = array();
            foreach ($query->result_array() as $key => $item) {
                foreach ($query->result_array() as $key2 => $item2) {
                    if ($key2 > $key) {
                        if(isset($item['shipper_id']) && isset($item2['shipper_id'])){
                            if ( $item['shipper_id'] == $item2['shipper_id']) {
                                $item['total_price'] = (double)$item['total_price'] + (double)$item2['total_price'];
                                $chiso_remove[$key2] = $key2;//index of same product and then remove it
                            }
                        }
                    }
                }
                $export2[] = $item;
            }

            //remove nhung thang giong di
            foreach ($export2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }
            //var_dump($export2);die;

            $wek = array();
            foreach ($export2 as $key => $row) {
                $wek[$key] = $row['total_price'];
            }
            // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
            array_multisort($wek, SORT_DESC, $export2);

            return $export2;
        } else {
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
        //var_dump($this->db->last_query());die;
        return $this->db->affected_rows();
    }

    public function update_order($where, $data)
    {
            //$data['updated_at'] = date('Y-m-d H:i:s', time());
            //$data['updated_by'] = $this->user_id;

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
        //var_dump($this->db->last_query());die;
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

    public function get_shipper_id($order_nummer){
        $this->db->select('shipper_id');
        $this->db->from('dongxuan_orders');
        $this->db->where("order_id", $order_nummer);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->shipper_id;
            }
        }else {
            return false;
        }
    }

    public function get_doanhthu_truoc($order_nummer){
        $this->db->select('total_price');
        $this->db->from('dongxuan_orders');
        $this->db->where("local_order_id", $order_nummer);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->total_price;
            }
        }else {
            return false;
        }
    }

    public function get_lastime_pay($customer_id, $list_time_lan1,$order_number_old,$shipper_id){
        $this->db->select('order_number');
        $this->db->select('customer');
        $this->db->select('customer_id');
        $this->db->select('key_word_customer');
        $this->db->select('total_price');
        $this->db->select('tongtien_no');
        $this->db->select('thanhtoan_lan1');
        $this->db->select('thanhtoan_lan2');
        $this->db->select('thanhtoan_lan3');
        $this->db->select('thanhtoan_lan4');
        $this->db->select('thanhtoan_lan5');
        $this->db->select('time_lan1');
        $this->db->select('time_lan2');
        $this->db->select('time_lan3');
        $this->db->select('time_lan4');
        $this->db->select('time_lan5');
        $this->db->select('note');
        $this->db->select('shipped_at');
        $this->db->select('shipper_name');
        $this->db->select('shipper_id');

        $this->db->from('voxy_package_orders');
        //$this->db->where_in("customer_id", $customer_id);
        if($shipper_id != ""){
            $this->db->where_in('shipper_id',$shipper_id);
        }

        if($shipper_id != ""){
            $this->db->where_in('take_by_lan1',$shipper_id);
        }

        if($order_number_old){
            foreach ($order_number_old as $order_nummer){
                $this->db->where("order_number != ", $order_nummer);
            }
            //$this->db->where_not_in("order_number", $order_number_old);
        }

        if($list_time_lan1){
            $this->db->where_in("time_lan1", $list_time_lan1);

            $this->db->or_where_in("time_lan2", $list_time_lan1);

            $this->db->or_where_in("time_lan3", $list_time_lan1);

            $this->db->or_where_in("time_lan4", $list_time_lan1);

            $this->db->or_where_in("time_lan5", $list_time_lan1);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            $data = array();
            foreach ($query->result_array() as $item){
                if(in_array($item['shipper_id'], $shipper_id)){
                    $data[] = $item;
                }
            }
            return $data;
        }else {
            return false;
        }
    }

    public function get_lastime_pay_other_tour($shipper_id,$shipped_at){
        $this->db->select('order_number');
        $this->db->select('customer');
        $this->db->select('customer_id');
        $this->db->select('key_word_customer');
        $this->db->select('total_price');
        $this->db->select('tongtien_no');
        $this->db->select('thanhtoan_lan1');
        $this->db->select('thanhtoan_lan2');
        $this->db->select('thanhtoan_lan3');
        $this->db->select('thanhtoan_lan4');
        $this->db->select('thanhtoan_lan5');
        $this->db->select('time_lan1');
        $this->db->select('time_lan2');
        $this->db->select('time_lan3');
        $this->db->select('time_lan4');
        $this->db->select('time_lan5');
        $this->db->select('note');
        $this->db->select('shipped_at');
        $this->db->select('shipper_name');
        $this->db->select('shipper_id');

        $this->db->from('voxy_package_orders');
        //$this->db->where_in("customer_id", $customer_id);

        if($shipper_id != ""){

            $this->db->where_not_in('shipper_id',$shipper_id);
            $this->db->where_in('take_by_lan1',$shipper_id);
        }

        if($shipped_at != ""){
            $this->db->where('time_lan1',$shipped_at);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            return $query->result_array();

        }else {
            return false;
        }
    }

    public function get_all_shipper_id(){
        $this->db->select('*');
        $this->db->from('dongxuan_shippers');
        //$this->db->where("order_id", $order_nummer);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $query->result();
            }
        }else {
            return false;
        }
    }

    public function get_firstname_shipper_id(){
        $this->db->select('id');
        $this->db->select('first_name');
        $this->db->from('dongxuan_shippers');
        //$this->db->where("order_id", $order_nummer);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            $arr = array();
            foreach ($query->result_array() as $row)
            {
                $arr[]= $row;
            }
            return $arr;
        }else {
            return false;
        }
    }

    public function get_name_shipper ($id){
        $this->db->select('first_name');
        $this->db->from('dongxuan_shippers');
        $this->db->where_in("id", $id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            $return = array();
            foreach ($query->result_array() as $row)
            {
                $return[] = $row['first_name'];
            }
            return implode(",", $return);
        }else {
            return false;
        }
    }

    public function get_all_shipper (){
        $this->db->select('id');
        $this->db->from('dongxuan_shippers');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            $return_arr = array();
            foreach ($query->result_array() as $item){
                $return_arr[] = $item['id'];
            }
            return $return_arr;
        }else {
            return false;
        }
    }

    public function name_shipper ($order_number){
        $this->db->select('shipper_name');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $order_number);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->shipper_name;
            }
        }else {
            return false;
        }
    }

    public function phone_shipper ($shipper_name){
        $this->db->select('phone');
        $this->db->from('dongxuan_shippers');
        $this->db->where("first_name", $shipper_name);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->phone;
            }
        }else {
            return false;
        }
    }

    public function get_shipped_at($id_order){
        $this->db->select('shipped_at');
        $this->db->from('dongxuan_orders');
        $this->db->where("order_id", $id_order);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->shipped_at;
            }
        }else {
            return false;
        }
    }

    public function get_shipped_at_voxy_order($id){
        $this->db->select('shipped_at');
        $this->db->from('voxy_package_orders');
        $this->db->where("id", $id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->shipped_at;
            }
        }else {
            return false;
        }
    }

    public function update_checked_xuathang($id,$data){
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_thanhtoan_lan1_xuathang($id_orders, $date_time_lan1){
        $total_price = $this->get_total_price($id_orders);
        $data = array(
            'time_lan1' => $date_time_lan1,
            'thanhtoan_lan1' => $total_price,
            'tongtien_no' => 0
        );
        $this->db->where('id', $id_orders);
        $this->db->update($this->_table_name, $data);
        //var_dump($this->db->last_query());die;

    }

    public function get_total_price($id){
        $this->db->select('total_price');
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->total_price;
            }
        }else {
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

                if(isset($whereCondition["custom_where"]['m.laixe'])){
                    $laixe = $whereCondition["custom_where"]['m.laixe'];
                }
            //var_dump($ngaydathang);die;
                //$laixe = $whereCondition["custom_where"]['m.shipper_id'];
            if(isset($ngaydathang)){
                if(isset($ngaydathang)){
                    $this->db->like("m.created_time",$ngaydathang);
                }

                if(isset($ngaygiaohang)){
                    $this->db->where("m.shipped_at",$ngaygiaohang);
                }

            }else{
                if(isset($ngaygiaohang)){
                    $this->db->where("m.shipped_at",$ngaygiaohang);
                }
            }

            if($laixe != "all"){
                $this->db->where_in("m.shipper_id",$laixe);
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
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;

        return $query->result();
    }

    public function get_list_table_xuathangtaikho($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
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

            if(isset($whereCondition["custom_where"]['m.created_time'])) {
                $ngaydathang = $whereCondition["custom_where"]["m.created_time"];
            }
            if(isset($whereCondition["custom_where"]['m.shipped_at'])){
                $ngaygiaohang = $whereCondition["custom_where"]['m.shipped_at'];
            }
            $laixe = $whereCondition["custom_where"]['m.shipper_id'];

//            if(isset($ngaydathang)){
//                $this->db->like("m.created_time",$ngaydathang);
//            }

            if(isset($ngaygiaohang)){
                $this->db->like("m.shipped_at",$ngaygiaohang);
            }

            if($laixe != "all"){
                $this->db->where_in("m.shipper_id",$laixe);
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

    public function get_price($donhang){
        $this->db->select('line_items');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $donhang);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->line_items;
            }
        }else {
            return false;
        }
    }

    public function get_line_items($order_number){
        $this->db->select('line_items');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $order_number);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->line_items;
            }
        }else {
            return false;
        }
    }

    public function get_list_orders($shipped_at, $list_shipper_id){
        $this->db->select('*');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at", $shipped_at);
        if(is_array($list_shipper_id)){
            $this->db->where_in("shipper_id", $list_shipper_id);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
                return $query->result_array();
        }else {
            return false;
        }
    }

    public function add_hanghong_hangve($colum,$order_number, $data){
        $data = array(
             $colum => $data,
        );

        $this->db->where('order_number', $order_number);
        $this->db->update($this->_table_name, $data);
    }

    public function get_infor_theo_ngay_hangve($ngay_giao_hang,$ngay_dat_hang)
    {
        $this->db->select('hangve');
        $this->db->select('line_items');
        $this->db->select('order_number');
        $this->db->select('shipper_id');
        $this->db->select('shipper_name');
        $this->db->select('shipped_at');
        $this->db->from('voxy_package_orders');

        if($ngay_dat_hang != ""){
            $this->db->where("shipped_at >=", $ngay_dat_hang);
        }
        if($ngay_giao_hang != ""){
            $this->db->where("shipped_at <=", $ngay_giao_hang);
        }


        $this->db->where("status !=", "red");
        $this->db->where("hangve is NOT NULL", NULL,FALSE);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;

        if ($query->result_array()) {
            $_export = array();
            foreach($query->result_array() as $item){
                foreach (json_decode($item['hangve']) as $item_con){
                    $_item = get_object_vars($item_con);
                    $_item['shipper_id'] = $item['shipper_id'];
                    $_item['shipper_name'] = $item['shipper_name'];
                    $_item['order_number'] = $item['order_number'];
                    $_item['shipped_at'] = $item['shipped_at'];
                    //get note tu line items
                    if($item['line_items']){

                        foreach(json_decode($item['line_items']) as $item_note){
                            $item_note = get_object_vars($item_note);

                            if($item_note['variant_id'] == $_item['variant_id']){
                                $_item['note'] = $item_note['item_note'];
                            }
                        }
                    }
                    $_export[]= $_item;
                }
            }
            $export2 = array();
            $chiso_remove = array();
            //sum inventory of same product
            foreach ($_export as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                foreach ($_export as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id'] && $item["shipper_id"] == $item2['shipper_id']){
                            $item['sl_nhap'] = (double)$item['sl_nhap'] + (double)$item2['sl_nhap'];
                            $item['thanhtien'] = (double)$item['thanhtien'] + (double)$item2['thanhtien'];
                            $item['order_number'] = $item['order_number'] .",". $item2['order_number'];
                            $item['shipper_name'] = $item['shipper_name'] .",". $item2['shipper_name'];
                            $item['shipped_at'] = $item['shipped_at'] .",". $item2['shipped_at'];
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
                $export2[] = $item;
            }

            //remove nhung thang giong di
            foreach ($export2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

//            echo "<pre>";
//            var_dump($export2);
//            echo "</pre>";
//            die;
            return $export2;
        } else {
            return false;
        }
    }

    public function get_infor_theo_ngay_hanghong($ngay_giao_hang,$ngay_dat_hang)
    {
        $this->db->select('hanghong');
        $this->db->select('line_items');
        $this->db->select('order_number');
        $this->db->select('shipper_id');
        $this->db->select('shipper_name');
        $this->db->select('shipped_at');
        $this->db->from('voxy_package_orders');

        if($ngay_dat_hang != ""){
            $this->db->where("shipped_at >=", $ngay_dat_hang);
        }
        if($ngay_giao_hang != ""){
            $this->db->where("shipped_at <=", $ngay_giao_hang);
        }


        $this->db->where("status !=", "red");
        $this->db->where("hanghong is NOT NULL", NULL,FALSE);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;

        if ($query->result_array()) {
            $_export = array();
            foreach($query->result_array() as $item){
              if(json_decode($item['hanghong']) ){
                  foreach (json_decode($item['hanghong']) as $item_con){
                      $_item = get_object_vars($item_con);
                      $_item['shipper_id'] = $item['shipper_id'];
                      $_item['shipper_name'] = $item['shipper_name'];
                      $_item['order_number'] = $item['order_number'];
                      $_item['shipped_at'] = $item['shipped_at'];
                      //get note tu line items
                      if($item['line_items']){

                          foreach(json_decode($item['line_items']) as $item_note){
                              $item_note = get_object_vars($item_note);

                              if($item_note['variant_id'] == $_item['variant_id']){
                                  $_item['note'] = $item_note['item_note'];
                              }
                          }
                      }
                      $_export[]= $_item;
                  }
              }

            }
            $export2 = array();
            $chiso_remove = array();
            //sum inventory of same product
            foreach ($_export as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                foreach ($_export as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id'] && $item["shipper_id"] == $item2['shipper_id']){
                            $item['sl_nhap'] = (double)$item['sl_nhap'] + (double)$item2['sl_nhap'];
                            $item['thanhtien'] = (double)$item['thanhtien'] + (double)$item2['thanhtien'];
                            $item['order_number'] = $item['order_number'] .",". $item2['order_number'];
                            $item['shipper_name'] = $item['shipper_name'] .",". $item2['shipper_name'];
                            $item['shipped_at'] = $item['shipped_at'] .",". $item2['shipped_at'];
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
                $export2[] = $item;
            }

            //remove nhung thang giong di
            foreach ($export2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

//            echo "<pre>";
//            var_dump($export2);
//            echo "</pre>";
//            die;
            return $export2;
        } else {
            return false;
        }
    }

    public function get_infor_theo_ngay_hangthem($ngay_giao_hang,$ngay_dat_hang)
    {
        $this->db->select('hangthem');
        $this->db->select('line_items');
        $this->db->select('order_number');
        $this->db->select('shipper_id');
        $this->db->select('shipper_name');
        $this->db->select('shipped_at');
        $this->db->from('voxy_package_orders');

        if($ngay_dat_hang != ""){
            $this->db->where("shipped_at >=", $ngay_dat_hang);
        }
        if($ngay_giao_hang != ""){
            $this->db->where("shipped_at <=", $ngay_giao_hang);
        }


        $this->db->where("status !=", "red");
        $this->db->where("hangthem is NOT NULL", NULL,FALSE);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;

        if ($query->result_array()) {
            $_export = array();
            foreach($query->result_array() as $item){
                if(json_decode($item['hangthem']) ){
                    foreach (json_decode($item['hangthem']) as $item_con){
                        $_item = get_object_vars($item_con);
                        $_item['shipper_id'] = $item['shipper_id'];
                        $_item['shipper_name'] = $item['shipper_name'];
                        $_item['order_number'] = $item['order_number'];
                        $_item['shipped_at'] = $item['shipped_at'];
                        //get note tu line items
                        if($item['line_items']){

                            foreach(json_decode($item['line_items']) as $item_note){
                                $item_note = get_object_vars($item_note);

                                if($item_note['variant_id'] == $_item['variant_id']){
                                    $_item['note'] = $item_note['item_note'];
                                }
                            }
                        }
                        $_export[]= $_item;
                    }
                }

            }
            $export2 = array();
            $chiso_remove = array();
            //sum inventory of same product
            foreach ($_export as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                foreach ($_export as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id'] && $item["shipper_id"] == $item2['shipper_id']){
                            $item['sl_nhap'] = (double)$item['sl_nhap'] + (double)$item2['sl_nhap'];
                            $item['thanhtien'] = (double)$item['thanhtien'] + (double)$item2['thanhtien'];
                            $item['order_number'] = $item['order_number'] .",". $item2['order_number'];
                            $item['shipper_name'] = $item['shipper_name'] .",". $item2['shipper_name'];
                            $item['shipped_at'] = $item['shipped_at'] .",". $item2['shipped_at'];
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
                $export2[] = $item;
            }

            //remove nhung thang giong di
            foreach ($export2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

//            echo "<pre>";
//            var_dump($export2);
//            echo "</pre>";
//            die;
            return $export2;
        } else {
            return false;
        }
    }

    public function get_infor_theo_ngay_hangthieu($ngay_giao_hang,$ngay_dat_hang)
    {
        $this->db->select('hangthieu');
        $this->db->select('line_items');
        $this->db->select('order_number');
        $this->db->select('shipper_id');
        $this->db->select('shipper_name');
        $this->db->select('shipped_at');
        $this->db->from('voxy_package_orders');

        if($ngay_dat_hang != ""){
            $this->db->where("shipped_at >=", $ngay_dat_hang);
        }
        if($ngay_giao_hang != ""){
            $this->db->where("shipped_at <=", $ngay_giao_hang);
        }


        $this->db->where("status !=", "red");
        $this->db->where("hangthieu is NOT NULL", NULL,FALSE);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;


        if ($query->result_array()) {
            $_export = array();
            foreach($query->result_array() as $item){
                foreach (json_decode($item['hangthieu']) as $item_con){
                    $_item = get_object_vars($item_con);//hang thieu
                    $_item['shipper_id'] = $item['shipper_id'];
                    $_item['shipper_name'] = $item['shipper_name'];
                    $_item['order_number'] = $item['order_number'];
                    $_item['shipped_at'] = $item['shipped_at'];

                    $_line_items = json_decode($item["line_items"]);//tong line_items
                    foreach ($_line_items as $product){
                        $product = get_object_vars($product);
                        if($_item['variant_id'] == $product['variant_id']){
                            $note = "";
                            if($product['item_note'] != ""){
                                $note .= $product['item_note'];
                            }

                        }
                    }

                    $_item['note'] = $note;


                    $_export[]= $_item;
                }
            }

            $export2 = array();
            $chiso_remove = array();
            //sum inventory of same product
            foreach ($_export as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                foreach ($_export as $key2 => $item2) {
                    if ($key2 > $key) {
                        if ($item['variant_id'] == $item2['variant_id']){
                            $item['sl_nhap'] = (double)$item['sl_nhap'] + (double)$item2['sl_nhap'];
                            $item['thanhtien'] = (double)$item['thanhtien'] + (double)$item2['thanhtien'];

                            $item['shipper_name'] = $item['shipper_name'] .",". $item2['shipper_name'];

                            if($item2['note'] != ""){
                                $item['note'] = $item['note'] . "-" .$item2['note'];
                            }
                            if($item2['order_number'] != ""){
                                $item['order_number'] = $item['order_number'] . "-" .$item2['order_number'];
                            }

                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
                $export2[] = $item;
            }

            //remove nhung thang giong di
            foreach ($export2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

            return $export2;
        } else {
            return false;
        }
    }


    public function count_of_orders($date){
        $this->db->select('COUNT(id) as count_id');
        $this->db->select('sum(total_price) as doanhthu');
        $this->db->from('voxy_package_orders');
        $this->db->like("shipped_at", $date);
        $this->db->where("status !=", "red");

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function count_of_orders_wrong($date){
        $this->db->select('id');
        $this->db->select('hangve');
        $this->db->from('voxy_package_orders');
        $this->db->like("shipped_at", $date);
        $this->db->where("hangve IS NOT NULL", null, false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function get_list_id($tungay, $denngay, $shipper_id, $shipper_are_id){
        $this->db->select('id');
        $this->db->from('voxy_package_orders');
        if($tungay){
            $this->db->where("shipped_at >=", $tungay);
        }

        if($denngay){
            $this->db->where("shipped_at <=", $denngay);
        }

        if($shipper_id){
            $this->db->where_in("shipper_id", $shipper_id);
        }

        if($shipper_are_id){
            $this->db->where_in("ship_area_id", $shipper_are_id);
        }


       // $this->db->where("hangve IS NOT NULL", null, false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;

        if ($query->result_array()){
            $data = array();
            foreach ($query->result_array() as $item){
                $data[] = $item['id'];
            }

            return $data;
        }else {
            return false;
        }
    }

    public function check_exist_product_in_order($product_id, $variant_id, $title,$order_number){
        $this->db->select('id');
        $this->db->select('line_items');
        $this->db->from('voxy_package_orders');
        $this->db->where_in("order_number", $order_number);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;

        if ($query->result_array()){
            $check = false;
            foreach ($query->result_array() as $item){
                $line_items = $item['line_items'];
                if($line_items){
                    $_line_items = json_decode($line_items);
                    foreach ($_line_items as $_item){
                        $item = get_object_vars($_item);
                        if($product_id == $item['product_id']){
                            //$check = true;
                        }

                        if($variant_id == $item['variant_id']){
                            $check = true;
                        }

                        if($title == $item['title']){
                            //$check = true;
                        }
                    }
                }
            }
            return $check;
        }else {
            return false;
        }

    }

    //save information history of ubersicht
    public function check_update_ubericht_history($date_save, $shipper_id){
        $this->db->select('id');
        $this->db->from('voxy_ubersicht_history');
        $this->db->where("date_save", $date_save);
        $this->db->where("shipper_id", $shipper_id);
        $query = $this->db->get();
        if ($query->result_array()){
            $id = "";
            foreach ($query->result_array() as $item){
                $id = $item['id'];
            }
            return $id;
        }else {
            return false;
        }
    }

    //$data_history is json data for this column
    public function add_history($ngay_chuyen_hang, $shipper_id, $data_order, $data_order_old, $data_driving_cost)
    {
            $data['created_at'] = date('d-m-Y H:i:s', time());
            $data['created_by'] = $this->user_id;
            $data['liefer_datum'] = $ngay_chuyen_hang;
            foreach ($shipper_id as $ship){
                $data['shipper_id'] = $ship;
            }

            $data['date_save'] = date('d-m-Y');

            $data['information_orders'] = json_encode($data_order);
            $data['information_orders_old'] = json_encode($data_order_old);
            $data['driving_costs'] = json_encode($data_driving_cost);

        $this->db->insert("voxy_ubersicht_history", $data);
    }

    public function update_history($id, $ngay_chuyen_hang, $shipper_id, $data_order, $data_order_old, $data_driving_cost)
    {
        $data['updated_at'] = date('d-m-Y H:i:s', time());
        $data['updated_by'] = $this->user_id;

        $data['liefer_datum'] = $ngay_chuyen_hang;

        foreach ($shipper_id as $ship){
            $data['shipper_id'] = $ship;
        }

        $data['information_orders'] = json_encode($data_order);
        $data['information_orders_old'] = json_encode($data_order_old);
        $data['driving_costs'] = json_encode($data_driving_cost);

        $this->db->where('id',$id);
        $this->db->update("voxy_ubersicht_history", $data);

        //var_dump($this->db->last_query());die;
    }


}