<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package_orders
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_chiphi_laixe extends data_base
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
        $this->_table_name = 'voxy_chiphi_laixe';
        $this->_key_name = 'id';
        //$this->_key_name = 'shipper_id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'laixe_id', 'tour_id', 'bienso', 'loaixe', 'chu_so_huu', 'ghichu',
            'tienxang', 'tienthuexe', 'khauhaoxe', 'chiphikhac','tongchiphi','lydo','shipped_at',
            'created_time', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'
        );

        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),

            'laixe_id' => array(
                    'type' => 'select',
                    'target_model' => 'm_voxy_shippers',
                    'target_value' => 'id',
                    'target_display' => 'first_name'
            ),

            'tour_id' => array(
                'type' => 'select',
                'target_model' => 'm_dongxuan_ship_areas',
                'target_value' => 'id',
                'target_display' => 'name',
                'where_condition' => array(
                    'm.status' => 1,
                ),
            ),

            'bienso' => array('type' => 'text',),
            'loaixe' => array('type' => 'text'),
            'chu_so_huu' => array('type' => 'text'),
            'ghichu' => array('type' => 'text'),

            'tienxang' => array('type' => 'text'),
            'tienthuexe' => array('type' => 'text'),
            'khauhaoxe' => array('type' => 'text'),
            'chiphikhac' => array('type' => 'text'),
            'tongchiphi' => array('type' => 'text'),
            'lydo' => array('type' => 'text'),

            'shipped_at' => array('type' => 'datetime'),

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
                'type' => 'select',
                'array_list' => $this->arr_status,
                'allow_null' => "true",
            ),
        );
        // form khi click them
        $this->_field_form = Array(
            'shipped_at' => 'Ngày giao hàng',
            'laixe_id' => 'Lái xe',
            'tour_id' => 'Tour',
            'bienso' => 'Biển số',
            'loaixe' => 'Loại xe',

            'tienxang' => 'Tiền xăng',
            'tienthuexe' => 'Tiền thuê xe',
            'khauhaoxe' => 'Khấu hao xe',
            'chiphikhac' => 'Chi phí khác',
            //'tongchiphi' => 'Tổng chi phí',
            'lydo' => 'Lý do cho chi phí khác',
            'ghichu' => 'Ghi chú',
            'chu_so_huu' => 'Xe thuê/Xe riêng',
            'status' => 'Trạng thái',
        );
        //table hien thi du lieu san pham
        $this->_field_table = Array(
            'm.shipped_at' => 'Ngày Giao',
            'm.laixe_id' => 'Lái xe',
            'm.bienso' => 'Biển số',
            'm.loaixe' => 'Loại xe',
            'm.chu_so_huu' => 'Chủ sở hữu',
            'm.tongchiphi' => 'Tổng chi phí',
            'm.ghichu' => 'Ghi Chú',
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
        $this->db->from('voxy_chiphi_laixe');
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
    public function get_data_pdf($order_day){
        if ($order_day == false){
            $order_day  = date("Y-m-d");
        }
        $this->db->select('line_items');
        $this->db->from('voxy_chiphi_laixe');
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
        $this->db->from('voxy_chiphi_laixe');
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
                                $item['total_price'] = (float)$item['total_price'] + (float)$item2['total_price'];
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

        //var_dump($data);die('update');

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
            $data['created_at'] = date('Y-m-d H:i:s', time());
            $data['created_by'] = $this->user_id;
        }

        //var_dump($data);die('add');

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

    public function check_import($laixe_id, $shipped_at){
        $this->db->select('id');
        $this->db->from('voxy_chiphi_laixe');
        $this->db->where("laixe_id", $laixe_id);
        $this->db->where("shipped_at", $shipped_at);
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

    public function get_all_infor($laixe_id, $shipped_at){
        $this->db->select('*');
        $this->db->from('voxy_chiphi_laixe');
        $this->db->where("laixe_id", $laixe_id);
        $this->db->where("shipped_at", $shipped_at);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
                return $query->result_array();
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
        $this->db->select('time_lan1');
        $this->db->select('note');
        $this->db->select('shipped_at');
        $this->db->select('shipper_name');

        $this->db->from('voxy_chiphi_laixe');
        //$this->db->where_in("customer_id", $customer_id);
        if($shipper_id != ""){
            $this->db->where_in('shipper_id',$shipper_id);
        }
        if($list_time_lan1){
            $this->db->where_in("time_lan1", $list_time_lan1);
        }

        if($order_number_old){
            $this->db->where_not_in("order_number", $order_number_old);
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
        $this->db->from('voxy_chiphi_laixe');
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

    public function update_checked_xuathang($id,$data){
        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
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

        if (get_object_vars(json_decode($this->session->userdata('voxy_chiphi_laixe_sessions')))['custom_where']) {

            $where_session = get_object_vars(get_object_vars(json_decode($this->session->userdata('voxy_chiphi_laixe_sessions')))['custom_where']);
            $where_session_like = get_object_vars(json_decode($this->session->userdata('voxy_chiphi_laixe_sessions')))['custom_like'];

            if(isset($where_session['m.tungay'])){
                $tungay = $where_session['m.tungay'];
            }

            if(isset($where_session['m.denngay'])){
                $denngay = $where_session['m.denngay'];
            }
            if(isset($where_session['m.laixe'])){
                $laixe = $where_session['m.laixe'];//where_in
            }
            if(isset($where_session['m.ship_areas'])){
                $ship_areas = $where_session['m.ship_areas'];//where_in
            }

            if(isset($tungay)){
                $this->db->where('m.shipped_at >=', $tungay);
            }

            if(isset($denngay)){
                $this->db->where('m.shipped_at <=', $denngay);
            }

            if(isset($laixe)){
                if($laixe != "all" && $laixe != ""){
                    $this->db->where_in('m.laixe_id', $laixe);
                }
            }

            if(isset($ship_areas)){
                if($ship_areas != "all" && $ship_areas != ""){
                    $this->db->where_in('m.tour_id', $ship_areas);
                }
            }

        }else if (intval($whereCondition) > 0) {
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
        $this->db->from('voxy_chiphi_laixe');
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
        $this->db->from('voxy_chiphi_laixe');

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
        $this->db->from('voxy_chiphi_laixe');

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
                foreach (json_decode($item['hanghong']) as $item_con){
                    $_item = get_object_vars($item_con);
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

    public function get_infor_theo_ngay_hangthem($ngay_giao_hang,$ngay_dat_hang)
    {
        $this->db->select('hangthem');
        $this->db->from('voxy_chiphi_laixe');

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
                foreach (json_decode($item['hangthem']) as $item_con){
                    $_item = get_object_vars($item_con);
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

    public function get_infor_theo_ngay_hangthieu($ngay_giao_hang,$ngay_dat_hang)
    {
        $this->db->select('hangthieu');
        $this->db->from('voxy_chiphi_laixe');

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
                    $_item = get_object_vars($item_con);
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
        $this->db->from('voxy_chiphi_laixe');
        $this->db->like("shipped_at", $date);
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
        $this->db->from('voxy_chiphi_laixe');
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
        $this->db->from('voxy_chiphi_laixe');
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

    public function get_chiphilaixe($ngay_chuyen_hang,$shipper_id){
        $this->db->select('*');
        $this->db->from('voxy_chiphi_laixe');

        $this->db->where("shipped_at", $ngay_chuyen_hang);
        $this->db->where_in("laixe_id", $shipper_id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }
}