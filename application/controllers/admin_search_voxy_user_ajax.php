<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 06/25/2018
 * Time: 10:13
 *
 * @author chuvantinh1991@gmail.com
 */

class Admin_search_voxy_user_ajax extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function search_ajax_voxy_user()
    {
        $this->load->model('m_admin_search_voxy_user_ajax', 'm_search_voxy_user_ajax');

        $key_search = $this->input->get('term'); //từ khóa tìm kiếm
        $list_user  = $this->m_search_voxy_user_ajax->get_voxy_user($key_search);

        $data_json = Array();
        foreach ($list_user as $one_user) {
            $item           = Array();
            $item['id']     = $one_user->native_id;
            $item['label']  = $one_user->domain_name.' - LMS: '.$one_user->native_id.' - Voxy: '.$one_user->voxy_id.' - Contact: '.$one_user->native_contact_id.' - Email: '.preg_replace('/\s+/', ' ', trim($one_user->voxy_email));
            $item['value']  = $one_user->native_id;//.' - Voxy: '.$one_user->voxy_id.' - Contact: '.$one_user->native_contact_id.' - Email: '.preg_replace('/\s+/', ' ', trim($one_user->voxy_email));
            $data_json[] = $item;
        }
        $output = json_encode($data_json);
        die($output);
    }

}