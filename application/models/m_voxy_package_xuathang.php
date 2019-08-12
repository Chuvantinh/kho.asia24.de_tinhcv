<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package_xuathang
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package_xuathang extends data_base
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
            'm.check_xuathang' => 'Xuất hàng',
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

    //get infor san pham for xuat hang theo kho
    public function xuathang($ngayxuathang = "", $shipper_id = "", $sorting, $list_id_to_nhathang)
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

        if($list_id_to_nhathang != null || $list_id_to_nhathang != ""){
            $this->db->where_in('id', $list_id_to_nhathang);
        }else{
            $this->db->like('shipped_at', $ngayxuathang);
            if( $shipper_id != ""){
                $this->db->like('shipper_id', $shipper_id);
            }
        }
        //if( $order_number != ""){
          //  $this->db->where('order_number', $order_number);
        //}
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();
            //var_dump($data);die;
        $_export = array();
        $i = 0;

        $list_order_id = array();
        $array_note_products = array();

        //get nur array of items
        foreach ($data as $item){
            $for = json_decode($item['line_items']);
            if($for) {
                foreach ( $for as $key2 => $item2 ) {
                    if(isset($item2->properties)){
                        if($item2->properties != null){
                            $array_note_products[$key2]['title'] = $item2->title;
                            $array_note_products[$key2]['item_note_value'] = get_object_vars($item2->properties[0])['value'];
                        }
                    }else{
                        $array_note_products[$key2]['title'] = $item2->title;
                        $array_note_products[$key2]['item_note_value'] = $item2->item_note;
                    }
                    $i++;

                    $__item2 = get_object_vars($item2);
                    if(strlen($__item2['title']))
                    $__item2['order_number'] = $item['order_number'];
                    $__item2['total_price'] = round((double)$__item2['quantity'] * (double)$__item2['price'],2);

                    $_export[$i] = $__item2;
                }
                $list_order_id[] = $item['id'];
            }
        }

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach($_export as $key => $item){
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($_export as $key2 => $item2){
                if($key2 > $key ){
                    if($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] ){
                        $item['quantity'] = $item['quantity'] +  $item2['quantity'];
                        $item['order_number'] = $item['order_number'] .",". $item2['order_number'];
                        $item['total_price'] = $item['total_price']  + $item2['total_price'];

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
//        foreach ($export2 as $key => $row) {
//            $band[$key]    = $row['location'];
//            $auflage[$key] = $row['id'];
//        }
//        $band  = array_column($export2, 'location');
//        $auflage = array_column($export2, 'id');
//        array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $export2);

        if ($sorting == "location") {
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['location'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'location');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $export2);
        } else {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        }

        //step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $export_end = array();
        foreach ($export2 as $item){
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            //$id_cat = $this->m_voxy_category->get_id($cat_id);
            //$arr_cat_id[$id_cat]['id'] = $id_cat;
            //$arr_cat_id[$id_cat]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export_end[] = $item;

            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
            /*
            if($cat_id == false){
                $cat_id = "";
            }

            if($cat_title == false){
                $cat_title = "G00-Hàng Linh Tinh";
            }
            */

            if($cat_title == false){
                $cat_title = "G00-Hàng Linh Tinh";
            }

            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
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

    public function xuathang_le($order_number = "",$sorting)
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
                if(isset($item2->properties)){
                    if($item2->properties != null){
                        $array_note_products[$key2]['title'] = $item2->title;
                        $array_note_products[$key2]['item_note_value'] = get_object_vars($item2->properties[0])['value'];
                    }
                }else{
                    if($item2->item_note != ""){
                        $array_note_products[$key2]['title'] = $item2->title;
                        $array_note_products[$key2]['item_note_value'] = get_object_vars($item2->properties[0])['value'];
                    }
                }

                $i++;
                //$_export[$i] = get_object_vars($item2);

                $__item2 = get_object_vars($item2);
                if(strlen($__item2['title']))
                    $__item2['order_number'] = $item['order_number'];
                $__item2['total_price'] = round((double)$__item2['quantity'] * (double)$__item2['price'],2);

                $_export[$i] = $__item2;
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

                        $item['order_number'] = $item['order_number'] .",". $item2['order_number'];
                        $item['total_price'] = $item['total_price']  + $item2['total_price'];
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
        if ($sorting == "location") {
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['location'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'location');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $export2);
        } else {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        }

        //step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $export_end = array();
        foreach ($export2 as $item){
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            //$id_cat = $this->m_voxy_category->get_id($cat_id);

            //$arr_cat_id[$id_cat]['id'] = $id_cat;
            //$arr_cat_id[$id_cat]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export_end[] = $item;

            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);

            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
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
                    if(isset($_item2['note'])){
                        if($_item2['note'] != "" && strlen($_item2['note']) != 202){
                            $array_note_products[$key2]['title'] = $_item2['title'];
                            $array_note_products[$key2]['item_note_value'] = $_item2['note'];
                        }
                    }
                $i++;
                $_export[$i] = get_object_vars($item2);
            }
        }

        //step 1: get category id and name, sap xep theo a -z
//        $arr_cat_id = array();
//        $export_end = array();
//        foreach ($_export as $item){
//            $export_end[] = $item;
//            if(isset($item['cat_id'])){
//                $id_cat = $this->m_voxy_category->get_id($item['cat_id']);
//                $arr_cat_id[$id_cat]['id'] = $id_cat;
//                $arr_cat_id[$id_cat]['cat_id'] = $item['cat_id'];
//            }
//        } //cho nay category sap xep sai nen sua lai theo cai ben duoi

        $arr_cat_id = array();
        $export2_new = array();
        foreach ($_export as $item) {
            if (!isset($item['product_id'])){
                $cat_id = false;
                $cat_title = false;
            }else{
                $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
                $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
            }

            if($cat_title == false){
                $cat_title = "G00-Hàng Lẻ Linh Tinh";
                $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
            }
            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export2_new[] = $item;
        }

        // step 2: remove cac cai giong
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export2_new;
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
//        $arr_cat_id = array();
//        $export_end = array();
//        foreach ($_export as $item){
//
//            $export_end[] = $item;
//            if(isset($item['cat_id'])){
//                $id_cat = $this->m_voxy_category->get_id($item['cat_id']);
//                $arr_cat_id[$id_cat]['id'] = $id_cat;
//                $arr_cat_id[$id_cat]['cat_id'] = $item['cat_id'];
//            }
//        }

        $arr_cat_id = array();
        $export2_new = array();
        foreach ($_export as $item) {
            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
            if($cat_title == false){
                $cat_title = "G00-Hàng Lẻ Linh Tinh";
                $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
            }
            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export2_new[] = $item;
        }

        // step 2: remove cac cai giong
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export2_new;
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

    public function get_order_from_mysql_odernumber($order_number){
        $this->db->select('*');
        $this->db->from('dongxuan_orders');
        $this->db->where('order_status','completed');
        $this->db->where('local_order_id', $order_number);
        $query = $this->db->get();
        $data = array();
        if ($query->result_array()){
            foreach ($query->result_array() as $item){
                $data[] = $item;
            }
            return $data;
        }else {
            return false;
        }
    }

    public function get_namne_ship_area($id){
        $this->db->select('name');
        $this->db->from('dongxuan_ship_areas');
        $this->db->where_in('id', $id);
        $query = $this->db->get();
        $data = array();
        if ($query->result_array()){
            foreach ($query->result_array() as $item){
                $data[] = $item['name'];
            }
            return $data;
        }else {
            return false;
        }
    }

    public function get_all_umsatz($tungay = "", $denngay = "", $laixe = ""){
        $this->db->select('line_items');
        $this->db->from('voxy_package_orders');
        $this->db->where('shipped_at >=', $tungay);
        $this->db->where('shipped_at <=', $denngay);
        if( $laixe != ""){
            $this->db->where_in('shipper_name', $laixe);
        }
        $query = $this->db->get();
        $data = $query->result_array();

        //loai bo nhung thang null di
        $array = array();
        foreach ($data as $item){
            if($item['line_items'] != null || $item['line_items'] != ""){
                foreach(json_decode($item['line_items']) as $item2){
                    $array[] = get_object_vars($item2);
                }

            }
        }
        $array2 = array();
        foreach ($array as $ar){
            $ar['total_price'] = $ar['quantity'] * $ar['price'];
            $array2[] = $ar;
        }

        $export2 = array();
        $chiso_remove = array();
        foreach ($array2 as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($array2 as $key2 => $item2) {
                if ($key2 > $key) {
                    if(isset($item['variant_id']) && isset($item2['variant_id'])){
                        if ( $item['variant_id'] == $item2['variant_id']) {
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

        return $export2;
    }

    public function get_all_hangtrave($tungay = "", $denngay = "", $laixe = ""){
        $this->db->select('hangve');
        $this->db->from('voxy_package_orders');
        $this->db->where('shipped_at >=', $tungay);
        $this->db->where('shipped_at <=', $denngay);
        if( $laixe != ""){
            $this->db->where_in('shipper_name', $laixe);
        }
        $query = $this->db->get();
        $data = $query->result_array();

        //loai bo nhung thang null di
        $array = array();
        foreach ($data as $item){
            if($item['hangve'] != null || $item['hangve'] != ""){
                foreach(json_decode($item['hangve']) as $item2){
                    $array[] = get_object_vars($item2);
                }

            }
        }

        $export2 = array();
        $chiso_remove = array();
        foreach ($array as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($array as $key2 => $item2) {
                if ($key2 > $key) {
                    if(isset($item['variant_id']) && isset($item2['variant_id'])){
                        if ( $item['variant_id'] == $item2['variant_id']) {
                                $item['sl_nhap'] = (int)$item['sl_nhap'] + (int)$item2['sl_nhap'];
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

        return $export2;
    }

    public function get_all_hanghong($tungay = "", $denngay = "", $laixe = ""){
        $this->db->select('hanghong');
        $this->db->from('voxy_package_orders');
        $this->db->where('shipped_at >=', $tungay);
        $this->db->where('shipped_at <=', $denngay);

        if($laixe != ""){
            $this->db->where_in('shipper_name', $laixe);
        }
        $this->db->where('hanghong is NOT NULL', NULL, FALSE);
        $query = $this->db->get();
        $data = $query->result_array();

        //loai bo nhung thang null di
        $array = array();
        foreach ($data as $item){
            if($item['hanghong'] != null || $item['hanghong'] != ""){
                foreach(json_decode($item['hanghong']) as $item2){
                    $array[] = get_object_vars($item2);
                }

            }
        }

        $export2 = array();
        $chiso_remove = array();
        foreach ($array as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($array as $key2 => $item2) {
                if ($key2 > $key) {
                    if(isset($item['variant_id']) && isset($item2['variant_id'])){
                        if ( $item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = (int)$item['sl_nhap'] + (int)$item2['sl_nhap'];
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

        return $export2;
    }

    public function get_all_hangthieu($tungay = "", $denngay = "", $laixe = ""){
        $this->db->select('hangthieu');
        $this->db->from('voxy_package_orders');
        $this->db->where('shipped_at >=', $tungay);
        $this->db->where('shipped_at <=', $denngay);
        if( $laixe != ""){
            $this->db->where_in('shipper_name', $laixe);
        }
        $query = $this->db->get();
        $data = $query->result_array();

        //loai bo nhung thang null di
        $array = array();
        foreach ($data as $item){
            if($item['hangthieu'] != null || $item['hangthieu'] != ""){
                foreach(json_decode($item['hangthieu']) as $item2){
                    $array[] = get_object_vars($item2);
                }

            }
        }

        $export2 = array();
        $chiso_remove = array();
        foreach ($array as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($array as $key2 => $item2) {
                if ($key2 > $key) {
                    if(isset($item['variant_id']) && isset($item2['variant_id'])){
                        if ( $item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = (int)$item['sl_nhap'] + (int)$item2['sl_nhap'];
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

        return $export2;
    }

    public function get_all_hangthem($tungay = "", $denngay = "", $laixe = ""){
        $this->db->select('hangthem');
        $this->db->from('voxy_package_orders');
        $this->db->where('shipped_at >=', $tungay);
        $this->db->where('shipped_at <=', $denngay);
        if( $laixe != ""){
            $this->db->where_in('shipper_name', $laixe);
        }
        $query = $this->db->get();
        $data = $query->result_array();

        //loai bo nhung thang null di
        $array = array();
        foreach ($data as $item){
            if($item['hangthem'] != null || $item['hangthem'] != ""){
                foreach(json_decode($item['hangthem']) as $item2){
                    $array[] = get_object_vars($item2);
                }

            }
        }

        $export2 = array();
        $chiso_remove = array();
        foreach ($array as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($array as $key2 => $item2) {
                if ($key2 > $key) {
                    if(isset($item['variant_id']) && isset($item2['variant_id'])){
                        if ( $item['variant_id'] == $item2['variant_id']) {
                            $item['sl_nhap'] = (int)$item['sl_nhap'] + (int)$item2['sl_nhap'];
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

        return $export2;
    }

    public function xuathang_baocao_xuathang_tong($tungay = "", $denngay = "", $laixe = "")
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');

        $this->db->select('*');
        $this->db->from('infor_xuathang');
        $this->db->where('date >=', $tungay);
        $this->db->where('date <=', $denngay);
        if( $laixe != ""){
            $this->db->where_in('laixe', $laixe);
        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();

        $_export = array();
        $i = 0;

        $array_note_products = array();
        //get nur array of items

        foreach ($data as $item){
            foreach (json_decode($item['variants']) as $key2 => $item2 ){
                //22.05.2019 change form list_products -> variants ,because report export of product ist wrong , it has with no-infor
                $_item2 = get_object_vars($item2);
                $_item2['laixe'] = $item['laixe'];
                if(isset($_item2['title']) ){
                    $array_note_products[$key2]['title'] = $_item2['title'];
                }

                if(isset($_item2['note'])){
                    $array_note_products[$key2]['item_note_value'] = $_item2['note'];
                }
                $i++;
                $_export[$i] = $_item2;
            }
        }


        $arr_variants = array();
        foreach ($data as $item){
            if(json_decode($item['variants'])){
                foreach (json_decode($item['variants']) as $item_variant){
                    $arr_variants[] = get_object_vars($item_variant);
                }
            }
        }

        $export_end = array();
        $arr_cat_id = array();
        foreach ($_export as $item) {
            /*
            if(!isset($item['quantity'])){
                foreach ($arr_variants as $variant){
                    if(isset($item['variant_id'])){
                        if($variant['variant_id'] == $item['variant_id']){
                            if(!isset($item['quantity'])){
                                $item['quantity'] = $variant['quantity'];
                            }
                        }
                    }
                }
            }
            */

            //get product id
            if($item['variant_id'] != ""){//cac san pham co dau #
                $id_database = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
            }else{
                $id_database = false;
            }

            if($id_database != false){
                $all_infor = $this->m_voxy_package->get_all_infor($id_database);

                if($all_infor){
                    $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                    $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

                    if($check_variant1 == true){
                        foreach ($all_infor as $pro_infor){
                            $item['product_id'] = $pro_infor['id_shopify'];
                            $item['sku'] = $pro_infor['sku1'];
                            $item['title'] = $pro_infor['title'];
                            $item['variant_title'] = $pro_infor['option1'];
                            $item['location'] = $pro_infor['location'];
                            $item['cat_id'] = $pro_infor['cat_id'];
                        }
                    }

                    if($check_variant2 == true){
                        foreach ($all_infor as $pro_infor){
                            $item['product_id'] = $pro_infor['id_shopify'];
                            $item['sku'] = $pro_infor['sku2'];
                            $item['title'] = $pro_infor['title'];
                            $item['variant_title'] = $pro_infor['option2'];
                            $item['location'] = $pro_infor['location'];
                            $item['cat_id'] = $pro_infor['cat_id'];
                        }
                    }
                }
                /*
                 else{
                    $item['product_id'] = $item['variant_id'];
                    $item['sku'] = "";
                    $item['title'] = "";
                    $item['variant_title'] = "";
                    $item['location'] = "";
                    $item['cat_id'] = "";
                }
                */
            }

            //get lai, chang may no thay doi group con biet dc
            if(isset($item['product_id'])){
                $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            }else{
                $cat_id = false;
            }

            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
            if($cat_title == false){
                $cat_title = "G00-Hàng Lẻ Linh Tinh";
                $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
            }
            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export_end[] = $item;

        }

        // step 2: sort tang dan
        ksort($arr_cat_id);

        //truong hop ko co quantity phai get o colum variants

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export_end;
        $return['array_note_products'] = $array_note_products;

        return $return;
    }

    public function xuathang_baocao_xuathang_le($tungay = "", $denngay = "", $laixe = "")
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        //cau lenh sql hier
        $this->db->select('*');
        $this->db->from('infor_xuathang_le');
        $this->db->where('date >=', $tungay);
        $this->db->where('date <=', $denngay);
        if( $laixe != ""){
            $this->db->where_in('laixe', $laixe);
        }
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
                $_item2['laixe'] = $item['laixe'];
                if(isset($_item2['title'])){
                    $array_note_products[$key2]['title'] = $_item2['title'];
                }

                if(isset($_item2['note'])){
                    $array_note_products[$key2]['item_note_value'] = $_item2['note'];
                }
                $i++;

                $_export[$i] = $_item2;
            }
        }

        $export_end = array();
        $arr_cat_id = array();

        foreach ($_export as $item) {
//            $export_end[] = $item;
//            if(isset($item['product_id'])){
//                $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
//                $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
//                $arr_cat_id[$cat_title]['title'] = $cat_title;
//                $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
//            }

            if(isset($item['product_id'])){
                $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            }else{
                $cat_id = false;
            }

            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
            if($cat_title == false){
                $cat_title = "G00-Hàng Lẻ Linh Tinh";
                $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
            }
            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export_end[] = $item;

        }

        // step 2: sort tang dan
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export_end;
        $return['array_note_products'] = $array_note_products;

        return $return;
    }

    public function xuathang_baocao_xuathang_taikho($tungay = "",$denngay = "",$laixe)
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_shippers');
        //cau lenh sql hier
        $this->db->select('*');
        $this->db->from('voxy_transfer_out_kho');
        $this->db->where('date >=', $tungay);
        $this->db->where('date <=', $denngay);
        if($laixe != ""){
            $this->db->where_in('laixe', $laixe);
        }
        $this->db->where('status',1);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();

        $_export = array();
        $i = 0;

        $array_note_products = array();
        //get nur array of items
        foreach ($data as $item){
            foreach (json_decode($item['product_variants']) as $key2 => $item2 ){
                $i++;
                $_item2 = get_object_vars($item2);
                $_item2['laixe'] = $this->m_voxy_shippers->get_name($item['laixe']);

                $_export[$i] = $_item2;
            }
        }
        //var_dump($_export);die;

        /*//step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $export_end = array();
        foreach ($_export as $item){
            $item['quantity'] = $item['sl_nhap'];//sl nhap bang nay chinh la so luong xuat ra khoi kho
            $export_end[] = $item;
            if(isset($item['cat_id'])){
                $id_cat = $this->m_voxy_category->get_id($item['cat_id']);
                $arr_cat_id[$id_cat]['id'] = $id_cat;
                $arr_cat_id[$id_cat]['cat_id'] = $item['cat_id'];
            }
        }

        // step 2: remove cac cai giong
        ksort($arr_cat_id);*/

        $export_end = array();
        $arr_cat_id = array();
        foreach ($_export as $item) {
            $item['quantity'] = $item['sl_nhap'];//sl nhap bang nay chinh la so luong xuat ra khoi kho
//            $export_end[] = $item;
//            $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
//            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
//
//            $arr_cat_id[$cat_title]['title'] = $cat_title;
//            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;

            if(isset($item['product_id'])){
                $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
            }else{
                $cat_id = false;
            }
            $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
            if($cat_title == false){
                $cat_title = "G00-Hàng Lẻ Linh Tinh";
                $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
            }
            $arr_cat_id[$cat_title]['title'] = $cat_title;
            $arr_cat_id[$cat_title]['cat_id'] = $cat_id;

            $item['cat_id'] = $cat_id;
            $export2_new[] = $item;
        }
        // step 2: sort tang dan
        ksort($arr_cat_id);

        $return = array();
        $return['result_catid'] = $arr_cat_id;
        $return['export2'] = $export_end;
        $return['array_note_products'] = $array_note_products;

        return $return;
    }

    public function baocao_get_variants($tungay, $denngay){
        $this->db->select('variants');
        $this->db->from('infor_xuathang');
        $this->db->where("date >= '".$tungay."' AND date <= '".$denngay."' ",null,false);
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

    public function baocao_get_variants_le_listkiem($tungay, $denngay){
        $this->db->select('variants');
        $this->db->from('infor_xuathang_le');
        $this->db->where("date >= '".$tungay."' AND date <= '".$denngay."' ",null,false);
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

            if(isset($whereCondition["custom_where"]['m.ngaydathang'])) {
                $ngaydathang = $whereCondition["custom_where"]["m.ngaydathang"];
            }
            if(isset($whereCondition["custom_where"]['m.ngaygiaohang'])){
                $ngaygiaohang = $whereCondition["custom_where"]['m.ngaygiaohang'];//xuat hang thi la 1 ngay, ccon cai bao cao tong hop thi 2 option
            }
            if(isset($whereCondition["custom_where"]['m.shipper_id'])){
                $laixe = $whereCondition["custom_where"]['m.shipper_id'];
            }

            if(!isset($ngaydathang)){
                if(isset($ngaygiaohang)){
                    $this->db->where("m.shipped_at",$ngaygiaohang);
                }
            }else{
                if(isset($ngaydathang)){
                    $this->db->where("m.shipped_at >=",$ngaydathang);
                }
            }

            if(isset($ngaygiaohang)){
                $this->db->where("m.shipped_at <=",$ngaygiaohang);
            }

            if(isset($laixe)){
                if($laixe != "all"){
                    $this->db->where_in("m.shipper_id",$laixe);
                }
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
        //$this->db->order_by('m.order_number', 'ASC');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $total = $this->db->query('SELECT FOUND_ROWS() AS total')->row()->total;

        return $query->result();
    }

    public function get_list_table_xuathangtaikho_baocaotonghop($search_text = "", $whereCondition = NULL, $limit = 0, $post = 0, $order = NULL, &$total = 0)
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

            if(isset($whereCondition["custom_where"]['m.tungay'])) {
                $tungay = $whereCondition["custom_where"]["m.tungay"];
            }
            if(isset($whereCondition["custom_where"]['m.denngay'])){
                $denngay = $whereCondition["custom_where"]['m.denngay'];
            }

            if(isset($whereCondition["custom_where"]['m.laixe'])){
                $laixe = $whereCondition["custom_where"]['m.laixe'];
            }


//            if(isset($ngaydathang)){
//                $this->db->like("m.created_time",$ngaydathang);
//            }

            if(isset($tungay)){
                $this->db->where("m.shipped_at >=",$tungay);
            }

            if(isset($denngay)){
                $this->db->where("m.shipped_at <=",$denngay);
            }
            if(isset($laixe)){
                if($laixe != "all"){
                    $this->db->where_in("m.shipper_id",$laixe);
                }
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

    public function xuathang_baocao_xuathang_tour($tungay, $denngay,$laixe){
        $this->db->select('*');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at >= '".$tungay."' AND shipped_at <= '".$denngay."' ",null,false);
        $this->db->where('status != ', 'red');
        if($laixe != ""){
            $this->db->where_in('shipper_id', $laixe);
        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }

    public function xuathang_baocao_xuathang_tour_tonghop($tungay, $denngay,$shipper_id,$shipper_area_id){
        $this->db->select('*');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at >= '".$tungay."' AND shipped_at <= '".$denngay."' ",null,false);
        if($shipper_id != false){
            $this->db->where_in("shipper_id",$shipper_id);
        }

        if($shipper_area_id != false){
            $this->db->where_in("ship_area_id",$shipper_area_id);
        }
        $this->db->where('status != ', 'red');
        $query = $this->db->get();

        //var_dump($this->db->last_query());die;

        if ($query->result_array()){
                return $query->result_array();
        }else {
            return false;
        }
    }

    public function xuathang_baocao_xuathang_theotour_tonghop_new($tungay, $denngay,$shipper_id,$shipper_area_id){
        $this->db->select('*');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at >= '".$tungay."' AND shipped_at <= '".$denngay."' ",null,false);
        if($shipper_id != false){
            $this->db->where("shipper_id",$shipper_id);
        }

        if($shipper_area_id != false){
            $this->db->where_in("ship_area_id",$shipper_area_id);
        }
        $this->db->where('status != ', 'red');
        $query = $this->db->get();

        //var_dump($query->result_array() == "");die;

        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function xuathang_baocao_hangve_tonghop($date,$date_end,$shipper_id,$shipper_area_id){
        $this->db->select('order_number');
        $this->db->select('hangve');
        $this->db->select('shipper_id');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at >= '".$date."' AND shipped_at <= '".$date_end."' ",null,false);
        if($shipper_id != false){
            $this->db->where_in("shipper_id",$shipper_id);
        }

        if($shipper_area_id != false){
            $this->db->where_in("ship_area_id",$shipper_area_id);
        }

        $query = $this->db->get();

        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }

    public function xuathang_baocao_hanghong_tonghop($date,$date_end,$shipper_id,$shipper_area_id){
        $this->db->select('hanghong');
        $this->db->select('order_number');
        $this->db->select('shipper_id');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at >= '".$date."' AND shipped_at <= '".$date_end."' ",null,false);
        if($shipper_id != false){
            $this->db->where_in("shipper_id",$shipper_id);
        }
        if($shipper_area_id != false){
            $this->db->where_in("ship_area_id",$shipper_area_id);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }

    public function xuathang_baocao_hangthieu_tonghop($date,$date_end,$shipper_id,$shipper_area_id){
        $this->db->select('hangthieu');
        $this->db->select('order_number');
        $this->db->select('shipper_id');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at >= '".$date."' AND shipped_at <= '".$date_end."' ",null,false);
        if($shipper_id != false){
            $this->db->where_in("shipper_id",$shipper_id);
        }
        if($shipper_area_id != false){
            $this->db->where_in("ship_area_id",$shipper_area_id);
        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }

    public function xuathang_baocao_hangthem_tonghop($date,$date_end,$shipper_id,$shipper_area_id){
        $this->db->select('hangthem');
        $this->db->select('order_number');
        $this->db->select('shipper_id');
        $this->db->from('voxy_package_orders');
        $this->db->where("shipped_at >= '".$date."' AND shipped_at <= '".$date_end."' ",null,false);
        if($shipper_id != false){
            $this->db->where_in("shipper_id",$shipper_id);
        }
        if($shipper_area_id != false){
            $this->db->where_in("ship_area_id",$shipper_area_id);
        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }


    //bao cao nhap hang theo nha cung cap
    public function baocao_nhaphang_nhacungcap($tungay, $denngay, $nhaccc){
        $this->db->select('*');
        $this->db->from('voxy_transfer');
        $this->db->where("date >= '".$tungay."' AND date <= '".$denngay."' ",null,false);
        $this->db->where_in('status', array(1,3));

        if($nhaccc != "") {
            $this->db->where('vendor', $nhaccc);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }

    public function baocao_nhaphang_nhacungcap_hangve($tungay, $denngay, $nhaccc){
        $this->db->select('*');
        $this->db->from('voxy_transfer');
        $this->db->where("date >= '".$tungay."' AND date <= '".$denngay."' ",null,false);
        $this->db->where('status', 3);

        if($nhaccc != "") {
            $this->db->where('vendor', $nhaccc);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }

    public function baocao_nhaphang_nhacungcap_hanghong($tungay, $denngay, $nhaccc){
        $this->db->select('*');
        $this->db->from('voxy_transfer');
        $this->db->where("date >= '".$tungay."' AND date <= '".$denngay."' ",null,false);
        $this->db->where('status', 4);

        if($nhaccc != "") {
            $this->db->where('vendor', $nhaccc);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            foreach ($query->result_array() as $row)
            {
                return $query->result_array();
            }
        }else {
            return false;
        }
    }

}