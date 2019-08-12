<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package_baocao_tonghop
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package_baocao_tonghop extends data_base
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
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'id_order', 'order_number', 'customer', 'financial_status',
            'fulfillment_status', 'total_price', 'line_items',
            'note', 'shipping_address', 'billing_address',
            'created_time', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status',
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
            'shipping_address' => 'Địa chỉ giao hàng',
            'billing_address' => 'Địa chỉ thanh toán',
            'line_items' => 'List Sản phẩm',
        );
        //table hien thi du lieu san pham
        $this->_field_table = Array(
            'm.order_number' => 'Đơn Đặt Hàng',
            //'m.created_time' => 'Thời gian',
            'm.shipped_at' => 'Ngày giao hàng',
            //'m.time_fulfillments' => 'FULL_F..Time',
            //'m.time_fulfillments_update' => 'FT days',
            //'m.time_refund' => 'Thời gian trả',
            //'m.refund_days' => 'Ngày trả',
            //'m.time_paid' => 'Thời gian trả tiền',
            //'m.customer' => 'Khách Hàng',
            'm.key_word_customer' => 'Khách Hàng',
            //'m.financial_status' => 'Trạng thái Tiền',
            //'m.fulfillment_status' => 'Trạng thái gửi hàng',
            'm.total_price' => 'Số tiền',
            'm.shipper_name' => 'Tài xế',
            'm.note' => 'Ghi Chú',
            //'m.check_xuathang' => 'Xuất hàng',
            'm.tongtien_no' => 'Tiền nợ'
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
        $this->db->from('voxy_package_xuathang');
        $this->db->where("order_number", $oder_number);
        $query = $this->db->get();
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }
    public function get_data_pdf($order_day){
        if ($order_day == false){
            $order_day  = date("Y-m-d");
        }
        $this->db->select('line_items');
        $this->db->from('voxy_package_xuathang');
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

    public function add_infor_xuathang($data)
    {
        if($this->_exist_created_field){
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['created_by'] = $this->user_id;
        }
        $this->db->insert('infor_xuathang', $data);
        return true;
    }

    public function update_infor_xuathang($data,$id)
    {
        //update products table
        $this->db->where('id', $id);
        $this->db->update('infor_xuathang',$data);
    }

    public function get_list_products($ngaydathang,$shipper_name,$name_kho){
        $this->db->select('list_products');
        $this->db->from('infor_xuathang');
        $this->db->where("date= '".$ngaydathang."' AND laixe = '".$shipper_name."' AND name_kho = '".$name_kho."'",null,false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $row['list_products'];
            }
        }else {
            return false;
        }
    }

    public function check_update($ngaydathang, $shipper_name){
        $this->db->select('id');
        $this->db->from('infor_xuathang');
        $this->db->where("date= '".$ngaydathang."' AND laixe = '".$shipper_name."' ",null,false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id;
            }
        }else {
            return false;
        }
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

    public function get_all_shipper_area_id(){
        $this->db->select('*');
        $this->db->from('dongxuan_ship_areas');
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

    public function get_name_shipper_area_id($id){
        $this->db->select('name');
        $this->db->from('dongxuan_ship_areas');
        $this->db->where_in("id", $id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $row['name'];
            }
        }else {
            return false;
        }
    }

    public function get_name_shipper ($id){
        $this->db->select('first_name');
        $this->db->from('dongxuan_shippers');
        $this->db->where("id", $id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->first_name;
            }
        }else {
            return false;
        }
    }

    public function name_shipper ($order_number){
        $this->db->select('shipper_name');
        $this->db->from('voxy_package_xuathang');
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

    //get infor san pham
    public function xuathang($ngayxuathang = "", $shipper_id = "")
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        //cau lenh sql hier
        $this->db->select('line_items');
        $this->db->select('order_number');
        $this->db->select('id');
        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        $this->db->where('check_xuathang is null ',null,false);//loai nhung thang dc xuat don le

        $this->db->like('shipped_at', $ngayxuathang);
        if( $shipper_id != ""){
            $this->db->like('shipper_id', $shipper_id);
        }
        //if( $order_number != ""){
          //  $this->db->where('order_number', $order_number);
        //}
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();

        $_export = array();
        $i = 0;

        $list_order_id = array();
        $array_note_products = array();

        //get nur array of items
        foreach ($data as $item){
            foreach (json_decode($item['line_items']) as $key2 => $item2 ){
                if($item2->properties != null){
                    $array_note_products[$key2]['title'] = $item2->title;
                    $array_note_products[$key2]['item_note_value'] = get_object_vars($item2->properties[0])['value'];
                }
                $i++;
                $_export[$i] = get_object_vars($item2);
            }
            $list_order_id[] = $item['id'];
        }

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach($_export as $key => $item){
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($_export as $key2 => $item2){
                if($key2 > $key ){
                    if($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] && $item['name'] == $item2['name'] ){
                        $item['quantity'] = $item['quantity'] +  $item2['quantity'];
                        $chiso_remove[$key2-1] = $key2-1;//index of same product and then remove it
                    }
                }
            }
            $export2[] = $item;
        }

        //remove nhung thang giong di
        foreach ($export2 as $key => $item){
            foreach ($chiso_remove as $key_reomove => $item_remove){
                unset($export2[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        foreach ($export2 as $key => $row) {
            $band[$key]    = $row['location'];
            $auflage[$key] = $row['id'];
        }
        $band  = array_column($export2, 'location');
        $auflage = array_column($export2, 'id');
        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $export2);

        //step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $export_end = array();
        foreach ($export2 as $item){
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $id_cat = $this->m_voxy_category->get_id($cat_id);
            $arr_cat_id[$id_cat]['id'] = $id_cat;
            $arr_cat_id[$id_cat]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export_end[] = $item;
        }

        // step 2: remove cac cai giong
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export_end;
        $return['array_note_products'] = $array_note_products;
        $return['list_order'] = implode($list_order_id,",");

        return $return;
    }

    public function get_variants($ngaydathang,$shipper_name){
        $this->db->select('variants');
        $this->db->from('infor_xuathang');
        $this->db->where("date= '".$ngaydathang."' AND laixe = '".$shipper_name."' ",null,false);
        $query = $this->db->get();
        //xuathang_listkiemvar_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $row['variants'];
            }
        }else {
            return false;
        }
    }

    public function get_list_product_infor_checkhang($ngaydathang,$shipper_name){
        $this->db->select('list_products');
        $this->db->from('infor_xuathang');
        $this->db->where("date= '".$ngaydathang."' AND laixe = '".$shipper_name."' ",null,false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $row['list_products'];
            }
        }else {
            return false;
        }
    }
    public function get_list_product_infor_checkhang_le($order_number){
        $this->db->select('list_products');
        $this->db->from('infor_xuathang_le');
        $this->db->where("order_number",$order_number);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $row['list_products'];
            }
        }else {
            return false;
        }
    }

    public function xuathang_le($order_number = "")
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        //cau lenh sql hier
        $this->db->select('line_items');
        $this->db->select('order_number');
        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        $this->db->where('order_number', $order_number);
        $this->db->where('check_xuathang is null ',null,false);
        ////  $this->db->like('shipper_id', $shipper_id);
        //}
        //if( $order_number != ""){
        //  $this->db->where('order_number', $order_number);
        //}
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();

        $_export = array();
        $i = 0;

        $array_note_products = array();
        //get nur array of items
        foreach ($data as $item){
            foreach (json_decode($item['line_items']) as $key2 => $item2 ){
                if($item2->properties != null){
                    $array_note_products[$key2]['title'] = $item2->title;
                    $array_note_products[$key2]['item_note_value'] = get_object_vars($item2->properties[0])['value'];
                }
                $i++;
                $_export[$i] = get_object_vars($item2);
            }
        }
        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach($_export as $key => $item){
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($_export as $key2 => $item2){
                if($key2 > $key ){
                    if($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] && $item['name'] == $item2['name'] ){
                        $item['quantity'] = $item['quantity'] +  $item2['quantity'];
                        $chiso_remove[$key2-1] = $key2-1;//index of same product and then remove it
                    }
                }
            }
            $export2[] = $item;
        }

        //remove nhung thang giong di
        foreach ($export2 as $key => $item){
            foreach ($chiso_remove as $key_reomove => $item_remove){
                unset($export2[$item_remove]);
                unset($chiso_remove[$key_reomove]);
            }
        }

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php
        foreach ($export2 as $key => $row) {
            $band[$key]    = $row['location'];
            $auflage[$key] = $row['id'];
        }
        $band  = array_column($export2, 'location');
        $auflage = array_column($export2, 'id');
        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $export2);

        //step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $export_end = array();
        foreach ($export2 as $item){
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $id_cat = $this->m_voxy_category->get_id($cat_id);
            $arr_cat_id[$id_cat]['id'] = $id_cat;
            $arr_cat_id[$id_cat]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export_end[] = $item;
        }

        // step 2: remove cac cai giong
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export_end;
        $return['array_note_products'] = $array_note_products;

        return $return;
    }

    public function get_variants_le($order_number){
        $this->db->select('variants');
        $this->db->from('infor_xuathang_le');
        $this->db->where('order_number',$order_number);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $row['variants'];
            }
        }else {
            return false;
        }
    }
    public function get_variants_le_listkiem($date, $laixe){
        $this->db->select('variants');
        $this->db->from('infor_xuathang_le');
        $this->db->where('date',$date);
        $this->db->where('laixe',$laixe);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $row['variants'];
            }
        }else {
            return false;
        }
    }

    public function update_infor_xuathang_le($data,$id)
    {
        //update products table
        $this->db->where('id', $id);
        $this->db->update('infor_xuathang_le',$data);
    }

    public function add_infor_xuathang_le($data)
    {
        if($this->_exist_created_field){
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['created_by'] = $this->user_id;
        }
        $this->db->insert('infor_xuathang_le', $data);
        return true;
    }

    public function check_update_le($order_number){
        $this->db->select('id');
        $this->db->from('infor_xuathang_le');
        $this->db->where('order_number',$order_number);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id;
            }
        }else {
            return false;
        }
    }

    //in list kiem
    public function xuathang_listkiem($ngayxuathang = "", $laixe = "")
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        //cau lenh sql hier
        $this->db->select('*');
        $this->db->from('infor_xuathang');

        $this->db->like('date', $ngayxuathang);
        if( $laixe != ""){
            $this->db->like('laixe', $laixe);
        }
        //if( $order_number != ""){
        //  $this->db->where('order_number', $order_number);
        //}
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();

        $_export = array();
        $i = 0;

        $array_note_products = array();
        //get nur array of items
        foreach ($data as $item){
            foreach (json_decode($item['list_products']) as $key2 => $item2 ){
                $_item2 = get_object_vars($item2);
                    $array_note_products[$key2]['title'] = $_item2['title'];
                    if(isset($_item2['note'])){
                        $array_note_products[$key2]['item_note_value'] = $_item2['note'];
                    }
                $i++;
                $_export[$i] = get_object_vars($item2);
            }
        }

        //step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $export_end = array();
        foreach ($_export as $item){

            $export_end[] = $item;
            if(isset($item['cat_id'])){
                $id_cat = $this->m_voxy_category->get_id($item['cat_id']);
                $arr_cat_id[$id_cat]['id'] = $id_cat;
                $arr_cat_id[$id_cat]['cat_id'] = $item['cat_id'];
            }
        }

        // step 2: remove cac cai giong
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export_end;
        $return['array_note_products'] = $array_note_products;

        return $return;
    }

    public function xuathang_listkiem_le($ngayxuathang = "", $laixe = "")
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        //cau lenh sql hier
        $this->db->select('*');
        $this->db->from('infor_xuathang_le');

        $this->db->like('date', $ngayxuathang);
        if( $laixe != ""){
            $this->db->like('laixe', $laixe);
        }
        //if( $order_number != ""){
        //  $this->db->where('order_number', $order_number);
        //}
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();

        $_export = array();
        $i = 0;

        $array_note_products = array();
        //get nur array of items
        foreach ($data as $item){
            foreach (json_decode($item['list_products']) as $key2 => $item2 ){
                $_item2 = get_object_vars($item2);
                $array_note_products[$key2]['title'] = $_item2['title'];
                if(isset($_item2['note'])){
                    $array_note_products[$key2]['item_note_value'] = $_item2['note'];
                }
                $i++;
                $_export[$i] = get_object_vars($item2);
            }
        }

//        //step 1: get category id and name, sap xep theo a -z
//        $arr_cat_id = array();
//        $export_end = array();
//        foreach ($_export as $item){
//
//            //$item['cat_id'] = $this->m_voxy_package->get_categories($item['product_id']);
//            $export_end[] = $item;
//            if(isset($item['cat_id'])){
//                $arr_cat_id[] = $item['cat_id'];
//            }
//        }
//
//        // step 2: remove cac cai giong
//        $result_catid = array_unique($arr_cat_id);


        //step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $export_end = array();
        foreach ($_export as $item){

            $export_end[] = $item;
            if(isset($item['cat_id'])){
                $id_cat = $this->m_voxy_category->get_id($item['cat_id']);
                $arr_cat_id[$id_cat]['id'] = $id_cat;
                $arr_cat_id[$id_cat]['cat_id'] = $item['cat_id'];
            }
        }

        // step 2: remove cac cai giong
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export_end;
        $return['array_note_products'] = $array_note_products;

        return $return;
    }

    public function get_order_from_mysql(){
        $this->db->select('*');
        $this->db->from('dongxuan_orders');
        $this->db->where('order_status','completed');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }
}