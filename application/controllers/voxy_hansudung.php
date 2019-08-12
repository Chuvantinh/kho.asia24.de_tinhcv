<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Voxy_category
 *
 * @author chuvantinh1991@gmail.com
 */
class Voxy_hansudung extends manager_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_class()
    {
        $this->name = Array(
            "class" => "voxy_hansudung",
            "view" => "voxy_hansudung",
            "model" => "m_voxy_hansudung",
            "object" => "Hạn Sử Dụng Sản phẩm còn hạn 3 tháng"
        );
    }

    public function index()
    {
        $this->manager();
    }

    public function manager($data = array())
    {
        $json_conds = $this->session->userdata('arr_package_search');
        $json_conds = json_decode($json_conds, TRUE);

        $data['form_conds'] = (array)$json_conds;
        $this->load->model('m_voxy_category');
        $data['category'] = $this->m_voxy_category->get_category();

        //$data['list_status'] = $this->data->arr_status;

        parent::manager($data);
    }

    protected function get_search_condition($data = array())
    {
        if (!count($data)) {
            $data = $this->input->get();
        }

        $where_data = array();
        $like_data = array();
        $list_field = array('cat_id', 'status');
        foreach ($list_field as $key => $value) {
            if (isset($data[$value])) {
                $data[$value] = trim($data[$value]);
                switch ($value) {
                    case 'cat_id':
                        if ($data['cat_id'] != '') {
                            $where_data['m.cat_id'] = $data['cat_id'];
                        }
                        break;
                    case 'status':
                        if ($data['status'] != '') {
                            $where_data['m.status'] = $data['status'];
                        }
                        break;
                }
            }
        }

        $data_return = array(
            'custom_where' => $where_data,
            'custom_like' => $like_data
        );
        $this->session->set_userdata('arr_package_search', json_encode($data_return));
        return $data_return;
    }

    public function ajax_list_data($data = Array())
    {
        $data_get = $this->input->get();
        if ($data_get && is_array($data_get)) {
            $this->data->custom_conds = $this->get_search_condition($data_get);
        } else {
            $json_conds = $this->session->userdata('arr_package_search');
            $json_conds = json_decode($json_conds, TRUE);
            if (isset($json_conds['custom_where']) && count($json_conds['custom_where']) == 0 && isset($json_conds['custom_like']) && count($json_conds['custom_like']) == 0) {
                $this->data->custom_conds = $this->get_search_condition();
            } else {
                $this->data->custom_conds = $json_conds;
            }
        }
        //$this->data->custom_conds = $json_conds;
        parent::ajax_list_data($data);
    }

    protected function _process_data_table($record)
    {
        if (!$record) {
            return array();
        }
        $key_table = $this->data->get_key_name();
        $this->load->model('m_voxy_category', 'category');
        /* Tùy biến dữ liệu các cột */
        if (is_array($record)) {
            foreach ($record as $key => $valueRecord) {
                $record[$key] = $this->_process_data_table($record[$key]);
            }
        } else {
            $record->custom_action = '<div class="action"><a class="detail e_ajax_link icon16 i-eye-3 " per="1" href="' . site_url($this->url["view"] . $record->$key_table) . '" title="Xem"></a>';
            $record->custom_check = "<input type='checkbox' name='_e_check_all' data-id='" . $record->$key_table . "' />";

        }
        return $record;
    }
}
?>