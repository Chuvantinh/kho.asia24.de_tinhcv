<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 01/19/2018
 * Time: 11:48
 *
 * @author chuvantinh1991@gmail.com
 */

class Use_package_lib
{
    protected $_ci;

    public function __construct()
    {
        $this->_ci = &get_instance();
        $this->_ci->load->library('ThuyVu_lib');
        $this->_ci->load->library('make_info_lib');
        $this->_ci->load->model('m_pricing', 'pricing');
        $this->_ci->load->model('m_voxy_used_package', 'used_package');
        $this->_ci->load->model('m_voxy_used_package_history', 'used_package_history');
    }

    /**
     * Hàm lấy thông tin 1 gói học của học viên
     * @param int $invoice_id
     * @param null $status
     * @param array $client_info
     * @return stdClass
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_package_user($invoice_id = 0, $status = NULL, $client_info = array())
    {
        if (!$invoice_id || !$client_info) {
            return $this->_error(NULL, 'USE_PACKAGE_LIB_INVALID_DATA', 'Lỗi dữ liệu !');
        }
        $where = array('m.invoice_id' => $invoice_id);
        if ($status) {
            $where['m.status'] = $status;
        }
        $package_user = $this->_ci->used_package->get_one($where, 'object');
        if ($package_user && is_object($package_user)) {
            return $this->_success($package_user, 1);
        } else {
            return $this->_error(NULL, 'USE_PACKAGE_LIB_NO_DATA', 'Không tìm thấy bản ghi phù hơp!');
        }
    }

    /**
     * Lấy tất cả danh sách gói của 1 contact
     * @param int $user_id
     * @param null $status
     * @param array $client_info
     * @return stdClass
     *
     * @author chuvantinh1991@gmail.com
     */
    public function get_packages_user($user_id = 0, $status = NULL, $client_info = array()) {
        $client_weight = isset($client_info['weight']) ? $client_info['weight'] : 0;
        $user_id = $this->_ci->make_info_lib->make_voxy_user_id($user_id, $client_weight);
        if (!$user_id) {
            return $this->_error(NULL, 'USE_PACKAGE_LIB_INVALID_DATA', 'Lỗi dữ liệu !');
        }
        $where = array('vu.user_id' => $user_id);
        if ($status) {
            $where['m.status'] = $status;
        }
        $package_user = $this->_ci->used_package->get_list($where);
        if ($package_user) {
            return $this->_success($package_user, count($package_user));
        } else {
            return $this->_error(NULL, 'USE_PACKAGE_LIB_NO_DATA', 'Không tìm thấy bản ghi phù hơp!');
        }
    }

    /**
     * Ham kich hoat goi hoc phi
     * @param int $invoice_id
     * @return stdClass
     *
     * @author chuvantinh1991@gmail.com
     */
    public function active_package($invoice_id = 0) {
        $package_info   = $this->_ci->used_package->get_one(array('m.invoice_id' => $invoice_id), 'object');

        if (!$package_info) {
            return $this->_error(array('invoice_id' => $invoice_id),'USE_PACKAGE_LIB_PACKAGE_NOT_FOUND', 'Không tồn tại gói yêu cầu!');
        }
        if (isset($package_info->start_time) && $package_info->start_time > 0) {
            return $this->_error(NULL, 'USE_PACKAGE_LIB_PACKAGE_ACTIVED', 'Gói đã được kích hoạt!');
        }
        if (isset($package_info->end_time) && $package_info->end_time < time()) {
            return $this->_error(NULL, 'USE_PACKAGE_LIB_PACKAGE_EXPIRED', 'Gói đã hết hạn!');
        }
        // can kiem tra them da co goi nao dang kich hoat hay khong
        //$user_id = $package_info->user_id;

        $this->_ci->pricing->trans_begin();
        $start_time     = strtotime(date('Y-m-d', time()));
        $use_time       = $package_info->use_time;
        if($use_time == -1){
            $end_time = NULL;
        } else {
            $end_time = $start_time + $use_time*86400;
        }
        $time_created = time();

        $actived_id = $this->_update_status_used_product($package_info, 'ACTIVED', $start_time, $end_time, 'Kích hoạt gói học phí !', $time_created);
        if(!$actived_id) {
            $this->_ci->pricing->trans_rollback();
            return $this->_error(NULL,'USE_PACKAGE_LIB_ACTIVE_PACKAGE_FAIL', 'Kích hoạt gói học phí thất bại !');

        }

        $this->_ci->pricing->trans_commit();
        return $this->_success(array('invoice_id' => $invoice_id), 1, 'OK', 'Kích hoạt gói thành công!');
    }

    /**
     * Ham thay doi trang thai goi hoc phi
     * @param $package_info
     * @param $status
     * @param $start_time
     * @param $end_time
     * @param $description
     * @param $time_created
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function _update_status_used_product($package_info, $status, $start_time, $end_time, $description, $time_created)
    {
        if(!$time_created){
            $time_created = time();
        }
        $status_old     = $package_info->status;
        $status_code    = $status . $time_created;
        $invoice_id     = $package_info->invoice_id;
        $used_product_data = array(
            'status'        => $status,
            'status_code'   => $status_code
        );
        if ($start_time) {
            $used_product_data['start_time'] = $start_time;
        }
        if ($end_time) {
            $used_product_data['end_time'] = $end_time;
        }
        $updated_id = $this->_ci->used_package->update(array('invoice_id' => $invoice_id), $used_product_data);

        if($updated_id){
            $data_add_used_package_history = array(
                'used_package_id'   => $package_info->id,
                'status_old'        => $status_old,
                'status_new'        => $status,
                'status_code'       => $status_code,
                'description'       => $description,
            );
            if($end_time){
                $data_add_used_package_history['end_time_old'] = $package_info->end_time;
                $data_add_used_package_history['end_time_new'] = $end_time;
            }
            $actived_history_id = $this->_ci->used_package_history->add($data_add_used_package_history);

            return $invoice_id;
        } else {
            return FALSE;
        }
    }

    /**
     * Tao temp response success de gui ve cho client
     * @param null          $data               Du lieu can tra ve
     * @param int           $total              Tong so du lieu
     * @param string        $status_code        ma code trang thai
     * @param string        $msg                mess khi tra ve
     * @return stdClass     doi tuong can tra ve
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _success($data = NULL, $total = -1, $status_code = 'OK', $msg = 'Thao tác thành công !') {
        $success                = new stdClass();
        $success->status        = TRUE;
        $success->status_code   = $status_code; // OK
        $success->msg           = $msg;
        $success->total         = $total;
        $success->data          = $data;

        return $success;
    }

    /**
     * Tao temp response error de gui ve cho client
     * @param null $data
     * @param string $status_code
     * @param string $msg
     * @return stdClass
     *
     * @author chuvantinh1991@gmail.com
     */
    public function _error($data = NULL, $status_code = 'FAIL', $msg = 'Thao tác thất bại !') {
        $error              = new stdClass();
        $error->status      = FALSE;
        $error->data        = $data;
        $error->status_code = $status_code; // FAIL/MAINTENANCE
        $error->msg         = $msg;

        return $error;
    }
}