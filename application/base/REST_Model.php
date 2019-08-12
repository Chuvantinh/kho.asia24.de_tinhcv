<?php

class REST_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * begin transaction
     */
    public function trans_begin() {
        $this->db->trans_begin();
    }

    /**
     * commit transaction
     */
    public function trans_commit() {
        $this->db->trans_commit();
    }

    /**
     * rollback transaction
     */
    public function trans_rollback() {
        $this->db->trans_rollback();
    }

    /**
     * get status transaction
     */
    public function trans_status() {
        return $this->db->trans_status();
    }

    /**
     * set error
     * @param type $status_code
     * @param type $msg
     * @return type
     */
    public function _error($status_code = 'FAIL', $msg = 'Fail!', $data = null) {
        $error = new stdClass();
        $error->status = false;
        $error->status_code = $status_code;
        $error->msg = $msg;
        $error->data = $data;
        return $error;
    }

    /**
     * set success
     */
    public function _success($data, $status_code = 'OK', $msg = 'Thao tác thành công!') {
        $success = new stdClass();
        $success->status = true;
        $success->status_code = $status_code;
        $success->msg = $msg;
        $success->data = $data;
        return $success;
    }

}
