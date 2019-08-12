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
 * Class Package_api
 *
 * @author chuvantinh1991@gmail.com
 */
class Package_api extends REST_Controller
{
    protected $client_info;
    public function __construct()
    {
        parent::__construct();
        $this->client_info = $this->get_client_info();
        $this->load->model('m_voxy_package', 'package');
    }

    public function list_packages_get()
    {
        $cat_id   = $this->get('cat_id');
        $total      = 0; // tong so ban ghi, bo qua limit
        $where      = array();
        if($cat_id) {
            $where = array('cat.cat_id' => $cat_id);
        }
        $list_package = $this->package->get_list($where, 0, 0, NULL, $total);

        $response = $this->_success($list_package, $total);
        $this->response($response, 200);
    }

    public function package_info_get()
    {
        $cat_id   = $this->get('pack_code');
        $total      = 0; // tong so ban ghi, bo qua limit
        if($cat_id) {
            $list_package = $this->package->get_one(array('m.pack_code' => $cat_id));
            if($list_package){$total = 1;}
        } else {
            $list_package = array();
        }

        $response = $this->_success($list_package, $total);
        $this->response($response, 200);
    }

}