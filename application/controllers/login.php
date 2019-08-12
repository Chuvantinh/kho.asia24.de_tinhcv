<?php

if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/**
 * Class Login
 *
 * @author chuvantinh1991@gmail.com
 */
class Login extends home_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model("M_login", "m_login");

        $data                   = Array();
        $data["login_url"]      = site_url("login/check");
        $data["recover_url"]    = site_url("login/reset_password");
        $data["img_header2"]     = $this->path_theme_file . 'images/patterns/logo.png';
        $data["form"]           = $this->m_login->get_form();
        $content                = $this->load->view($this->path_theme_view . "login/content", $data, true);
        $header_page            = $this->load->view($this->path_theme_view . "login/header", $data, true);
        $title                  = "[Login Manager Service]";
        $description            = NULL;
        $keywords               = NULL;
        $canonical              = NULL;
        $this->master_page_blank($content, $header_page, $title, $description, $keywords, $canonical);
    }

    /**
     * Ham kiem tra dang nhap
     *
     * @author chuvantinh1991@gmail.com
     */
    public function check()
    {
        if ($this->input->is_ajax_request() && $this->input->post()) {
            $this->load->model("m_admin_accounts", "account");

            $dataReturn = Array(
                'state'     => 0,
                'msg'       => 'Tên đăng nhập hoặc mật khẩu không chính xác !'
            );

            $email              = $this->input->post("admin_email");
            $pass               = $this->gen_string_password($this->input->post("admin_password"));
            $admin_login_info   = $this->account->check_login($email, $pass);
            if ($admin_login_info && is_object($admin_login_info)) {
                $admin_login_info->permission_data  = $this->get_user_permission_data($admin_login_info->id);
                $admin_login_info->menus_data       = $this->get_user_menus_data($admin_login_info->permission_data);
                $this->session->set_userdata("USER", $admin_login_info);

                $dataReturn["state"]    = 1;
                $dataReturn["msg"]      = "Đăng nhập thành công !";
                $dataReturn["redirect"] = site_url();
            }
            echo json_encode($dataReturn);
        } else {
            redirect();
        }
    }

    /**
     * ghi de ham require_login
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function require_login()
    {
        return false;
    }

    /**
     * Ghi de ham check_permission, mac dinh tra ve true de co quyen truy cap chuc nang
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function check_permission()
    {
        return true;
    }

    /**
     * Logout, xoa sesson va quay ve trang login
     *
     * @author chuvantinh1991@gmail.com
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect("login");
    }

}