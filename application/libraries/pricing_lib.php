<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 01/19/2018
 * Time: 11:48
 *
 * @author chuvantinh1991@gmail.com
 */

class Pricing_lib
{
    protected $_ci;

    public function __construct()
    {
        $this->_ci = &get_instance();
        $this->_ci->load->library('ThuyVu_lib');
        $this->_ci->load->library('make_info_lib');
        $this->_ci->load->model('m_pricing', 'pricing');
        $this->_ci->load->model('m_voxy_invoice', 'invoice');
        $this->_ci->load->model('m_voxy_invoice_detail', 'invoice_detail');
        $this->_ci->load->model('m_voxy_invoice_history', 'invoice_history');
        $this->_ci->load->model('m_voxy_package', 'package');
        $this->_ci->load->model('m_voxy_users', 'users');
        $this->_ci->load->model('m_voxy_user_mapping', 'user_mapping');
        $this->_ci->load->model('m_voxy_used_package', 'used_package');
        $this->_ci->load->model('m_voxy_used_package_history', 'used_package_history');
    }

    /**
     * Hàm tạo temp thông tin Client
     * @param array $client_info
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_client_info($client_info = array())
    {
        return array(
            'client_ip'              => isset($client_info['client_ip'])        ? trim($client_info['client_ip'])           : '',
            'client_domain'          => isset($client_info['client_domain'])    ? trim($client_info['client_domain'])       : '',
            'client_auth_username'   => isset($client_info['auth_username'])    ? trim($client_info['auth_username'])       : '',
            'client_weight_id'       => isset($client_info['weight_id'])        ? intval(trim($client_info['weight_id']))   : 0 ,
            'client_weight'          => isset($client_info['weight'])           ? intval(trim($client_info['weight']))      : 0 ,
            'client_connect'         => isset($client_info['connect'])          ? trim($client_info['connect'])             : '',
        );
    }

    /**
     * Hàm tạo temp thông tin User
     * @param array $user_info
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_user_info($user_info = array())
    {
        return array(
            'student_id'    => isset($user_info['user_id'])      ? intval(trim($user_info['user_id']))   : 0,
            'voxy_user_id'  => 0,
            'contact_id'    => isset($user_info['contact_id'])   ? trim($user_info['contact_id'])        : '',
            'user_email'    => isset($user_info['user_email'])   ? trim($user_info['user_email'])        : '',
            'user_phone'    => isset($user_info['user_phone'])   ? trim($user_info['user_phone'])        : '',
            'user_name'     => isset($user_info['full_name'])    ? trim($user_info['full_name'])         : '',
            'level_study'   => isset($user_info['level_study'])  ? trim($user_info['level_study'])       : '',
            'lang'          => (isset($user_info['lang']))       ? trim($user_info['lang'])              : '',
        );
    }

    /**
     * Hàm tạo temp thông tin Advisor mua gói
     * @param array $buyer_info
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_buyer_info($buyer_info = array())
    {
        return array(
            'buyer_id'      => isset($buyer_info['buyer_id'])     ? intval(trim($buyer_info['buyer_id']))   : 0 ,
            'buyer_email'   => isset($buyer_info['buyer_email'])  ? trim($buyer_info['buyer_email'])        : '',
            'buyer_name'    => isset($buyer_info['buyer_name'])   ? trim($buyer_info['buyer_name'])         : '',
            'buyer_system'  => '',
        );
    }

    /**
     * Hàm tạo temp thông tin hóa đơn
     * @param array $invoice_info
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_invoice_info($invoice_info = array())
    {
        $invoice_code_tmp = (isset($invoice_info['invoice_code']) && $invoice_info['invoice_code']) ? trim($invoice_info['invoice_code']) : FALSE;
        return array(
            'invoice_code'          => $this->get_invoice_code($invoice_code_tmp),
            'package_code'          => isset($invoice_info['package_code'])             ? trim($invoice_info['package_code'])                   : NULL,
            'package_actual_price'  => isset($invoice_info['package_actual_price'])     ? intval(trim($invoice_info['package_actual_price']))   : NULL,
            'invoice_description'   => isset($invoice_info['invoice_description'])      ? trim($invoice_info['invoice_description'])            : 'Mua gói học phí !',
            'native_package_id'     => isset($invoice_info['native_package_id'])        ? intval(trim($invoice_info['native_package_id']))      : NULL,
            'native_package_parent' => isset($invoice_info['native_package_parent'])    ? trim($invoice_info['native_package_parent'])          : NULL,
            'native_package_type'   => isset($invoice_info['native_package_type'])      ? trim($invoice_info['native_package_type'])            : NULL,
            'native_package_code'   => isset($invoice_info['native_package_code'])      ? trim($invoice_info['native_package_code'])            : NULL,
            'time_created'          => isset($invoice_info['time_created'])             ? trim($invoice_info['time_created'])                   : NULL,
        );
    }

    /**
     * Hàm tạo temp thông tin gói học mà học viên mua
     * @param array $package_info
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_package_info($package_info = array())
    {
        return array(
            'cat_id'          => isset($package_info['cat_id'])         ? strtoupper(trim($package_info['cat_id']))       : '',
            'package_code'      => isset($package_info['pack_code'])        ? strtoupper(trim($package_info['pack_code']))      : '',
            'package_name'      => isset($package_info['pack_name'])        ? trim($package_info['pack_name'])                  : '',
            'native_parent'     => isset($package_info['native_parent'])    ? strtoupper(trim($package_info['native_parent']))  : '',
            'package_type'      => isset($package_info['pack_type'])        ? strtoupper(trim($package_info['pack_type']))      : '',
            'description'       => isset($package_info['description'])      ? trim($package_info['description'])                : '',
            'package_use_time'  => (isset($package_info['pack_use_time']) && intval(trim($package_info['pack_use_time'])))  ? intval(trim($package_info['pack_use_time']))  : -2,
            'package_cost'      => (isset($package_info['pack_cost']) && intval(trim($package_info['pack_cost'])))          ? intval(trim($package_info['pack_cost']))      : -1,
        );
    }

    /**
     * Dữ liệu hóa đơn
     * @param $user_info
     * @param $invoice_info
     * @param $package_info
     * @param $buyer_info
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_data_add_invoice($user_info, $invoice_info, $package_info, $buyer_info)
    {
        return array(
            'user_id'               => $user_info['voxy_user_id'],
            'invoice_code'          => $invoice_info['invoice_code'],
            'invoice_price'         => 0, // cheat mua gia khong dong
            'invoice_package_price' => $package_info['package_cost'],
            'invoice_description'   => $invoice_info['invoice_description'],
            'invoice_status'        => 'COMPLETED',
            'buyer_id'              => $buyer_info['buyer_id'],
            'buyer_name'            => $buyer_info['buyer_email'].' - '.$buyer_info['buyer_name'],
            'buyer_system'          => $buyer_info['buyer_system'],
            'user_contact_id'       => $user_info['contact_id'],
            'user_name'             => $user_info['user_name'],
            'user_email'            => $user_info['user_email'],
            'user_phone'            => $user_info['user_phone'],
        );
    }

    /**
     * Dữ liệu chi tiết hóa đơn
     * @param $invoice_code
     * @param $invoice_info
     * @param $package_info
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_data_add_invoice_detail($invoice_code, $invoice_info, $package_info)
    {
        $data_add = array(
            'invoice_code'          => $invoice_code,
            'cat_id'              => $package_info['cat_id'],
            'package_code'          => $package_info['package_code'],
            'package_type'          => $package_info['package_type'],
            'native_parent'         => $package_info['native_parent'],
            'package_use_time'      => $package_info['package_use_time'],
            'package_cost'          => $package_info['package_cost'],
            'actual_cost'           => $invoice_info['package_actual_price'],
            'package_name'          => $package_info['package_name'],
            'package_description'   => json_encode($package_info),
        );
        return $data_add;
    }

    /**
     * Ham tien ich: mua goi hoc va ghi nhan lich su hoa don
     *
     * @param array $user_info          Thông tin của người sử dụng: contact_id,student_id,user_name,user_email,user_phone
     * @param array $buyer_info         Thông tin người mua: buyer_id, buyer_name
     * @param array $invoice_info       Mảng thông tin thêm của hóa đơn: reason,client_api,timecreated, invoice_code
     * @param array $client_info       Mang thong tin Client call API toi
     * @param bool $active_now          có actived luôn ko,mặc định là ko
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    public function pricing($user_info = array(), $buyer_info = array(), $invoice_info = array(), $client_info = array(), $active_now = FALSE)
    {
        // Xử lý một số thông tin gửi lên
        // Thông tin Advisor mua cho
        $buyer_info = $this->make_buyer_info($buyer_info);
        $buyer_info['buyer_system'] = $client_info['client_domain'].'_'.$client_info['client_ip'];

        // Thong tin yeu cau mua goi
        $invoice_info = $this->make_invoice_info($invoice_info);
        $time_created = isset($invoice_info['time_created']) ? $invoice_info['time_created'] : time();

        // Thông tin Client
        $client_info = $this->make_client_info($client_info);
        if (!($client_info['client_weight_id'] && $client_info['client_weight'])) {
            return $this->_error($client_info, 'CLIENT_INFO_NOT_FOUND_PRICING_LIB_', 'Lỗi dữ liệu Client Info');
        }

        // Thông tin người dùng
        $user_info = $this->make_user_info($user_info);
        if (!($user_info['student_id'] && $user_info['contact_id'] && $user_info['user_email'])) {
            return $this->_error(NULL,'USER_INFO_NOT_FOUND_PRICING_LIB', 'Lỗi dữ liệu User Info!');
        }

        //insert data voxy_users
        $voxy_user_id = $this->_add_voxy_user($user_info, $client_info);
        if(!$voxy_user_id){
            return $this->_error(NULL,'ADD_USER_FAIL_PRICING_LIB', 'Thêm người dùng thất bại !');
        }
        $user_info['voxy_user_id'] = $voxy_user_id;

        $package_info = $this->_ci->package->get_one(array('m.pack_code' => $invoice_info['package_code']), 'array');
        if(!$package_info){
            return $this->_error($invoice_info['package_code'],'PACKAGE_CODE_NOT_FOUND_PRICING_LIB', 'Thông tin gói học không tồn tại !');
        }
        $package_info = $this->make_package_info($package_info);

        $this->_ci->pricing->trans_begin();
        //Them vao bang invoice
        $data_add_invoice = $this->make_data_add_invoice($user_info, $invoice_info, $package_info, $buyer_info);
        $add_invoice_id = $this->_ci->invoice->add($data_add_invoice);
        $invoice_code = "TPEVOXY_" . date('Ymd') . '_' . $add_invoice_id;
        $data_add_invoice['invoice_code'] = $invoice_code;
        $this->_ci->invoice->update($add_invoice_id, Array('invoice_code' => $invoice_code));
        if (!$add_invoice_id || $this->_ci->pricing->trans_status() === FALSE) {
            $this->_ci->pricing->trans_rollback();
            return $this->_error($data_add_invoice,'ADD_INVOICE_FAIL_PRICING_LIB', 'Thêm hóa đơn mua gói thất bại !');
        }

        //Them vao bang invoice_product_details
        $data_add_invoice_detail = $this->make_data_add_invoice_detail($invoice_code, $invoice_info, $package_info);
        $add_invoice_detail_id = $this->_ci->invoice_detail->add($data_add_invoice_detail);
        if (!$add_invoice_detail_id || $this->_ci->pricing->trans_status() === FALSE) {
            $this->_ci->pricing->trans_rollback();
            return $this->_error($data_add_invoice_detail,'ADD_INVOICE_DETAIL_FAIL_PRICING_LIB', 'Thêm chi tiết hóa đơn mua gói thất bại !');
        }

        //Them vao bang invoice_history
        $data_add_invoice_history = array(
            'invoice_code'          => $invoice_code,
            'status_old'            => null,
            'status_new'            => $data_add_invoice['invoice_status'],
            'status_description'    => json_encode(array('msg' => $invoice_info['invoice_description'])),
            'value_old'             => NULL,
            'value_new'             => json_encode($data_add_invoice),
            'system_created'        => $buyer_info['buyer_system'],
        );
        $add_invoice_history_id = $this->_ci->invoice_history->add($data_add_invoice_history);
        if (!$add_invoice_history_id || $this->_ci->pricing->trans_status() === FALSE) {
            $this->_ci->pricing->trans_rollback();
            return $this->_error($data_add_invoice_history,'ADD_INVOICE_HISTORY_FAIL_PRICING_LIB', 'Thêm lịch sử hóa đơn mua gói thất bại !');
        }

        // them vao bang used_package
        $created_at = time();
        $use_package_status     = 'DEACTIVED';
        $data_add_use_package = array(
            'invoice_id'    => $add_invoice_id,
            'use_time'      => $package_info['package_use_time'],
            'start_time'    => NULL,
            'end_time'      => NULL,
            'status'        => $use_package_status,
            'status_code'   => $use_package_status . $created_at,
            'created_at'    => $created_at
        );
        $used_package_id = $this->_ci->used_package->add($data_add_use_package);
        if (!$used_package_id || $this->_ci->pricing->trans_status() === FALSE) {
            $this->_ci->pricing->trans_rollback();
            return $this->_error($data_add_use_package,'ADD_USE_PACKAGE_FAIL_PRICING_LIB', 'Thêm sử dụng hóa đơn mua gói thất bại !');
        }

        // them vao bang used_package_history
        $data_add_used_package_history = array(
            'used_package_id'   => $used_package_id,
            'status_old'        => NULL,
            'status_new'        => $use_package_status,
            'description'       => 'Mua gói học phí!',
            'status_code'       => $use_package_status . $created_at,
            'created_at'        => $created_at,
        );
        $used_package_history_id = $this->_ci->used_package_history->add($data_add_used_package_history);
        if (!$used_package_history_id || $this->_ci->pricing->trans_status() === FALSE) {
            $this->_ci->pricing->trans_rollback();
            return $this->_error($data_add_used_package_history,'ADD_USE_PACKAGE_HISTORY_FAIL_PRICING_LIB', 'Thêm lịch sử sử dụng hóa đơn mua gói thất bại !');
        }
        // ket thuc giao dich
        $this->_ci->pricing->trans_commit();

        $data_add_invoice_detail['package_status'] = $use_package_status;
        $data_response = array(
            'student_id'        => $user_info['student_id'],
            'invoice_id'        => $add_invoice_id,
            'invoice_detail'    => $data_add_invoice_detail
        );
        return $this->_success($data_response);
    }

    /**
     * Hàm tạo mã hóa đơn
     *
     * @param bool $invoice_code    mã hóa đơn
     * @return bool|string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_invoice_code($invoice_code = FALSE)
    {
        $rand = 'TPEVOXY' . '_' . microtime() . rand(0, 1000);
        if (!$invoice_code) {
            $invoice_code = $rand;
        }
        $i = 0;
        while ($this->_ci->invoice->get_one(array('m.invoice_code' => $invoice_code))) {
            $i++;
            $invoice_code = $rand . '_' . $i;
        }
        return $invoice_code;
    }

    /**
     * Ham them user
     * @param $user_info
     * @param $client_info
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _add_voxy_user($user_info, $client_info)
    {
        $return_data        = FALSE;

        $voxy_user_id       = $this->_ci->make_info_lib->make_voxy_user_id($user_info['student_id'], $client_info['client_weight']);
        $voxy_user_email    = $this->_ci->make_info_lib->make_voxy_user_email($user_info['user_email'], $client_info['client_connect']);
        $voxy_user_level    = $this->_ci->make_info_lib->make_voxy_user_level($user_info['level_study']);
        $voxy_user_lang     = $this->_ci->make_info_lib->make_voxy_user_lang($user_info['lang']);
        $voxy_user_name     = $this->_ci->make_info_lib->make_voxy_user_name($user_info['user_name']);

        if(!($client_info['client_weight_id'] && intval($client_info['client_weight_id']) && $voxy_user_id && $user_info['contact_id'] && $voxy_user_email)){
            return $return_data;
        }

        $voxy_user_info     = $this->_ci->users->check_user_id_exists($voxy_user_id);
        if($voxy_user_info){
            $return_data = $voxy_user_id;
            // edit user info
            $edit_user_data = array();
            if($voxy_user_info->native_language != $voxy_user_lang){
                $edit_user_data['native_language'] = $voxy_user_lang;
            }
            if($voxy_user_info->native_language != $voxy_user_level){
                $edit_user_data['native_language'] = $voxy_user_level;
            }
            if($voxy_user_info->first_name != $voxy_user_name){
                $edit_user_data['first_name'] = $voxy_user_name;
            }
            if($voxy_user_info->phone_number != $user_info['user_phone']){
                $edit_user_data['phone_number'] = $user_info['user_phone'];
            }
            if (count($edit_user_data)) {
                $edit_user_data['status'] = 0;
                $this->_ci->users->update($voxy_user_info->id, $edit_user_data);
            }
        } else {
            // can check trung Email
            $voxy_user_data     = array(
                'user_id'           => $voxy_user_id,
                'user_email'        => $voxy_user_email,
                'first_name'        => $voxy_user_name,
                'phone_number'      => $user_info['user_phone'],
                'native_language'   => $voxy_user_lang,
                'level'             => $voxy_user_level,
                'status'            => 0
            );
            $this->_ci->pricing->trans_begin();
            $vu_id = $this->_ci->users->add($voxy_user_data);
            if(!$vu_id){
                $this->_ci->pricing->trans_rollback();
                return $return_data;
            }
            $voxy_user_mapping_data = array(
                'weight_id'     => $client_info['client_weight_id'],
                'vu_id'         => $vu_id,
                'student_id'    => $user_info['student_id'],
                'contact_id'    => $user_info['contact_id'],
                'student_email' => trim($user_info['user_email']),
            );
            $user_mapping_id = $this->_ci->user_mapping->add($voxy_user_mapping_data);
            if(!$user_mapping_id){
                $this->_ci->pricing->trans_rollback();
                return $return_data;
            }
            $this->_ci->pricing->trans_commit();
            $return_data = $voxy_user_id;
        }
        return $return_data;
    }

    /**
     * Tao temp response success de gui ve cho client
     * @param null          $data               Du lieu can tra ve
     * @param int           $total              Tong so du lieu
     * @param string        $status_code        ma code trang thai
     * @param string        $msg                mess khi tra ve
     * @return stdClass     doi tuong can tra ve
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _success($data = NULL, $total = -1, $status_code = 'OK', $msg = 'Thao tác thành công !') {
        $success                = new stdClass();
        $success->status        = TRUE;
        $success->status_code   = $status_code; // OK
        $success->msg           = $msg;
        $success->total         = $total;
        $success->data          = $data;

        return $success;
    }

    /**
     * Tao temp response error de gui ve cho client
     * @param null $data
     * @param string $status_code
     * @param string $msg
     * @return stdClass
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _error($data = NULL, $status_code = 'FAIL', $msg = 'Thao tác thất bại !') {
        $error              = new stdClass();
        $error->status      = FALSE;
        $error->data        = $data;
        $error->status_code = $status_code; // FAIL/MAINTENANCE
        $error->msg         = $msg;

        return $error;
    }
}