<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 12/18/2017
 * Time: 15:19
 *
 * @author chuvantinh1991@gmail.com
 */

/**
 * Class ThuyVu_lib
 *
 * @author chuvantinh1991@gmail.com
 */
class ThuyVu_lib
{
    /**
     * @param string $str
     * @return string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function ChangeCodeUtf8($str = '')
    {
        $str = trim($str);
        if (!$str) {
            return '';
        }

        $str = htmlspecialchars($str, ENT_QUOTES);
        return $str;
    }

    /**
     * @param string $str
     * @return mixed|string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function StripUnicode($str = '') {
        $str = trim($str);
        if (!$str) {
            return '';
        }

        $arrUnicode = array(
            'a'=>array('á','à','ả','ã','ạ','ă','ắ','ặ','ằ','ẳ','ẵ','â','ấ','ầ','ẩ','ẫ','ậ'),
            'A'=>array('Á','À','Ả','Ã','Ạ','Ă','Ắ','Ặ','Ằ','Ẳ','Ẵ','Â','Ấ','Ầ','Ẩ','Ẫ','Ậ'),
            'd'=>array('đ'),
            'D'=>array('Đ'),
            'e'=>array('é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ'),
            'E'=>array('É','È','Ẻ','Ẽ','Ẹ','Ê','Ế','Ề','Ể','Ễ','Ệ'),
            'i'=>array('í','ì','ỉ','ĩ','ị'),
            'I'=>array('Í','Ì','Ỉ','Ĩ','Ị'),
            'o'=>array('ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ'),
            'O'=>array('Ó','Ò','Ỏ','Õ','Ọ','Ô','Ố','Ồ','Ổ','Ỗ','Ộ','Ơ','Ớ','Ờ','Ở','Ỡ','Ợ'),
            'u'=>array('ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự'),
            'U'=>array('Ú','Ù','Ủ','Ũ','Ụ','Ư','Ứ','Ừ','Ử','Ữ','Ự'),
            'y'=>array('ý','ỳ','ỷ','ỹ','ỵ'),
            'Y'=>array('Ý','Ỳ','Ỷ','Ỹ','Ỵ')
        );

        foreach($arrUnicode as $nonUnicode => $subArrUnicode) {
            $str = str_replace($subArrUnicode, $nonUnicode, $str);
        }

        return $str;
    }

    /**
     * @param string $str
     * @param string $replaceCharacters
     * @return mixed|string
     *
     * @author chuvantinh1991@gmail.com
     */
    public function StringCodeFormat($str = '', $replaceCharacters = '') {
        $str = trim($str);
        if (!$str) {
            return '';
        }

        if(!$replaceCharacters) {
            $replaceCharacters = '-';
        }

        $str = $this->ChangeCodeUtf8($str);
        // xoa cac ky tu tieng viet co dau
        $str = $this->StripUnicode($str);
        // cho ve chu thuong
        $str = strtolower($str);
        // xoa cac ky tu dac biet
        $str = preg_replace("/!|@|%|\^|\*|\(|\)|\-|\+|\=|\<|\>|$|\?|\/|,|\.|\:|\;|\\' | |\"|\&|\#|\[|\]|~|_/", $replaceCharacters, $str);
        $str = str_replace('$', $replaceCharacters, $str);
        // xoa cac ky tu -- lap lai nhieu lan
        $str = explode($replaceCharacters, $str);
        foreach($str as $key => $characters){
            if($characters == ''){
                unset($str[$key]);
            }
        }

        $str = implode($replaceCharacters, $str);
        return $str;
    }

    /**
     * Ham cat lay chuoi URL day du: http(s)://domain
     * Sung dung mang de cat
     * @param string $str_url
     * @return string : http(s)://(www)lms.topicanative.edu.vn
     *
     * @author chuvantinh1991@gmail.com
     */
    public function cut_full_domain($str_url = '') {
        $_domain = '';
        if(!$str_url){
            return $_domain;
        }
        $bits = explode('/', $str_url);
        if($bits[0] == 'http:' || $bits[0] == 'https:'){
            $_domain = $bits[0].'//'.$bits[2];
        } else if($bits[0]){
            $_domain = 'http://'.$bits[0];
        }
        unset($bits);
        return $_domain;
    }

    /**
     * Ham cat lay chuoi URL day du: http(s)://domain
     * Sung dung regex
     * @param string $str_url
     * @return mixed|string : http(s)://(www)lms.topicanative.edu.vn
     *
     * @author chuvantinh1991@gmail.com
     */
    function preg_full_domain ($str_url = '') {
        $_domain = '';
        if(!$str_url){
            return $_domain;
        }
        $_domain = preg_replace('/^((http(s)?:\/\/)?([^\/]+)?)(.*)/','$1',$str_url);
        return $_domain;
    }

    /**
     * Ham cat lay domain: domain
     * su dung mang
     * @param string $str_url
     * @return string : lms.topicanative.edu.vn
     *
     * @author chuvantinh1991@gmail.com
     */
    function cut_short_domain($str_url = ''){
        $_domain = '';
        if(!$str_url){
            return $_domain;
        }

        $bits = explode('/', $str_url);
        if($bits[0] == ''){
            unset($bits[0]);
            $_domain = $this->cut_short_domain(implode('/', $bits));
        } else if($bits[0] == 'http:' || $bits[0] == 'https:'){
            $_domain = $bits[2];
        } else if($bits[0]){
            $_domain = $bits[0];
        }
        $bits = explode('.', $_domain);
        if($bits[0] == 'www'){
            unset($bits[0]);
            $_domain = implode('.', $bits);
        }
        unset($bits);
        return $_domain;
    }
}