<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class home_base
 *
 * @author chuvantinh1991@gmail.com
 */
class home_base extends MX_Controller
{
    /**
     * @var Object -- Thông tin chung của user đã đăng nhập (chưa đăng nhập, $user_info = NULL)
     */
    var $USER               = null;

    /**
     * @var Object -- Biến đối tượng config các thông tin của site, mới đầu sẽ fix cứng trong base, sau sẽ tách vào cơ sở dữ liệu
     */
    var $site_config        = null;

    /**
     * Truyền biến bất quy tắc qua view, biến được gán giá trị trong hàm _setting_config()
     */
    var $path_theme_view    = '';
    var $path_theme_file    = '';
    var $path_static_file   = '';
    var $favicon_link       = '';
    var $title_default      = '[Manager - Service]';

    /**
     * @var string data-barack body site
     */
    var $json_item_barack   = ";mtb;";

    /**
     * @var string salt string in gen password user
     */
    var $md5_salt           = '1@ANmC^%^wrFO';

    /**
     * @var string prefix password user
     */
    var $str_prefix         = '$2y$tvv$';

    public function __construct()
    {
        parent::__construct();
        //date_default_timezone_set('Euro/Berlin');

		$this->load->library("session");
        $this->load->helper('text');
        session_start();
        $_SESSION['enable_editor']      = true;
        $_SESSION['img_absolute_path']  = 'gallery/'; /* không bắt đầu bằng dấu "/" */
        $_SESSION['img_domain']         = base_url();

        $this->_setting_config();
        $this->require_login();

        if (!$this->check_permission()) {
            if (isset($this->USER->permission_data) && is_array($this->USER->permission_data) && count($this->USER->permission_data)) {
                if ($this->input->is_ajax_request()) {
                    $data_return = Array(
                        'callback'  => 'permission_error',
                        'state'     => 0,
                        'msg'       => 'Bạn ko có quyền truy nhập',
                    );
                    echo json_encode($data_return);
                    exit;
                } else {
                    $redirect = site_url();
                    echo "<h2>Bạn không có quyền truy cập chức năng này!</h2>";
                    echo "Mục đích của bạn đến đây làm gì?<br />";
                    echo "Click vào <a href='" . $redirect . "'>đây</a> để về lại thế giới của bạn.";
                    exit;
                }
            } else {
                redirect(site_url('login'));
            }
        }
    }

    /**
     * Kiem tra User da dang nhap hay chua, chua dang nhap thi redirect ve trang dang nhap
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function require_login()
    {
        if($this->session->userdata('USER')){
            $this->USER = $this->session->userdata('USER');
            $this->USER->permission_data = $this->get_user_permission_data($this->USER->id);
        } else {
            redirect(site_url('login'));
        }
    }

    /**
     * Ham kiem tra user co quyen trong chuc nang hay khong
     * @return bool : false - khong co quyen, true - co quyen
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function check_permission()
    {
        $per_data = isset($this->USER->permission_data) ? $this->USER->permission_data : array();
        if(!count($per_data)) {
            return FALSE;
        }
        $class  = $this->router->fetch_class();
        $method = $this->router->fetch_method();

        if (is_array($per_data) && count($per_data) > 0) {
            foreach ($per_data as $controller => $arr_action) {
                if($controller == '*'){
                    foreach ($arr_action as $one_action){
                        if($one_action == '*' || $one_action == $method){
                            return true;
                        }
                    }
                } else if ($controller == $class) {
                    foreach ($arr_action as $one_action){
                        if($one_action == '*' || $one_action == $method){
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $content
     * @param null $header_page
     * @param null $title
     * @param null $description
     * @param null $keywords
     * @param null $canonical
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function master_page_blank($content, $header_page = NULL, $title = NULL, $description = NULL, $keywords = NULL, $canonical = NULL)
    {
        $data["title"]              = $title ? $title : $this->title_default;
        $data["description"]        = $description ? $description : $this->title_default;
        $data["keywords"]           = $this->title_default . $keywords;
        $data["canonical"]          = $canonical ? $canonical : NULL;
        $data["icon"]               = $this->site_config->favicon_link;
        /* head chung của các masterPage */
        // $data["header_base"]     = null;
        $data["header_base"]        = $this->load->view($this->site_config->path_theme_view . "base_master/head", $data, TRUE);
        /* head riêng của các masterPage */
        $data["header_master_page"] = "";
        /* head riêng của các từng page */
        $data["header_page"]        = $header_page ? $header_page : "";
        /* Lấy thông tin phần html */
        $data ["content"]           = $content;

        $this->load->view($this->site_config->path_theme_view . "base_master/master_page_blank", $data);
    }

    /**
     * @param $content
     * @param null $head_page
     * @param null $title
     * @param null $description
     * @param null $keywords
     * @param null $canonical
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function master_page($content, $head_page = NULL, $title = NULL, $description = NULL, $keywords = NULL, $canonical = NULL)
    {
        $data = Array();
        /* Lấy thông tin phần head */
        $data["title"]                  = $title ? $title : $this->title_default;
        $data["description"]            = $description ? $description : $this->title_default;
        $data["keywords"]               = $this->title_default . $keywords;
        $data["canonical"]              = $canonical ? $canonical : NULL;
        $data["icon"]                   = $this->site_config->favicon_link;
        /* head chung của các masterPage */
        $data["header_base"]            = $this->load->view($this->site_config->path_theme_view . "base_master/head", $data, TRUE);
        /* head riêng của các masterPage */
        $data["header_master_page"]     = "";
        /* head riêng của các từng page */
        $data["header_page"]            = $head_page ? $head_page : "";

        /* Lấy thông tin phần html */
        $data["header"]                 = $this->get_header();
        $data["menu_bar"]               = $this->get_menu_bar();
        $data["breadcrumb"]             = $this->get_breadcrumb();
        $data["content"]                = $content;
        $data["left_content"]           = $this->get_left_content();
        $data["right_content"]          = $this->get_right_content();
        $data["footer"]                 = $this->get_footer();

        $this->load->view($this->site_config->path_theme_view . "base_master/master_page", $data);
    }

    /**
     * @param array $data
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_header($data = Array()) {
        return $this->load->view($this->site_config->path_theme_view . "base_master/header", $data, TRUE);
    }

    /**
     * @param array $data
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_menu_bar($data = Array()) {
        $data["logout_url"]         = site_url('login/logout');
        $data["changer_info_url"]   = site_url('admin_accounts/edit/'.$this->USER->id);
        $data["avatar"]             = $this->site_config->path_static_file . "images/default_avatar.jpg";
        $data["user_name"]          = $this->USER->user_name;

        return $this->load->view($this->site_config->path_theme_view . "base_master/menu_bar", $data, TRUE);
    }

    /**
     * Get left content
     * @param array $data
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_left_content($data = Array()) {
        $data["menu_data"]  = $this->_get_left_content_data();
        return $this->load->view($this->site_config->path_theme_view . "base_master/left_content", $data, TRUE);
    }

    /**
     * @param array $data
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_breadcrumb($data = Array())
    {
        return $this->load->view($this->site_config->path_theme_view . "base_master/breadcrumb", $data, TRUE);
    }

    /**
     * Get Right Content
     * @param array $data
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_right_content($data = Array())
    {
        return $this->load->view($this->site_config->path_theme_view . "base_master/right_content", $data, TRUE);
    }

    /**
     * Get footer content
     * @param array $data
     * @return mixed
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_footer($data = Array())
    {
        return $this->load->view($this->site_config->path_theme_view . "base_master/footer", $data, TRUE);
    }

    /**
     * Ham ren random string passwd
     * @param int $length
     * @return bool|string
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function random_password($length = 8)
    {
        if(!$length){
            return false;
        }
        $alphabet       = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass           = array(); //remember to declare $pass as an array
        $alphaLength    = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Hàm lấy html view khu vực phân trang
     * @param type $total = Tổng số trang
     * @param type $current = Trang hiện tại
     * @param type $display = Số link hiển thị
     * @param type $link = Link gốc
     * @param type $key = Key cần thêm
     * @return type HtmlString
     */
    protected function _get_pagging($total, $current, $display, $link, $key = "p")
    {
        $data["total_page"]         = $total;
        $data["current_page"]       = $current;
        $data["page_link_display"]  = $display;
        $data["link"]               = $link;

        $data["key"]                = $key;

        $viewFile = "base_manager/pagging";
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'pagging.php')) {
            $viewFile = $this->name["view"] . '/' . 'pagging';
        }

        return $this->load->view($this->site_config->path_theme_view . $viewFile, $data, true);
    }

    /**
     * 	Hàm cài đặt thông tin chung (theme_path, view_path...)
     * 	Lúc load view: dùng biến $this->site_config->path_theme_view ( cụ thể: $this->load->view($this->site_config->path_theme_view . "folder/file_view"); )
     * 		Mặc định $this->site_config->path_theme_view = "/"
     * 	Lúc load css, js, images: dùng biến $this->site_config->path_theme_file.
     * 		Mặc định $this->site_config->path_theme_file = "http://domainname.com/themes/"
     *
     * @author chuvantinh1991@gmail.com
     */
    private function _setting_config()
    {
        $this->site_config      = new stdClass();
        $this->path_theme_view  = $this->site_config->path_theme_view     = '/';
        $this->path_theme_file  = $this->site_config->path_theme_file     = base_url('themes') . '/';
        $this->path_static_file = $this->site_config->path_static_file    = base_url('static') . '/';
        $this->favicon_link     = $this->site_config->favicon_link        = $this->site_config->path_static_file . "icons/favicon.ico";
    }

    /**
     * Ham gen chuoi password cho he thong
     * @param string $password
     * @return null|string
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function gen_string_password($password = '')
    {
        if(!$password){
            return null;
        }

        return $this->str_prefix . md5($this->str_prefix . $this->md5_salt . sha1($password . $this->md5_salt));
    }

    /**
     * ham get data menu left
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function _get_left_content_data()
    {
        // cheat != production -> get DB 24/24
        if(defined('ENVIRONMENT') && ENVIRONMENT != 'production'){
            return $this->get_user_menus_data($this->USER->permission_data);

        } else if(isset($this->USER->menus_data) && count($this->USER->menus_data) > 0){
            return $this->USER->menus_data;

        } else {
            return $this->get_user_menus_data($this->USER->permission_data);
        }

    }

    /**
     * Ham kiem tra User co quyen hien thi menu trai hay ko
     * @param array $per_data
     * @return array
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_user_menus_data($per_data = array()){
        $this->load->model('m_admin_menus', 'menus');
        $list_data_menu     = $this->menus->get_list(array('m.status' => 1));
        $list_parent_menu   = array();
        $list_child_menu    = array();
        foreach ($list_data_menu AS $one_parent_menu) {
            $one_parent_menu    = (array) $one_parent_menu;
            $_one_parent_menu   = array();
            $_one_parent_menu['text']       = trim($one_parent_menu['display']);
            $_one_parent_menu['icon']       = trim($one_parent_menu['icon']);
            $_one_parent_menu['url']        = (trim($one_parent_menu['url']) == '#' ? trim($one_parent_menu['url']) : site_url(trim($one_parent_menu['url'])));
            $_one_parent_menu['controller'] = trim($one_parent_menu['controller']);
            $_one_parent_menu['method']     = trim($one_parent_menu['method']);
            if(trim($one_parent_menu['class'])){
                $_one_parent_menu['class']  = trim($one_parent_menu['class']);
            }

            if(!$one_parent_menu['parent_id']) {
                $list_parent_menu[$one_parent_menu['id']] = $_one_parent_menu;
            } else {
                $_one_parent_menu['parent_id'] = $one_parent_menu['parent_id'];
                $_one_parent_menu['id'] = $one_parent_menu['id'];
                $list_child_menu[$one_parent_menu['id']] = $_one_parent_menu;
            }
            unset($_one_parent_menu);
        }

        foreach ($list_child_menu AS $one_child_menu) {
            $parent_id  = $one_child_menu['parent_id'];
            unset($one_child_menu['parent_id']);
            $child_id   = $one_child_menu['id'];
            unset($one_child_menu['id']);

            if(isset($list_parent_menu[$parent_id])) {
                $list_parent_menu[$parent_id]['child'][$child_id] = $one_child_menu;
            }
        }
        unset($list_child_menu);

        $menu_return = array();
        // biet la dat vong lap long nay hoi tu,
        // nhung de sap xep theo dung thu tu menu nhu ban dau lay ra
        // nen chap nhan (dang nao no cung chi chay 1 lan roi luu vao session)
        if (is_array($per_data) && count($per_data) > 0 && count($list_parent_menu) > 0) {
            foreach ($list_parent_menu as $parent_key => $one_parent_menu){
                if (isset($one_parent_menu['child']) && count($one_parent_menu['child']) > 0) {
                    $_one_parent_menu = $one_parent_menu;
                    unset($_one_parent_menu['controller']);
                    unset($_one_parent_menu['method']);
                    unset($_one_parent_menu['child']);

                    $menu_return[$parent_key] = $_one_parent_menu;
                    foreach ($per_data as $controller => $arr_action) {
                        if($controller == '*'){
                            $menu_return = $list_parent_menu;
                            return $menu_return;
                        }
                        foreach ($one_parent_menu['child'] as $one_child_key => $one_child_menu) {
                            if(isset($one_child_menu['controller']) && isset($one_child_menu['method'])) {
                                if($controller == $one_child_menu['controller'] && (in_array($one_child_menu['method'], $arr_action) || in_array('*', $arr_action))){
                                    $menu_return[$parent_key]['child'][$one_child_key] = $one_child_menu;
                                }
                            }
                        }
                    }

                    if(!isset($menu_return[$parent_key]['child'])){
                        unset($menu_return[$parent_key]);
                    }
                } else {
                    if(isset($one_parent_menu['controller']) && isset($one_parent_menu['method'])) {
                        unset($one_parent_menu['child']);
                        foreach ($per_data as $controller => $arr_action) {
                            if ($controller == '*') {
                                $menu_return = $list_parent_menu;
                                return $menu_return;
                            }
                            if ($controller == $one_parent_menu['controller'] && (in_array($one_parent_menu['method'], $arr_action) || in_array('*', $arr_action))) {
                                $menu_return[$parent_key] = $one_parent_menu;
                            }
                        }
                    }
                }
            }
        }
        return $menu_return;
    }

    /**
     * Ham lay thong tin tat ca quyen tren he thong cua 1 User
     * @param int $user_id
     * @return array        Mang permission
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_user_permission_data($user_id = 0){
        $return_data = array();

        if(!$user_id){
            return $return_data;
        }
        $this->load->model("m_admin_accounts", "account");
        $user_role_data = $this->account->get_user_role_data($user_id);
        if(is_array($user_role_data) && count($user_role_data) > 0){
            foreach ($user_role_data as $key => $one_data){
                $func_controller = trim($one_data->func_controller);
                $func_action = trim($one_data->func_action);
                if($func_controller == '*' && $func_action == '*'){
                    $return_data = array(
                        '*' => array('*')
                    ); // full permission
                    break;
                }
                if($func_action == '*'){
                    unset($return_data[$func_controller]);
                    $return_data[$func_controller][0] = '*';
                    continue;
                }
                if(isset($return_data[$func_controller][0]) && $return_data[$func_controller][0] == '*'){
                    continue;
                } else {
                    $return_data[$func_controller][] = $func_action;
                }
            }
        }

        return $return_data;
    }
}
				