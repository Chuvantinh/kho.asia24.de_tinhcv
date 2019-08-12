<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Trang chu he thong
 * Class Admin_dashboard
 *
 * @author chuvantinh1991@gmail.com
 */
class Admin_dashboard extends home_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function ajax_baocao_top10_banchay(){
        $export_select = $this->input->post('export_select');
        $export_sorting = $this->input->post('export_sorting');

        //$data = $this->baocao_top10_banchay($select);
        $this->session->set_userdata(array(
            "selected_banchay" => $export_select
        ));
        $this->session->set_userdata(array(
            "selected_banchay_sorting" => $export_sorting
        ));

        if($export_select || $export_sorting){
            $data_return["state"] = 1;
            $data_return["msg"] = "True";
            $data_return["html"] = "";
            echo json_encode($data_return);
            return TRUE;
        }else{
            $data_return["state"] = 0;
            $data_return["msg"] = "False";
            $data_return["html"] = "";
            echo json_encode($data_return);
            return FALSE;
        }

//        if($data != false){
//            //$viewFile = '/admin_dashboard/top10_import';
//            //$content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
//            $data_return["state"] = 1;
//            $data_return["msg"] = "Ok";
//
//            if ($data) {
//                $data_top10 = array();
//                $arr_top10 = array();
//                $all_product = 0;
//                $gdp = 0;
//                foreach ($data as $item) {
//                    $all_product += $item['quantity'];
//                    $gdp = round(($item['quantity'] / $all_product) * 100, 2);
//                    $arr_top10['label'] = $item['title'] . "(" . $item['variant_title'] . ")";
//                    $arr_top10['y'] = $item['quantity'];
//                    $arr_top10['gdp'] = $gdp;
//                    $arr_top10['url'] = "germany.png";
//                    $arr_top10['dv'] = $item['variant_title'];
//                    $data_top10[] = $arr_top10;
//                }
//            } else {
//                $data_top10 = array();
//            }
//
//            $data_return["html"] = $data_top10;
//            echo json_encode($data_return);
//            return TRUE;
//        }else{
//            $data_return["state"] = 0;
//            $data_return["msg"] = "False";
//            $data_return["html"] = "";
//            echo json_encode($data_return);
//            return FALSE;
//        }
    }

    public function baocao_top10_banchay($select, $select_banchay_sorting){
        $this->load->model('m_voxy_package_xuathang');

        switch($select){
            case 0:{//homnay
                $date = date("Y-m-d");
                $date_end = date("Y-m-d");
                break;
            }
            case 1:{//homqua
                $hom_qua = strtotime('-1 day',strtotime(date("Y-m-d")));

                $date_end  = strftime("%Y-%m-%d", $hom_qua);
                $date = $date_end;
                break;
            }
            case 2:{//tuannay
                $today = date("Y-m-d");
                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 week");
                $date_end = strftime("%Y-%m-%d", $week);
                $date = date("Y-m-d");
                break;
            }
            case 3:{//thang nay
                $date = date("Y-m-d");
                $date_end = date("Y-m-01");
                break;
            }
            case 4:{//thangtruoc
                $today = date("Y-m-d");
                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 month");
                $date_end = strftime("%Y-%m-%d", $week);
                $date = date("Y-m-d");
                break;
            }
            default:{
                $date = date("Y-m-d");
                $date_end = date("Y-m-d");
            }
        }

        if($date != ""){//hien thi ngay hom nay
            $laixe = "";
            $_laixe = "";

            $_all_products = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_tong($date_end,$date, $laixe);//bang infor xuathang
            $_all_products_le = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_le($date_end, $date, $laixe); //bang infor xuathang le
            $_all_products_xuattaikho = $this->m_voxy_package_xuathang->xuathang_baocao_xuathang_taikho($date_end,$date,$_laixe); //bang transfer_outkho//laixe theo id

            $all_products['export2'] = array_merge($_all_products['export2'], $_all_products_le['export2'],$_all_products_xuattaikho['export2']);

            $export2 = array();
            $chiso_remove = array();
            //sum inventory of same product
            foreach ($all_products['export2'] as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                foreach ($all_products['export2'] as $key2 => $item2) {
                    if ($key2 > $key) {
                        if(isset($item['variant_id']) && isset($item2['variant_id'])){
                            if ( $item['variant_id'] == $item2['variant_id']) {
                                if(isset($item['quantity']) && isset($item2['quantity'])){
                                    $item['quantity'] = (int)$item['quantity'] + (int)$item2['quantity'];
                                    $chiso_remove[$key2] = $key2;//index of same product and then remove it
                                }
                            }
                        }
                    }
                }
                $export2[] = $item;
            }

            //remove nhung thang giong di
            foreach ($export2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

            //var_dump($export2);die;

            //sap xep
            if($select_banchay_sorting == "quantity"){
                //sap xep theo sl xuat
                $wek = array();
                foreach ($export2 as $key => $row) {
                    if (!isset($row['quantity'])) {
                        $row['quantity'] = "";
                    }
                    $wek[$key] = $row['quantity'];
                }
                // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
                array_multisort($wek, SORT_DESC, $export2);
            }else{
                //sap xep theo sl xuat
                $wek = array();
                foreach ($export2 as $key => $row) {
                    if (!isset($row['thanhtien'])) {
                        $row['thanhtien'] = "";
                    }
                    $wek[$key] = $row['thanhtien'];
                }
                // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
                array_multisort($wek, SORT_DESC, $export2);
            }

        }
        $export2 = array_slice($export2, 0, 9);
        //var_dump($export2);die;
        return $export2;
    }

    public function ajax_baocao_top10_nhaphang(){
        $import_select = $this->input->post('import_select');
        $import_sorting = $this->input->post('import_sorting');
        //$data = $this->baocao_top10_nhaphang($select);
        $this->session->set_userdata(array(
            "selected_nhaphang" => $import_select
        ));

        $this->session->set_userdata(array(
            "selected_nhaphang_sorting" => $import_sorting
        ));

        //var_dump($this->session->userdata('selected_nhaphang'));die;
        if($import_sorting || $import_select){
            $data_return["state"] = 1;
            $data_return["msg"] = "True";
            $data_return["html"] = "";
            echo json_encode($data_return);
            return TRUE;
        }else{
            $data_return["state"] = 0;
            $data_return["msg"] = "False";
            $data_return["html"] = "";
            echo json_encode($data_return);
            return FALSE;
        }

//        if($data != false){
//            //$viewFile = '/admin_dashboard/top10_import';
//            //$content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
//            $data_return["state"] = 1;
//            $data_return["msg"] = "Ok";
//
//            if ($data) {
//                $data_top10_import = array();
//                $arr_top10 = array();
//                $all_product = 0;
//                foreach ($data as $item) {
//                    $all_product += $item['sl_nhap'];
//                    $gdp = round(($item['sl_nhap'] / $all_product) * 100, 2);
//                    $arr_top10['label'] = $item['title'] . "(" . $item['variant_title'] . ")";
//                    $arr_top10['y'] = $item['sl_nhap'];
//                    $arr_top10['gdp'] = $gdp;
//                    $arr_top10['url'] = "germany.png";
//                    $arr_top10['dv'] = $item['variant_title'];
//                    $data_top10_import[] = $arr_top10;
//                }
//            } else {
//                $data_top10_import = array();
//            }
//
//            $data_return["html"] = $data_top10_import;
//            echo json_encode($data_return);
//            return TRUE;
//        }else{
//            $data_return["state"] = 0;
//            $data_return["msg"] = "False";
//            $data_return["html"] = "";
//            echo json_encode($data_return);
//            return FALSE;
//        }
    }


    public function baocao_top10_nhaphang($select, $selected_import_sorting){
        $this->load->model('m_voxy_transfer');
        switch($select){
            case 0:{//homnay
                $date = date("Y-m-d");
                $date_end = date("Y-m-d");
                break;
            }
            case 1:{//homqua
                $hom_qua = strtotime('-1 day',strtotime(date("Y-m-d")));

                $date_end  = strftime("%Y-%m-%d", $hom_qua);
                $date = $date_end;
                break;
            }
            case 2:{//tuannay
                $today = date("Y-m-d");
                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 week");
                $date_end = strftime("%Y-%m-%d", $week);
                $date = date("Y-m-d");
                break;
            }
            case 3:{//thang nay
                $date = date("Y-m-d");
                $date_end = date("Y-m-01");
                break;
            }
            case 4:{//thangtruoc
                $today = date("Y-m-d");
                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 month");
                $date_end = strftime("%Y-%m-%d", $week);
                $date = date("Y-m-d");
                break;
            }
            default:{
                $date = date("Y-m-d");
                $date_end = date("Y-m-d");
            }
        }

        if($date != "") {//hien thi ngay hom nay
            $data = $this->m_voxy_transfer->get_infor_theo_ngay("",$date,$date_end,"");
            if($data == false){
                return false;
            }
            $export = array();//xu ly all product sang array
            if($data){
                foreach ($data as $item){

                    $_item = get_object_vars(json_decode($item['product_variants']));
                    foreach ($_item as $item_con){
                        $item_con->quantity = $item_con->sl_nhap;
                        $export[] =  get_object_vars($item_con);
                    }
                }
            }

            $export2 = array();
            $chiso_remove = array();
            foreach ($export as $key => $item) {
                // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
                foreach ($export as $key2 => $item2) {
                    if ($key2 > $key) {
//                        && $item['gianhapnew'] == $item2['gianhapnew']
                        if ($item['variant_id'] == $item2['variant_id'] ) {
                            if (!isset($item['sl_nhap'])) {
                                $item['sl_nhap'] = 0;
                            }
                            $item['sl_nhap'] = (float)$item['sl_nhap'] + (float)$item2['sl_nhap'];
                            $item['thanhtien'] = (float)$item['thanhtien'] + (float)$item2['thanhtien'];
                            $chiso_remove[$key2] = $key2;//index of same product and then remove it
                        }
                    }
                }
                $export2[] = $item;
            }

            //remove nhung thang giong di
            foreach ($export2 as $key => $item) {
                foreach ($chiso_remove as $key_reomove => $item_remove) {
                    unset($export2[$item_remove]);
                    unset($chiso_remove[$key_reomove]);
                }
            }

            //sap xep
            if($selected_import_sorting == "quantity"){
                //sap xep theo sl xuat
                $wek = array();
                foreach ($export2 as $key => $row) {
                    if (!isset($row['quantity'])) {
                        $row['quantity'] = "";
                    }
                    $wek[$key] = $row['quantity'];
                }
                // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
                array_multisort($wek, SORT_DESC, $export2);
                $data = array_slice($export2 ,0,9);
            }else{
                //sap xep theo doanh so
                $wek = array();
                foreach ($export2 as $key => $row) {
                    if (!isset($row['thanhtien'])) {
                        $row['thanhtien'] = "";
                    }
                    $wek[$key] = $row['thanhtien'];
                }
                // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
                array_multisort($wek, SORT_DESC, $export2);
                $data = array_slice($export2 ,0,9);
            }

        }

        return $data;
    }

    public function ajax_baocao_doanhthu_laixe(){
        $select = $this->input->post('select');
        //$data = $this->baocao_doanhthu_laixe($select);

        $this->session->set_userdata(array(
            "selected_laixe" => $select
        ));
        //var_dump($this->session->userdata("selected_laixe"));die;
        if($select){
            $data_return["state"] = 1;
            $data_return["msg"] = "True";
            $data_return["html"] = "";
            echo json_encode($data_return);
            return TRUE;
        }else{
            $data_return["state"] = 0;
            $data_return["msg"] = "False";
            $data_return["html"] = "";
            echo json_encode($data_return);
            return FALSE;
        }


//        switch($select){
//            case 0:{//homnay
//                $selected = 0;
//                break;
//            }
//            case 1:{//homqua
//                $hom_qua = strtotime('-1 day',strtotime(date("Y-m-d")));
//                $date_end  = strftime("%Y-%m-%d", $hom_qua);
//                $date = $date_end;
//                break;
//            }
//            case 2:{//tuannay
//                $today = date("Y-m-d");
//                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 week");
//                $date_end = strftime("%Y-%m-%d", $week);
//                $date = date("Y-m-d");
//                break;
//            }
//            case 3:{//thang nay
//                $date = date("Y-m-d");
//                $date_end = date("Y-m-01");
//                break;
//            }
//            case 4:{//thangtruoc
//                $today = date("Y-m-d");
//                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 month");
//                $date_end = strftime("%Y-%m-%d", $week);
//                $date = date("Y-m-d");
//                break;
//            }
//            default:{
//                $date = date("Y-m-d");
//                $date_end = date("Y-m-d");
//            }
//        }
//    //var_dump($data);die;
//        if($data != false){
//            //$viewFile = '/admin_dashboard/top10_import';
//            //$content = $this->load->view($this->path_theme_view . $viewFile, $data, true);
//            $data_return["state"] = 1;
//            $data_return["msg"] = "Ok";
//
//            if ($data) {
//                $data_laixe = array();
//                $arr_colum = array();
//                foreach ($data as $item) {
//                    $arr_colum['label'] = $item['shipper_name'];
//                    $arr_colum['y'] = $item['total_price'];
//                    $data_laixe[] = $arr_colum;
//                }
//            } else {
//                $data_laixe = array();
//            }
//
//            $data_return["html"] = $data_laixe;
//            echo json_encode($data_return);
//            return TRUE;
//        }else{
//            $data_return["state"] = 0;
//            $data_return["msg"] = "False";
//            $data_return["html"] = "";
//            echo json_encode($data_return);
//            return FALSE;
//        }
    }

    public function baocao_doanhthu_laixe($select){
        $this->load->model('m_voxy_package_orders');
        switch($select){
            case 0:{//homnay
                $date = date("Y-m-d");
                $date_end = date("Y-m-d");
                break;
            }
            case 1:{//homqua
                $hom_qua = strtotime('-1 day',strtotime(date("Y-m-d")));
                $date_end  = strftime("%Y-%m-%d", $hom_qua);
                $date = $date_end;
                break;
            }
            case 2:{//tuannay
                $today = date("Y-m-d");
                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 week");
                $date_end = strftime("%Y-%m-%d", $week);
                $date = date("Y-m-d");
                break;
            }
            case 3:{//thang nay
                $date = date("Y-m-d");
                $date_end = date("Y-m-01");
                break;
            }
            case 4:{//thangtruoc
                $today = date("Y-m-d");
                $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 month");
                $date_end = strftime("%Y-%m-%d", $week);
                $date = date("Y-m-d");
                break;
            }
            default:{
                $date = date("Y-m-d");
                $date_end = date("Y-m-d");
            }
        }
        //var_dump($date, $date_end);die;

        if($date != ""){//hien thi ngay hom nay
            $data = $this->m_voxy_package_orders->get_infor_vs_laixe($date, $date_end, $select);
        }
        //var_dump($data);die;
        return $data;
    }


    public function index()
    {
        $data = Array(
            'user_name' => $this->USER->user_name,
        );
        $this->load->model('m_voxy_package_orders');
        //nhap hang
        if($this->session->userdata("selected_nhaphang") !== false){
            $selected_import = $this->session->userdata("selected_nhaphang");
        }else{
            $selected_import = 1;
        }


        if($this->session->userdata("selected_nhaphang_sorting") !== false){
            $selected_import_sorting = $this->session->userdata("selected_nhaphang_sorting");
        }else{
            $selected_import_sorting = "quantity";
        }
        //end nhap hang
        //xuathang
        if($this->session->userdata("selected_banchay") !== false){
            $selected_banchay = $this->session->userdata("selected_banchay");
        }else{
            $selected_banchay = 1;
        }

        if($this->session->userdata("selected_banchay_sorting") !== false){
            $selected_banchay_sorting = $this->session->userdata("selected_banchay_sorting");
        }else{
            $selected_banchay_sorting = "quantity";
        }
        //end xuathang

        if($this->session->userdata("selected_laixe") !== false){
            $selected_laixe = $this->session->userdata("selected_laixe");
        }else{
            $selected_laixe = 1;
        }
        //

        //ko dung dc ajax de load lai cai select, jquery nay k mua, nen phai load lai toan bo trang

        // $data['data_import'] = $this->baocao_top10_nhaphang($selected_import, $selected_import_sorting);//la ngay hom qua
      // $data['data_top10_banchay'] = $this->baocao_top10_banchay($selected_banchay, $selected_banchay_sorting);// ngay hom qua

        //var_dump($selected_laixe);die;
        $data['data_product_laixe'] = $this->baocao_doanhthu_laixe($selected_laixe);// ngay hom qua

        //view hien thi ben tren , ket qua ban ngay hom nay
        $date_now = date("Y-m-d");

        $data['number_of_orders'] = $this->m_voxy_package_orders->count_of_orders($date_now)[0]['count_id'];
        $data['sum_doanhthu'] = $this->m_voxy_package_orders->count_of_orders($date_now)[0]['doanhthu'];

        $list_hangve = $this->m_voxy_package_orders->count_of_orders_wrong($date_now);
        if($list_hangve == false){
            $cout_hangve = 0;
            $doanhthu_trave = 0;
        }else{
            $doanhthu_trave = 0;
            $cout_hangve  = 0;
            foreach ($list_hangve as $item){
                $cout_hangve++;
                $hangve = json_decode($item['hangve']);
               foreach ($hangve as $row){
                   $row = get_object_vars($row);
                   $doanhthu_trave += $row['thanhtien'];
               }

            }
        }

        $data['sum_doanhthu_trave'] = $doanhthu_trave;
        $data['cout_hangve'] = $cout_hangve;
        //so voi cung ki thang truoc
        $today = date("Y-m-d");
        $week = strtotime(date("Y-m-d", strtotime($today)) . " -1 month");
        $date_thangtruoc = strftime("%Y-%m-%d", $week);
        $sum_doanhthu_last_month = $this->m_voxy_package_orders->count_of_orders($date_thangtruoc)[0]['count_id'];
        if($data['sum_doanhthu'] == 0){
            $procent = 0;
        }else{
            $procent = round(($sum_doanhthu_last_month / $data['sum_doanhthu']) * 100,2);
        }


        $data['procent'] = $procent;
        // end view hien thi ben tren , ket qua ban ngay hom nay
        $content        = $this->load->view($this->path_theme_view . "admin_dashboard/index", $data, true);
        $header_page    = NULL;
        $title          = NULL;
        $description    = NULL;
        $keywords       = NULL;
        $canonical      = NULL;

        $this->master_page($content, $header_page, $title, $description, $keywords, $canonical);
    }

}