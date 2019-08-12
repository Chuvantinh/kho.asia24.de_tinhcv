<?php
/**
 * Created by PhpStorm.
 * User: vuvan
 * Date: 06/15/2018
 * Time: 15:25
 *
 * @author chuvantinh1991@gmail.com
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Phan quyen cho cac role
 * Class Admin_role_function
 *
 * @author chuvantinh1991@gmail.com
 */
class Admin_role_function extends manager_base
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('m_admin_functions', 'm_admin_functions');
    }

    /**
     * Setting Controller
     *
     * @author chuvantinh1991@gmail.com
     */
    public function setting_class()
    {
        $this->name = Array(
            "class"     => "admin_role_function",
            "view"      => "admin_role_function",
            "model"     => "m_admin_role_function",
            "object"    => "Admin Role Function"
        );
    }

    /**
     * @param int $role_id
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    public function index($role_id = 0)
    {
        $data       = Array();
        $role_id    = intval($role_id);
        if (!$role_id) {
            $redirect = site_url('admin_roles');
            echo '<h2>ID Nhóm quyền người dùng(Role) không hợp lệ!</h2>';
            echo "Vui lòng <a href='" . $redirect . "'>Click vào đây</a> để lựa chọn Nhóm quyền người dùng hợp lệ.";
            return FALSE;
        }

        // lấy danh sách các function mà user sẽ cấp cho role
        $list_function_by_role_id = $this->m_admin_functions->get_list_action_by_role_id($role_id);

        // nếu role_id == 1 thì lấy tất cả các action
        $list_all_function = Array();
        if ($this->USER->role_id == 1) {
            if($role_id == 1){
                $list_all_function = $this->m_admin_functions->get_list_action_by_role_admin();
            } else {
                $list_all_function = $this->m_admin_functions->get_full_list_action();
            }
        }

        $list_function_exist_role = Array();
        foreach ($list_function_by_role_id as $key => $value) {
            $list_function_exist_role[] = $value->id;
        }

        $list_role_function = Array();
        foreach ($list_all_function as $item) {
            $temp = Array(
                'id'			=> $item->id,
                'controller'	=> $item->controller,
                'action'		=> $item->action,
                'description'	=> $item->description,
                'checked'		=> in_array($item->id, $list_function_exist_role) ? true : false,
            );

            $list_role_function[$item->controller][$item->action] = $temp;
        }

        $title = "Quản lý " . (isset($data["title"]) ? $data["title"] : $this->name["object"]);
        $data['USER']                           = $this->USER;
        $data['role_id']                        = $role_id;
        $data['list_role_function']             = $list_role_function;
        $data['title']                          = $title;
        $data['ajax_update_role_controller']    = site_url($this->name["class"] . "/update_role_controller");
        $data['ajax_update_role_function']      = site_url($this->name["class"] . "/update_role_function");

        $viewFile   = $this->name["view"] . '/' . 'manager';
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
     * Ham xu ly them va xoa tung quyen(tung mdetho trong controller) cho nhom nguoi dung
     * @return bool     TRUE- thanh cong, FALSE-that bai
     *
     * @author chuvantinh1991@gmail.com
     */
    public function update_role_function()
    {
        $data_input     = $this->input->post();
        $function_id    = $data_input['function_id'];
        $role_id        = $data_input['role_id'];
        $checked        = $data_input['checked'];

        $role_id        = intval($role_id);
        $function_id    = intval($function_id);
        if (!$role_id || !$function_id || !$this->USER->id) {
            return $this->response_error();
        }

        if($checked === 'true'){
            try {
                $this->db->trans_begin();
                $exist_role = $this->data->get_list_exist_role_function_id($role_id, Array($function_id));
                // neu ton tai ban ghi, chung to da cap quyen, nen khong can lam gi
                if($exist_role && is_array($exist_role)){
                    return $this->response_susscess();
                // neu khong ton tai ban ghi thi se them moi quyen
                } else {
                    $add_status = $this->data->add(Array(
                        'role_id'       => $role_id,
                        'function_id'   => $function_id
                    ));

                    if ($this->db->trans_status() === FALSE || !$add_status) {
                        $this->db->trans_rollback();
                        return $this->response_error();
                    } else {
                        $this->db->trans_commit();
                        return $this->response_susscess();
                    }
                }
            } catch (Throwable $ex){
                $this->db->trans_rollback();
                return $this->response_error();
            }
        } else if($checked === 'false'){
            try{
                $this->db->trans_begin();
                $exist_role = $this->data->get_list_exist_role_function_id($role_id, Array($function_id));
                // neu ton tai ban ghi thi xoa quyen da cap
                if($exist_role && is_array($exist_role)){
                    $del_status = $this->data->delete_by_custom(Array(
                        'role_id'       => $role_id,
                        'function_id'   => $function_id,
                        'editable'      => 1
                    ));

                    if ($this->db->trans_status() === FALSE || !$del_status) {
                        $this->db->trans_rollback();
                        return $this->response_error();
                    } else {
                        $this->db->trans_commit();
                        return $this->response_susscess();
                    }
                // neu khong ton tai ban ghi thi khong can lam gi
                } else {
                    return $this->response_susscess();
                }
            } catch (Throwable $ex){
                $this->db->trans_rollback();
                return $this->response_error();
            }
        } else {
            echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><strong><i class="icon24 i-close-4"></i>Cấp quyền gặp lỗi. Vui lòng thử lại sau!</div>';
            return FALSE;
        }
    }

    /**
     * Ham xu ly them va xoa toan bo quyen nhom nguoi dung voi 1 controller
     * @return bool     TRUE- thanh cong, FALSE-that bai
     *
     * @author chuvantinh1991@gmail.com
     */
    public function update_role_controller()
    {
        $data_input = $this->input->post();
        $controller = $data_input['controller'];
        $role_id    = $data_input['role_id'];
        $checked    = $data_input['checked'];

        if ($controller == '00') {
            $controller = '*';
        }
        $role_id    = intval($role_id);
        if (!$role_id || !$controller || !$this->USER->id) {
            return $this->response_error();
        }

        // lấy danh sách id function bởi controller
        $list_all_function_id       = $this->get_list_function_id_by_controller($controller);
        $list_exists_function_id    = $this->get_list_exist_role_function_id($role_id, $list_all_function_id);

        if($checked === "true") {
            // them role moi
            try {
                $this->db->trans_begin();
                foreach ($list_all_function_id as $one_id){
                    if(!in_array($one_id, $list_exists_function_id)){
                        $this->data->add(Array(
                            'role_id'       => $role_id,
                            'function_id'   => $one_id
                        ));
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return $this->response_error();
                } else {
                    $this->db->trans_commit();
                    return $this->response_susscess();
                }
            } catch (Throwable $ex) {
                $this->db->trans_rollback();
                return $this->response_error();
            }
        // xoa bo role cu
        } else if($checked === "false") {
            try {
                $this->db->trans_begin();
                foreach ($list_exists_function_id as $one_id) {
                    $this->data->delete_by_custom(Array(
                        'role_id'       => $role_id,
                        'function_id'   => $one_id,
                        'editable'      => 1
                    ));
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return $this->response_error();
                } else {
                    $this->db->trans_commit();
                    return $this->response_susscess();
                }
            } catch (Throwable $ex) {
                $this->db->trans_rollback();
                return $this->response_error();
            }
        // loi thieu tham so
        } else {
            echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><strong><i class="icon24 i-close-4"></i>Cấp quyền gặp lỗi. Vui lòng thử lại sau!</div>';
            return FALSE;
        }
    }

    /**
     * Cap quyen bi loi
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function response_error()
    {
        echo FALSE;
        return FALSE;
    }

    /**
     * Cap quyen thanh cong
     * @return bool
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function response_susscess()
    {
        echo TRUE;
        return TRUE;
    }

    /**
     * Ham tim kiem toan bo ID Function thuoc 1 Controller
     *
     * @param string $controller    Ten controller can tim kiem
     * @return array                Mang ID Function thuoc Controller
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_list_function_id_by_controller($controller = '')
    {
        $list_all_function_id   = $this->m_admin_functions->get_list_id_action_by_controller($controller);
        $list_function_id       = Array();
        foreach ($list_all_function_id as $key => $value) {
            $list_function_id[] = $value->id;
        }
        return $list_function_id;
    }

    /**
     * lay danh sach role_function_id da cap quyen cho nhom quyen nguoi dung
     *
     * @param int $role_id              ID Role nguoi dung
     * @param array $list_function_id   Mang ID Function can kiem tra
     * @return array                    Mang ID Function ma da cap quyen cho nhom quyen nguoi dung
     *
     * @author chuvantinh1991@gmail.com
     */
    protected function get_list_exist_role_function_id($role_id = 0, $list_function_id = Array())
    {
        $list_exists_role_function = $this->data->get_list_exist_role_function_id($role_id, $list_function_id);
        $list_exists_function_id   = Array();
        foreach ($list_exists_role_function as $value){
            $list_exists_function_id[$value->function_id] = $value->function_id;
        }
        return $list_exists_function_id;
    }
}
