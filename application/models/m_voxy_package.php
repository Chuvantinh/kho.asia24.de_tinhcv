<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package extends data_base
{
    var $vendor = array(
        '0' => '- Lựa chọn giá trị -',
        'LIL' => 'LIL',
        'HD' => 'Hoang Duc',
        'ASIA24' => 'Asia 24',
        'ASIA KK' => 'Asia KK',
        'Asia sale' => 'Asiasale Haus 9',
    );
    var $arr_status = array(
        '1' => 'Active',
        '0' => 'Deactive',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = 'voxy_package';
        $this->_key_name = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema = Array(
            'id', 'id_shopify', 'cat_id', 'title', 'expri_day', 'location',
            'variant1_id', 'option1', 'price1', 'barcode1', 'sku1', 'inventory_quantity1',
            'variant2_id', 'option2', 'price2', 'barcode2', 'sku2', 'inventory_quantity2',
            'price', 'compare_price', 'image', 'description',
            'product_type', 'vendor', 'status',
            'created_at', 'created_by', 'updated_at', 'updated_by',
            'number_hethang', 'number_luutru',
            'keyword_si', 'mwst', 'le_mindest_price','si_mindest_price','heso_convert'
        );
        $this->_rule = Array(
            'id' => array(
                'type' => 'hidden'
            ),
            'id_shopify' => array(
                'type' => 'hidden'
            ),
            'variant1_id' => array(
                'type' => 'hidden'
            ),
            'variant2_id' => array(
                'type' => 'hidden'
            ),
            'cat_id' => array(
                'type' => 'select',
                'target_model' => 'm_voxy_category',
                'target_value' => 'cat_id',
                'target_display' => 'title',
            ),
            'pack_type' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'title' => array(
                'type' => 'text',
                'maxlength' => 255,
                'required' => 'required',
            ),
            'expri_day' => array(
                'type' => 'datetime',
                'maxlength' => 20,
            ),
            'location' => array(
                'type' => 'select',
                'target_model' => 'm_location',
                'target_value' => 'name',
                'target_display' => 'name',
                'where_condition' => array(
                    'm.status' => 1,
                ),
            ),
            'variants' => array(
                'type' => 'packung_karton',
            ),
            'compare_price' => array(
                'type' => 'float',
                'maxlength' => 11,
            ),
            'heso_convert' => array(
                'type' => 'float',
                'maxlength' => 11,
            ),
            'image' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'description' => array(
                'type' => 'rich_editor',
            ),
            'product_type' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'vendor' => array(
                'type' => 'select',
                'array_list' => $this->vendor,
                'allow_null' => "true",
            ),
            'status' => array(
                'type' => 'select',
                'array_list' => $this->arr_status,
                'allow_null' => "true",
            ),
            'number_hethang' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'number_luutru' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'keyword_si' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'mwst' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'le_midest_price' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
            'si_midest_price' => array(
                'type' => 'text',
                'maxlength' => 255,
            ),
        );
        $this->_field_form = Array(
            'id' => 'ID',
            'id_shopify' => 'ID_Shopify',
            'variant1_id' => 'variant1_id',
            'variant2_id' => 'variant2_id',
            'cat_id' => 'Danh mục',
            'title' => 'Tên',
            'location' => 'Vị trí',
            'variants' => 'Các Loại',
            //'number_hethang' => 'Cài đặt hết hàng',
            //'number_luutru' => 'Cài đặt lưu trữ hàng',
            'heso_convert' => 'Packung/Karton',
            'keyword_si' => 'Tìm nhanh Sỉ',
            'mwst' => 'MWST',
            //'description' => 'Mô tả',
            //'product_type' => 'Loại sản phẩm',
            //'vendor' => 'Nhà cung cấp',
            'status' => 'Trạng thái',
            'expri_day' => 'Hạn sử dụng',
        );

        $this->_field_search = Array(
            'm.title' => 'title',
            'm.sku1' => 'sku1',
            'm.sku2' => 'sku2',
            'm.location' => 'Vị trí',
            'm.keyword_si' => 'Từ Khóa',
        );

        $this->_field_table = Array(
            'm.id' => 'ID',
            'm.sku1' => 'Artikel Nr.',
            //'m.sku2' => 'Sku Si',
            'm.cat_id' => 'Danh Mục',
            'm.title' => 'Tên SP',
            //'m.expri_day' => 'Hạn sử dụng',
            //'m.heso_convert' => 'Hệ số Convert',
            //'m.location' => 'Vị trí',
            //'m.gia_mua_le' => 'giá mua-lẻ',
            //'m.gia_mua_si' => 'giá mua-sỉ',
            //'m.barcode1' => 'Barcode Lẻ',
            //'m.barcode2' => 'Barcode Sỉ',
            'm.inventory_quantity2' => 'SL Sỉ',
            'm.inventory_quantity1' => 'SL Lẻ',
            //'m.price1' => 'giá bán lẻ',
            //'m.price2' => 'giá bán sỉ',
            'm.keyword_si' => 'Từ Khóa',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->select('m.*, cat.status AS cat_status, cat.cat_id AS cat_id, cat.title AS cat_title');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_category AS cat', 'cat.cat_id = m.cat_id');
        $this->db->order_by('m.id', 'DESC');

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

    public function update_variant_id_shopify($id, $variant1_id, $variant2_id)
    {
        $data = array(
            'variant1_id' => $variant1_id,
            'variant2_id' => $variant2_id,
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

    public function get_variant1_id_shopify($list_id)
    {
        $this->db->select('variant1_id');
        $this->db->from($this->_table_name);
        if (is_array($list_id)) {
            $this->db->where_in($this->_key_name, $list_id);
        } else if (intval($list_id) > 0) {
            $this->db->where($this->_key_name, $list_id);
        } else {
            $this->db->where($this->_key_name, json_encode($list_id));
        }
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->variant1_id;
            }
        } else {
            return false;
        }
    }

    public function get_variant2_id_shopify($list_id)
    {
        $this->db->select('variant2_id');
        $this->db->from($this->_table_name);
        if (is_array($list_id)) {
            $this->db->where_in($this->_key_name, $list_id);
        } else if (intval($list_id) > 0) {
            $this->db->where($this->_key_name, $list_id);
        } else {
            $this->db->where($this->_key_name, json_encode($list_id));
        }
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->variant2_id;
            }
        } else {
            return false;
        }
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

    public function update_giaban_le($id, $giaban_le)
    {
        $data = array(
            'price1' => $giaban_le,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_giaban_si($id, $giaban_si)
    {
        $data = array(
            'price2' => $giaban_si,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_giavon_le($id, $giavon_le)
    {
        $data = array(
            'gia_mua_le' => $giavon_le,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_giavon_si($id, $giavon_si)
    {
        $data = array(
            'gia_mua_si' => $giavon_si,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
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

    public function check_variant1($variant_id){
        $this->db->select("*");
        $this->db->distinct();
        $this->db->from($this->_table_name);
        $this->db->where("variant1_id", $variant_id);
        $query = $this->db->get();
        if ($query->result_array()) {
            return true;
        } else {
            return false;
        }
    }

    public function check_variant2($variant_id){
        $this->db->select('variant2_id');
        $this->db->distinct();
        $this->db->from($this->_table_name);
        $this->db->where("variant2_id", $variant_id);
        $query = $this->db->get();
        if ($query->result_array()) {
            return true;
        } else {
            return false;
        }
    }

    public function get_id_from_variant($variantid){
        $this->db->select('id');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("m.variant1_id = "."'$variantid' ".  "OR m.variant2_id = "."'$variantid'", null, false);
        $query = $this->db->get();

       // var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result_array() as $item) {
                return $item['id'];
            }
        } else {
            return false;
        }
    }

    public function get_id_from_sku2($sku2){
        $this->db->select('id');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("sku2",$sku2);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result_array() as $item) {
                return $item['id'];
            }
        } else {
            return false;
        }
    }

    public function get_id_from_sku1($sku1){
        $this->db->select('id');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("sku1",$sku1);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result_array() as $item) {
                return $item['id'];
            }
        } else {
            return false;
        }
    }

    public function get_idshopify_from_variant($variantid){
        $this->db->select('id_shopify');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("m.variant1_id = "." ' $variantid .' ".  "OR m.variant2_id = "." ' $variantid .' ", null, false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result_array() as $item) {
                return $item['id_shopify'];
            }
        } else {
            return false;
        }
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
        $this->db->where("m.barcode1 LIKE '%" . $barcode . "%' OR m.barcode2 LIKE '%" . $barcode . "%' OR m.title LIKE CONCAT('%', CONVERT('".$barcode."', BINARY),'%') OR m.sku1 = '" .$barcode ."' OR m.sku2 = '".$barcode."'", null, false);
        $query = $this->db->get();

        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->id;
            }
        } else {
            return false;
        }
    }

    public function get_id_shopify_from_barcode($barcode)
    {
        $this->db->select('m.id_shopify');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("m.barcode1 LIKE '%" . $barcode . "%' OR m.barcode2 LIKE '%" . $barcode . "%' ", null, false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->id_shopify;
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
        if (empty($id)) {
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
    public function get_search_pro($text)
    {
        if (empty($text)) {
            return false;
        }
        $text = trim($text);
        $this->db->select('*');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("m.barcode1 LIKE '%" . $text . "%' OR m.barcode2 LIKE '%" . $text . "%' 
        OR m.title LIKE '%" . $text . "%' 
        OR m.sku1 LIKE '%" . $text . "%' 
        OR m.sku2 LIKE '%" . $text . "%' 
        OR m.location LIKE '%" . $text . "%' 
        OR m.keyword_si LIKE '%" . $text . "%' ", null, false);
        $this->db->order_by('title', 'DESC');
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

    public function get_all_infor($id){
        $this->db->select('*');
        $this->db->from('voxy_package');
        $this->db->where('id',$id);
        $query = $this->db->get();
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_nur_product_inkho()
    {
        $this->db->select('*');
        $this->db->from('voxy_package');
        $this->db->where("location is NOT NULL", null, false);
        $this->db->where("location  != "," ");
        $this->db->order_by('title', 'ASC');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_location_selected($id)
    {
        $this->db->select('location');
        $this->db->from('voxy_package');
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function check_location_already($id)
    {
        $this->db->select('location');
        $this->db->from('voxy_package');
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->location;
            }
        } else {
            return false;
        }
    }

    public function check_location_used($location){
        $this->db->select('location');
        $this->db->from('voxy_package');
        $this->db->like("location", $location);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->location;
            }
        } else {
            return false;
        }
    }

    public function update_location_variant1($id, $location){
        $data = array(
            'location' => $location,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);

    }

    public function get_sku($name)
    {
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

    public function get_quantity_now_variant1($variant1_id){
        $this->db->select('m.inventory_quantity1');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("variant1_id", $variant1_id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->inventory_quantity1;
            }
        } else {
            return false;
        }
    }

    public function get_quantity_now_variant2($variant2_id){
        $this->db->select('m.inventory_quantity2');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where("variant2_id", $variant2_id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->inventory_quantity2;
            }
        } else {
            return false;
        }
    }
    //cong them vao
    public function update_inventory($quantity_packung, $quantity_verpackung, $id)
    {
//get existing stock quantity
        $this->db->select('m.inventory_quantity1,m.inventory_quantity2');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        $inventory_quantity1 = ($row->inventory_quantity1 != null) ? $row->inventory_quantity1 : 0;//packung
        $inventory_quantity2 = ($row->inventory_quantity2 != null) ? $row->inventory_quantity2 : 0; //verpackung

        //I am assuming that $quantidade is the quantity of the order
        $new_inventory_quantity1 = $inventory_quantity1 + $quantity_packung;
        $new_inventory_quantity2 = $inventory_quantity2 + $quantity_verpackung;

        //update products table
        $this->db->where('id', $id);
        $this->db->update('voxy_package',
            array(
                'inventory_quantity1' => $new_inventory_quantity1,
                'inventory_quantity2' => $new_inventory_quantity2,
            ));
    }

    public function update_plus_inventory1($quantity_packung, $id)
    {
        //get existing stock quantity
        $this->db->select('m.inventory_quantity1');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();

        //var_dump($row->inventory_quantity1);die;

        $inventory_quantity1 = ($row->inventory_quantity1 != null) ? $row->inventory_quantity1 : 0;//packung

        //I am assuming that $quantidade is the quantity of the order
        $new_inventory_quantity1 = $inventory_quantity1 + $quantity_packung;

        //update products table
        $this->db->where('id', $id);
        $this->db->update('voxy_package',
            array(
                'inventory_quantity1' => $new_inventory_quantity1
            ));
    }

    public function update_plus_inventory2($quantity_verpackung, $id)
    {
        //get existing stock quantity
        $this->db->select('m.inventory_quantity2');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        $inventory_quantity2 = ($row->inventory_quantity2 != null) ? $row->inventory_quantity2 : 0;//verpackung

        //I am assuming that $quantidade is the quantity of the order
        $new_inventory_quantity2 = $inventory_quantity2 + $quantity_verpackung;

        //update products table
        $this->db->where('id', $id);
        $this->db->update('voxy_package',
            array(
                'inventory_quantity2' => $new_inventory_quantity2
            ));
    }
    //thay the
    public function update_inventory1($variant1_id,$new_inventory_quantity1){
        $this->db->where('variant1_id', $variant1_id);
        $this->db->update('voxy_package',
            array(
                'inventory_quantity1' => $new_inventory_quantity1
            ));
    }

    public function update_inventory2($variant2_id,$new_inventory_quantity2){
        $this->db->where('variant2_id', $variant2_id);
        $this->db->update('voxy_package',
            array(
                'inventory_quantity2' => $new_inventory_quantity2
            ));
    }

    public function update_minus_inventory1($quantity_packung, $id)
    {
    //get existing stock quantity
        $this->db->select('m.inventory_quantity1');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        $inventory_quantity1 = ($row->inventory_quantity1 != null) ? $row->inventory_quantity1 : 0;//packung
        //$inventory_quantity2 = ($row->inventory_quantity2 != null) ? $row->inventory_quantity2 : 0; //verpackung

        //I am assuming that $quantidade is the quantity of the order
        $new_inventory_quantity1 = $inventory_quantity1 - $quantity_packung;

        //update products table
        $this->db->where('id', $id);
        $this->db->update('voxy_package',
            array(
                'inventory_quantity1' => $new_inventory_quantity1
            ));
    }

    public function update_minus_inventory2($quantity_verpackung, $id)
    {
        //get existing stock quantity
        $this->db->select('m.inventory_quantity2');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        $inventory_quantity2 = ($row->inventory_quantity2 != null) ? $row->inventory_quantity2 : 0;//verpackung

        //I am assuming that $quantidade is the quantity of the order
        $new_inventory_quantity2 = $inventory_quantity2 - $quantity_verpackung;

        //update products table
        $this->db->where('id', $id);
        $this->db->update('voxy_package',
            array(
                'inventory_quantity2' => $new_inventory_quantity2
            ));
    }

//get id tiep theo
    public function get_nex_autocriment_id()
    {
        $row = $this->db->query('
            SELECT AUTO_INCREMENT
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = "kho.asia24.de"
            AND TABLE_NAME = "voxy_package"')->row();
        if ($row) {
            return $row->AUTO_INCREMENT;
        } else {
            return false;
        }
    }

    public function  get_list_title(){
        $this->db->select('id');
        $this->db->select('title');
        $this->db->from('voxy_package');
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function  get_title_productid ($id_shopify){
        $this->db->select('title');
        $this->db->from('voxy_package');
        $this->db->where('id_shopify', $id_shopify);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->title;
            }
        } else {
            return false;
        }
    }

    public function get_old_collection_id($id){
        $this->db->select('cat_id');
        $this->db->from('voxy_package');
        $this->db->where('id',$id);
        $query = $this->db->get();
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->cat_id;
            }
        } else {
            return false;
        }
    }

    public function get_all_products(){
        $this->db->select('id');
        $this->db->select('cat_id');
        $this->db->from('voxy_package');
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_all_products_inkho(){
        $this->db->select('*');
        $this->db->from('voxy_package');
        $this->db->where('location !='," ");
        $this->db->order_by('location', 'asc');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function update_mwst($id,$mwst){
        $data = array(
            'mwst' => $mwst,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);

    }

    public function get_mwst ($sku){
        $this->db->select('mwst');
        $this->db->from('voxy_package');

        $this->db->where('sku1 = "'.$sku.'" || sku2 = "'.$sku.'" ', null,false);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->mwst;
            }
        } else {
            return false;
        }
    }

    public function get_variant_id_from_id($id){
        $this->db->select('variant1_id');
        $this->db->select('variant2_id');
        $this->db->from('voxy_package');
        $this->db->where('id', $id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_id_shopify_from_variant_ids($variant1_id, $variant2_id) {
        $this->db->select('id_shopify');
        $this->db->from('voxy_package');
        $this->db->where('variant1_id', $variant1_id);
        $this->db->where('variant2_id', $variant2_id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->id_shopify;
            }
        } else {
            return false;
        }
    }

    public function get_gia_mua_le($id){
        $this->db->select('gia_mua_le');
        $this->db->from('voxy_package');
        $this->db->where("id",$id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->gia_mua_le;
            }
        } else {
            return false;
        }
    }

    public function get_gia_mua_si($id){
        $this->db->select('gia_mua_si');
        $this->db->from('voxy_package');
        $this->db->where("id",$id);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            foreach ($query->result() as $row) {
                return $row->gia_mua_si;
            }
        } else {
            return false;
        }
    }

}