<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 01/19/2018
 * Time: 17:21
 *
 * @author chuvantinh1991@gmail.com
 */

/**
 * Class Pricing_api
 *
 * @author chuvantinh1991@gmail.com
 */
class Pricing_api extends REST_Controller
{
    protected $client_info;
    public function __construct()
    {
        parent::__construct();
        $this->client_info = $this->get_client_info();
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->load->library('pricing_lib');
    }

    /**
     * Mua goi hoc cho nhieu doi tuong cung 1 luc
     *
     * @param
     *   $param_pricing = array(
     *      'user_info'    => array(
     *           'user_id'       => $contact_info->student_id,   // Bat buoc
     *           'contact_id'    => $contact_id,                 // Bat buoc
     *           'user_email'    => $contact_info->user_name,    // Bat buoc
     *           'user_phone'    => $contact_info->phone,
     *           'full_name'     => $contact_info->full_name,
     *           'level_study'   => $contact_info->level_study,
     *           'lang'          => $contact_info->lang,
     *           ),
     *       'buyer_info'   => array(
     *           'buyer_id'      => $this->session->userdata('id'),
     *           'buyer_email'   => $this->session->userdata("userName"),
     *           'buyer_name'    => $this->session->userdata("userDisplayName")
     *           ),
     *       'invoice_info' => array(
     *           'package_code'          => $data['package_code'],
     *           'package_description'   => 'Advisor: '.$this->session->userdata("userDisplayName").' - User: '.$contact_id.'_'.$contact_info->full_name.' - Reason: ' . $data['description'],
     *           //'package_actual_price'   => trim($data['package_actual_price']),
     *           'native_package_id'     => $contact_info->product_id,
     *           'native_package_parent' => $contact_info->package_parent,
     *           'native_package_type'   => $contact_info->package_type,
     *           'native_package_code'   => $contact_info->package_code,
     *           ),
     *       'active_now' => false // kich hoat luon hay khong
     *   );
     *
     * @author chuvantinh1991@gmail.com
     */
    public function pricing_post(){
        $data = $this->post();
        if (is_object($data)) {
            $data = (array) $data;
        }
        if (!is_array($data)) {
            $response = $this->_error($data,'VOXY_INVALID_DATA', 'Dữ liệu không chính xác!');
            $this->response($response, 200);
        }

        $response_data_arr = array();
        foreach ($data as $key => $pricingData) {
            if (is_object($pricingData)) {
                $pricingData = (array) $pricingData;
            }
            if (!is_array($pricingData)) {
                $response = $this->_error($pricingData, 'VOXY_INVALID_DATA', 'Dữ liệu không chính xác!');
                $this->response($response, 200);
            }
            $user_info      = isset($pricingData['user_info'])      ? $pricingData['user_info']     : array();
            $buyer_info     = isset($pricingData['buyer_info'])     ? $pricingData['buyer_info']    : array();
            $invoice_info   = isset($pricingData['invoice_info'])   ? $pricingData['invoice_info']  : array();
            $active_now     = isset($pricingData['active_now'])     ? $pricingData['active_now']    : false; // mac dinh ko kich hoat
            $response_tmp   = $this->pricing_lib->pricing($user_info, $buyer_info, $invoice_info, $this->client_info, $active_now);
            $response_data_arr[$key] = $response_tmp;
        }
        $response = $this->_success($response_data_arr, 'DONE_ALL', 'Yêu cầu mua hàng thành công!');
        $this->response($response, 200);
    }
}