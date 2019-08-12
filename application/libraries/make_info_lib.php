<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 01/19/2018
 * Time: 11:48
 *
 * @author chuvantinh1991@gmail.com
 */

class Make_info_lib
{
    protected $_ci;

    public function __construct()
    {
        $this->_ci = &get_instance();
        $this->_ci->load->library('ThuyVu_lib');
        $this->_ci->load->model('m_sys_level_mapping', 'level_mapping');
        $this->_ci->load->model('m_voxy_connect_api', 'voxy_connect_api');
    }

    /**
     * Ham make voxy User_id
     * @param int $student_id
     * @param int $weight
     * @return bool|int
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_voxy_user_id($student_id = 0, $weight = 0){
        $student_id     = intval($student_id);
        $weight         = intval($weight);
        if(!$student_id || $weight === FALSE){
            return FALSE;
        }
        return ($weight + $student_id);
    }

    /**
     * Ham make Voxy user email
     * @param string $student_email
     * @param string $connect
     * @return bool|string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_voxy_user_email($student_email = '', $connect = ''){
        $student_email  = strtolower(trim($student_email));
        $connect        = strtolower(trim($connect));
        if(!$student_email || $connect === FALSE){
            return FALSE;
        }
        // return ($connect . $student_email);
        return ($student_email);
    }

    /**
     * Ham convert Ten hoc vien ve khong dau de gui sang Voxy
     * @param string $student_name
     * @return string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_voxy_user_name($student_name = '') {
        $str_name_return = '';
        if(!($student_name && is_string($student_name))) {
            return $str_name_return;
        }

        $str_name_return    = $this->_ci->thuyvu_lib->ChangeCodeUtf8($student_name);
        $str_name_return    = $this->_ci->thuyvu_lib->StripUnicode($str_name_return);

        return $str_name_return;
    }

    /**
     * Ham make voxy user lang
     * @param string $student_lang
     * @return string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_voxy_user_lang($student_lang = ''){
        $return_lang    = 'en';
        $student_lang   = strtolower(trim($student_lang));
        if(!$student_lang){
            return $return_lang;
        }

        $list_lang = $this->_ci->voxy_connect_api->_get_list_supported_languages();
        if(is_array($list_lang) && array_key_exists($student_lang, $list_lang)){
            $return_lang = $student_lang;
        }
        return $return_lang;
    }

    /**
     * Ham make voxy user level tu native level
     * @param string $student_level
     * @return int
     *
     * @author chuvantinh1991@gmail.com
     */
    public function make_voxy_user_level($student_level = ''){
        $voxy_user_level    = 1; // default
        $student_level      = strtolower($student_level);
        if(!$student_level){
            return $voxy_user_level;
        }
        $level_temp = $this->_ci->level_mapping->get_voxy_level($student_level);
        if($level_temp) {
            $voxy_user_level = $level_temp;
        }
        return $voxy_user_level;
    }
}