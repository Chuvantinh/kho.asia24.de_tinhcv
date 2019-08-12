<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_package
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_package_variants extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_package_variants",
            "view" => "voxy_package_variants",
            "model" => "m_voxy_package_variants",
            "object" => "Variants  Của Sản Phẩm"
        );
    }

    public function index()
    {
        $this->manager();
    }

    /**
     * @param array $data Mang du lieu truyen ra view
     *
     * @author chuvantinh1991@gmail.com
     */
    public function manager($data = array())
    {
        $json_conds = $this->session->userdata('arr_package_search');
        $json_conds = json_decode($json_conds, TRUE);

        $data['form_conds'] = (array)$json_conds;
        $this->load->model('m_voxy_category');
        $data['category'] = $this->m_voxy_category->get_category();

        $data['list_status'] = $this->data->arr_status;

        parent::manager($data);
    }

    /**
     * Ham xu ly thong tin tim kiem
     * @param array $data
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_search_condition($data = array())
    {
        if (!count($data)) {
            $data = $this->input->get();
        }

        $where_data = array();
        $like_data = array();
        $list_field = array('cat_id', 'status');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'cat_id':
                        if ($data['cat_id'] != '') {
                            $where_data['m.cat_id'] = $data['cat_id'];
                        }
                        break;
                    case 'status':
                        if ($data['status'] != '') {
                            $where_data['m.status'] = $data['status'];
                        }
                        break;
                }
            }
        }

        $data_return = array(
            'custom_where' => $where_data,
            'custom_like' => $like_data
        );
        $this->session->set_userdata('arr_package_search', json_encode($data_return));
        return $data_return;
    }

    /**
     * Hàm lấy dữ liệu của một danh sách bản ghi
     * Hàm này có cấu trúc nhận dữ liệu POST khá phức tạp bao gồm
     *      - q     => chuỗi tìm kiếm
     *      - limit => Số bản ghi muốn lấy ra
     *      - order => sắp xếp theo thứ tự nào
     *      - page  => trang đang xem
     * Mặc định các biến này được quản lý ở file form.js, chỉ cần quan tâm khi viết đè
     * @param Array $data Biến muốn gửi thêm để hiển thị ra view(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     * @return json Gửi dữ liệu json về client
     */
    public function ajax_list_data($data = Array())
    {
        $data_get = $this->input->get();
        if ($data_get && is_array($data_get)) {
            $this->data->custom_conds = $this->get_search_condition($data_get);
        } else {
            $json_conds = $this->session->userdata('arr_package_search');
            $json_conds = json_decode($json_conds, TRUE);
            if (isset($json_conds['custom_where']) && count($json_conds['custom_where']) == 0 && isset($json_conds['custom_like']) && count($json_conds['custom_like']) == 0) {
                $this->data->custom_conds = $this->get_search_condition();
            } else {
                $this->data->custom_conds = $json_conds;
            }
        }
        parent::ajax_list_data($data);
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        $this->load->model('m_voxy_category', 'category');
        /* Tùy biến dữ liệu các cột */
        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            $record->custom_action = '<div class="action"><a class="detail e_ajax_link icon16 i-eye-3 " per="1" href="' . site_url($this->url["view"] . $record->$key_table) . '" title="Xem"></a>';
            if (!isset($record->editable) || (isset($record->editable) && $record->editable)) {
                $record->custom_action .= '<a class="edit e_ajax_link icon16 i-pencil" per="1" href="' . site_url($this->url["edit"] . $record->$key_table) . '" title="Sửa"></i></a>';
                $record->custom_action .= '<a class="delete e_ajax_confirm e_ajax_link icon16 i-remove" per="1" href="' . site_url($this->url["delete"] . $record->$key_table) . '" title="Xóa"></a></div>';
            }
            $record->custom_check = "<input type='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

            if (isset($record->status) && isset($this->data->arr_status)) {
                $record->status = (isset($this->data->arr_status[$record->status]) ? $this->data->arr_status[$record->status] : $record->status);
            }

            if (isset($record->created_at) && intval($record->created_at)) {
                $record->created_at = date('d-m-Y H:i', intval($record->created_at));
            }

            if (isset($record->parent_status) && isset($this->category->arr_status)) {
                $record->parent_status = (isset($this->category->arr_status[$record->parent_status]) ? $this->category->arr_status[$record->parent_status] : $record->parent_status);
            }
            if (isset($record->id_shopify) && isset($record->id_shopify)) {
                $this->load->model('m_voxy_package');
                $record->id_shopify = $this->m_voxy_package->get_name_from_id($record->id_shopify);
            }
        }
        return $record;
    }

    public function add_save($data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }

        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_variants');

        $data_return["callback"] = "save_form_add_response";
        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        if (!isset($data['variant_title'])) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Dữ liệu Titel SP  không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        //du lieu post lay dc tu form them
        $id_shopify = $data['id_shopify'];
        $variant_title = $data['variant_title'];
        $location = $data['location'];
        $expri_day = $data['expri_day'];
        $price = $data['price'];
        $barcode = $data['barcode'];
        $sku = $data['sku'];
        $inventory_quantity = $data['inventory_quantity'];
        //end lay du lieu tu form

        $variant_data['variant'] = array(
            'option1' => $variant_title,
            'price' => $price,
            'inventory_policy' => 'continue',
            'inventory_management' => 'shopify',
            'inventory_quantity' => $inventory_quantity,
            'sku' => $sku,
            'barcode' => $barcode,
            'metafields' => [
                array(
                    "key" => "expri_day",
                    "value" => $expri_day,
                    "value_type" => "string",
                    "namespace" => "global"
                ),
                array(
                    "key" => "location",
                    "value" => $location,
                    "value_type" => "string",
                    "namespace" => "global"
                )
            ],
        );

        //day du lieu len may chu
        $data_post = json_encode($variant_data);
        $result = $this->m_voxy_connect_api_tinhcv->shopify_add_variant($id_shopify, $data_post);

        //them data vao database
        $insert_id = $this->data->add($data);

        $data[$this->data->get_key_name()] = $insert_id;
        //end them vao database
        if ($insert_id) {
            //get variant id tu ket qua tra ve , cap nhat lai vao database
            $array = get_object_vars($result["variant"]);
            $variant_id = $array['id'];
            $this->m_voxy_package_variants->update_variant_id($insert_id, $variant_id);
            //end update variant id  to database

            //get id_location vs id_expriday for database from result,sau do cap nhat vao database,de sau nay edit dc
            //and update vaof data base
            $result_location_expriday = $this->m_voxy_connect_api_tinhcv->shopify_get_variants_metafield($id_shopify, $variant_id);
            $id_location = 0;
            $id_expriday = 0;

            foreach ($result_location_expriday['metafields'] as $_item) {
                $item = get_object_vars($_item);
                if ($item['key'] == 'expri_day') {
                    $id_expriday = $item['id'];
                } elseif ($item['key'] == 'location') {
                    $id_location = $item['id'];
                } else {

                }
            }

            $this->m_voxy_package_variants->update_id_location_variant($insert_id, $id_location);
            $this->m_voxy_package_variants->update_id_expriday_variant($insert_id, $id_expriday);
            //end xu ly metafield

            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $data;
            if ($result) {
                $data_return["state"] = 1; /* state = 1 : insert thành công */
                $data_return["msg"] = "Thêm bản ghi thành công vào database và may chu";
            } else {
                $data_return["state"] = 0; /* state = 1 : insert ko thành công */
                $data_return["msg"] = "Thêm bản ghi không thành công  vào may chu , database thì ok";
            }

            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            echo json_encode($data_return);
            return $insert_id;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Thêm bản ghi thất bại, vui lòng thử lại sau";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    public function edit($id = 0, $data = Array())
    {
        parent::edit($id, $data);
    }

    public function edit_save($id = 0, $data = Array(), $data_return = Array(), $re_validate = true)
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return false;
        }
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_variants');

        $data_return["callback"] = "save_form_edit_response";
        $id = intval($id);
        if (!$id) {
            $data_return["state"] = 0; /* state = 0 : dữ liệu không hợp lệ */
            $data_return["msg"] = "Bản ghi không tồn tại !";
            echo json_encode($data_return);
            return FALSE;
        }

        if (sizeof($data) == 0) {
            $data = $this->input->post();
        }

        //get data post and edit in shopify before

        //du lieu post lay dc tu form them
        $id_shopify = $data['id_shopify'];
        $variant_id = $data['variant_id'];
        $variant_title = $data['variant_title'];
        $expri_day = $data['expri_day'];
        $location = $data['location'];
        $price = $data['price'];
        $barcode = $data['barcode'];
        $sku = $data['sku'];
        $inventory_quantity = $data['inventory_quantity'];

        //cap nhat lai metafields truoc ---------------------------------------------------
        $id_location = $this->data->get_meta_location($id);
        $id_expri_day = $this->data->get_meta_expri_day($id);


        if ($id_location != null && $id_expri_day != null) { //neu da ton tai id location and id expriday
            $_data_location['metafield'] = array(
                'id' => $id_location,
                'value' => $location,
            );
            $data_location = json_encode($_data_location);

            $_data_expriday['metafield'] = array(
                'id' => $id_expri_day,
                'value' => $expri_day,
            );
            $data_expriday = json_encode($_data_expriday);

            // step 2 update that  PUT, PUT /admin/metafields/#{metafield_id}.json
                // neu k co gi thay doi thi k phai update
            if ($id_location != null) {
                $metafield_location = $this->m_voxy_connect_api_tinhcv->shopify_update_metafields($id_location, $data_location);
            }

            if ($expri_day != null) {
                $metafield_expri_day = $this->m_voxy_connect_api_tinhcv->shopify_update_metafields($id_expri_day, $data_expriday);
            }
            //edit variant
//            $variant_data['variant'] = array(
//                'id' => $variant_id,
//                'option1' => $variant_title,
//                'price' => $price,
//                'inventory_policy' => 'continue',
//                'inventory_management' => 'shopify',
//                'inventory_quantity' => $inventory_quantity,
//                'sku' => $sku,
//                'barcode' => $barcode,
//            );
            //phai get tat ca variant cua id shopify
            $update = $this->data->update($id, $data);//add to database
            $variant_items = $this->data->get_all_variants($id_shopify);

            foreach ($variant_items as $key =>$item) {
                $array_variants[] =
                    array(
                        'option1' => $item['variant_title'],
                        'price' => $item['variant_title'],
                        'inventory_policy' => 'continue',
                        'inventory_management' => 'shopify',
                        'inventory_quantity' => $item['inventory_quantity'],
                        'sku' => $item['sku'],
                        'barcode' => $item['barcode'],
                    );
            }

            $product_data['product'] = array(
                'id' => $id_shopify,
                'variants' =>  $array_variants,
            );
            //edit  variant of product

            //day du lieu len shopify
            $data_post = json_encode($product_data);
            //$result = $this->m_voxy_connect_api_tinhcv->shopify_edit_variant($variant_id, $data_post);
            $result = $this->m_voxy_connect_api_tinhcv->shopify_edit_product($id_shopify, $data_post);

            //----------------------------------------------------------------------------------------
        } else { //trung hop chua co  id_location und id_expri_day , ta phai them vao variant , sau do cap nhat ve database nhu add_save
            // them moi luc nhap lieu chi co title va barcode
            $variant_data['variant'] = array(
                    'option1' => $variant_title,
                    'price' => $price,
                    'inventory_policy' => 'continue',
                    'inventory_management' => 'shopify',
                    'inventory_quantity' => $inventory_quantity,
                    'sku' => $sku,
                    'barcode' => $barcode,
                    'metafields' => [
                        array(
                            "key" => "expri_day",
                            "value" => $expri_day,
                            "value_type" => "string",
                            "namespace" => "global"
                        ),
                        array(
                            "key" => "location",
                            "value" => $location,
                            "value_type" => "string",
                            "namespace" => "global"
                        )
                    ],
                );
            //day du lieu len shopify
            $data_post = json_encode($variant_data);
            $result = $this->m_voxy_connect_api_tinhcv->shopify_add_variant($id_shopify, $data_post);
            $insert_id = $this->data->add($data); // them du lieu vao database
            $array = get_object_vars($result["variant"]);
            $variant_id = $array['id'];
            $this->m_voxy_package_variants->update_variant_id($insert_id, $variant_id);
            //get id_location vs id_expriday for database from id collection,sau do cap nhat vao database,de sau nay edit dc
            $result_location_expriday = $this->m_voxy_connect_api_tinhcv->shopify_get_variants_metafield($id_shopify, $variant_id);
            $id_location = 0;
            $id_expriday = 0;
            foreach ($result_location_expriday['metafields'] as $_item) {
                $item = get_object_vars($_item);
                if ($item['key'] == 'expri_day') {
                    $id_expriday = $item['id'];
                } elseif ($item['key'] == 'location') {
                    $id_location = $item['id'];
                } else {

                }
            }
            $this->m_voxy_package_variants->update_id_location_variant($id, $id_location);
            $this->m_voxy_package_variants->update_id_expriday_variant($id, $id_expriday);
            //end id_location id_expri_day cho vao database

        }

        //du lieu tra ve sau khi post
        if (!$result) {
            //$this->response(array('status' => 'failed'),200);
            $data_return["state"] = 0; /* state = 1 : insert không thành công */
            $data_return["msg"] = "Sửa bản ghi không thành công vao shopify";
        } else {
            //$this->response(array('status' => 'success'), 1709);
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sửa bản ghi thành công vao shopify";
        }
        //end to shopify


        if ($update) {
            $data_return["key_name"] = $this->data->get_key_name();
            $data_return["record"] = $this->_process_data_table($this->data->get_one($id));
            $data_return["state"] = 1; /* state = 1 : insert thành công */
            $data_return["msg"] = "Sửa bản ghi thành công !";
            $data_return["redirect"] = isset($data_return['redirect']) ? $data_return['redirect'] : "";

            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"] = 2; /* state = 2 : Lỗi thêm bản ghi */
            $data_return["msg"] = "Sửa bản ghi thất bại, vui lòng thử lại sau !";
            echo json_encode($data_return);
            return FALSE;
        }
    }

    public function delete($id = 0, $data = Array())
    {
        if (FALSE) { //Kiểm tra phân quyền
            redirect();
            return FALSE;
        }
        $this->load->model('m_voxy_connect_api_tinhcv');
        $this->load->model('m_voxy_package_variants');

        $data_return["callback"] = "delete_respone";
        $id = intval($id);
        if ($this->input->post() || $id > 0) {
            if (isset($data["list_id"]) && sizeof($data["list_id"])) {
                $list_id = $data["list_id"];
            } else {
                if ($this->input->post() && $id == "0") {
                    $list_id = $this->input->post("list_id");
                } elseif ($id > 0) {
                    $list_id = Array($id);
                }
            }
            //get variant_id from id
            $variant_id = $this->m_voxy_package_variants->get_variant_id($list_id); //cho nay phai check lai xoa variant
            $id_shopify = $this->m_voxy_package_variants->get_id_shopify($id);

            //remove per Curl in model list
            $_variant_id = 0;
            foreach ($variant_id as $item){
                $_variant_id = $item['variant_id'];
            }

            $result = $this->m_voxy_connect_api_tinhcv->shopify_delete_variant($id_shopify, $_variant_id);

            // lay du lieu luu lich su xoa
            $data_history = array();
            foreach ($list_id as $one_id) {
                $data_history[] = $this->data->get_one($one_id, 'object');
            }

            $this->data->delete_by_id($list_id);//xoa du lieu san pham
            //du lieu tra ve sau khi delete
            if (!$result) {
                $data_return["state"] = 0; /* state = 0 : delete that bai */
                $data_return["msg"] = "Xoá bản ghi không thành công trên hệ thống  may chu";
            } else {
                $data_return["state"] = 1; /* state = 1 : delete thành công */
                $data_return["msg"] = "Xoá bản ghi thành công trên hệ thống may chu";
            }
            if ($affted_row) {
                try {
                    $this->load->model('m_voxy_package_history', 'package_history');
                    foreach ($data_history as $one_history) {
                        $data_history = array(
                            'pack_code' => isset($one_history->pack_code) ? $one_history->pack_code : '',
                            'value_old' => json_encode($one_history),
                            'value_new' => '',
                            'action' => 'delete'
                        );
                        $this->package_history->add($data_history);
                    }
                } catch (Exception $ex) {
                    // chi de tranh anh huong den viec gui thong tin ve nguoi dung
                }

                $data_return["list_id"] = $list_id;
                $data_return["state"] = 1;
                $data_return["msg"] = "Xóa bản ghi thành công !";
            } else {
                $data_return["list_id"] = $list_id;
                $data_return["state"] = 0;
                $data_return["msg"] = "Bản ghi đã được xóa từ trước hoặc không thể bị xóa. Vui lòng tải lại trang !";
            }

            echo json_encode($data_return);
            return TRUE;
        } else {
            $data_return["state"] = 0;
            $data_return["msg"] = "Không xác định được ID dữ liệu !";
            echo json_encode($data_return);
            return FALSE;
        }
    }

}
