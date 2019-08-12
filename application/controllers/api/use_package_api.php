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
 * Class Use_package_api
 *
 * @author chuvantinh1991@gmail.com
 */
class Use_package_api extends REST_Controller
{
    protected $client_info;
    public function __construct()
    {
        parent::__construct();
        $this->client_info = $this->get_client_info();
        $this->load->library('use_package_lib');
        $this->load->library('make_info_lib');
        $this->load->model('m_voxy_used_package', 'used_package');
    }

    /**
     * Hàm lấy chi tiết 1 gói đang học
     * @author chuvantinh1991@gmail.com
     */
    public function use_package_post()
    {
        $invoice_id = $this->post('invoice_id');
        $status     = $this->post('status');
        $response   = $this->use_package_lib->get_package_user($invoice_id, $status, $this->client_info);
        $this->response($response, 200);
    }

    /**
     * Lấy danh sách gói của học viên
     * @author chuvantinh1991@gmail.com
     */
    public function use_packages_post() {
        $user_id    = $this->post('user_id');
        $status     = $this->post('status');
        $response   = $this->use_package_lib->get_packages_user($user_id, $status, $this->client_info);
        $this->response($response, 200);
    }

}