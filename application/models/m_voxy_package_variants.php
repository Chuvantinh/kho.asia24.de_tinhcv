<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_package_variants extends data_base
{
    var $vendor = array(
        '0'         => '- Lựa chọn giá trị -',
        'LIL' => 'LIL',
        'HD'    => 'Hoang Duc',
        'ASIA24'    => 'Asia 24',
        'ASIA KK'    => 'Asia KK',
        'Asia sale'    => 'Asiasale Haus 9',

    );
    var $arr_status = array (
        '1' => 'Active',
        '0' => 'Deactive',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name          = 'voxy_package_variants';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id','id_shopify', 'variant_id', 'variant_title',
            'location', 'expri_day', 'id_location', 'id_expriday',
            'price','barcode','sku','inventory_quantity',
            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'id_shopify'            => array(
                'type'              => 'select',
                'target_model'      => 'm_voxy_package',
                'target_value'      => 'id_shopify',
                'target_display'    => 'title',
            ),
            'variant_id'            => array(
                'type'          => 'hidden'
            ),

            'variant_title'        => array(
                'type'              => 'text',
                'maxlength'     => 255,
                'required'      => 'required',
            ),
            'expri_day' => array(
                'type'          => 'datetime',
                'maxlength'     => 20,
                'required'      => 'required',
            ),
            'location'     => array(
                'type'              => 'select',
                'target_model'      => 'm_location',
                'target_value'      => 'name',
                'target_display'    => 'name',
                'where_condition'   => array(
                    'm.status'          => 1,
                ),
            ),
            'price'     => array(
                'type'          => 'float',
                'maxlength'     => 11,
            ),
            'barcode'     => array(
                'type'          => 'text',
                'maxlength'     => 11,
            ),
            'sku'     => array(
                'type'          => 'text',
                'maxlength'     => 11,
            ),
            'inventory_quantity'     => array(
                'type'          => 'text',
                'maxlength'     => 11,
            ),
        );
        $this->_field_form  = Array(
            'id'            => 'ID',
            'id_shopify'    => 'Tên sản phẩm cha',
            'variant_id' => 'id variant',
            'variant_title'         => 'Đơn vị',
            'expri_day'     => 'Hạn sử dụng',
            'location'      => 'Vị trí',
            'price'   => 'Giá',
            'barcode'  => 'Mã Barcode',
            'sku'        => 'Mã Sản Phẩm',
            'inventory_quantity'        => 'Số lượng',
        );
        $this->_field_table = Array(
            'm.id'          => 'ID',
            'm.id_shopify'        => 'Sản phẩm cha',
            'm.variant_title'       => 'Đơn vị',
            'm.expri_day'   => 'Hạn sử dụng',
            'm.location'    => 'Vị trí',
            'm.inventory_quantity'    => 'Số lượng'
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*, cat.status AS cat_status, cat.cat_id AS cat_id, cat.title AS cat_title');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_package AS cat', 'cat.id_shopify = m.id_shopify');

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
    public function check_exist_barcode($barcode = '', $id = 0){
        if(!((is_string($barcode) && trim($barcode) != '') || (intval($barcode) && $barcode))){
            return NULL;
        }

        $barcode = trim($barcode);
        $this->setting_select();
        $this->db->where('m.barcode', $barcode);
        if($id){
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
     * @param Int| array $where  Điều kiện dạng tùy biến int hoặc Array;
     *                          <p>Nếu là Array thì điều kiện là WHERE_IN $this->key=$where</p>
     *                          <p>Nếu là Int thì điều kiện là WHERE $this->key=$where</p>
     * @return  array list of id shopify
     */
    public function update_variant_id($id, $variant_id)
    {
        $data = array(
            'variant_id' => $variant_id,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function get_variant_id($list_id){
        $this->db->select('variant_id');
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

    public function check_id_shopify($id){
        $this->db->select('id');
        $this->db->from($this->_table_name);
        $this->db->where("id_shopify", $id);
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

    public function update_id_location_variant($id, $id_location)
    {
        $data = array(
            'id_location' => $id_location,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function update_id_expriday_variant($id, $expriday)
    {
        $data = array(
            'id_expriday' => $expriday,
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table_name, $data);
    }

    public function get_meta_location($id){
        $this->db->select('id_location');
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id_location;
            }
        }else {
            return false;
        }
    }

    public function get_meta_expri_day($id){
        $this->db->select('id_expriday');
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id_expriday;
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

    public  function get_product_expriday (){


    }

    public function get_categories($product_id){
        $this->db->select('cat_id');
        $this->db->distinct();
        $this->db->from($this->_table_name);
        $this->db->where("id_shopify", $product_id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result_array() as $item){
                return $item['cat_id'];
            }
        }else {
            return false;
        }
    }

    public function get_id($barcode){

        $this->db->select('id');
        $this->db->from($this->_table_name);
        $this->db->where("barcode1 =".$barcode. " or barcode2 =".$barcode,null,false);
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

    public function get_all_variants($id_shopify){
        $this->db->select('*');
        //$this->db->distinct();
        $this->db->from($this->_table_name);
        $this->db->where("id_shopify", $id_shopify);
        $query = $this->db->get();
        if ($query->result_array()){
            return $query->result_array();
        }else {
            return false;
        }
    }

    public function get_id_shopify ($id){
        $this->db->select('id_shopify');
        //$this->db->distinct();
        $this->db->from($this->_table_name);
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->result_array()){
            foreach ($query->result() as $row)
            {
                return $row->id_shopify;
            }
        }else {
            return false;
        }
    }
}