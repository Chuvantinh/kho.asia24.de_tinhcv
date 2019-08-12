<?php

/**
 * Class Auth
 *
 * @author chuvantinh1991@gmail.com
 */
class Auth {
    var $md5_salt   = '1@ANmC^%^wrFO';
    var $str_prefix = '$2y$tvv$';

    //put your code here
    function auth_api($username, $password) {
        $password   = $this->gen_rest_string_password($password);
        $CI         = &get_instance();
        $CI->db->select();
        $CI->db->from('admin_rest_users AS m');
        $CI->db->where('m.status', 1);
        $CI->db->where('m.user_name', $username);
        $CI->db->where('m.password', $password);
        $query = $CI->db->get();
        
        if($query->num_rows()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Ham gen chuoi password cho he thong
     * @param string $password
     * @return null|string
     *
     * @author chuvantinh1991@gmail.com
     */
    private function gen_rest_string_password($password = '')
    {
        if(!$password){
            return null;
        }

        return $this->str_prefix . md5($this->str_prefix . $this->md5_salt . sha1($password . $this->md5_salt));
    }

}
