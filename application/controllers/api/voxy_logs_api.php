<?php

/**
 * Class Voxy_logs_api
 */
class Voxy_logs_api extends REST_Controller
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

    }

    public function invoice_logs_get() {
        $this->load->model('m_voxy_used_package');
        $weight_id = $this->client_info['weight_id'];
        $time_from = $this->get('time_from');
        $data = $this->m_voxy_used_package->select_by_time_range($time_from,$weight_id);
        $response = $this->_success($data, 1);
        $this->response($response, 200);
    }

}