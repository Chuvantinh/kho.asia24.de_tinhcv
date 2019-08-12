<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 06/05/2018
 * Time: 09:29
 *
 * @author chuvantinh1991@gmail.com
 */

class Admin_custom_tools extends manager_base
{
    /**
     * @var array   Mang du lieu mau tra ve Ajax
     */
    private $ajax_data_return = Array(
        'status'    => FALSE,
        'msg'       => 'Dữ liệu không tồn tại !',
        'data'      => Array()
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->model('m_voxy_connect_api', 'm_voxy_connect');
    }

    /**
     * Setting Controller
     *
     * @author chuvantinh1991@gmail.com
     */
    public function setting_class()
    {
        $this->name = Array(
            "class"     => "admin_custom_tools",
            "view"      => "admin_custom_tools",
            "model"     => "m_admin_custom_tools",
            "object"    => "Admin Tools"
        );
    }

    /**
     * Hàm index, tự động gọi tới hàm manager
     */
    public function index()
    {
        $this->manager();
    }

    /**
     * Ham tong hop du lieu tra ve Ajax
     *
     * @author chuvantinh1991@gmail.com
     */
    public function ajax_response()
    {
        $this->ajax_data_return['data'] = (object) $this->ajax_data_return['data'];
        echo json_encode($this->ajax_data_return);
    }

    // HAM XU LY AJAX =======================

    /**
     * Ham xu ly tao tai khoan Voxy
     *
     * @author chuvantinh1991@gmail.com
     */
    public function voxy_create_new_user()
    {
        $data = $this->input->post();

        if(!(isset($data['external_user_id']) && $data['external_user_id'])){
            $this->ajax_data_return['msg'] = 'ID người dùng không hợp lệ !';
            return $this->ajax_response();
        }
        if(isset($data['expiration_date']) && $data['expiration_date'] != ''){
            $data['expiration_date'] = strtotime($data['expiration_date']);
        }
        if(isset($data['date_of_next_vpa']) && $data['date_of_next_vpa'] != ''){
            $data['date_of_next_vpa'] = strtotime($data['date_of_next_vpa']);
        }
        if(isset($data['can_reserve_group_sessions']) && $data['can_reserve_group_sessions'] != ''){
            $data['can_reserve_group_sessions'] = strtolower($data['can_reserve_group_sessions']);
        }

        $api_data = $this->m_voxy_connect->register_a_new_user($data['external_user_id'], $data);
        if($api_data && isset($api_data['error_message'])) {
            $this->ajax_data_return['msg'] = $api_data['error_message'];
        } else {
            $this->ajax_data_return['status'] = TRUE;
            $this->ajax_data_return['msg'] = 'Tạo tài khoản người dùng thành công !';
            $this->ajax_data_return['data'] = $api_data;
        }

        return $this->ajax_response();
    }

    /**
     * Ham xu ly Call API sang Voxy de lay thong tin nguoi dung
     * Su dụng Ajax Post goi vao day
     *
     * @author chuvantinh1991@gmail.com
     */
    public function voxy_show_user_info()
    {
        $params = $this->input->post();

        if(!(isset($params['external_user_id']) && $params['external_user_id'])){
            $this->ajax_data_return['msg'] = 'LMS ID không hợp lệ !';
        }
        $user_info = $this->m_voxy_connect->get_info_of_one_user($params['external_user_id']);
        if($user_info && isset($user_info['error_message'])) {
            $this->ajax_data_return['msg'] = $user_info['error_message'];
        } else {
            $this->ajax_data_return['status'] = TRUE;
            $this->ajax_data_return['msg'] = 'Lấy dữ liệu thành công !';
            $this->ajax_data_return['data'] = $user_info;
        }
        return $this->ajax_response();
    }

    /**
     * Ham xu ly chinh sua thong tin tai khoan nguoi dung tren voxy
     *
     * @author chuvantinh1991@gmail.com
     */
    public function voxy_update_user_info()
    {
        $data = $this->input->post();

        if(!(isset($data['external_user_id']) && $data['external_user_id'])){
            $this->ajax_data_return['msg'] = 'ID người dùng không hợp lệ !';
            return $this->ajax_response();
        }
        if(isset($data['expiration_date']) && $data['expiration_date'] != ''){
            $data['expiration_date'] = strtotime($data['expiration_date']);
        }
        if(isset($data['date_of_next_vpa']) && $data['date_of_next_vpa'] != ''){
            $data['date_of_next_vpa'] = strtotime($data['date_of_next_vpa']);
        }
        if(isset($data['can_reserve_group_sessions']) && $data['can_reserve_group_sessions'] != ''){
            $data['can_reserve_group_sessions'] = strtolower($data['can_reserve_group_sessions']);
        }

        if(isset($data['feature_group']) && $data['feature_group'] != ''){
            $api_data = $this->m_voxy_connect->add_user_to_feature_group($data['external_user_id'], $data['feature_group']);
            if($api_data && isset($api_data['error_message'])) {
                $this->ajax_data_return['msg'] = $api_data['error_message'];
                return $this->ajax_response();
            }
        }

        $api_data = $this->m_voxy_connect->update_profile_of_one_user($data['external_user_id'], $data);
        if($api_data && isset($api_data['error_message'])) {
            $this->ajax_data_return['msg'] = $api_data['error_message'];
        } else {
            $this->ajax_data_return['status'] = TRUE;
            $this->ajax_data_return['msg'] = 'Cập nhật thông tin người dùng thành công !';
            $this->ajax_data_return['data'] = $api_data;
        }

        return $this->ajax_response();
    }

    /**
     * Ham xu ly lay duong link dang nhap vao voxy voi quyen 1 nguoi dung
     *
     * @author chuvantinh1991@gmail.com
     */
    public function voxy_login_by_user()
    {
        $data = $this->input->post();
        if(!(isset($data['external_user_id']) && $data['external_user_id'])){
            $this->ajax_data_return['msg'] = 'ID người dùng không hợp lệ !';
            return $this->ajax_response();
        }

        $api_data = $this->m_voxy_connect->get_a_user_auth_token($data['external_user_id']);
        if($api_data && isset($api_data['error_message'])) {
            $this->ajax_data_return['msg'] = $api_data['error_message'];
        } else {
            $this->ajax_data_return['status']   = TRUE;
            $this->ajax_data_return['msg']      = 'Lấy link vào học Voxy thành công !';
            $this->ajax_data_return['data']     = $api_data;
        }

        return $this->ajax_response();
    }

    /**
     * Ham xu ly lay danh sach Feature Group tren he thong voxy
     *
     * @author chuvantinh1991@gmail.com
     */
    public function voxy_show_all_feature_group()
    {
        $api_data = $this->m_voxy_connect->get_list_feature_group();
        if($api_data && isset($api_data['error_message'])) {
            $this->ajax_data_return['msg'] = $api_data['error_message'];
        } else {
            $this->ajax_data_return['status']   = TRUE;
            $this->ajax_data_return['msg']      = 'Lấy danh sách Feature Group thành công !';
            $this->ajax_data_return['data']     = $api_data;
        }

        return $this->ajax_response();
    }

    /**
     * Ham load file excel va chuyen nguoi dung ve group id da truyen vao
     *
     * $fileInput = Array(
     *      'name'      => '',
     *      'type'      => '',
     *      'error'     => '',
     *      'tmp_name'  => '',
     * )
     * @author ThuyVux <thuyvu.hdvn@gmail.com>
     */
    public function voxy_add_users_to_feature_group()
    {
        $fileInput = $_FILES['fileinput'];
        if(!$fileInput || !is_array($fileInput)){
            $this->ajax_data_return['msg'] = 'Danh sách người dùng Upload bị lỗi';
            return $this->ajax_response();
        }

        $this->load->library('Excel');
        $objReader      = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel    = $objReader->load($fileInput['tmp_name']);
        $objWorksheet   = $objPHPExcel->getActiveSheet();
        $highestRow     = $objWorksheet->getHighestDataRow(); // e.g. 10
        $highestColumn  = $objWorksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

        $dataUser       = Array();
        $dataNotUser    = Array();
        for ($row = 6; $row <= $highestRow; $row++) {
            $userId     = trim($objWorksheet->getCell("B" . $row)->getValue());
            $groupId    = trim($objWorksheet->getCell("C" . $row)->getValue());
            if($userId && $groupId){
                $dataUser[$userId] = $groupId;
            } else {
                $dataNotUser[] = Array('user_id' => $userId, 'group_id' => $groupId);
            }
        }

        $dataReturn = Array();
        if($dataUser){
            ksort($dataUser);
            foreach ($dataUser as $userId => $groupId) {
                $apiReponse = $this->m_voxy_connect->add_user_to_feature_group($userId, $groupId);
                $dataReturn[$userId] = Array(
                    'user_id'   => $userId,
                    'group_id'  => $groupId,
                    'status'    => $apiReponse
                );
            }

            $this->ajax_data_return['status']       = TRUE;
            $this->ajax_data_return['msg']          = 'Cập nhật danh sách Người dùng vào Group thành công !';
            $this->ajax_data_return['data']         = $dataReturn;
            $this->ajax_data_return['data_fail']    = (object) $dataNotUser;
        } else {
            $this->ajax_data_return['msg']      = 'Danh sách người dùng cần cập nhật RỖNG !';
        }

        return $this->ajax_response();
    }

    /**
     * Ham load file excel va chuyen ngay het han cho nguoi dung
     *
     * $fileInput = Array(
     *      'name'      => '',
     *      'type'      => '',
     *      'error'     => '',
     *      'tmp_name'  => '',
     * )
     * @author ThuyVux <thuyvu.hdvn@gmail.com>
     */
    public function voxy_update_expiration_date_users()
    {
        $fileInput = $_FILES['fileinput'];
        if(!$fileInput || !is_array($fileInput)){
            $this->ajax_data_return['msg'] = 'Danh sách người dùng Upload bị lỗi';
            return $this->ajax_response();
        }

        $this->load->library('Excel');
        $objReader      = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel    = $objReader->load($fileInput['tmp_name']);
        $objWorksheet   = $objPHPExcel->getActiveSheet();
        $highestRow     = $objWorksheet->getHighestDataRow(); // e.g. 10
        $highestColumn  = $objWorksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

        $dataUser       = Array();
        $dataNotUser    = Array();
        for ($row = 6; $row <= $highestRow; $row++) {
            $userId             = $objWorksheet->getCell("B" . $row)->getValue();
            $expirationDate     = $objWorksheet->getCell("C" . $row)->getValue();
            if(is_int($expirationDate) || is_float($expirationDate)){
                $expirationDate = date('m/d/Y', PHPExcel_Shared_Date::ExcelToPHP($expirationDate));
            } else if(is_string($expirationDate) && count(explode('/', $expirationDate)) !== 3){
                $dataNotUser[] = Array('user_id' => $userId, 'expiration_date' => $expirationDate);
                continue;
            }
            $expirationDate     = strtotime($expirationDate);
            if($userId && intval($expirationDate)){
                $dataUser[$userId] = $expirationDate;
            } else {
                $dataNotUser[] = Array('user_id' => $userId, 'expiration_date' => $expirationDate);
            }
        }

        $dataReturn = Array();
        if($dataUser){
            ksort($dataUser);
            foreach ($dataUser as $userId => $expirationDate) {
                $apiReponse = $this->m_voxy_connect->update_profile_of_one_user($userId, Array('expiration_date' => $expirationDate));
                $dataReturn[$userId] = Array(
                    'user_id'           => $userId,
                    'expiration_date'   => $expirationDate,
                    'expiration_time'   => date('Y-m-d', $expirationDate),
                    'status'            => $apiReponse
                );
            }

            $this->ajax_data_return['status']       = TRUE;
            $this->ajax_data_return['msg']          = 'Cập nhật ngày hết hạn cho danh sách Người dùng thành công !';
            $this->ajax_data_return['data']         = $dataReturn;
            $this->ajax_data_return['data_fail']    = (object) $dataNotUser;
        } else {
            $this->ajax_data_return['msg']      = 'Danh sách người dùng cần cập nhật RỖNG !';
        }

        return $this->ajax_response();
    }


    // GET DATA - TRUYEN RA VIEW ====================

    /**
     * Lay danh sach Lang ma voxy support
     * @return Array    Mang Lang
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_native_language()
    {
        return $this->m_voxy_connect->_get_list_supported_languages();
    }

    /**
     * Lay danh sach Level tren he thong Voxy
     * @return Array    Mang level
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_level()
    {
        return $this->m_voxy_connect->_get_list_voxy_level();
    }

    /**
     * lay danh sach cac trang thai co the tao reserve_group_sessions
     * @return Array    Mang trang thai
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_can_reserve_group_sessions()
    {
        return $this->m_voxy_connect->_get_list_voxy_can_reserve_group_sessions_status();
    }

    /**
     * Lay danh sach cac Zone he thong Native
     * @return array    Mang thong tin system weight
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_list_system_weight()
    {
        $list_sys_weight = Array();
        $this->load->model('m_sys_weight_config', 'm_sys_weight');
        $_list_sys = $this->m_sys_weight->get_list();
        if($_list_sys) {
            foreach ($_list_sys as $_weight){
                $list_sys_weight[$_weight->id] = Array(
                    'id'        => $_weight->id,
                    'name'      => $_weight->name,
                    'weight'    => $_weight->weight,
                    'connect'   => $_weight->connect,
                );
            }
        }
        ksort($list_sys_weight);
        return $list_sys_weight;
    }

    // ==========================================

    /**
     * Hàm hiển thị bảng quản lý cơ sở dữ liệu ra view tong
     *
     * @param Array $data Biến muốn gửi thêm để <b>hiển thị ra view</b>(dùng khi hàm khác gọi tới hoặc hàm ghi đè gọi tới)
     */
    protected function manager($data = Array())
    {
        $this->session->set_userdata("search_string", "");
        $data["add_link"]           = isset($data["add_link"])              ? $data["add_link"]         : $this->url["add"];
        $data["delete_list_link"]   = isset($data["delete_list_link"])      ? $data["delete_list_link"] : site_url($this->url["delete"]);
        $data["ajax_data_link"]     = isset($data["ajax_data_link"])        ? $data["ajax_data_link"]   : site_url($this->name["class"] . "/ajax_list_data");
        $data["form_url"]           = isset($data["form_url"])              ? $data["form_url"]         : $data["ajax_data_link"];
        $data["form_conds"]         = isset($data["form_conds"])            ? $data["form_conds"]       : array();
        $data["title"] = $title     = "Quản lý " . (isset($data["title"])   ? $data["title"]            : $this->name["object"]);

        $data['loading_gif']            = $this->path_theme_file . 'images/preloaders/loading-spiral.gif';
        $data['USER']                   = $this->USER;
        $data['list_native_language']   = $this->get_native_language();
        $data['list_level']             = $this->get_level();
        $data['list_system_weight']     = $this->get_list_system_weight();
        $data['list_can_reserve_group_sessions'] = $this->get_can_reserve_group_sessions();

        $data['voxy_create_new_user']           = site_url($this->name["class"] . "/voxy_create_new_user");
        $data['voxy_show_user_info']            = site_url($this->name["class"] . "/voxy_show_user_info");
        $data['voxy_update_user_info']          = site_url($this->name["class"] . "/voxy_update_user_info");
        $data['voxy_login_by_user']             = site_url($this->name["class"] . "/voxy_login_by_user");
        $data['voxy_show_all_feature_group']    = site_url($this->name["class"] . "/voxy_show_all_feature_group");
        $data['voxy_show_all_user']             = site_url($this->name["class"] . "/voxy_show_all_user");

        $data['voxy_add_users_to_feature_group']                = site_url($this->name["class"] . "/voxy_add_users_to_feature_group");
        $data['voxy_add_users_to_feature_group_file_temp']      = $this->path_theme_file . 'file_temp/voxy_add_users_to_feature_group_file_temp.xlsx';

        $data['voxy_update_expiration_date_users']              = site_url($this->name["class"] . "/voxy_update_expiration_date_users");
        $data['voxy_update_expiration_date_users_file_temp']    = $this->path_theme_file . 'file_temp/voxy_update_expiration_date_users_file_temp.xlsx';

        $viewFile = $this->name["view"] . '/' . 'manager';
        $content    = $this->load->view($this->path_theme_view . $viewFile, $data, true);
        $head_page  = $this->load->view($this->path_theme_view . 'base_manager/header_manager', $data, true);
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header', $data, true);
        }
        if (file_exists(APPPATH . "views/" . $this->path_theme_view . $this->name["view"] . '/' . 'header_manager.php')) {
            $head_page .= $this->load->view($this->path_theme_view . $this->name["view"] . '/header_manager', $data, true);
        }
        $this->master_page($content, $head_page, $title);
    }

    /**
     * Ham tong hop toan khung toan trang
     *
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
     * Hàm thêm cột vào bản ghi trước khi đưa ra bảng quản lý
     * Mặc định hàm này sẽ thêm 2 cột là cột chứa 3 nút (thêm, sửa xóa) và cột "input"
     * @param Array $record Mảng chứa các bản ghi
     * @return type
     */
    protected function _add_colum_action($record)
    {
        $form = $this->data->get_form();
        $dataReturn = Array();
        $dataReturn["schema"]   = $form["schema"];
        $dataReturn["rule"]     = $form["rule"];
        $dataReturn["colum"]    = $form["field_table"];

        /* Thêm cột action */
        // $dataReturn["colum"]["custom_action"] = "Action";
        /* Thêm cột check */
        // $dataReturn["colum"]["custom_check"] = "<input type='checkbox' class='e_check_all' />";

        $record = $this->_process_data_table($record);
        $dataReturn["record"] = $record;
        return $dataReturn;
    }

    public function __destruct()
    {
        //TODO: code
    }
}