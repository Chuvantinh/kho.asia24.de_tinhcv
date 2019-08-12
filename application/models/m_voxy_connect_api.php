<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_connect_api
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_connect_api extends data_base
{
    var $_voxy_server;
    var $_voxy_key;
    var $_voxy_api_secret;
    var $_voxy_difference_time  = 7*3600; // chenh lech mui gio cua my voi vietnam
    var $_curl_time_out         = 10; // second
    var $_curl_ssl_verifypeer   = FALSE;

    /**
     * Du lieu mac dinh neu api voxy tra ve loi
     * {
     *  'error_message':'message error string!'
     * }
     */
    var $_data_return           = Array(
        1 => array('error_message' => 'Có tham số đầu vào không phù hợp !'),
        2 => array('error_message' => 'Gặp lỗi xử lý gọi VOXY API !'),
        3 => array('error_message' => 'VOXY API mất kết nối !'),
        4 => array('error_message' => 'Kết quả VOXY API trả về gặp lỗi !'),
        5 => array('error_message' => 'Kết quả VOXY API trả về rỗng !'),
    );

    public function __construct()
    {
        parent::__construct();
        $this->config->load('api_config');
        $api_config = $this->config->item("api_voxy");

        $this->_voxy_server             = isset($api_config['server'])          ? trim($api_config['server'])           : '';
        $this->_voxy_key                = isset($api_config['key'])             ? trim($api_config['key'])              : '';
        $this->_voxy_api_secret         = isset($api_config['api_secret'])      ? trim($api_config['api_secret'])       : '';
        $this->_voxy_difference_time    = isset($api_config['difference_time']) ? trim($api_config['difference_time'])  : $this->_voxy_difference_time;
        $this->load->model('m_admin_tool_logs', 'm_admin_tool_logs');
    }

    public function setting_table()
    {
        $this->_table_name          = '';
        $this->_key_name            = 'id';
        $this->_exist_created_field = false;
        $this->_exist_updated_field = false;
        $this->_exist_deleted_field = false;
        $this->_schema              = Array();
        $this->_rule                = Array();
        $this->_field_form          = Array();
        $this->_field_table         = Array();
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
    }

    /**
     * API VOXY: Ham lay toan bo danh sach user theo ngay tham gia co tu voxy
     * GET /partners/users/
     *
     * @param int $start_date   Ngay tham gia tu(Y-m-d)
     * @param int $end_date     Ngay tham gian den(Y-m-d)
     * @param int $page         Page (int)
     * @return array            Mang du lieu
     *
     *  [{
     *       "current_page": 0,
     *       "total_pages": 0,
     *       "users": [{
     *           "external_user_id": "",
     *           "email_address": ""
     *        }]
     *   }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_all_user($start_date = 0, $end_date = 0, $page = 1)
    {
        $start_date = (($start_date && intval(trim($start_date)))   ? date('Y-m-d', intval(trim($start_date)))   : '');
        $end_date   = (($end_date   && intval(trim($end_date)))     ? date('Y-m-d', intval(trim($end_date)))     : '');
        $page       = (($page       && intval(trim($page)))         ? intval(trim($page))                               : 1 );

        $arr_params = array(
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'page'          => $page
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $log_starttime = microtime(TRUE);
            $log = Array(
                'group_function'    => 'voxy_connect_api_get_list_all_user',
                'params'            => json_encode($arr_params),
            );
            $id_log = $this->m_admin_tool_logs->add($log);

            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users?'. $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $err            = curl_error($ch);
            if($id_log) {
                $log['response']    = $response;
                $log['error']       = $err;
                $log['curl_info']   = json_encode(curl_getinfo($ch));
                $log['time_diff']   = microtime(TRUE) - $log_starttime;
                $this->m_admin_tool_logs->update($id_log, $log);
            }

            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay toan bo thong tin tai khoan cua hv
     * GET /partners/users/{external_user_id}/
     *
     * @param string $user_id      ID cua hv tren voxy
     * @return array                object thong tin cua HV
     * {
     *   "external_user_id": "",
     *   "first_name": "",
     *   "email_address": "",
     *   "date_joined": "",
     *   "access_type": "",
     *   "native_language": "",
     *   "expiration_date": "",
     *   "date_of_next_vpa": "",
     *   "tutoring_credits": 0,
     *   "phone_number": "",
     *   "level": 0,
     *   "can_reserve_group_sessions": false,
     *   "segments": [{
     *      "label": "",
     *      "user_ids": [
     *          null
     *      ]
     *   }],
     *   "tutoring_credits_used": 0
     * }
     * @author HafThuysVux <thuyvu.hdvn@email.com>
     */
    public function get_info_of_one_user($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);

        try {
            $log_starttime = microtime(TRUE);
            $log = Array(
                'group_function'    => 'voxy_connect_api_get_info_of_one_user',
                'params'            => json_encode((object)Array()),
            );
            $id_log = $this->m_admin_tool_logs->add($log);

            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/' . $user_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $err            = curl_error($ch);
            if($id_log) {
                $log['response']    = $response;
                $log['error']       = $err;
                $log['curl_info']   = json_encode(curl_getinfo($ch));
                $log['time_diff']   = microtime(TRUE) - $log_starttime;
                $this->m_admin_tool_logs->update($id_log, $log);
            }

            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham cap nhat thong tin mot User
     * PUT /partners/users/{external_user_id}/
     *
     * @param string $user_id   ID cuar user tren voxy
     * @param array $params     Mang param can cap nhat tren voxy
     * @return array            Mang ket qua cap nhat
     * {}
     * @author chuvantinh1991@gmail.com
     */
    public function update_profile_of_one_user($user_id = '', $params = array())
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id) || !$params) {
            return $this->_data_return[1];
        }
        $user_id                = trim($user_id);
        $new_external_user_id   = (isset($params['new_external_user_id']) ? trim($params['new_external_user_id']) : '');
        $email_address          = (isset($params['email_address']) ? trim($params['email_address']) : '');
        $first_name             = (isset($params['first_name']) ? $this->_handling_string_unicode(trim($params['first_name'])) : '');
        $last_name              = (isset($params['last_name']) ? $this->_handling_string_unicode(trim($params['last_name'])) : '');
        $native_language        = ((isset($params['native_language']) && array_key_exists(trim($params['native_language']), $this->_get_list_supported_languages())) ? trim($params['native_language']) : '');
        $expiration_date        = ((isset($params['expiration_date']) && intval(trim($params['expiration_date']))) ? date('Y-m-d', intval(trim($params['expiration_date']))) : '');
        $phone_number           = (isset($params['phone_number']) ? trim($params['phone_number']) : '');
        $date_of_next_vpa       = ((isset($params['date_of_next_vpa']) && intval(trim($params['date_of_next_vpa']))) ? date('Y-m-d', intval(trim($params['date_of_next_vpa']))) : '');
        $tutoring_credits       = ((isset($params['tutoring_credits']) && intval(trim($params['tutoring_credits']))) ? intval(trim($params['tutoring_credits'])) : '');
        $level                  = ((isset($params['level']) && intval(trim($params['level'])) && array_key_exists(intval(trim($params['level'])), $this->_get_list_voxy_level())) ? intval(trim($params['level'])) : '');
        $can_reserve_group_sessions = ((isset($params['can_reserve_group_sessions']) && trim($params['can_reserve_group_sessions'])) ? trim($params['can_reserve_group_sessions']) : '');

        $arr_params = array(
            'new_external_user_id'  => $new_external_user_id,               // string
            'email_address'         => $email_address,                      // string format Email
            'first_name'            => $first_name,                         // string
            'last_name'             => $last_name,                          // string
            'native_language'       => $native_language,                    // string format: en vi th
            'expiration_date'       => $expiration_date,                    // string format: YYYY-MM-DD
            'phone_number'          => $phone_number,                       // string format: +5511912345678
            'date_of_next_vpa'      => $date_of_next_vpa,                   // string format: YYYY-MM-DD
            'tutoring_credits'      => $tutoring_credits,                   // integer
            'level'                 => $level,                              // integer 1->5
            'can_reserve_group_sessions' => $can_reserve_group_sessions,    // boolean true - false
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $log_starttime = microtime(TRUE);
            $log = Array(
                'group_function'    => 'voxy_connect_api_update_profile_of_one_user',
                'params'            => json_encode($arr_params),
            );
            $id_log = $this->m_admin_tool_logs->add($log);

            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/'. $user_id);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $res        = curl_exec($ch);
            $err        = curl_error($ch);
            if($id_log) {
                $log['response']    = $res;
                $log['error']       = $err;
                $log['curl_info']   = json_encode(curl_getinfo($ch));
                $log['time_diff']   = microtime(TRUE) - $log_starttime;
                $this->m_admin_tool_logs->update($id_log, $log);
            }

            $response   = json_decode($res);

            if($res === '' && $response === NULL) {
                $data_return = TRUE;
            } else if($response === FALSE || (!is_object($response) && !is_array($response))){
                $data_return = $this->_data_return[4];
            } else {
                if(is_object($response)){
                    $response = (array) $response;
                }
                if(isset($response['error_message'])){
                    $data_return = $response;
                } else {
                    $data_return = TRUE;
                }
            }
            // $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay thong tin User Access Type
     * PUT /partners/users/{external_user_id}/access_type/
     *
     * @param string $user_id       ID nguoi dung
     * @param string $access_type   Kieu access type
     * @return array                Mang du ket qua
     * {}
     * @author chuvantinh1991@gmail.com
     */
    public function get_user_access_type($user_id = '', $access_type = 'wse-show-gift')
    {
        $list_access_type = array(
            'wse-show-gift' => 'wse-show-gift',
            'wse-trial'     => 'wse-trial',
            'wse-premium'   => 'wse-premium',
        );
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)
            || ! is_string($access_type) || ! in_array(trim($access_type), $list_access_type)
        ) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        $arr_params = array(
            'access_type'   => trim($access_type), // string
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/'. $user_id . '/access_type/');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay toan bo danh sach danh gia voxy cua 1 nguoi dung
     * GET /partners/users/{external_user_id}/assessments/
     *
     * @param string $user_id   ID nguoi dung
     * @return array            Mang du lieu tra ve
     * {
     *   "date_completed": "",
     *   "has_speaking_section": false,
     *   "total_score": 0,
     *   "grammar_score": 0,
     *   "listening_score": 0,
     *   "reading_score": 0,
     *   "speaking_score": 0,
     *   "overall_level_name": "",
     *   "reading_level_name": "",
     *   "grammar_level_name": "",
     *   "listening_level_name": "",
     *   "speaking_level_name": "",
     *   "level": [{
     *       "id": "",
     *       "name": "",
     *       "scale_id": 0
     *   }],
     *   "possible_points": 0,
     *   "adjusted_score": 0
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_assessment_of_one_user($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/' . $user_id . '/assessments/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham dang nhap Voxy voi 1 User chi dinh
     * GET /partners/users/{external_user_id}/auth_token/
     *
     * @param string $user_id      ID cua user can login
     * @return array            Mang du lieu tra ve
     *   {
     *       "auth_token": "",
     *       "actions": {
     *           "start": ""
     *       }
     *   }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_a_user_auth_token($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        try {
            $log_starttime = microtime(TRUE);
            $log = Array(
                'group_function'    => 'voxy_connect_api_get_a_user_auth_token',
                'params'            => json_encode((object)Array()),
            );
            $id_log = $this->m_admin_tool_logs->add($log);

            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/' . $user_id . '/auth_token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $err            = curl_error($ch);
            if($id_log) {
                $log['response']    = $response;
                $log['error']       = $err;
                $log['curl_info']   = json_encode(curl_getinfo($ch));
                $log['time_diff']   = microtime(TRUE) - $log_starttime;
                $this->m_admin_tool_logs->update($id_log, $log);
            }

            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham them tutoring_credits cho nguoi dung
     * POST /partners/users/{external_user_id}/entitlements/
     *
     * @param string $user_id   ID nguoi dung
     * @param int $credits      credits int
     * @return array            Mang du lieu ket qua
     * {}
     *
     * @author chuvantinh1991@gmail.com
     */
    public function add_tutoring_credits_to_user($user_id = '', $credits = 1)
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)
            || !(is_integer($credits) || ! intval($credits))
        ) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        $arr_params = array(
            'credits'   => intval($credits)
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL,$this->_voxy_server . '/users/' . $user_id . '/entitlements/');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham Reset units cua nguoi dung
     * POST /partners/users/{external_user_id}/units/
     *
     * @param string $user_id   ID nguoi dung
     * @return array            Mang du lieu ket qua
     * {}
     *
     * @author chuvantinh1991@gmail.com
     */
    public function reset_user_units($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        $arr_params = array();
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL,$this->_voxy_server . '/users/' . $user_id . '/units/');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham tao tai thong tin tren voxy
     * POST /partners/users/{external_user_id}
     *
     * @param string $user_id   ID user
     * @param array $params     Mang du lieu thong tin HV
     * @return array            Mang du lieu ket qua
     * {
     *   "external_user_id": "",
     *   "first_name": "",
     *   "email_address": "",
     *   "date_joined": "",
     *   "access_type": "",
     *   "native_language": "",
     *   "expiration_date": "",
     *   "date_of_next_vpa": "",
     *   "tutoring_credits": 0,
     *   "phone_number": "",
     *   "level": 0,
     *   "can_reserve_group_sessions": false,
     *   "segments": [{
     *       "label": "",
     *       "user_ids": [
     *           null
     *       ]
     *   }],
     *   "feature_group_id": 0
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function register_a_new_user($user_id = '', $params = array()) {
        $email_address      = (isset($params['email_address']) ? trim($params['email_address']) : '');
        $first_name         = (isset($params['first_name']) ? $this->_handling_string_unicode(trim($params['first_name'])) : '');

        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)
            || ! is_array($params)
            || ! $email_address || ! $first_name
        ) {
            return $this->_data_return[1];
        }
        $user_id            = trim($user_id);
        $native_language    = ((isset($params['native_language']) && array_key_exists(trim($params['native_language']), $this->_get_list_supported_languages())) ? trim($params['native_language']) : 'en');
        $expiration_date    = ((isset($params['expiration_date']) && intval(trim($params['expiration_date']))) ? date('Y-m-d', intval(trim($params['expiration_date']))) : '');
        $phone_number       = (isset($params['phone_number']) ? trim($params['phone_number']) : '');
        $date_of_next_vpa   = ((isset($params['date_of_next_vpa']) && intval(trim($params['date_of_next_vpa']))) ? date('Y-m-d', intval(trim($params['date_of_next_vpa']))) : '');
        $tutoring_credits   = ((isset($params['tutoring_credits']) && intval(trim($params['tutoring_credits']))) ? intval(trim($params['tutoring_credits'])) : 0);
        $level              = ((isset($params['level']) && intval(trim($params['level'])) && array_key_exists(intval(trim($params['level'])), $this->_get_list_voxy_level())) ? intval(trim($params['level'])) : 1);
        $can_reserve_group_sessions = ((isset($params['can_reserve_group_sessions']) && trim($params['can_reserve_group_sessions']) == TRUE) ? TRUE : FALSE);

        $arr_params = array(
            'email_address'     => $email_address,                          // string format Email
            'first_name'        => $first_name,                             // string
            'native_language'   => $native_language,                        // string format: en vi th
            'expiration_date'   => $expiration_date,                        // string format: YYYY-MM-DD
            'phone_number'      => $phone_number,                           // string format: +5511912345678
            'date_of_next_vpa'  => $date_of_next_vpa,                       // string format: YYYY-MM-DD
            'tutoring_credits'  => $tutoring_credits,                       // integer
            'level'             => $level,                                  // integer 1->5
            'can_reserve_group_sessions' => $can_reserve_group_sessions,    // boolean true - false
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $log_starttime = microtime(TRUE);
            $log = Array(
                'group_function'    => 'voxy_connect_api_register_a_new_user',
                'params'            => json_encode($arr_params),
            );
            $id_log = $this->m_admin_tool_logs->add($log);

            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL,$this->_voxy_server . '/users/' . $user_id);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response   = curl_exec($ch);
            $err        = curl_error($ch);
            if($id_log) {
                $log['response']    = $response;
                $log['error']       = $err;
                $log['curl_info']   = json_encode(curl_getinfo($ch));
                $log['time_diff']   = microtime(TRUE) - $log_starttime;
                $this->m_admin_tool_logs->update($id_log, $log);
            }

            $response   = json_decode($response);
            if($response === FALSE || (!is_object($response) && !is_array($response))){
                $data_return = $this->_data_return[4];
            } else {
                if(is_object($response)){
                    $response = (array) $response;
                }
                if(isset($response['error_message'])){
                    $data_return = $response;
                } else {
                    $data_return = TRUE;
                }
            }

            //$data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach lich su hoc units cua 1 user
     * GET /partners/users/{external_user_id}/units
     *
     * @param string $user_id   ID cua nguoi dung
     * @return array            Mang ket qua tra ve
     * [{
     *   "unit_name": "",
     *   "status": "",
     *   "lessons": {
     *       "unit1": {
     *          "status": "",
     *          "resource_title": "",
     *          "performance": 0
     *      },
     *      "unit2": {
     *          "status": "",
     *           "resource_title": "",
     *           "performance": 0
     *       },
     *       ...
     *  },
     *   "total_lessons": 0
     * }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_user_units($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/' . $user_id . '/units');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham liet ke danh sach cac buoi hoc nhom cua mot nguoi dung
     * GET /partners/users/{external_user_id}/group_sessions
     *
     * @param string $user_id   ID cua nguoi dung
     * @return array            Mang ket qua tra ve
     * {
     *   "id": 0,
     *   "title": "",
     *   "starts_at": "",
     *   "ends_at": "",
     *   "attended": false
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_user_group_sessions_attendance($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/' . $user_id . '/group_sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham Liet ke danh sach buoi day kem cua mot nguoi dung
     * GET /partners/users/{external_user_id}/private_sessions
     *
     * @param string $user_id   ID cua nguoi dung
     * @return array            Mang ket qua tra ve
     * {
     *   "id": 0,
     *   "user_id": "",
     *   "starts_at": "",
     *   "ends_at": "",
     *   "attended": false
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_user_private_sessions($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/' . $user_id . '/private_sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay thong tin thoi gian nguoi dung gianh cho hoc tap
     * GET /partners/users/{external_user_id}/time_on_task
     *
     * @param string $user_id   ID cua nguoi dung
     * @param int $start_date   Ngay bat dau
     * @param int $end_date     Ngay ket thuc
     * @return array            Mang ket qua tra ve
     * {
     *   "external_user_id": "",
     *   "total": 0,
     *   "assessments": 0,
     *   "lessons": 0,
     *   "grammar_guide": 0,
     *   "word_bank": 0,
     *   "private_sessions": 0,
     *   "group_sessions": 0,
     *   "resource_reading": 0
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_time_spent_on_the_platform_by_user($user_id = '', $start_date = 0, $end_date = 0)
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        $start_date = (($start_date && intval(trim($start_date)))   ? date('Y-m-d', intval(trim($start_date)))   : '');
        $end_date   = (($end_date   && intval(trim($end_date)))     ? date('Y-m-d', intval(trim($end_date)))     : '');
        $arr_params = array(
            'start_date'    => $start_date,
            'end_date'      => $end_date,
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/users/' . $user_id . '/time_on_task?' . $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach feedback group session
     * GET /partners/group_sessions/{group_session_id}/feedback/
     *
     * @param string $group_session_id      Group ID
     * @return array                        Mang du lieu ket qua
     * [{
     *   "group_session_id_1": {
     *       "attended": false,
     *       "comments": false,
     *       "fluency": {
     *           "description": "",
     *           "score": ""
     *       },
     *       "listening": {
     *           "description": "",
     *           "score": ""
     *       },
     *       "grammar": {
     *           "description": "",
     *           "score": ""
     *       },
     *       "pronunciation": {
     *           "description": "",
     *           "score": ""
     *       },
     *       "vocabulary": {
     *           "description": "",
     *           "score": ""
     *       }
     *   },
     *   "group_session_id_2": {
     *       ...
     *   },
     * }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_group_session_feedback($group_session_id = '')
    {
        if (!(is_string($group_session_id) || is_integer($group_session_id)) || ! trim($group_session_id)) {
            return $this->_data_return[1];
        }
        $group_session_id   = trim($group_session_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/group_sessions/' . $group_session_id . '/feedback/' );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay toan bo danh sach cac buoi hop nhom cho nguoi dung
     * GET /partners/group_sessions/tutor/{external_user_id}
     *
     * @param string $user_id   ID nguoi dung
     * @return array            Mang du lieu tra ve
     * {
     *   "id": 0,
     *   "title": "",
     *   "starts_at": "",
     *   "ends_at": "",
     *   "user_ids": [
     *       null
     *   ]
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_all_group_sessions_for_tutor($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/group_sessions/tutor/' . $user_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham Lay danh sach thong tin phan hoi cua nguoi day kem cho nguoi dung
     * GET /partners/private_sessions/{id}/feedback/
     *
     * @param string $private_session_id    private session ID
     * @return array                        Mang du lieu ket qua
     * {
     *   "session_id": "",
     *   "starts_at": "",
     *   "ends_at": "",
     *   "tutor_notes": "",
     *   "targeted_feedback": "",
     *   "next_steps": "",
     *   "grammar_score": 0,
     *   "vocabulary_score": 0,
     *   "pronunciation_score": 0,
     *   "listening_score": 0,
     *   "communication_score": 0
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_private_session_feedback($private_session_id = '')
    {
        if (!(is_string($private_session_id) || is_integer($private_session_id)) || ! trim($private_session_id)) {
            return $this->_data_return[1];
        }
        $private_session_id = trim($private_session_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/private_sessions/' . $private_session_id . '/feedback/' );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay toan bo danh sach cac buoi hop nhom rieng cua nguoi day kem voi nguoi dung
     * GET /partners/private_sessions/tutor/{external_user_id}
     *
     * @param string $user_id   ID nguoi dung
     * @return array            Mang du lieu tra ve
     * {
     *   "id": 0,
     *   "user_id": "",
     *   "starts_at": "",
     *   "ends_at": "",
     *   "attended": false
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_private_session_for_tutor($user_id = '')
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/private_sessions/tutor/' . $user_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay toan bo danh sach segments tu voxy
     * GET /partners/segments/
     *
     * @return array    Mang danh sach segments
     * {
     *   "label": "",
     *   "user_ids": [
     *       null
     *   ]
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_all_segments()
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/segments/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham tao moi mot segments voi mot nhan
     * POST /partners/segments/{label}
     *
     * @param string $label     Nhan cho segments
     * @return array            Mang du lieu tra ve
     * [{
     *    "user_ids": [],
     *    "label": "testgroup2"
     * }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function create_segment_with_label($label = '')
    {
        if (!(is_string($label) || is_integer($label)) || ! trim($label)) {
            return $this->_data_return[1];
        }
        $label  = trim($label);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL,$this->_voxy_server . '/segments/' . $label);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach lich su hoc unit(lesson) cua user trong segments
     * GET /partners/segments/{label}/users/units
     *
     * @param string $label     Nhan cua segment
     * @return array            Mang du lieu tra ve
     * [{
     *   "user_id": 0,
     *   "units": {
     *       "unit_name": "",
     *       "status": "",
     *       "lessons": "",
     *       "total_lessons": 0,
     *       "last_updated": ""
     *   }
     * }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_user_units_in_segment($label = '')
    {
        if (!(is_string($label) || is_integer($label)) || ! trim($label)) {
            return $this->_data_return[1];
        }
        $label  = trim($label);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/segments/' . $label . '/users/units');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach cac group session nguoi dung trong segment
     * GET /partners/segments/{label}/users/group_sessions
     *
     * @param string $label     Nhan cua segment
     * @return array|mixed      Mang du lieu tra ve
     * {
     *   "user_id": 0,
     *   "group_sessions": [{
     *       "id": 0,
     *       "title": "",
     *       "starts_at": "",
     *       "ends_at": "",
     *       "user_ids": [
     *           null
     *       ]
     *   }]
     * }
     * @author chuvantinh1991@gmail.com
     */
    public function list_user_group_sessions_in_segment($label = '')
    {
        if (!(is_string($label) || is_integer($label)) || ! trim($label)) {
            return $this->_data_return[1];
        }
        $label  = trim($label);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/segments/' . $label . '/users/group_sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham them mot nguoi dung vao segment
     * POST /partners/segments/{label}/users/{external_user_id}
     *
     * @param string $user_id   ID nguoi dung
     * @param string $label     Nhan segment
     * @return array            Mang du lieu ket qua
     * {}
     *
     * @author chuvantinh1991@gmail.com
     */
    public function add_user_to_a_segment($user_id = '', $label = ''){
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)
            || !(is_string($label) || is_integer($label)) || ! trim($label)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        $label      = trim($label);
        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL,$this->_voxy_server . '/segments/' . $label . '/users/' . $user_id);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach cac chom tin nang
     * GET /partners/feature_groups/
     *
     * @return array|mixed
     * [{
     *   "id": 2420,
     *   "name": "Super Basic Week L",
     *   "features": [{
     *       "allow_tutoring": false,
     *       "limit_access_to_vpa": false,
     *       "time_between_vpas": 0,
     *       "time_to_forced_vpa": 0,
     *       "new_users_start_with_vpa": false,
     *       "tutoring_credits_alotted": 0,
     *       "custom_live_chat_id": "",
     *       "logout_url": "http://lms.topicanative.edu.vn/login/index.php",
     *       "tutor_credit_purchase_url": "",
     *       "custom_support_url": "http://lms.topicanative.edu.vn/local/voxy/view/faq.php",
     *       "terms_and_conditions_url": "http://lms.topicanative.edu.vn/local/voxy/view/terms_conditions.php",
     *       "mobile_auth_url": "https://lmsxy.topicanative.edu.vn/partner-site-authentication-api",
     *       "orientation_video_url_1_en": "",
     *       "orientation_video_url_2_en": "",
     *       "orientation_video_url_1_es": "",
     *       "orientation_video_url_2_es": "",
     *       "orientation_video_url_1_pt": "",
     *       "orientation_video_url_2_pt": "",
     *       "voxy_password_required": false,
     *       "has_group_tutoring_enabled": false,
     *       "has_public_group_sessions_visibility_enabled": false,
     *       "has_public_grammar_families_enabled": false,
     *       "has_vpa_enabled": false,
     *       "group_sessions_limit": "",
     *       "has_private_tutoring_enabled": false,
     *       "has_achievement_test_enabled": false,
     *       "has_orientation_videos_enabled": false,
     *       "show_voxy_tutors_for_private_tutoring": false,
     *       "is_whitelabeled": false
     *   }],
     *   "active_users": [{
     *       "external_user_id": "111406",
     *       "email": "maihuongabbank@gmail.com"
     *   }]
     * }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_feature_group()
    {
        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/feature_groups');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham them mot nguoi dung vao mot nhom tinh nang
     * POST /partners/feature_groups/{group_id}/users/{external_user_id}
     *
     * @param int $user_id      ID nguoi dung
     * @param int $group_id     ID nhom can them (1709 la group mac dinh, khong nhin thay lop truc tuyen voxy)
     * @return array            Mang du lieu ket qua
     * {}
     *
     * @author chuvantinh1991@gmail.com
     */
    public function add_user_to_feature_group($user_id = 0, $group_id = 1709) {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)
            || !(is_string($group_id) || is_integer($group_id)) || ! trim($group_id)) {
            return $this->_data_return[1];
        }
        $user_id    = trim($user_id);
        $group_id   = trim($group_id);

        try {
            $log_starttime = microtime(TRUE);
            $log = Array(
                'group_function'    => 'voxy_connect_api_add_user_to_feature_group',
                'params'            => json_encode((object)Array()),
            );
            $id_log = $this->m_admin_tool_logs->add($log);

            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/feature_groups/'. $group_id . '/users/' . $user_id);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response   = curl_exec($ch);
            $err        = curl_error($ch);
            if($id_log) {
                $log['response']    = $response;
                $log['error']       = $err;
                $log['curl_info']   = json_encode(curl_getinfo($ch));
                $log['time_diff']   = microtime(TRUE) - $log_starttime;
                $this->m_admin_tool_logs->update($id_log, $log);
            }

            $response   = json_decode($response);
            if($response === FALSE || (!is_object($response) && !is_array($response))){
                $data_return = $this->_data_return[4];
            } else {
                if(is_object($response)){
                    $response = (array) $response;
                }
                if(isset($response['error_message'])){
                    $data_return = $response;
                } else {
                    $data_return = TRUE;
                }
            }
            // $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach cac group session cua mot nhoms tinh nang(feature group)
     * GET /partners/feature_groups/{group_id}/group_sessions
     *
     * @param string $group_id      feature group ID
     * @return array                Mang du lieu ket qua
     * [{
     *   "id": 0,
     *   "title": "",
     *   "tutor": "",
     *   "starts_at": "",
     *   "ends_at": "",
     *   "user_ids": [
     *      null
     *   ],
     *   "attendees": [
     *       null
     *   ]
     * }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_group_sessions_for_a_feature_group($group_id = ''){
        if (!(is_string($group_id) || is_integer($group_id)) || ! trim($group_id)) {
            return $this->_data_return[1];
        }
        $group_id  = trim($group_id);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/feature_groups/' . $group_id . '/group_sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay tat ca cac phien nhom co su tham gia cua nguoi su dung cua to chuc
     * GET /partners/group_sessions/
     *
     * @return array        Mang du lieu ket qua
     * [{
     *   "id": 43070,
     *   "title": "Technology",
     *   "tutor": "Vincent T.",
     *   "starts_at": "2017-09-26 08:30:00",
     *   "ends_at": "2017-09-26 09:00:00",
     *   "session_id": 43070,
     *   "user_ids": [
     *       "99189",
     *   ],
     *   "attendees": [
     *       null
     *   ]
     *   }]
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_all_group_sessions_attended_by_users()
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/group_sessions/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach Campaigns co san
     * GET /partners/campaigns/
     *
     * @return array        Mang du lieu ket qua
     * {
     *   "count": 0,
     *   "next": "",
     *   "previous": "",
     *   "results": [{
     *       "id": 0,
     *       "name": "",
     *       "user_campaigns": [{
     *           "id": 0,
     *           "external_user_id": "",
     *           "campaign_id": 0,
     *           "start_date": ""
     *       }]
     *   }]
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_available_campaigns()
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/campaigns/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay thong tin cua mot campaign
     * GET /partners/campaigns/{pk}/
     *
     * @param string $pk      ID cua campaign
     * @return array          Mang du lieu ket qua
     * {
     *   "id": 0,
     *   "name": "",
     *   "user_campaigns": [{
     *       "id": 0,
     *       "external_user_id": "",
     *       "campaign_id": 0,
     *       "start_date": ""
     *   }]
     * }
     * Or Not found
     * {
     *   "detail": "Not found."
     * }
     *
     * @author HafThuysVux <thuyvu.hdvn@email.com>
     */
    public function get_info_of_one_campaign($pk = '')
    {
        if (!(is_string($pk) || is_integer($pk)) || ! trim($pk)) {
            return $this->_data_return[1];
        }
        $pk     = trim($pk);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/campaigns/' . $pk);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham cap nhat(them) nguoi dung vao campaign
     * PUT /partners/user_campaigns/{pk}/
     *
     * @param string $pk            pk (chua hieu la gi)
     * @param string $user_id       id nguoi dung
     * @param int $campaign_id      campaign id
     * @param int $start_date       Ngay bat dau(Y-m-d)
     * @return array                Mang du lieu tra ve
     * {
     *   "id": 0,
     *   "external_user_id": "",
     *   "campaign_id": 0,
     *   "start_date": ""
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function update_user_to_campaign($pk = '', $user_id = '', $campaign_id = 0, $start_date = 0)
    {
        $start_date = (($start_date && intval(trim($start_date))) ? date('Y-m-d', intval(trim($start_date))) : '');
        if (!(is_string($pk) || is_integer($pk)) || ! trim($pk)
            || !(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)
            || !(is_string($campaign_id) || is_integer($campaign_id)) || ! intval(trim($campaign_id))
            || ! $start_date
        ) {
            return $this->_data_return[1];
        }
        $pk             = trim($pk);
        $user_id        = trim($campaign_id);
        $campaign_id    = intval(trim($campaign_id));

        $arr_params     = array(
            'external_user_id'  => $user_id,                // string
            'campaign_id'       => $campaign_id,            // integer
            'start_date'        => $start_date,             // string format YYYY-MM-DD
        );
        $str_params     = $this->_gen_string_params($arr_params);

        try {
            $ch         = curl_init();
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/user_campaigns/'. $pk);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay danh sach nguoi dung va so luong hoat dong(activities) da hoan thanh trong mot khoang ngay
     * GET /partners/user_activities/
     *
     * @param int $start_date   Ngay bat dau(Y-m-d)
     * @param int $end_date     Ngay ket thuc(Y-m-d)
     * @param int $page         So Page(int)
     * @return array            mang du lieu tra ve
     * {
     *   "count": 0,
     *   "next": "",
     *   "previous": "",
     *   "results": [{
     *       "external_user_id": "",
     *       "email": "",
     *       "first_name": "",
     *       "last_name": "",
     *       "activities": 0
     *       }]
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_user_and_their_number_of_completed_activities($start_date = 0, $end_date = 0, $page = 1)
    {
        $start_date = (($start_date && intval(trim($start_date)))   ? date('Y-m-d', intval(trim($start_date)))   : '');
        $end_date   = (($end_date   && intval(trim($end_date)))     ? date('Y-m-d', intval(trim($end_date)))     : '');
        $page       = (($page       && intval(trim($page)))         ? intval(trim($page))                               : 1 );
        $arr_params = array(
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'page'          => $page
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/user_activities?'. $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * API VOXY: Ham lay so luong hoat dong(activities) cua mot nguoi dung trong mot khoang ngay
     * GET /partners/user_activities/{external_user_id}/
     *
     * @param string $user_id       ID nguoi dung
     * @param int $start_date       Ngay bat dau(Y-m-d)
     * @param int $end_date         Ngay ket thuc(Y-m-d)
     * @return array                Mang du lieu ket qua
     * {
     *   "external_user_id": "",
     *   "email": "",
     *   "first_name": "",
     *   "last_name": "",
     *   "activities": 0
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_number_completed_activities_of_one_user($user_id = '', $start_date = 0, $end_date = 0)
    {
        if (!(is_string($user_id) || is_integer($user_id)) || ! trim($user_id)) {
            return $this->_data_return[1];
        }
        $start_date = (($start_date && intval(trim($start_date)))   ? date('Y-m-d', intval(trim($start_date)))   : '');
        $end_date   = (($end_date   && intval(trim($end_date)))     ? date('Y-m-d', intval(trim($end_date)))     : '');
        $arr_params = array(
            'start_date'    => $start_date,
            'end_date'      => $end_date
        );
        $str_params = $this->_gen_string_params($arr_params);

        try {
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header($str_params));
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/user_activities?'. $str_params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;

    }

    /**
     * API VOXY: Ham lay danh sach ngon ngu Voxy ho tro
     * GET /supported_languages/
     *
     * @return array        Mang du lieu ket qua
     * {
     *   "supported_languages": [
     *       "th",
     *       "ur",
     *       "en",
     *       "vi",
     *       "eu",
     *      ...
     *   ]
     * }
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_list_supported_languages()
    {
        try {
            $log_starttime = microtime(TRUE);
            $log = Array(
                'group_function'    => 'voxy_connect_api_get_list_supported_languages',
                'params'            => json_encode((object)Array()),
            );
            $id_log = $this->m_admin_tool_logs->add($log);

            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            curl_setopt($ch, CURLOPT_URL, $this->_voxy_server . '/supported_languages');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);

            $response       = curl_exec($ch);
            $err        = curl_error($ch);
            if($id_log) {
                $log['response']    = $response;
                $log['error']       = $err;
                $log['curl_info']   = json_encode(curl_getinfo($ch));
                $log['time_diff']   = microtime(TRUE) - $log_starttime;
                $this->m_admin_tool_logs->update($id_log, $log);
            }

            $data_return    = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return    = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * ThuyVV: Ham tra ve danh sach lang ma voxy support (lay tu voxy ve)
     *
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _get_list_supported_languages(){
        return Array(
            'vi' => 'vi',
            'en' => 'en',
            'th' => 'th',
            'ca' => 'ca',
            'cy' => 'cy',
            'ga' => 'ga',
            'cs' => 'cs',
            'gl' => 'gl',
            'pt' => 'pt',
            'tr' => 'tr',
            'lv' => 'lv',
            'lt' => 'lt',
            'te' => 'te',
            'pl' => 'pl',
            'ta' => 'ta',
            'hr' => 'hr',
            'de' => 'de',
            'da' => 'da',
            'hi' => 'hi',
            'he' => 'he',
            'ml' => 'ml',
            'mk' => 'mk',
            'ur' => 'ur',
            'ms' => 'ms',
            'el' => 'el',
            'zh' => 'zh',
            'is' => 'is',
            'it' => 'it',
            'kn' => 'kn',
            'ar' => 'ar',
            'eu' => 'eu',
            'et' => 'et',
            'az' => 'az',
            'id' => 'id',
            'es' => 'es',
            'ru' => 'ru',
            'nl' => 'nl',
            'nb' => 'nb',
            'ro' => 'ro',
            'fr' => 'fr',
            'bg' => 'bg',
            'uk' => 'uk',
            'bn' => 'bn',
            'fa' => 'fa',
            'fi' => 'fi',
            'hu' => 'hu',
            'ja' => 'ja',
            'ka' => 'ka',
            'sr' => 'sr',
            'sq' => 'sq',
            'ko' => 'ko',
            'sv' => 'sv',
            'sk' => 'sk',
            'sl' => 'sl',
        );
    }

    /**
     * ThuyVV: Ham tra ve danh sach level tren voxy
     *
     * @return array    Mang voxy level
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _get_list_voxy_level()
    {
        return Array(
            1 => 'Super Basic', // super basic
            2 => 'Basic',       // basic
            3 => 'Pre Inter',   // pre inter
            4 => 'Inter',       // inter
            5 => 'Advan',       // advan
        );
    }

    /**
     * ThuyVV: Ham tra ve cac trang thai cho phép tutoring_credits
     *
     * @return array    Mang tutoring_credits
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _get_list_voxy_tutoring_credits_status()
    {
        return Array(
            1 => 'YES',
            0 => 'NO'
        );
    }

    public function _get_list_voxy_can_reserve_group_sessions_status()
    {
        return Array(
            'TRUE'  => 'TRUE',
            'FALSE' => 'FALSE'
        );
    }

    /**
     * Ham tien ich mo rong xu ly
     */

    /**
     * Ham gen ra mang Header truyen sang voxy
     *
     * @param string $str_params    Chuoi String params
     * @return array                Mang header tra ve
     *
     * @author chuvantinh1991@gmail.com
     */
    private function _gen_curl_header($str_params = '') {
        if(is_string($str_params) && trim($str_params) != ''){
            return array('AUTHORIZATION:Voxy ' . $this->_voxy_key . ':' . hash('sha256', ($this->_voxy_api_secret . $str_params)));
        } else {
            return array('AUTHORIZATION:Voxy ' . $this->_voxy_key . ':' . hash('sha256', ($this->_voxy_api_secret)));
        }
    }

    /**
     * Ham convert Ten hoc vien ve khong dau de gui sang Voxy
     *
     * @param string $str      Chuoi ky tu unicode
     * @return string          Chuoi ky tu khong dau
     *
     * @author chuvantinh1991@gmail.com
     */
    private function _handling_string_unicode($str = '') {
        $str_name_return = '';
        if(! $str || ! is_string($str)) {
            return $str_name_return;
        }

        $this->load->library('ThuyVu_lib');
        $str_name_return    = $this->thuyvu_lib->ChangeCodeUtf8($str);
        $str_name_return    = $this->thuyvu_lib->StripUnicode($str_name_return);

        return $str_name_return;
    }

    /**
     * Ham chuan hoa mang du lieu tra ve cho nguoi dung
     *
     * @param string $result        Ket qua tra ve tu voxy
     * @return array                Mang du lieu tra ve
     *
     * @author chuvantinh1991@gmail.com
     */
    private function _handling_the_result($result = '') {
        if($result === FALSE || $result === NULL){
            return $this->_data_return[3];
        }
        $result = json_decode($result);
        if(is_object($result)){
            $result = (array) $result;
        } else if($result === FALSE || ! is_array($result)){
            return $this->_data_return[4];
        }
        if(count($result) == 0){
            $data_return = $this->_data_return[5];
        } else {
            $data_return = $result;
        }

        return $data_return;
    }

    /**
     * Ham convert array param sang url param de gui sang voxy
     *
     * @param array $arr_params     Mang param can gui sang voxy
     * @return string               Chuoi params da convert
     */
    private function _gen_string_params($arr_params = Array()){
        $__str_params = '';
        if(!is_array($arr_params)){
            return $__str_params;
        }
        ksort($arr_params);
        foreach ($arr_params as $key => $value) {
            if(trim($value) == '') {
                continue;
            }
            if($key == "email_address") {
                $__str_params .= $key . '=' . str_replace('@','%40', trim($value)) . '&';
            } else {
                $__str_params .= $key . '=' . trim($value) . '&';
            }
        }
        if(strlen($__str_params) > 0 && substr($__str_params, -1) == '&'){
            $__str_params = substr($__str_params, 0, -1);
        }

        return $__str_params;
    }

}