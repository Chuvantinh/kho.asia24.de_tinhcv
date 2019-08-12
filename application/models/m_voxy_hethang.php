<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_hethang extends data_base
{
    var $vendor = array(
        '0'         => '- Lựa chọn giá trị -',
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
        $this->_table_name          = 'voxy_package';
        $this->_key_name            = 'id';
        $this->_exist_created_field = true;
        $this->_exist_updated_field = true;
        $this->_exist_deleted_field = false;
        $this->_schema      = Array(
            'id','id_shopify', 'cat_id', 'title', 'expri_day', 'location',
            'option1','price1','barcode1','sku1','inventory_quantity1',
            'option2','price2','barcode2','sku2','inventory_quantity2',
            'price', 'compare_price','image','description',
            'product_type', 'vendor','status',
            'created_at', 'created_by', 'updated_at', 'updated_by'
        );
        $this->_rule        = Array(
            'id'            => array(
                'type'          => 'hidden'
            ),
            'id_shopify'            => array(
                'type'          => 'hidden'
            ),
            'cat_id'        => array(
                'type'              => 'select',
                'target_model'      => 'm_voxy_category',
                'target_value'      => 'cat_id',
                'target_display'    => 'title',
            ),
            'pack_type'     => array(
                'type'          => 'text',
                'maxlength'     => 255,
            ),
            'title'     => array(
                'type'          => 'text',
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

            'variants'     => array(
                'type'          => 'packung_karton',
            ),
            'compare_price'     => array(
                'type'          => 'float',
                'maxlength'     => 11,
            ),
            'image'     => array(
                'type'          => 'text',
                'maxlength'     => 255,
            ),
            'description'   => array(
                'type'          => 'rich_editor',
            ),
            'product_type'     => array(
                'type'          => 'text',
                'maxlength'     => 255,
            ),
            'vendor'     => array(
                'type'          => 'select',
                'array_list'    => $this->vendor,
                'allow_null'    => "true",
            ),
            'status'        => array(
                'type'          => 'select',
                'array_list'    => $this->arr_status,
                'allow_null'    => "true",
            )
        );
        $this->_field_form  = Array(
            'id'            => 'ID',
            'id_shopify'    => 'ID_Shopify',
            'cat_id'        => 'Danh mục',
            'title'         => 'Tên',
            'expri_day'     => 'Hạn sử dụng',
            'location'      => 'Vị trí trong kho hàng',
            'variants'      => 'Cac Loai san pham',
            'description'   => 'Mô tả sản phẩm',
            'product_type'  => 'Loại sản phẩm',
            'vendor'        => 'Người bán',
            'status'        => 'Trạng thái',
        );
        $this->_field_table = Array(
            'm.id'          => 'ID',
            //'m.cat_id'        => 'Danh Mục',
            'm.title'       => 'Tên SP',
            'm.inventory_quantity1'       => 'Sl Le',
            'm.inventory_quantity2'       => 'Sl Si',
            //'m.expri_day'   => 'Hạn sử dụng',
            'm.location'    => 'Vị trí',
            //'m.status'      => 'Trạng thái',
        );
    }

    public function setting_select()
    {
        $this->db->select('m.*, cat.status AS cat_status, cat.cat_id AS cat_id, cat.title AS cat_title');
        $this->db->from($this->_table_name . ' AS m');
        $this->db->join('voxy_category AS cat', 'cat.cat_id = m.cat_id');
        $this->db->where('m.inventory_quantity1 < 100 OR m.inventory_quantity2 < 100',null,false);

        if (isset($this->custom_conds["custom_where"]) && count($this->custom_conds["custom_where"]) > 0) {
            $custom_where = $this->custom_conds["custom_where"];
            $this->db->where($custom_where);
        }
        if (isset($this->custom_conds["custom_like"]) && count($this->custom_conds["custom_like"]) > 0) {
            $custom_like = $this->custom_conds["custom_like"];
            $this->db->like($custom_like);
        }
    }
}