<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 12/16/2017
 * Time: 17:21
 *
 * @author chuvantinh1991@gmail.com
 */

/**
 * Class Login_api
 *
 * @author chuvantinh1991@gmail.com
 */
class Login_api extends REST_Controller
{
    /**
     * @var array Thông tin dữ liệu Client
     */
    protected $client_info;

    public function __construct()
    {
        parent::__construct();
        $this->client_info = $this->get_client_info();
        $this->load->library('ThuyVu_lib');
        $this->load->library('make_info_lib');
        $this->load->library('use_package_lib');
        $this->load->model('m_voxy_connect_api', 'voxy_api');
        $this->load->model('m_voxy_users', 'users');
        $this->load->model('m_voxy_used_package', 'used_package');
    }

    /**
     * Ham Login dang nhap tu LMS
     *
     * @author chuvantinh1991@gmail.com
     */
    public function login_post()
    {
        $user_id        = $this->post('user_id');
        $contact_id     = $this->post('contact_id');
        $email          = $this->post('email');
        $email          = strtolower($email);
        $full_name      = $this->post('full_name');
        $full_name      = $this->thuyvu_lib->StripUnicode($full_name);

        if(!$user_id || !$email) {
            $data_error                 = new stdClass();
            $data_error->user_id        = $user_id;
            $data_error->email_address  = $email;

            $response = $this->_error($data_error, 'LOGIN_API_INVALID_DATA','Thiếu tham số đăng nhập VoxyService !');
            $this->response($response, 200);
        }

        $voxy_user_id = $this->make_info_lib->make_voxy_user_id($user_id, $this->client_info['weight']);
        if(!$voxy_user_id) {
            $response = $this->_error(array('voxy_user_id' => $voxy_user_id),'LOGIN_API_USER_ID_INVALID_DATA', 'ID người dùng không hợp lệ !');
            $this->response($response, 200);
        }

        // Kiem tra xem co mua goi nao khong
        $package_actived_info   = array();
        $package_deactived_info = array();
        $invoice_id             = 0;
        $list_package = $this->use_package_lib->get_packages_user($user_id, NULL, $this->client_info);
        if(isset($list_package->status) && $list_package->status === TRUE && count($list_package->data) > 0){
            foreach ($list_package->data AS $key => $package){
                if(isset($package->status)){
                    if($package->status == 'ACTIVED'){
                        $invoice_id = $package->invoice_id;
                        $package_actived_info[] = $package;
                        break;
                    }
                    if($package->status == 'DEACTIVED'){
                        $package_deactived_info[$package->cat_code][] = $package;
                    }
                }
            }
        }

        $check_package = FALSE;
        if(count($package_actived_info) > 0){
            $check_package = TRUE;
        } else if(isset($package_deactived_info['NAV2']) && count($package_deactived_info['NAV2']) > 0){
            // kich hoat goi hoc
            $invoice_id     = $package_deactived_info['NAV2'][0]->invoice_id;
            $active_status  = $this->active_package($invoice_id);
            if(!$active_status) {
                $response = $this->_error(NULL, 'LOGIN_API_PACKAGE_ACTIVE_FAIL','Kích hoạt gói học phí không thành công !');
                $this->response($response, 200);
            }
            $check_package = TRUE;
        }
        // lay thong tin goi hoc sau kich hoat
        $package_info = $this->used_package->get_one(array('m.invoice_id' => $invoice_id), 'object');

        // Neu khong co goi hoc phi nao duoc kich hoat
        if(!$check_package || !$package_info){
            $response = $this->_error(NULL, 'LOGIN_API_PACKAGE_ACTIVED_NOT_FOUND','Bạn cần mua gói học phí !');
            $this->response($response, 200);
        }
        $time_end = $package_info->end_time;

        $check_acc_invoxy = $this->check_exists_user_in_voxy($voxy_user_id);
        // Neu chua co acc tren voxy thi tao moi user
        if(!$check_acc_invoxy){
            $user_info = $this->users->get_one(array('user_id' => $voxy_user_id), 'object');
            if(!$user_info){
                $response = $this->_error(array('voxy_user_id' => $voxy_user_id),'LOGIN_API_CHECK_USER_NOT_FOUND', 'ID người dùng không tồn tại trên hệ thống !');
                $this->response($response, 200);
            }
            // $voxy_user_id
            $user_email = $user_info->user_email;
            $user_name  = $user_info->first_name;
            $user_phone = $user_info->phone_number;
            $user_lang  = $user_info->native_language;
            $user_level = $user_info->level;

            $add_user = $this->add_new_user_in_voxy($voxy_user_id, $user_email, $user_name, $user_level, $user_lang, $user_phone);
            if(!$add_user){
                $response = $this->_error(NULL,'LOGIN_API_ADD_USER_VOXY_FAIL', 'Thêm người dùng Voxy thất bại !');
                $this->response($response, 200);
            }
            $this->voxy_api->add_user_to_feature_group($voxy_user_id, 1709);
        }

        // update lai thoi gian het han cua goi tren voxy
        $this->voxy_api->update_profile_of_one_user($voxy_user_id, array('expiration_date' => $time_end));

        $data_link = $this->get_token_login($voxy_user_id);
        if(!$data_link) {
            $response = $this->_error(NULL,'LOGIN_API_LOGIN_VOXY_FAIL', 'Đăng nhập Voxy không thành công !');
            $this->response($response, 200);
        }

        $response = $this->_success($data_link);
        $this->response($response, 200);
    }

    /** HoangND2
     *  Api lmsxy goi den de xac thuc user, tra ve voxy id
     */
    public function app_login_post() {
        $user_id    = (int) $this->post('student_id');
        $weight_id  = (int) $this->post('weight_id');
        if($user_id && is_integer($user_id) && $weight_id && is_integer($weight_id)) {
            $this->load->model('m_sys_weight_config', 'm_sys_weight');
            $weight_info = $this->m_sys_weight->get_one(Array('m.id' => $weight_id));

            if($weight_info && is_object($weight_info)) {
                // cheat weight info rieng cho he thong LMS XY
                $this->client_info['weight_id'] = $weight_id;
                $this->client_info['weight']    = $weight_info->weight;
                $this->client_info['connect']   = $weight_info->connect;
            } else {
                $response = $this->_error(NULL, 'LOGIN_API_WEIGHT_NOT_FOUND','App: WEIGHT hệ thống không tồn tại !');
                $this->response($response, 200);
            }

            // make voxy_user_id
            $voxy_user_id = $this->make_info_lib->make_voxy_user_id($user_id, $this->client_info['weight']);
            if(!$voxy_user_id) {
                $response = $this->_error(array('voxy_user_id' => $voxy_user_id),'LOGIN_API_USER_ID_INVALID_DATA', 'App: ID người dùng không hợp lệ !');
                $this->response($response, 200);
            }

            // Kiem tra xem co mua goi nao khong
            $package_actived_info   = array();
            $package_deactived_info = array();
            $invoice_id             = 0;
            $list_package = $this->use_package_lib->get_packages_user($user_id, NULL, $this->client_info);
            if(isset($list_package->status) && $list_package->status === TRUE && count($list_package->data) > 0){
                foreach ($list_package->data AS $key => $package){
                    if(isset($package->status)){
                        if($package->status == 'ACTIVED'){
                            $invoice_id = $package->invoice_id;
                            $package_actived_info[] = $package;
                            break;
                        }
                        if($package->status == 'DEACTIVED'){
                            $package_deactived_info[$package->cat_code][] = $package;
                        }
                    }
                }
            }

            $check_package = FALSE;
            if(count($package_actived_info) > 0){
                $check_package = TRUE;
            } else if(isset($package_deactived_info['NAV2']) && count($package_deactived_info['NAV2']) > 0){
                // kich hoat goi hoc
                $invoice_id     = $package_deactived_info['NAV2'][0]->invoice_id;
                $active_status  = $this->active_package($invoice_id);
                if(!$active_status) {
                    $response = $this->_error(NULL, 'LOGIN_API_PACKAGE_ACTIVE_FAIL','App: Kích hoạt gói học phí không thành công !');
                    $this->response($response, 200);
                }
                $check_package = TRUE;
            }
            // lay thong tin goi hoc sau kich hoat
            $package_info = $this->used_package->get_one(array('m.invoice_id' => $invoice_id), 'object');

            // Neu khong co goi hoc phi nao duoc kich hoat
            if(!$check_package || !$package_info){
                $response = $this->_error(NULL, 'LOGIN_API_PACKAGE_ACTIVED_NOT_FOUND','App: Bạn cần mua gói học phí !');
                $this->response($response, 200);
            }
            $time_end = $package_info->end_time;

            $check_acc_invoxy = $this->check_exists_user_in_voxy($voxy_user_id);
            // Neu chua co acc tren voxy thi tao moi user
            if(!$check_acc_invoxy){
                $user_info = $this->users->get_one(array('user_id' => $voxy_user_id), 'object');
                if(!$user_info){
                    $response = $this->_error(array('voxy_user_id' => $voxy_user_id),'LOGIN_API_CHECK_USER_NOT_FOUND', 'App: ID người dùng không tồn tại trên hệ thống !');
                    $this->response($response, 200);
                }
                // $voxy_user_id
                $user_email = $user_info->user_email;
                $user_name  = $user_info->first_name;
                $user_phone = $user_info->phone_number;
                $user_lang  = $user_info->native_language;
                $user_level = $user_info->level;

                $add_user = $this->add_new_user_in_voxy($voxy_user_id, $user_email, $user_name, $user_level, $user_lang, $user_phone);
                if(!$add_user){
                    $response = $this->_error(NULL,'LOGIN_API_ADD_USER_VOXY_FAIL', 'App: Thêm người dùng Voxy thất bại !');
                    $this->response($response, 200);
                }
                $this->voxy_api->add_user_to_feature_group($voxy_user_id, 1709);
            }

            // update lai thoi gian het han cua goi tren voxy
            $this->voxy_api->update_profile_of_one_user($voxy_user_id, array('expiration_date' => $time_end));

            $response = $this->_success(Array('voxy_user_id' => $voxy_user_id), 1);
            $this->response($response, 200);
        } else {
            $response = $this->_error(NULL,'LOGIN_APP_MISSING_ARGS', 'App: Thiếu tham số đăng nhập Voxyservice !');
            $this->response($response, 200);
        }
    }

    /**
     * Kich hoat goi Voxy
     * @param int $invoice_id
     * @return bool     TRUE - thanh cong, FALSE nguoc lai
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function active_package($invoice_id = 0)
    {
        $response = $this->use_package_lib->active_package($invoice_id);
        if(isset($response->status) && $response->status === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Kiem tra User da ton tai tren voxy chua
     * @param int $user_id
     * @return bool|null
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function check_exists_user_in_voxy($user_id = 0)
    {
        if(!$user_id){
            return NULL;
        }
        $check_exists_user = $this->voxy_api->get_info_of_one_user($user_id);
        if(!$check_exists_user || (isset($check_exists_user) && isset($check_exists_user['error_message']))){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Them moi user len voxy
     * @param $user_id
     * @param $user_email
     * @param $user_name
     * @param $user_level
     * @param $user_lang
     * @param $user_phone
     * @return bool|null
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function add_new_user_in_voxy($user_id, $user_email, $user_name, $user_level, $user_lang, $user_phone){
        if(!$user_id || !$user_email){
            return NULL;
        }

        $param = array(
            'email_address'     => $user_email,
            'first_name'        => $this->thuyvu_lib->StringCodeFormat($user_name, '_'),
            'native_language'   => $user_lang,
            // 'phone_number'      => $user_phone,
            'level'             => $user_level,
        );

        $add_user = $this->voxy_api->register_a_new_user($user_id, $param);
        if(isset($add_user['error_message'])){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Lay token dang nhap vao voxy
     * @param int $user_id
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_token_login($user_id = 0)
    {
        $data_link = FALSE;
        if(!$user_id){
            return $data_link;
        }
        $check_token = $this->voxy_api->get_a_user_auth_token($user_id);
        if(!$check_token || (isset($check_token) && isset($check_token['error_message']))){
            return FALSE;
        }
        return (is_string($check_token['actions']->start) ? $check_token['actions']->start : FALSE);

    }

}