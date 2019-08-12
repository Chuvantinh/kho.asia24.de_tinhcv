<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_htmltoPDF extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //phieu nhat hang
    public function pdf_day($category_id, $list_id_to_nhathang, $ngay_chuyen_hang, $kho, $shipper_id = "", $allready, $sorting)
    {
        //$order_day = $ngay_dat_hang;
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');

        $this->db->select('id');
        $this->db->select('line_items');
        //$this->db->select('order_number');
        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        if ($allready == true) {
            $this->db->where('check_nhat_hang', 1);
        } else {
            $this->db->where('check_nhat_hang IS NULL', null, false);//nhugn cai nao ma chua dc nhat hang
        }
        //if($order_day && $order_day != ""){
            //$this->db->like('created_time', $order_day);
        //}
        if($list_id_to_nhathang == null || $list_id_to_nhathang == ""){
            if($ngay_chuyen_hang && $ngay_chuyen_hang != ""){
                $this->db->like('shipped_at', $ngay_chuyen_hang);
            }

            if ($shipper_id != "") {
                $this->db->where_in('shipper_id', $shipper_id);
            }
        }else{
            if($list_id_to_nhathang != ""){
                $this->db->where_in('id', $list_id_to_nhathang);
            }
        }

        $query = $this->db->get();

        //var_dump($this->db->last_query());die;
        $data = $query->result_array();
        $_export = array();
        $i = 0;

        $array_note_products = array();
        $list_oder_nummer = array();
        //get nur array of items

        foreach ($data as $item) {
            $line_items = $item['line_items'];
            foreach (json_decode($line_items) as $key2 => $item2) {

                    $array_note_products[$i]['title'] = $item2->title;
                    $array_note_products[$i]['item_note_value'] = $item2->item_note;

                $i++;
                $_export[$i] = get_object_vars($item2);
            }
            //$list_oder_nummer['oder_number'] = $item['order_number'];// save oder da dc nhat hang
            $list_oder_nummer[] = $item['id'];// save oder da dc nhat hang
        }
        //danh dau nhung order nao da dc nhat hang
        foreach ($list_oder_nummer as $item_id) {
            $this->m_voxy_package_orders->update_id_check_nhat_hang($item_id);
        }

        $export2 = array();
        $chiso_remove = array();
        //sum inventory of same product
        foreach ($_export as $key => $item) {
            // kiem tra co tat ca bao nhieu product trong list, rôi tang quantity len, go bo nhung thang giong nhau
            foreach ($_export as $key2 => $item2) {
                if ($key2 > $key) {
                    if ($item['title'] == $item2['title'] && $item['variant_title'] == $item2['variant_title'] && $item['name'] == $item2['name']) {
                        $item['quantity'] = $item['quantity'] + $item2['quantity'];
                        $chiso_remove[$key2 - 1] = $key2 - 1;//index of same product and then remove it
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

        // Hole eine Liste von Spalten
        // http://php.net/manual/de/function.array-multisort.php

        if ($sorting == "location") {
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['location'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'location');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_DESC, $export2);
        } else {
            //sort theo alphabe tang dan
            foreach ($export2 as $key => $row) {
                $band[$key] = $row['title'];
                $auflage[$key] = $row['sku'];
            }
            $band = array_column($export2, 'title');
            $auflage = array_column($export2, 'sku');
            array_multisort($band, SORT_ASC, $auflage, SORT_ASC, $export2);
        }

        if ($sorting == "category") {
            $output = '<div style="font-family: DejaVu Sans;line-height: 15px;margin: 0; padding: 0; width: 100%">
            <div class="title" style="font-weight: bold;width: 100%; text-align: center">
                <span style="width: 10%;float: left;text-align: center;text-align: left !important;">SKU</span>
                <span style="width: 70%;float: left;text-align: center">Tên</span>
                <span style="width: 5%;float: left;text-align: center"></span>
                <span style="width: 5%;float: left;text-align: center">SL</span>
                <span style="width: 10%;float: left;text-align: center">Đơn Vị</span>
                <span style="display:none; width: 20%;float: left;text-align: center">Vị trí</span>
            </div>
            ';
            //step 1: get category id and name, sap xep theo a -z
            $arr_cat_id = array();
            $export2_new = array();
            foreach ($export2 as $item) {
                $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
                $cat_title = $this->m_voxy_category->get_cat_title($cat_id);
                if($cat_title == false){
                    $cat_title = "G00-Hàng Lẻ Linh Tinh";
                    $arr_cat_id[$cat_title]['cat_id'] = $cat_id;
                }
                $arr_cat_id[$cat_title]['title'] = $cat_title;
                $arr_cat_id[$cat_title]['cat_id'] = $cat_id;

                $item['cat_id'] = $cat_id;
                $export2_new[] = $item;
            }

            // step 2: sort tang dan
            ksort($arr_cat_id);

            $arr_cat_id_new = array();
            if($category_id != false){
                foreach ($arr_cat_id as $item){
                    foreach ($category_id as $key => $item_new){
                        if($item_new == $item['cat_id']){
                            $arr_cat_id_new[$key]['title'] = $item['title'];
                            $arr_cat_id_new[$key]['cat_id'] = $item['cat_id'];
                        }
                    }
                }
            }else{
                $arr_cat_id_new = $arr_cat_id;
            }
            // step3: in ra theo product va categories
            $id = 0;
            $k = 0;

            foreach ($arr_cat_id_new as $catid) { //category
                if ($kho == "AKL") {
                    if ($catid['cat_id'] == "91459649625") {
                        $output .= '<p style="text-align: center;margin: 0; padding: 10px 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 17px;">' . $catid['title'] . '</p>';
                    }
                } elseif ($kho == "lil") {
                    foreach ($export2_new as $item2) {
                        if ($catid['cat_id'] === $this->m_voxy_package->get_categories($item2['product_id'])) {
                            if (strpos($item2['location'], 'AH') !== false) {
                                $output .= '<p style="text-align: center;margin: 0; padding: 10px 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 17px;">' . $catid['title'] . '</p>';
                                break;
                            }
                        }
                    }
                } elseif ($kho == "cua_hang") {//trong cua hang
                    if ($catid['cat_id'] == false) {
                        $output .= '<p style="text-align: center;margin: 0; padding: 10px 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 17px;">G00-Hàng Lẻ Linh Tinh</p>';
                    } else {
                        foreach ($export2_new as $item5) {
                            if ($catid['cat_id'] === $this->m_voxy_package->get_categories($item5['product_id'])) {
                                if (strpos($item5['location'], 'AH') !== false || strpos($item5['location'], 'AKL') !== false) {

                                } else {
                                    $output .= '<p style="text-align: center;margin: 0; padding: 10px 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 17px;">' . $catid['title'] . '</p>';
                                    break;
                                }
                            }
                        }
                    }

                } elseif ($kho == "all") {
                        $output .= '<p style="text-align: center;margin: 0; padding: 10px 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 17px;">' . $catid['title'] . '</p>';
                }

                foreach ($export2_new as $row) {//san pham
                    $k++;
                    if ($catid['cat_id'] == $row['cat_id']) {  //check product co thuoc san pham do khong thi moi in ra
                        //xu ly chuoi location overlengt 12
                        if (strlen($row['location']) > 11) {
                            $array_location = explode(",", $row['location']);
                            $row['location'] = "";
                            foreach ($array_location as $key => $loca) {
                                $row['location'] .= $loca . "<br>";
                            }
                        }

                        //xu ly do dai cua sku
                        if (strlen($row['sku']) > 5) {
                            $row['sku'] = substr($row['sku'], 0, 5);
                        }

                        //check in ra trong kho nao $row['location'] vs $kho
                        if ($kho == "all") { // in tat ca k phan biet
                            $id++;
                            $value_note = "";
                            foreach ($array_note_products as $item_note) {
                                if ($item_note['title'] === $row['title']) {
                                    if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                        $value_note .= "<b style='margin-left: 20px;'>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                                    }
                                }
                            }

                            $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 70%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                            if ($value_note != "") {
                                $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                            }
                            $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 5%;height: auto;float: left; padding-left: 15px;text-align: justify;"><b>' . $row['quantity'] . '</b></div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="display:none; width: 20%;height: auto;float: left">' . $row['location'] . '</div>
                    </div>
                ';
                        } elseif ($kho == 'lil') {
                            if (strpos($row['location'], 'AH') !== false) {
                                $id++;
                                $value_note = "";
                                foreach ($array_note_products as $item_note) {
                                    if ($item_note['title'] === $row['title']) {
                                        if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                            $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                }
                                $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 70%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                                if ($value_note != "") {
                                    $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                                }
                                $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 5%;height: auto;float: left; padding-left: 15px;text-align: justify;"><b>' . $row['quantity'] . '</b></div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="display:none;width: 20%;height: auto;float: left">' . $row['location'] . '</div>
                    </div>
                    <br>
                    <br>
                           ';
                            }
                        } elseif ($kho == 'AKL') {
                            if (strpos($row['location'], 'AKL') !== false) {
                                $id++;
                                $value_note = "";
                                foreach ($array_note_products as $item_note) {
                                    if ($item_note['title'] === $row['title']) {
                                        if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                            $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                }
                                $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 70%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                                if ($value_note != "") {
                                    $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                                }
                                $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 5%;height: auto;float: left; padding-left: 15px;text-align: justify;"><b>' . $row['quantity'] . '</b></div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="display:none;width: 20%;height: auto;float: left">' . $row['location'] . '</div>
                    </div>
                    <br>
                    <br>
                            ';
                            }
                        } elseif ($kho == 'cua_hang') {
                            if ($row['location'] == false) {
                                $id++;
                                $value_note = "";
                                foreach ($array_note_products as $item_note) {
                                    if ($item_note['title'] === $row['title']) {
                                        if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                            $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                                        }
                                    }
                                }
                                $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 70%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                                if ($value_note != "") {
                                    $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                                }
                                $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 5%;height: auto;float: left; padding-left: 15px;text-align: justify;"><b>' . $row['quantity'] . '</b></div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="display:none;width: 20%;height: auto;float: left">' . $row['location'] . '</div>
                    </div>
                    <br>
                    <br>
                        ';
                            }
                        }
                        //end check in ra trong kho nao
                    }
                }
            }
        } else {//loc theo location below
            $output = '<div style="font-family: DejaVu Sans; font-size: 15px;margin: 0; padding: 0; width: 100%">
            <div class="title" style="font-weight: bold;width: 100%; text-align: center">
                <span style="width: 10%;float: left;text-align: center;text-align: left !important;">SKU</span>
                <span style="width: 45%;float: left;text-align: center">Tên</span>
                <span style="width: 5%;float: left;text-align: center"></span>
                <span style="width: 10%;float: left;text-align: center">SL</span>
                <span style="width: 10%;float: left;text-align: center">Đơn Vị</span>
                <span style="width: 20%;float: left;text-align: center">Vị trí</span>
            </div>
            ';
            //step 1: get category id and name, sap xep theo a -z
            $arr_cat_id = array();
            foreach ($export2 as $item) {
                $cat_id = $this->m_voxy_package->get_categories($item['product_id']);
                $id_cat = $this->m_voxy_category->get_id($cat_id);
                $arr_cat_id[$id_cat]['id'] = $id_cat;
                $arr_cat_id[$id_cat]['cat_id'] = $cat_id;
            }

            // step 2: sort tang dan
            ksort($arr_cat_id);

            // step3: in ra theo product va categories
            $id = 0;
            $k = 0;
            foreach ($export2 as $row) { //san pham in theo thu tu tang dan
                $k++;
                //xu ly chuoi location overlengt 12
                $location_xuly = "";
                if (strlen($row['location']) > 11) {
                    $array_location = explode(",", $row['location']);
                    if (is_array($array_location)) {
                        foreach ($array_location as $key => $loca) {
                            $location_xuly .= $loca . "<br>";
                        }
                    }
                } else {
                    $location_xuly = $row['location'];
                }

                //xu ly do dai cua sku
                if (strlen($row['sku']) > 5) {
                    $row['sku'] = substr($row['sku'], 0, 5);
                }

                //check in ra trong kho nao $row['location'] vs $kho
                if ($kho == "all") { // in tat ca k phan biet
                    $id++;
                    $value_note = "";
                    foreach ($array_note_products as $item_note) {
                        if ($item_note['title'] === $row['title']) {
                            if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                            }
                        }
                    }
                    $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 45%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                    if ($value_note != "") {
                        $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                    }
                    $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 10%;height: auto;float: left">' . $row['quantity'] . '</div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="width: 20%;height: auto;float: left">' . $location_xuly . '</div>
                    </div>
                ';
                } elseif ($kho == 'lil') {
                    if (strpos($row['location'], 'AH') !== false) {
                        $id++;
                        $value_note = "";
                        foreach ($array_note_products as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                    $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                                }
                            }
                        }
                        $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 45%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                        if ($value_note != "") {
                            $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                        }
                        $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 10%;height: auto;float: left">' . $row['quantity'] . '</div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="width: 20%;height: auto;float: left">' . $location_xuly . '</div>
                    </div>
                    <br>
                    <br>
                           ';
                    }
                } elseif ($kho == 'AKL') {
                    if (strpos($row['location'], 'AKL') !== false) {
                        $id++;
                        $value_note = "";
                        foreach ($array_note_products as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                    $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                                }
                            }
                        }
                        $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 45%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                        if ($value_note != "") {
                            $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                        }
                        $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 10%;height: auto;float: left">' . $row['quantity'] . '</div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="width: 20%;height: auto;float: left">' . $location_xuly . '</div>
                    </div>
                    <br>
                    <br>
                            ';
                    }
                } elseif ($kho == 'cua_hang') {
                    if ($row['location'] == false) {
                        $id++;
                        $value_note = "";
                        foreach ($array_note_products as $item_note) {
                            if ($item_note['title'] === $row['title']) {
                                if ($item_note['item_note_value'] != null && $item_note['item_note_value'] != "") {
                                    $value_note .= "<b>NOTE--></b>" . $item_note['item_note_value'] . "<br>";
                                }
                            }
                        }
                        $output .= '
                    <div class="infomation" style="width: 100%; text-align: center; clear: left;">
                       <div style="width: 10%;height: auto;float: left;text-align: left !important;">' . $row['sku'] . '</div>
                        <div style="width: 45%;height: auto;float: left; text-align: left">' . $row['title'] . ' ';
                        if ($value_note != "") {
                            $output .= '<br><span style="text-transform: uppercase">' . $value_note . '</span>';
                        }
                        $output .= '
                        </div>
                        <div style="width: 5%;height: auto;float: left; text-align: left">---></div>
                        <div style="width: 10%;height: auto;float: left">' . $row['quantity'] . '</div>
                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"><b>' . $row['variant_title'] . '</b></div>
                        <div style="width: 20%;height: auto;float: left">' . $location_xuly . '</div>
                    </div>
                    <br>
                    <br>
                        ';
                    }
                }
                //end check in ra trong kho nao
            }
        }
        $output .= '</div>';
        //$output .= '<div style="clear:left; font-family: DejaVu Sans">Số lượng loại hàng: '.$k.'</div>';
        //var_dump($output);die;
//        $output .= '<div class="note" style="clear:left; margin-top: 70px; border: 1px solid">';
//        foreach ($data as $item){
//            if($item['note'] != "") {
//            $output .= '<p>'.$item['order_number'].'-'.$item['note'].'</p>';
//            }
//        }
//        $output .=  '</div>';
        return $output;
    }

    public function get_infor_kunden($oder_number)
    {
        $this->db->select('customer');
        $this->db->select('created_time');
        $this->db->select('shipped_at');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $oder_number);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function pdf_order($oder_number)
    {
        $this->load->model('m_voxy_package');

        $this->db->select('line_items');
        $this->db->select('total_price');
        $this->db->select('order_number');
        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $oder_number);
        $query = $this->db->get();
        $data = $query->result_array();

        //var_dump($this->db->last_query());die;
        $_export = array();
        $i = 0;
        //get nur array of items
        if($data[0]['line_items'] != "" || $data[0]['line_items'] != null){
            foreach (json_decode($data[0]['line_items']) as $item) {
                $_export[] = get_object_vars($item);;
            }
        }

        $wek= array();
        foreach ($_export as $key => $row) {
            $wek[$key] = $row['title'];
        }
        // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
        array_multisort($wek, SORT_ASC, $_export);

        $output = '<table width="100%" style="page-break-inside: auto; border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
<thead style="border: 1px solid;">
    <tr>
                <th style="border: 1px solid;">Nr</th>
                <th style="border: 1px solid;">SKU</th>
                <th style="width:50%;border: 1px solid;">Artikelbezeichung</th>
                <th style="border: 1px solid;">Menge</th>
                <th style="border: 1px solid;">Einheit</th>
                <th style="border: 1px solid;">MwSt</th>
                <th style="border: 1px solid;">Einzelpreis</th>
                <th style="border: 1px solid;width: 5%;">Total€</th>
            </tr>
</thead>         
';
        $output .= '<tbody style="border: 1px solid;">';
        $id = 0;
        $total_price = 0;
        $netto = 0;
        foreach ($_export as $row) {
            $id++;

            $gesamt =  $row['price'] * $row['quantity'];

            $mwst = 7;
            if(isset($row['sku'])){
                $mwst = $this->m_voxy_package->get_mwst($row['sku']);
            }
            if($mwst == false){
                $mwst = 7;
            }
            $netto += $gesamt/(($mwst/100) + 1);
            if( isset($row['hangve']) || isset($row['hanghong']) || isset($row['hangthieu']) || isset($row['hangthem'])  ) {
                $sl_cuoi = $row['quantity'] - (double)$row['hangve'] - (double)$row['hanghong'] - (double)$row['hangthieu'] + (double)$row['hangthem'];
            }else{
                $sl_cuoi = $row['quantity'];
            }
            $total_price += $row['price'] * $sl_cuoi;
            $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $id . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">' . $row['sku'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;">' . $row['title'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $sl_cuoi . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $row['variant_title'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $mwst . "%". '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $row['price'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;width: 5%;">' . $row['price'] * $sl_cuoi . '</td>
			</tr>
			';
        }
        $output .= '</tbody>';
        $output .= '</table><br>';

        $steuer = $total_price - $netto;

        $output .= '
        <table>
            <tr>
                    <td style="width: 430px;">
                        <p style="float:left;font-size: 13px;font-family: DejaVu Sans;">Unterschrift:</p> <br><br><br><br><br>
                        <hr style="font-size: 13px;width: 200px;margin: 0; padding: 0;">
                    </td>
                    <td style="width: 150px;">
                       <p><span><b>Netto:</b></span></p> 
                       <span><b>MwSt:</b></span></p>
                       <p><span><b>Gesamtbetrag:</b></span></p>
                    </td>
                    <td style="width: 130px;">
                        <p><span><b>' . number_format($netto, 2) . ' €</b></span></p>
                        <p><span><b>' . number_format($steuer, 2) . ' €</b></span></p>
                        <p><span><b>' . number_format($total_price, 2) . ' €</b></span></p>
                    </td>
                </tr>
        </table>';

        $output .= '<div class="note">';
        foreach ($data as $item) {
            if ($item['note'] != "") {
                $output .= '<p style="margin-top: 30px; border: 1px solid">' . $item['order_number'] . '-' . $item['note'] . '</p>';
            }
        }
        $output .= '</div>';
        //var_dump($output);die;
        return $output;
    }

    public function pdf_product()
    {

        $this->db->select('*');
        $this->db->from('voxy_package');
        $query = $this->db->get();
        $data = $query->result_array();

        //ksort tag theo khoa, krsort giam theo khoa hehe :D
        //ksort($export);

        //step 1: get category id and name, sap xep theo a -z
        $arr_cat_id = array();
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        foreach ($data as $item) {
            if ($item['cat_id'] != null) {
                $arr_cat_id[] = $item['cat_id'];
            }
        }

        // step 2: remove cac cai giong
        $result_catid = array_unique($arr_cat_id);

        $output = '<table width="100%" cellspacing="5" cellpadding="5" style="font-family: DejaVu Sans"> 
            <tr>
                <th>ID</th>
                <th>Tên SP</th>
                
            </tr>
            ';
        // in ra theo product va categories
        foreach ($result_catid as $catid) {
            $output .= '
                    <tr>
                        <td></td>
                        <td style="text-align: center;"><b>Collection:' . $this->m_voxy_category->get_cat_title($catid) . '</b></td>
                    </tr>
                    ';
            foreach ($data as $row) {
                //check product co thuoc san pham do khong thi moi in ra
                if ($catid === $row['cat_id']) {
                    $output .= '
                    <tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['title'] . '</td>
                    </tr>
                    ';
                }
            }
        }
        $output .= '</table>';
        return $output;
    }

    public function list_order_money_day($ngay_chuyen_hang)
    {
        $this->db->select('order_number');
        $this->db->select('customer');
        $this->db->select('total_price');
        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        $this->db->like('shipped_at', $ngay_chuyen_hang);
//        if( $shipper_id != ""){
//            $this->db->like('shipper_id', $shipper_id);
//        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        return $query->result_array();
    }

    //thông tin khách hàng và lái xe
    public function print_money_day($ngay_chuyen_hang, $shipper_id)
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_kunden');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_chiphi_laixe');
        $this->load->model('m_voxy_shippers');

        $this->db->select('order_number');
        $this->db->select('line_items');
        $this->db->select('customer');
        $this->db->select('customer_id');
        $this->db->select('note');
        $this->db->select('key_word_customer');
        $this->db->select('created_time');
        $this->db->select('total_price');
        $this->db->select('thanhtoan_lan1');
        $this->db->select('thanhtoan_lan2');
        $this->db->select('thanhtoan_lan3');
        $this->db->select('thanhtoan_lan4');
        $this->db->select('thanhtoan_lan5');
        $this->db->select('tongtien_no');

        $this->db->select('time_lan1');

        $this->db->select('hangve');
        $this->db->select('hanghong');
        $this->db->select('hangthieu');
        $this->db->select('hangthem');

        $this->db->from('voxy_package_orders');

        $this->db->where('status != ', 'red');
        $this->db->like('shipped_at', $ngay_chuyen_hang);
        $this->db->order_by('id','desc');
        if ($shipper_id != "") {
            $this->db->where_in('shipper_id', $shipper_id);
        }

        $query = $this->db->get();

       // var_dump($this->db->last_query());die;

        $data = $query->result_array();

        $data_order = $data;
        $data_order_old = "";
        $data_driving_cost = "";

        //save history of the report :colum information_orders
        $data_save = date('d-m-Y');
        if(is_array($shipper_id) && count($shipper_id) == 1){// chi add history only for one driver , if you have 2 drivers , I don't add the history to the database.

            foreach ($shipper_id as $ship){
                $id_ubericht_history = $this->m_voxy_package_orders->check_update_ubericht_history($data_save, $ship);
            }
        }
        //end save history of the report

//        echo "<pre>";
//        var_dump($data);
//        echo "</pre>";die;

        $output = '<table width="100%" style="border-collapse: collapse;page-break-inside: auto;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
<thead style="border: 1px solid;">
    <tr>
                <th style="border: 1px solid;">Order</th>
                <th style="border: 1px solid;">Khách Hàng</th>
                <th style="border: 1px solid;">Doanh Thu</th>
                <th style="border: 1px solid;">DT Thực</th>
                <th style="border: 1px solid;">Đã Thu</th>
                <th style="border: 1px solid;">Còn Nợ</th>
                <th style="border: 1px solid;">Ghi chú</th>
            </tr>
</thead>         
';
        $check = false;

        $output .= '<tbody>';

        $total_price = 0;
        $tongtien_thu = 0;
        $tongtien_no = 0;
        $tong_tongtienno = 0;
        $tong_tongtienthu = 0;
        $list_kunden = array();
        $list_order_number = array();
        $list_time_lan1 = array();

        $tong_doanhthu_truoc = 0;

        foreach ($data as $item) {

            //get all note trong order ra ngoai
            $line_items = json_decode($item['line_items']);

            $list_note = array();
            foreach ($line_items as $key2 => $arr){
                $arr = get_object_vars($arr);
                if($arr['item_note'] != ""){
                    $item['note'] .= "<br>"."-". $arr['item_note'];
                }
            }

            if($item['hangve'] != "" || $item['hangthieu'] != "" || $item['hanghong'] != "" || $item['hangthem'] != ""){
                $check = true;
            }

            $list_kunden[] = $item['customer_id'];
            $list_order_number[] = $item['order_number'];
            if($ngay_chuyen_hang != ""){
                $list_time_lan1[] = $ngay_chuyen_hang;
            }

            $total_price += $item['total_price'];

            $tongtien_thu = (float)$item['thanhtoan_lan1'] + (float)$item['thanhtoan_lan2'] + (float)$item['thanhtoan_lan3'] + (float)$item['thanhtoan_lan4'] + (float)$item['thanhtoan_lan5'];
            $tongtien_no = $item['tongtien_no'];
            if($tongtien_thu == ""){
                $tongtien_thu_print = " ";
            }else{
                $tongtien_thu_print = number_format($tongtien_thu,2) ." €";
            }

            $tong_tongtienthu += $tongtien_thu;
            $tong_tongtienno += $tongtien_no;

            if($tongtien_no == ""){
                //$tongtien_no_print =  $item['total_price'] ." €";
                $tongtien_no_print =  0 ." €";
            }else if($tongtien_no == 0){
                $tongtien_no_print = " ";
            }else{
                $tongtien_no_print = number_format($tongtien_no,2) ." €";
            }
            if(json_decode($item['customer'])){
                $json_customer = get_object_vars(json_decode($item['customer']));
            }

            if(isset($item['key_word_customer']) && $item['key_word_customer'] != ""){
                $key_word = $item['key_word_customer'];
            }else{
                $key_word = null;
            }

            //get id khachhang
            $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($item['customer_id']);

            if (isset($json_customer['d_first_name'])) {
                $frist_name = $json_customer['d_first_name'];
            } elseif (isset($json_customer['first_name'])) {
                $frist_name = $json_customer['first_name'];
            }else {
                $frist_name = "";
            }

            if (isset($json_customer['d_last_name'])) {
                $last_name = $json_customer['d_last_name'];
            } elseif (isset($json_customer['last_name'])) {
                $last_name = $json_customer['last_name'];
            } else {
                $last_name = "";
            }

            if($key_word != null){
                $customer_name = $key_word ;
            }else{
                $customer_name = $frist_name . " " . $last_name;
            }

            if ($json_customer) {
                if(isset($json_customer['phone'])){
                    if (strlen($json_customer['phone']) > 11) {
                        $array_phone = explode("/", $json_customer['phone']);
                        $phone = "";
                        foreach ($array_phone as $key => $_phone) {
                            $phone .= $_phone . "<br>";
                        }
                    }
                }
                $adresse = $json_customer['address1'] . " " . $json_customer['zip'] . " " . $json_customer['city'];
            }

            $datum = $item['created_time'];
            $date = date_create($datum);
            $datum = date_format($date, 'd-m-Y');

            $doanhthu_truoc = $this->m_voxy_package_orders->get_doanhthu_truoc($item['order_number']);
            if($doanhthu_truoc != false){
                $doanhthu_truoc_print = number_format($doanhthu_truoc,2). " €";
            }else{
                $doanhthu_truoc = 0;
                $doanhthu_truoc_print = "0 €";
            }

            $tong_doanhthu_truoc += $doanhthu_truoc;

            $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
				    ' . $id_khachang . "-".$customer_name . '
				</td>
				
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid;text-align: right;">' . $doanhthu_truoc_print. '</td>
				
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . number_format($item['total_price'], 2) . " €" . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . $tongtien_thu_print . '
				</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . $tongtien_no_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $item['note']. '</td>
			</tr>
			';
        }

        $list_kunden = array_unique($list_kunden);
        $list_time_lan1 = array_unique($list_time_lan1);
        //var_dump($list_order_number);die;

        if($list_time_lan1){
            $data_lastime = $this->m_voxy_package_orders->get_lastime_pay($list_kunden,$list_time_lan1, $list_order_number,$shipper_id);
            //add them ,  nhan tien cho don hang cua lai xe khac, vi ong khac chay ho tour nay truoc do
            $data_lastime_other_tour = $this->m_voxy_package_orders->get_lastime_pay_other_tour($shipper_id,$ngay_chuyen_hang);
            if(isset($data_lastime_other_tour) && $data_lastime_other_tour != false){
                $data_lastime = array_merge($data_lastime, $data_lastime_other_tour);
            }
            //save history of the report: update data information_orders_old
                $data_order_old = $data_lastime;
            //end save history of the report
        }

        if(isset($data_lastime) && $data_lastime != false){
            $lastime_total_price = 0 ;
            $lastime_total_dathu = 0 ;
            $lastime_total_no = 0 ;
            foreach ($data_lastime as $item){

                if(isset($item['key_word_customer']) && $item['key_word_customer'] != ""){
                    $key_word = $item['key_word_customer'];
                }else{
                    $key_word = null;
                }

                //get id khachhang
                $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($item['customer_id']);

                if (isset($json_customer['d_first_name'])) {
                    $frist_name = $json_customer['d_first_name'];
                } elseif (isset($json_customer['first_name'])) {
                    $frist_name = $json_customer['first_name'];
                }else {
                    $frist_name = "";
                }

                if (isset($json_customer['d_last_name'])) {
                    $last_name = $json_customer['d_last_name'];
                } elseif (isset($json_customer['last_name'])) {
                    $last_name = $json_customer['last_name'];
                } else {
                    $last_name = "";
                }

                if($key_word != null){
                    $customer_name = $key_word ;
                }else{
                    $customer_name = $frist_name . " " . $last_name;
                }

                if($item['thanhtoan_lan1'] == 0 || $item['thanhtoan_lan1'] == null){
                    $thanhtoan_lan1_print = " ";
                }else{
                    $thanhtoan_lan1_print = number_format($item['thanhtoan_lan1'],2)." €";
                }

                if($item['thanhtoan_lan2'] == 0 || $item['thanhtoan_lan2'] == null){
                    $thanhtoan_lan2_print = " ";
                }else{
                    $thanhtoan_lan2_print = number_format($item['thanhtoan_lan2'],2)." €";
                }

                if($item['thanhtoan_lan3'] == 0 || $item['thanhtoan_lan3'] == null){
                    $thanhtoan_lan3_print = " ";
                }else{
                    $thanhtoan_lan3_print = number_format($item['thanhtoan_lan3'],2)." €";
                }

                if($item['thanhtoan_lan4'] == 0 || $item['thanhtoan_lan4'] == null){
                    $thanhtoan_lan4_print = " ";
                }else{
                    $thanhtoan_lan4_print = number_format($item['thanhtoan_lan4'],2)." €";
                }

                if($item['thanhtoan_lan5'] == 0 || $item['thanhtoan_lan5'] == null){
                    $thanhtoan_lan5_print = " ";
                }else{
                    $thanhtoan_lan5_print = number_format($item['thanhtoan_lan5'],2)." €";
                }

                if($item['tongtien_no'] == 0 || $item['tongtien_no'] == null){
                    $tongtien_no_lan1_print = " ";
                }else{
                    $tongtien_no_lan1_print = number_format($item['tongtien_no'],2) ." €";
                }
//				<td style="text-align: right;">' . number_format($item['total_price'], 2) . " €" . '</td>
                if($item['note'] == ""){
                    $item['note'] = "Nợ cũ";
                }

                $your_string = $item['shipped_at'];
                $item['shipped_at'] = date("d-m", strtotime($your_string));

                $lastime_total_price += $item['total_price'];
                $lastime_total_no += $item['tongtien_no'];

                if($item['time_lan1'] == $ngay_chuyen_hang){
                    $lastime_total_dathu += $item['thanhtoan_lan1'];

                    $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan1_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                }

                if($item['time_lan2'] != "" and $item['time_lan2'] == $ngay_chuyen_hang){
                    $lastime_total_dathu += $item['thanhtoan_lan2'];

                    $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan2_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                }

                if($item['time_lan3'] != "" and $item['time_lan3'] == $ngay_chuyen_hang){
                    $lastime_total_dathu += $item['thanhtoan_lan3'];

                    $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan3_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                }

                if($item['time_lan4'] != "" and $item['time_lan4'] == $ngay_chuyen_hang){
                    $lastime_total_dathu += $item['thanhtoan_lan4'];

                    $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan4_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                }

                if($item['time_lan5'] != "" and $item['time_lan5'] == $ngay_chuyen_hang){
                    $lastime_total_dathu += $item['thanhtoan_lan5'];

                    $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan5_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                }

            }
        }

        if(!isset($lastime_total_price)){
            $lastime_total_price = 0;
        }

        if(!isset($lastime_total_dathu)){
            $lastime_total_dathu = 0;
        }

        if(!isset($lastime_total_no)){
            $lastime_total_no = 0;
        }

        //them chi phi lai xe vào table
        if($shipper_id) {
            $data_chiphi_laixe = $this->m_voxy_chiphi_laixe->get_chiphilaixe($ngay_chuyen_hang,$shipper_id);
            //save history of the report: update  driving_costs
            $data_driving_cost = $data_chiphi_laixe;
            //end save history of the report

            if(isset($data_chiphi_laixe)){
                $chi_phi_lai_xe = $data_chiphi_laixe[0]['tongchiphi'];
                $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">Trừ chi phí lái xe</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;border-right: 1px solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($data_chiphi_laixe[0]['tongchiphi'], 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;border-right: 1px solid;"></td>
                        
                    </tr>
                ';
            }else{
                $chi_phi_lai_xe = 0;
            }

        }

//end them chi phi lai xe vào table
        $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>Tổng</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($tong_doanhthu_truoc, 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($total_price, 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($tong_tongtienthu + $lastime_total_dathu - $chi_phi_lai_xe, 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($tong_tongtienno , 2) . " €" . '</b></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                    </tr>
        ';

        $output .= '</tbody>';
        $output .= '</table><br>';

        if($check == true){
            $output .= "<p style='font-family: DejaVu Sans;text-align: center;font-weight: bold' >Chi tiết mặt hàng đã thay đổi</p>";
            //them phan hang ve, hong ,thieu, them
            $output .= '<table width="100%" style="border-collapse: collapse;page-break-inside: auto;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
                <thead style="border: 1px solid;">
                    <tr>
                                <th style="border: 1px solid;">Order</th>
                                <th style="border: 1px solid;">Sản phẩm</th>
                                <th style="border: 1px solid;">Trạng thái</th>
                                <th style="border: 1px solid;">Số lượng</th>
                                <th style="border: 1px solid;">Đơn vị</th>
                                <th style="border: 1px solid;">Thành tiền</th>
                            </tr>
                </thead>         
                ';

            $output .= '<tbody>';
            foreach ($data as $key => $item) {
                //hangve
                if($item['hangve'] != null || $item['hangve'] != ""){
                    $array = json_decode($item['hangve']);

                    foreach ($array as $row){
                        $row = get_object_vars($row);

                        $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        $information = $this->m_voxy_package->get_all_infor($id);
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $title = $information[0]['title'];
                        if($check_variant1 == true){
                            $variant_title = $information[0]['option1'];
                        }
                        if($check_variant2 == true){
                            $variant_title = $information[0]['option2'];
                        }
                        $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #1a8eed">hàng về</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                    }
                }
                //end hangve

                //hanghong
                if($item['hanghong'] != null || $item['hanghong'] != ""){
                    $array = json_decode($item['hanghong']);

                    foreach ($array as $row){
                        $row = get_object_vars($row);
                        $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        $information = $this->m_voxy_package->get_all_infor($id);
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $title = $information[0]['title'];
                        if($check_variant1 == true){
                            $variant_title = $information[0]['option1'];
                        }
                        if($check_variant2 == true){
                            $variant_title = $information[0]['option2'];
                        }
                        $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #40cc4b">hàng hỏng</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                    }
                }
                //end hanghong

                //hangthieu
                if($item['hangthieu'] != null || $item['hangthieu'] != ""){
                    $array = json_decode($item['hangthieu']);

                    foreach ($array as $row){
                        $row = get_object_vars($row);
                        $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        $information = $this->m_voxy_package->get_all_infor($id);
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $title = $information[0]['title'];
                        if($check_variant1 == true){
                            $variant_title = $information[0]['option1'];
                        }
                        if($check_variant2 == true){
                            $variant_title = $information[0]['option2'];
                        }
                        $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #7c10cc">hàng thiếu</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                    }
                }
                //end hangthieu

                //hangthem
                if($item['hangthem'] != null || $item['hangthem'] != ""){
                    $array = json_decode($item['hangthem']);

                    foreach ($array as $row){
                        $row = get_object_vars($row);
                        $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                        $information = $this->m_voxy_package->get_all_infor($id);
                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                        $title = $information[0]['title'];
                        if($check_variant1 == true){
                            $variant_title = $information[0]['option1'];
                        }
                        if($check_variant2 == true){
                            $variant_title = $information[0]['option2'];
                        }
                        $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #8d313d">hàng thêm</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                    }
                }
                //end hangthem

            }
            $output .= '</tbody>';
            $output .= '</table><br>';
            //end  them phan hang ve, hong ,thieu, them
        }

        if($data_chiphi_laixe){
            $output .= "<p style='font-family: DejaVu Sans;text-align: center;font-weight: bold' >Chi tiết chi phí lái xe</p>";
            $output .= '<table width="100%" style="border-collapse: collapse;page-break-inside: auto;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
<thead style="border: 1px solid;">
    <tr>
                <th style="border: 1px solid;">Lái xe</th>
                <th style="border: 1px solid;">Tiền xăng</th>
                <th style="border: 1px solid;">Tiền thuê xe</th>
                <th style="border: 1px solid;">Chi phí khác</th>
                <th style="border: 1px solid;">Nộp thiếu</th>
                <th style="border: 1px solid;">Nộp thừa</th>
                <th style="border: 1px solid;">Ghi Chú</th>
            </tr>
</thead>         
';
            $output .= '<tbody>';

            foreach ($data_chiphi_laixe as $item) {
                $laixe_name = $this->m_voxy_shippers->get_name($item['laixe_id']);
                $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $laixe_name . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['tienxang'],2) . ' €</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['tienthuexe'],2). '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['chiphikhac']) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['nopthieu_laixe'],2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['nopthua_laixe'],2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;border-right: 1px solid;">' . $item['ghichu'] . '</td>
			</tr>
			';
            }
            $output .= '</tbody>';
            $output .= '</table><br>';

        }

        if($id_ubericht_history == false){
            //add
            $this->m_voxy_package_orders->add_history($ngay_chuyen_hang, $shipper_id, $data_order, $data_order_old, $data_driving_cost);
        }else{
            $this->m_voxy_package_orders->update_history($id_ubericht_history, $ngay_chuyen_hang, $shipper_id, $data_order, $data_order_old, $data_driving_cost);
        }
        //var_dump($output);die;
        return $output;
    }

    //history of ubersicht table, ubersicht_history
    public function print_money_day_history($ngay_chuyen_hang, $shipper_id)
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_kunden');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_chiphi_laixe');
        $this->load->model('m_voxy_shippers');

        $this->db->select('*');
        $this->db->from('voxy_ubersicht_history');
        $this->db->order_by('id','desc');
        if ($shipper_id != "") {
            $this->db->where_in('shipper_id', $shipper_id);
        }

        if($ngay_chuyen_hang != ""){
            $this->db->where('liefer_datum', $ngay_chuyen_hang);
        }

        $query = $this->db->get();

        // var_dump($this->db->last_query());die;

        $__data = $query->result_array();
        if(!$__data){
            echo "Không có dữ liệu lịch sử của Übersicht  vào ngày này, kiểm tra lại thông tin ngày tháng và lái xe. Danke";die;
        }

        $output = "";

        foreach ($__data as $items){
            $output .= "<p style='font-family: DejaVu Sans'>Lịch sử ngày : " .$items['date_save']." </p>";
            $data_orders = array();
            foreach (json_decode($items['information_orders']) as $orders){
                $orders = get_object_vars($orders);
                $data_orders[] = $orders;
            }

            $data_orders_old = array();
            if($items['information_orders_old'] != "" && $items['information_orders_old'] != "false"){
                foreach (json_decode($items['information_orders_old']) as $old){
                    $old = get_object_vars($old);
                    $data_orders_old[] = $old;
                }
            }


            $driving_costs = array();

            if($items['driving_costs'] != "" && $items['driving_costs'] != "false"){
                foreach (json_decode($items['driving_costs']) as $cost){
                    $cost = get_object_vars($cost);
                    $driving_costs[] = $cost;
                }
            }

            $output .= '<table width="100%" style="border-collapse: collapse;page-break-inside: auto;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
                <thead style="border: 1px solid;">
                    <tr>
                                <th style="border: 1px solid;">Order</th>
                                <th style="border: 1px solid;">Khách Hàng</th>
                                <th style="border: 1px solid;">Doanh Thu</th>
                                <th style="border: 1px solid;">DT Thực</th>
                                <th style="border: 1px solid;">Đã Thu</th>
                                <th style="border: 1px solid;">Còn Nợ</th>
                                <th style="border: 1px solid;">Ghi chú</th>
                            </tr>
                </thead>         
                ';

            $check = false;

            $output .= '<tbody>';

            $total_price = 0;
            $tongtien_thu = 0;
            $tongtien_no = 0;
            $tong_tongtienno = 0;
            $tong_tongtienthu = 0;
            $list_kunden = array();
            $list_order_number = array();
            $list_time_lan1 = array();

            $tong_doanhthu_truoc = 0;

            foreach ($data_orders as $item) {

                //get all note trong order ra ngoai
                $line_items = json_decode($item['line_items']);

                $list_note = array();
                foreach ($line_items as $key2 => $arr){
                    $arr = get_object_vars($arr);
                    if($arr['item_note'] != ""){
                        $item['note'] .= "<br>"."-". $arr['item_note'];
                    }
                }

                if($item['hangve'] != "" || $item['hangthieu'] != "" || $item['hanghong'] != "" || $item['hangthem'] != ""){
                    $check = true;
                }

                $list_kunden[] = $item['customer_id'];
                $list_order_number[] = $item['order_number'];
                if($ngay_chuyen_hang != ""){
                    $list_time_lan1[] = $ngay_chuyen_hang;
                }

                $total_price += $item['total_price'];

                $tongtien_thu = (float)$item['thanhtoan_lan1'] + (float)$item['thanhtoan_lan2'] + (float)$item['thanhtoan_lan3'] + (float)$item['thanhtoan_lan4'] + (float)$item['thanhtoan_lan5'];
                $tongtien_no = $item['tongtien_no'];
                if($tongtien_thu == ""){
                    $tongtien_thu_print = " ";
                }else{
                    $tongtien_thu_print = number_format($tongtien_thu,2) ." €";
                }

                $tong_tongtienthu += $tongtien_thu;
                $tong_tongtienno += $tongtien_no;

                if($tongtien_no == ""){
                    //$tongtien_no_print =  $item['total_price'] ." €";
                    $tongtien_no_print =  0 ." €";
                }else if($tongtien_no == 0){
                    $tongtien_no_print = " ";
                }else{
                    $tongtien_no_print = number_format($tongtien_no,2) ." €";
                }
                if(json_decode($item['customer'])){
                    $json_customer = get_object_vars(json_decode($item['customer']));
                }

                if(isset($item['key_word_customer']) && $item['key_word_customer'] != ""){
                    $key_word = $item['key_word_customer'];
                }else{
                    $key_word = null;
                }

                //get id khachhang
                $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($item['customer_id']);

                if (isset($json_customer['d_first_name'])) {
                    $frist_name = $json_customer['d_first_name'];
                } elseif (isset($json_customer['first_name'])) {
                    $frist_name = $json_customer['first_name'];
                }else {
                    $frist_name = "";
                }

                if (isset($json_customer['d_last_name'])) {
                    $last_name = $json_customer['d_last_name'];
                } elseif (isset($json_customer['last_name'])) {
                    $last_name = $json_customer['last_name'];
                } else {
                    $last_name = "";
                }

                if($key_word != null){
                    $customer_name = $key_word ;
                }else{
                    $customer_name = $frist_name . " " . $last_name;
                }

                if ($json_customer) {
                    if(isset($json_customer['phone'])){
                        if (strlen($json_customer['phone']) > 11) {
                            $array_phone = explode("/", $json_customer['phone']);
                            $phone = "";
                            foreach ($array_phone as $key => $_phone) {
                                $phone .= $_phone . "<br>";
                            }
                        }
                    }
                    $adresse = $json_customer['address1'] . " " . $json_customer['zip'] . " " . $json_customer['city'];
                }

                $datum = $item['created_time'];
                $date = date_create($datum);
                $datum = date_format($date, 'd-m-Y');

                $doanhthu_truoc = $this->m_voxy_package_orders->get_doanhthu_truoc($item['order_number']);
                if($doanhthu_truoc != false){
                    $doanhthu_truoc_print = number_format($doanhthu_truoc,2). " €";
                }else{
                    $doanhthu_truoc = 0;
                    $doanhthu_truoc_print = "0 €";
                }

                $tong_doanhthu_truoc += $doanhthu_truoc;

                $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
				    ' . $id_khachang . "-".$customer_name . '
				</td>
				
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid;text-align: right;">' . $doanhthu_truoc_print. '</td>
				
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . number_format($item['total_price'], 2) . " €" . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . $tongtien_thu_print . '
				</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . $tongtien_no_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $item['note']. '</td>
			</tr>
			';
            }

            $list_kunden = array_unique($list_kunden);
            $list_time_lan1 = array_unique($list_time_lan1);
            //var_dump($list_order_number);die;

            if($list_time_lan1){
                $data_lastime = $data_orders_old;
            }

            if(isset($data_lastime) && $data_lastime != false){
                $lastime_total_price = 0 ;
                $lastime_total_dathu = 0 ;
                $lastime_total_no = 0 ;
                foreach ($data_lastime as $item){

                    if(isset($item['key_word_customer']) && $item['key_word_customer'] != ""){
                        $key_word = $item['key_word_customer'];
                    }else{
                        $key_word = null;
                    }

                    //get id khachhang
                    $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($item['customer_id']);

                    if (isset($json_customer['d_first_name'])) {
                        $frist_name = $json_customer['d_first_name'];
                    } elseif (isset($json_customer['first_name'])) {
                        $frist_name = $json_customer['first_name'];
                    }else {
                        $frist_name = "";
                    }

                    if (isset($json_customer['d_last_name'])) {
                        $last_name = $json_customer['d_last_name'];
                    } elseif (isset($json_customer['last_name'])) {
                        $last_name = $json_customer['last_name'];
                    } else {
                        $last_name = "";
                    }

                    if($key_word != null){
                        $customer_name = $key_word ;
                    }else{
                        $customer_name = $frist_name . " " . $last_name;
                    }

                    if($item['thanhtoan_lan1'] == 0 || $item['thanhtoan_lan1'] == null){
                        $thanhtoan_lan1_print = " ";
                    }else{
                        $thanhtoan_lan1_print = number_format($item['thanhtoan_lan1'],2)." €";
                    }

                    if($item['thanhtoan_lan2'] == 0 || $item['thanhtoan_lan2'] == null){
                        $thanhtoan_lan2_print = " ";
                    }else{
                        $thanhtoan_lan2_print = number_format($item['thanhtoan_lan2'],2)." €";
                    }

                    if($item['thanhtoan_lan3'] == 0 || $item['thanhtoan_lan3'] == null){
                        $thanhtoan_lan3_print = " ";
                    }else{
                        $thanhtoan_lan3_print = number_format($item['thanhtoan_lan3'],2)." €";
                    }

                    if($item['thanhtoan_lan4'] == 0 || $item['thanhtoan_lan4'] == null){
                        $thanhtoan_lan4_print = " ";
                    }else{
                        $thanhtoan_lan4_print = number_format($item['thanhtoan_lan4'],2)." €";
                    }

                    if($item['thanhtoan_lan5'] == 0 || $item['thanhtoan_lan5'] == null){
                        $thanhtoan_lan5_print = " ";
                    }else{
                        $thanhtoan_lan5_print = number_format($item['thanhtoan_lan5'],2)." €";
                    }

                    if($item['tongtien_no'] == 0 || $item['tongtien_no'] == null){
                        $tongtien_no_lan1_print = " ";
                    }else{
                        $tongtien_no_lan1_print = number_format($item['tongtien_no'],2) ." €";
                    }
//				<td style="text-align: right;">' . number_format($item['total_price'], 2) . " €" . '</td>
                    if($item['note'] == ""){
                        $item['note'] = "Nợ cũ";
                    }

                    $your_string = $item['shipped_at'];
                    $item['shipped_at'] = date("d-m", strtotime($your_string));

                    $lastime_total_price += $item['total_price'];
                    $lastime_total_no += $item['tongtien_no'];

                    if($item['time_lan1'] == $ngay_chuyen_hang){
                        $lastime_total_dathu += $item['thanhtoan_lan1'];

                        $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan1_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                    }

                    if($item['time_lan2'] != "" and $item['time_lan2'] == $ngay_chuyen_hang){
                        $lastime_total_dathu += $item['thanhtoan_lan2'];

                        $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan2_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                    }

                    if($item['time_lan3'] != "" and $item['time_lan3'] == $ngay_chuyen_hang){
                        $lastime_total_dathu += $item['thanhtoan_lan3'];

                        $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan3_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                    }

                    if($item['time_lan4'] != "" and $item['time_lan4'] == $ngay_chuyen_hang){
                        $lastime_total_dathu += $item['thanhtoan_lan4'];

                        $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan4_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                    }

                    if($item['time_lan5'] != "" and $item['time_lan5'] == $ngay_chuyen_hang){
                        $lastime_total_dathu += $item['thanhtoan_lan5'];

                        $output .= '
                    <tr style="font-style: italic;">
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;">< style="color: red"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan5_print . '</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;"></span></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
                    </tr>
                    ';
                    }

                }
            }

            if(!isset($lastime_total_price)){
                $lastime_total_price = 0;
            }

            if(!isset($lastime_total_dathu)){
                $lastime_total_dathu = 0;
            }

            if(!isset($lastime_total_no)){
                $lastime_total_no = 0;
            }

            //them chi phi lai xe vào table
            if($shipper_id) {
                $data_chiphi_laixe = $driving_costs;

                if($data_chiphi_laixe){
                    $chi_phi_lai_xe = $data_chiphi_laixe[0]['tongchiphi'];
                    $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">Trừ chi phí lái xe</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;border-right: 1px solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($data_chiphi_laixe[0]['tongchiphi'], 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;border-right: 1px solid;"></td>
                        
                    </tr>
        ';
                }else{
                    $chi_phi_lai_xe = 0;
                }

            }

//end them chi phi lai xe vào table

            $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>Tổng</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($tong_doanhthu_truoc, 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($total_price, 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($tong_tongtienthu + $lastime_total_dathu - $chi_phi_lai_xe, 2) . " €" . '</b></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($tong_tongtienno , 2) . " €" . '</b></td>
                        <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
                    </tr>
        ';

            $output .= '</tbody>';
            $output .= '</table><br>';

            if($check == true){
                $output .= "<p style='font-family: DejaVu Sans;text-align: center;font-weight: bold' >Chi tiết mặt hàng đã thay đổi</p>";
                //them phan hang ve, hong ,thieu, them
                $output .= '<table width="100%" style="border-collapse: collapse;page-break-inside: auto;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
                <thead style="border: 1px solid;">
                    <tr>
                                <th style="border: 1px solid;">Order</th>
                                <th style="border: 1px solid;">Sản phẩm</th>
                                <th style="border: 1px solid;">Trạng thái</th>
                                <th style="border: 1px solid;">Số lượng</th>
                                <th style="border: 1px solid;">Đơn vị</th>
                                <th style="border: 1px solid;">Thành tiền</th>
                            </tr>
                </thead>         
                ';

                $output .= '<tbody>';
                foreach ($data_orders as $key => $item) {
                    //hangve
                    if($item['hangve'] != null || $item['hangve'] != ""){
                        $array = json_decode($item['hangve']);

                        foreach ($array as $row){
                            $row = get_object_vars($row);

                            $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                            $information = $this->m_voxy_package->get_all_infor($id);
                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                            $title = $information[0]['title'];
                            if($check_variant1 == true){
                                $variant_title = $information[0]['option1'];
                            }
                            if($check_variant2 == true){
                                $variant_title = $information[0]['option2'];
                            }
                            $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #1a8eed">hàng về</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                        }
                    }
                    //end hangve

                    //hanghong
                    if($item['hanghong'] != null || $item['hanghong'] != ""){
                        $array = json_decode($item['hanghong']);

                        foreach ($array as $row){
                            $row = get_object_vars($row);
                            $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                            $information = $this->m_voxy_package->get_all_infor($id);
                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                            $title = $information[0]['title'];
                            if($check_variant1 == true){
                                $variant_title = $information[0]['option1'];
                            }
                            if($check_variant2 == true){
                                $variant_title = $information[0]['option2'];
                            }
                            $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #40cc4b">hàng hỏng</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                        }
                    }
                    //end hanghong

                    //hangthieu
                    if($item['hangthieu'] != null || $item['hangthieu'] != ""){
                        $array = json_decode($item['hangthieu']);

                        foreach ($array as $row){
                            $row = get_object_vars($row);
                            $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                            $information = $this->m_voxy_package->get_all_infor($id);
                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                            $title = $information[0]['title'];
                            if($check_variant1 == true){
                                $variant_title = $information[0]['option1'];
                            }
                            if($check_variant2 == true){
                                $variant_title = $information[0]['option2'];
                            }
                            $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #7c10cc">hàng thiếu</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                        }
                    }
                    //end hangthieu

                    //hangthem
                    if($item['hangthem'] != null || $item['hangthem'] != ""){
                        $array = json_decode($item['hangthem']);

                        foreach ($array as $row){
                            $row = get_object_vars($row);
                            $id = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
                            $information = $this->m_voxy_package->get_all_infor($id);
                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                            $title = $information[0]['title'];
                            if($check_variant1 == true){
                                $variant_title = $information[0]['option1'];
                            }
                            if($check_variant2 == true){
                                $variant_title = $information[0]['option2'];
                            }
                            $output .= '
                    <tr>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $item['order_number'] . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: #8d313d">hàng thêm</span></td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['sl_nhap'] . '
                        </td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $variant_title . '</td>
                        <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-right: 1px solid">' . $row['thanhtien'] ." €". '</td>
                    </tr>
                 ';
                        }
                    }
                    //end hangthem

                }
                $output .= '</tbody>';
                $output .= '</table><br>';
                //end  them phan hang ve, hong ,thieu, them
            }

            if($data_chiphi_laixe){
                $output .= "<p style='font-family: DejaVu Sans;text-align: center;font-weight: bold' >Chi tiết chi phí lái xe</p>";
                $output .= '<table width="100%" style="border-collapse: collapse;page-break-inside: auto;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
<thead style="border: 1px solid;">
    <tr>
                <th style="border: 1px solid;">Lái xe</th>
                <th style="border: 1px solid;">Tiền xăng</th>
                <th style="border: 1px solid;">Tiền thuê xe</th>
                <th style="border: 1px solid;">Chi phí khác</th>
                <th style="border: 1px solid;">Nộp thiếu</th>
                <th style="border: 1px solid;">Nộp thừa</th>
                <th style="border: 1px solid;">Ghi Chú</th>
            </tr>
</thead>         
';
                $output .= '<tbody>';

                foreach ($data_chiphi_laixe as $item) {
                    $laixe_name = $this->m_voxy_shippers->get_name($item['laixe_id']);
                    $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $laixe_name . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['tienxang'],2) . ' €</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['tienthuexe'],2). '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['chiphikhac']) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['nopthieu_laixe'],2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . number_format($item['nopthua_laixe'],2) . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;border-right: 1px solid;">' . $item['ghichu'] . '</td>
			</tr>
			';
                }
                $output .= '</tbody>';
                $output .= '</table><br>';

            }

            $output .= '<style>
                               .page-break{
                                     page-break-after: always;
                               }
                        </style>';
            $output .= '<div class="page-break"></div>';
        }

        //var_dump($output);die;
        return $output;
    }

    //thông tin khách hàng và lái xe excel
    public function print_money_day_excel($ngay_chuyen_hang, $shipper_id)
    {
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_package_kunden');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        //cau lenh sql hier
        //$this->db->select('id');
        //$this->db->select('line_items');
        $this->db->select('order_number');
        $this->db->select('customer');
        $this->db->select('customer_id');
        $this->db->select('note');
        $this->db->select('key_word_customer');
        $this->db->select('created_time');
        $this->db->select('total_price');
        $this->db->select('total_price_before');
        $this->db->select('thanhtoan_lan1');
        $this->db->select('thanhtoan_lan2');
        $this->db->select('thanhtoan_lan3');
        $this->db->select('thanhtoan_lan4');
        $this->db->select('thanhtoan_lan5');
        $this->db->select('tongtien_no');

        $this->db->select('time_lan1');
        $this->db->select('note');

        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        $this->db->like('shipped_at', $ngay_chuyen_hang);
        $this->db->order_by('id','desc');
        if ($shipper_id != "") {
            $this->db->where_in('shipper_id', $shipper_id);
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();
        return $data;
    }

    public function get_list_order($ngay_chuyen_hang, $shipper_id)
    {
        $this->load->model('m_voxy_package_orders');
        $this->db->select('id');
        $this->db->select('created_time');
        $this->db->select('shipped_at');
        $this->db->select('order_number');
        $this->db->select('customer');
        $this->db->select('shipper_name');
        $this->db->select('note');
        $this->db->select('total_price');
        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        $this->db->like('shipped_at', $ngay_chuyen_hang);
        if ($shipper_id != "") {
            $this->db->where_in('shipper_id', $shipper_id);
        }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if ($query->result_array()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    //table bao cáo  kinh doanh , để check xem lỗ lãi theo từng đơn hàng như nào
    public function pdf_order_kinhdoanh($oder_number)
    {
        $this->load->model('m_voxy_package');

        $this->db->select('line_items');
        $this->db->select('hanghong');
        $this->db->select('hangve');
        $this->db->select('hangthieu');
        $this->db->select('hangthem');
        $this->db->select('order_number');
        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $oder_number);
        $query = $this->db->get();
        $data = $query->result_array();

        //var_dump($this->db->last_query());die;
        $_export = array();

        $hanghong = $data[0]['hanghong'];
        $hangve = $data[0]['hangve'];
        $hangthieu = $data[0]['hangthieu'];
        $hangthem = $data[0]['hangthem'];

        $i = 0;
        //get nur array of items
        if($data[0]['line_items'] != "" || $data[0]['line_items'] != null){
            foreach (json_decode($data[0]['line_items']) as $item) {
                $_export[] = get_object_vars($item);
            }
        }

        $wek= array();
        foreach ($_export as $key => $row) {
            $wek[$key] = $row['title'];
        }
        // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
        array_multisort($wek, SORT_ASC, $_export);

        $output = "
          <div style='text-align: left; font-family:DejaVu Sans;font-size: 13px'><b>Lieferschein</b></div>
        ";
        $output .= '<table width="100%" style="page-break-inside: auto; border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
<thead style="border: 1px solid;">
    <tr>
                <th style="border: 1px solid;">Nr</th>
                <th style="border: 1px solid;">SKU</th>
                <th style="width:50%;border: 1px solid;">Mặt hàng</th>
                <th style="border: 1px solid;">SL</th>
                <th style="border: 1px solid;">Về</th>
                <th style="border: 1px solid;">Hỏng</th>
                <th style="border: 1px solid;">Thiếu</th>
                <th style="border: 1px solid;">Thêm</th>
                <th style="border: 1px solid;">SL.Cuối</th>
                <th style="border: 1px solid;">Đơn vị</th>
                <th style="border: 1px solid;">Giá vốn</th>
                <th style="border: 1px solid;">Giá bán</th>
                <th style="border: 1px solid;">Vốn</th>
                <th style="border: 1px solid;">D.Thu</th>
                <th style="border: 1px solid;">LN.gộp</th>
                <th style="border: 1px solid;">LN.%</th>
            </tr>
</thead>         
';
        $output .= '<tbody style="border: 1px solid;">';
        $id = 0;
        
        $total_price = 0;
        $netto = 0;
        $tongloinhuan = 0;
        $tongvon = 0;
        
        foreach ($_export as $row) {
            $id++;
            if(!isset($row['hangve'])){
                $row['hangve'] = 0;
                $hangve_print = "";
            }else{
                $hangve_print = $row['hangve'];
            }
            if($hangve_print == 0){
                $hangve_print = "";
            }

            if(!isset($row['hanghong'])){
                $row['hanghong'] = 0;
                $hanghong_print = "";
            }else{
                $hanghong_print = $row['hanghong'];
            }
            if($hanghong_print == 0){
                $hanghong_print = "";
            }

            if(!isset($row['hangthieu'])){
                $row['hangthieu'] = 0;
                $hangthieu_print = "";
            }else{
                $hangthieu_print = $row['hangthieu'];
            }
            if($hangthieu_print == 0){
                $hangthieu_print = "";
            }

            if(!isset($row['hangthem'])){
                $row['hangthem'] = 0;
                $hangthem_print = "";
            }else{
                $hangthem_print = $row['hangthem'];
            }
            if($hangthem_print == 0){
                $hangthem_print = "";
            }

            $sl_cuoicung = $row['quantity'] - $row['hangve'] - $row['hanghong']- $row['hangthieu'] + $row['hangthem'];

            $total_price += $row['price'] * $sl_cuoicung;
            $gesamt =  $row['price'] * $sl_cuoicung;

            $mwst = 7;
            if(isset($row['sku'])){
                $mwst = $this->m_voxy_package->get_mwst($row['sku']);
            }
            if($mwst == false){
                $mwst = 7;
            }
            $netto += $gesamt/(($mwst/100) + 1);

            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);

            if(isset($row['variant_id']) && $row['variant_id'] != ""){
                $idnew = $this->m_voxy_package->get_id_from_variant($row['variant_id']);
            }else {
                $idnew = false;
            }

            if ($check_variant1 == true) {
                //$this->m_voxy_package->update_plus_inventory1($item['sl_nhap'], $id);//in DB
                //gia von la gia mua
                if($idnew != false){
                    $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                }else{
                    $giavon = 0;
                }

            }
            if ($check_variant2 == true) {
                if($idnew != false){
                    $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                }else{
                    $giavon = 0;
                }
            }

            if(!isset($giavon)){
                $giavon = 0;
            }

            $tongvon += (double)$giavon * (double)$sl_cuoicung;
            $loinhuan = ($row['price'] - (double)$giavon) * (double)$sl_cuoicung;//doanhthu
            $tongloinhuan += $loinhuan;

            if($loinhuan == 0 || $row['price'] == 0){
                $phantram = 0;
            }else{
                $phantram = number_format(($row['price']-$giavon)*100/$row['price'],1);
            }

            $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $id . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;">' . $row['sku'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;">' . $row['title'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $row['quantity'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $hangve_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $hanghong_print. '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $hangthieu_print . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $hangthem_print. '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . $sl_cuoicung . '</b></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;font-size: 11px">' . $row['variant_title'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $giavon . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $row['price'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $giavon *  $sl_cuoicung . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $row['price'] *  $sl_cuoicung. '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>' . $loinhuan . '</b></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $phantram . '</td>
			</tr>
			';
        }
        if($tongloinhuan == 0){
            $tong_phantam = 0;
        }else{
            if($total_price == 0){
                $tong_phantam = 0;
            } else {
                $tong_phantam = number_format(($tongloinhuan/$total_price)*100,1);
            }
        }

        $output .= "
                    <tr>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>Tổng</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$tongvon</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$total_price</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$tongloinhuan</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;border-right: 0.01mm solid;'><b>$tong_phantam</b></td>
                    </tr>
                    <br>
        ";
        $output .= '</tbody>';
        $output .= '</table><br>';

        /*

        if($hanghong != null && $hanghong != ""){
            $k = 0;
            $output .= "<b style='font-family: DejaVu Sans;font-size: 13px'>Hàng hỏng</b>";

            $total_price_hanghong = 0;
            $tongloinhuan_hanghong = 0;
            $tongvon_hanghong = 0;
            
            $output .= '
                <table  width="100%" style="border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px">
                <thead style="border: 1px solid;">
                     <tr>
                        <th style="border: 1px solid;">Nr</th>
                        <th style="border: 1px solid;">SKU</th>
                        <th style="width:50%;border: 1px solid;">Mặt hàng</th>
                        <th style="border: 1px solid;">SL</th>
                        <th style="border: 1px solid;">Đơn vị</th>
                        <th style="border: 1px solid;">Giá vốn</th>
                        <th style="border: 1px solid;">Giá bán</th>
                        <th style="border: 1px solid;">Vốn</th>
                        <th style="border: 1px solid;">Thành tiền</th>
                     </tr>
                </thead>
            ';
            foreach (json_decode($hanghong) as $item){
                $item = get_object_vars($item);
                $k++;
                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

                if(isset($item['variant_id']) && $item['variant_id'] != ""){
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                }else {
                    $idnew = false;
                }

                if ($check_variant1 == true) {
                    //GET SKU,get don vi ,gia ban, thanh tien.
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }

                    $item['quantity'] = $item['sl_nhap'];
                    //gia von la gia mua
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    }else{
                        $giavon = 0;
                    }

                }
                if ($check_variant2 == true) {
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }
                    $item['quantity'] = $item['sl_nhap'];
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    }else{
                        $giavon = 0;
                    }
                }

                $item['price'] = $item['thanhtien'] / $item['sl_nhap'];

                $tongvon_hanghong += (double)$giavon * (int)$item['sl_nhap'];
                $loinhuan_hanghong = ($item['price'] - (double)$giavon)* (int)$item['sl_nhap'];

                $tongloinhuan_hanghong += $loinhuan_hanghong;

                if($item['price'] == 0){
                    $phantram_hanghong = "n/a";
                }else{
                    $phantram_hanghong = number_format(($item['price']-$giavon)*100/$item['price'],1);
                }

                $total_price_hanghong += $item['price'] * (int)$item['sl_nhap'];

                $output .= '
                <tr>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $k . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['sku'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;">' . $item['title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['quantity'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['variant_title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;"><b>' . $giavon . '</b></td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . (double)$giavon * (int)$item['sl_nhap'] . '</td>
                    <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] * $item['sl_nhap'] . '</td>
                    
                </tr>
			';

            }

//            <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $loinhuan_hanghong . '</td>
//                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;border-right: 0.01mm solid;">' . $phantram_hanghong . '</td>

            $tong_phantam_hanghong = number_format(($total_price_hanghong-$tongvon_hanghong)*100/$total_price_hanghong,1);
            $output .= "
                    <tr>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>Tổng</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$tongvon_hanghong</b></td>
                        <td style='border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$total_price_hanghong</b></td>
                    </tr>
                    <br>
        ";

//            <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$tongloinhuan_hanghong</b></td>
//                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;border-right: 0.01mm solid;'><b>$tong_phantam_hanghong</b> %</td>

            $output .= "</table>";
        }//end hanghong

        if($hangve != null && $hangve != ""){
            $k = 0;
            $output .= "<b style='font-family: DejaVu Sans;font-size: 13px'>Hàng Trả Về</b>";

            $output .= '
                <table  width="100%" style="border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px">
                <thead style="border: 1px solid;">
                     <tr>
                        <th style="border: 1px solid;">Nr</th>
                        <th style="border: 1px solid;">SKU</th>
                        <th style="width:50%;border: 1px solid;">Mặt hàng</th>
                        <th style="border: 1px solid;">SL</th>
                        <th style="border: 1px solid;">Đơn vị</th>
                        <th style="border: 1px solid;">Giá vốn</th>
                        <th style="border: 1px solid;">Giá bán</th>
                        <th style="border: 1px solid;">Vốn</th>
                        <th style="border: 1px solid;">Thành tiền</th>
                     </tr>
                </thead>
            ';

            $total_price_hangve = 0;
            $tongloinhuan_hangve = 0;
            $tongvon_hangve = 0;
            
            foreach (json_decode($hangve) as $item){
                $item = get_object_vars($item);
                $k++;
                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

                if(isset($item['variant_id']) && $item['variant_id'] != ""){
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                }else {
                    $idnew = false;
                }

                if ($check_variant1 == true) {
                    //GET SKU,get don vi ,gia ban, thanh tien.
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }

                    $item['quantity'] = $item['sl_nhap'];
                    //gia von la gia mua
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    }else{
                        $giavon = 0;
                    }

                }
                if ($check_variant2 == true) {
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }
                    $item['quantity'] = $item['sl_nhap'];
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    }else{
                        $giavon = 0;
                    }
                }

                $item['price'] = $item['thanhtien'] / $item['sl_nhap'];

                $tongvon_hangve += (double)$giavon * (int)$item['sl_nhap'];
                $loinhuan_hangve = ($item['price'] - (double)$giavon)* (int)$item['sl_nhap'];

                $tongloinhuan_hangve += $loinhuan_hangve;


                if($item['price'] == 0){
                    $phantram_hangve = "n/a";
                }else{
                    $phantram_hangve = number_format(($item['price']-$giavon)*100/$item['price'],1);
                }

                $total_price_hangve += $item['price'] * (int)$item['sl_nhap'];

                $output .= '
                <tr>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $k . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['sku'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;">' . $item['title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['quantity'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['variant_title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;"><b>' . $giavon . '</b></td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . (double)$giavon * (int)$item['sl_nhap'] . '</td>
                    <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] * $item['sl_nhap'] . '</td>
                </tr>
			';

            }

            $tong_phantam_hangve = number_format(($total_price_hangve-$tongvon_hangve)*100/$total_price_hangve,1);
            $output .= "
                    <tr>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>Tổng</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$tongvon_hangve</b></td>
                        <td style='border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$total_price_hangve</b></td>
                    </tr>
                    <br>
        ";
            $output .= "</table>";

        }//end hangve

        if($hangthieu != null && $hangthieu != ""){
            $k = 0;
            $output .= "<b style='font-family: DejaVu Sans;font-size: 13px'>Hàng Thiếu</b>";

            $output .= '
                <table  width="100%" style="border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px">
                <thead style="border: 1px solid;">
                     <tr>
                        <th style="border: 1px solid;">Nr</th>
                        <th style="border: 1px solid;">SKU</th>
                        <th style="width:50%;border: 1px solid;">Mặt hàng</th>
                        <th style="border: 1px solid;">SL</th>
                        <th style="border: 1px solid;">Đơn vị</th>
                        <th style="border: 1px solid;">Giá vốn</th>
                        <th style="border: 1px solid;">Giá bán</th>
                        <th style="border: 1px solid;">Vốn</th>
                        <th style="border: 1px solid;">Thành tiền</th>
                     </tr>
                </thead>
            ';

            $total_price_hangthieu = 0;
            $tongloinhuan_hangthieu = 0;
            $tongvon_hangthieu = 0;

            foreach (json_decode($hangthieu) as $item){
                $item = get_object_vars($item);
                $k++;
                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

                if(isset($item['variant_id']) && $item['variant_id'] != ""){
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                }else {
                    $idnew = false;
                }

                if ($check_variant1 == true) {
                    //GET SKU,get don vi ,gia ban, thanh tien.
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }

                    $item['quantity'] = $item['sl_nhap'];
                    //gia von la gia mua
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    }else{
                        $giavon = 0;
                    }

                }
                if ($check_variant2 == true) {
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }
                    $item['quantity'] = $item['sl_nhap'];
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    }else{
                        $giavon = 0;
                    }
                }

                $item['price'] = $item['thanhtien'] / $item['sl_nhap'];

                $tongvon_hangthieu += (double)$giavon * (int)$item['sl_nhap'];
                $loinhuan_hangthieu = ($item['price'] - (double)$giavon)* (int)$item['sl_nhap'];

                $tongloinhuan_hangthieu += $loinhuan_hangthieu;

                if($item['price'] == 0){
                    $phantram_thieu = "n/a";
                }else{
                    $phantram_thieu = number_format(($item['price']-$giavon)*100/$item['price'],1);
                }

                $total_price_hangthieu += $item['price'] * $item['sl_nhap'];

                $output .= '
                <tr>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $k . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['sku'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;">' . $item['title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['quantity'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['variant_title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;"><b>' . $giavon . '</b></td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . (double)$giavon * (int)$item['sl_nhap'] . '</td>
                    <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] * $item['sl_nhap'] . '</td>
                </tr>
			';

            }

            $tong_phantam_hangthieu = number_format(($total_price_hangthieu-$tongvon_hangthieu)*100/$total_price_hangthieu,1);
            $output .= "
                    <tr>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>Tổng</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$tongvon_hangthieu</b></td>
                        <td style='border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$total_price_hangthieu</b></td>
                    </tr>
                    <br>
        ";

            $output .= "</table>";

        }//end hangthieu

        //hangthem
        if($hangthem != null && $hangthem != ""){
            $k = 0;
            $output .= "<b style='font-family: DejaVu Sans;font-size: 13px'>Hàng Thêm</b>";

            $total_price_hangthem = 0;
            $tongloinhuan_hangthem = 0;
            $tongvon_hangthem = 0;
            $output .= '
                <table  width="100%" style="border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px">
                <thead style="border: 1px solid;">
                     <tr>
                        <th style="border: 1px solid;">Nr</th>
                        <th style="border: 1px solid;">SKU</th>
                        <th style="border: 1px solid;">Mặt hàng</th>
                        <th style="border: 1px solid;">SL</th>
                        <th style="border: 1px solid;">Đơn vị</th>
                        <th style="border: 1px solid;">Giá vốn</th>
                        <th style="border: 1px solid;">Giá bán</th>
                        <th style="border: 1px solid;">Vốn</th>
                        <th style="border: 1px solid;">Thành tiền</th>
                     </tr>
                </thead>
            ';
            foreach (json_decode($hangthem) as $item){
                $item = get_object_vars($item);
                $k++;
                $check_variant1 = $this->m_voxy_package->check_variant1($item['variant_id']);
                $check_variant2 = $this->m_voxy_package->check_variant2($item['variant_id']);

                if(isset($item['variant_id']) && $item['variant_id'] != ""){
                    $idnew = $this->m_voxy_package->get_id_from_variant($item['variant_id']);
                }else {
                    $idnew = false;
                }

                if ($check_variant1 == true) {
                    //GET SKU,get don vi ,gia ban, thanh tien.
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }

                    $item['quantity'] = $item['sl_nhap'];
                    //gia von la gia mua
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_le($idnew);
                    }else{
                        $giavon = 0;
                    }

                }
                if ($check_variant2 == true) {
                    $data_get = $this->m_voxy_package->get_all_infor($idnew);
                    foreach ($data_get as $item2){
                        $item['sku'] = $item2['sku2'];
                        $item['title'] = $item2['title'];
                        $item['variant_title'] = $item2['option2'];
                    }
                    $item['quantity'] = $item['sl_nhap'];
                    if($idnew != false){
                        $giavon = $this->m_voxy_package->get_gia_mua_si($idnew);
                    }else{
                        $giavon = 0;
                    }
                }

                $item['price'] = $item['thanhtien'] / $item['sl_nhap'];
                $tongvon_hangthem += (double)$giavon * (int)$item['sl_nhap'];
                $loinhuan_hangthem = ($item['price'] - (double)$giavon)* (int)$item['sl_nhap'];

                $tongloinhuan_hangthem += $loinhuan_hangthem;

                if($item['price'] == 0){
                    $phantram_hangthem = "n/a";
                }else{
                    $phantram_hangthem = number_format(($item['price']-$giavon)*100/$item['price'],1);
                }

                $total_price_hangthem += $item['price'] * (int)$item['sl_nhap'];

                $output .= '
                <tr>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $k . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['sku'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;border-top: 0.01mm solid;">' . $item['title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['quantity'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['variant_title'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;"><b>' . $giavon . '</b></td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] . '</td>
                    <td style="border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . (double)$giavon * (int)$item['sl_nhap'] . '</td>
                    <td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;">' . $item['price'] * $item['sl_nhap'] . '</td>
                </tr>
			';

            }

            $tong_phantam_hangthem = number_format(($total_price_hangthem-$tongvon_hangthem)*100/$total_price_hangthem,1);
            $output .= "
                    <tr>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>Tổng</b></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;width: 50%;text-align: left;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'></td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$tongvon_hangthem</b></td>
                        <td style='border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'><b>$total_price_hangthem</b></td>
                    </tr>
                    <br>
        ";

            $output .= "</table>";
        }//end hangthem

//var_dump($output);die;
        //$steuer = $total_price - $netto;

        if(!isset($total_price_hanghong)){
            $total_price_hanghong = 0;
        }

        if(!isset($total_price_hangve)){
            $total_price_hangve = 0;
        }
        if(!isset($total_price_hangthieu)){
            $total_price_hangthieu = 0;
        }
        if(!isset($tong_vonhangve)){
            $tong_vonhangve = 0;
        }
        if(!isset($total_price_hanghong)){
            $total_price_hanghong = 0;
        }
        if(!isset($total_price_hanghong)){
            $total_price_hanghong = 0;
        }

        if(!isset($total_price_hangthem)){
            $total_price_hangthem = 0;
        }

        $output .= "<br><br>";

        $output .= "<b style='font-family: DejaVu Sans;font-size: 13px'>Tổng Hợp</b>";

        $output .= '
                <table  width="100%" style="border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px">
                <thead style="border: 1px solid;">
                     <tr>
                        <th style="border: 1px solid;">Doanh thu thuần</th>
                        <th style="border: 1px solid;">Vốn thuần</th>
                        <th style="border: 1px solid;">Lợi nhuận thuần</th>
                        <th style="border: 1px solid;">Lợi nhuận %</th>
                     </tr>
                </thead>
            ';

        if(!isset($tongvon_hanghong)){
            $tongvon_hanghong = 0;
        }

        if(!isset($tongvon_hangve)){
            $tongvon_hangve = 0;
        }

        if(!isset($tongvon_hangthieu)){
            $tongvon_hangthieu = 0;
        }

        if(!isset($tongvon_hangthem)){
            $tongvon_hangthem = 0;
        }

        $tonghop_doanhthu_thuan = $total_price - $total_price_hanghong - $total_price_hangve - $total_price_hangthieu + $total_price_hangthem ;
        $tonghop_vonthuan = $tongvon - $tongvon_hanghong - $tongvon_hangve - $tongvon_hangthieu + $tongvon_hangthem ;
        $tonghop_loinhuanthuan = $tonghop_doanhthu_thuan - $tonghop_vonthuan;
        if($tonghop_doanhthu_thuan == 0){
            $tonghop_loinhuan_phantram = 0;
        }else{
            $tonghop_loinhuan_phantram = number_format(($tonghop_doanhthu_thuan-$tonghop_vonthuan)*100/$tonghop_doanhthu_thuan,1);
        }

        $output .= "
                    <tr>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'>$tonghop_doanhthu_thuan</td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'>$tonghop_vonthuan</td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;'>$tonghop_loinhuanthuan</td>
                        <td style='border-left: 1px solid;border-bottom: 0.01mm solid;border-top: 0.01mm solid;border-right: 0.01mm solid;'>$tonghop_loinhuan_phantram %</td>
                    </tr>
                    <br>
        ";
        */

        $output .= "</table>";

        $output .= '<div class="note">';
        foreach ($data as $item) {
            if ($item['note'] != "") {
                $output .= '<p style="margin-top: 30px; border: 1px solid">' . $item['order_number'] . '-' . $item['note'] . '</p>';
            }
        }
        $output .= '</div>';
        //var_dump($output);die;
        return $output;
    }

    public function pdf_order_kinhdoanh_excel($oder_number){
        $this->load->model('m_voxy_package');

        $this->db->select('line_items');
        $this->db->select('hanghong');
        $this->db->select('hangve');
        $this->db->select('hangthieu');
        $this->db->select('hangthem');
        $this->db->select('order_number');
        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where("order_number", $oder_number);
        $query = $this->db->get();
        $data = $query->result_array();

        //var_dump($this->db->last_query());die;
        $_export = array();

//        $hanghong = $data[0]['hanghong'];
//        $hangve = $data[0]['hangve'];
//        $hangthieu = $data[0]['hangthieu'];
//        $hangthem = $data[0]['hangthem'];

        $i = 0;
        //get nur array of items
        if($data[0]['line_items'] != "" || $data[0]['line_items'] != null){
            foreach (json_decode($data[0]['line_items']) as $item) {
                $_export[] = get_object_vars($item);
            }
        }

        $wek= array();
        foreach ($_export as $key => $row) {
            $wek[$key] = $row['title'];
        }
        // Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
        array_multisort($wek, SORT_ASC, $_export);
        return $_export;
    }

    //bao cao tien no, quan ly don hang
    public function pdf_day_tienno($list_id_to_nhathang, $ngay_dat_hang, $ngay_chuyen_hang, $shipper_id = "",$ship_are_id)
    {
        //$order_day = $ngay_dat_hang;
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');
        $this->load->model('m_voxy_package_kunden');

        $this->db->select('id');
        $this->db->select('order_number');
        $this->db->select('total_price');
        $this->db->select('thanhtoan_lan1');
        $this->db->select('thanhtoan_lan2');
        $this->db->select('thanhtoan_lan3');
        $this->db->select('thanhtoan_lan4');
        $this->db->select('thanhtoan_lan5');
        $this->db->select('customer_id');
        $this->db->select('time_lan1');

        //$this->db->select('tongdathu');
        $this->db->select('tongtien_no');
        $this->db->select('shipper_name');
        $this->db->select('shipped_at');
        $this->db->select('customer');
        $this->db->select('key_word_customer');
        $this->db->select('shipper_name');

        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        //$this->db->where('tongtien_no != ', '');
        //$this->db->where('tongtien_no > 0 ', null,false);

        if($list_id_to_nhathang == null || $list_id_to_nhathang == ""){

            if($ngay_dat_hang && $ngay_dat_hang != ""){
                $this->db->where('shipped_at >=', $ngay_dat_hang);
            }

            if($ngay_chuyen_hang && $ngay_chuyen_hang != ""){
                $this->db->where('shipped_at <=', $ngay_chuyen_hang);
            }

            if ($shipper_id != "") {
                $this->db->where_in('shipper_id', $shipper_id);
            }

            if ($ship_are_id != "") {
                $this->db->where_in('ship_area_id', $ship_are_id);
            }
        }else{
            if($list_id_to_nhathang != ""){
                $this->db->where_in('id', $list_id_to_nhathang);
            }
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();

        $output = "
          <div style='text-align: center;margin-bottom: 10px; font-family:DejaVu Sans;font-size: 13px'><b>Báo Cáo Nợ</b></div>
        ";
        $output .= '<table width="100%" style="page-break-inside: auto; border-collapse: collapse;font-family: DejaVu Sans; text-align: center;font-size: 13px"> 
<thead style="border: 1px solid;">
    <tr>
                <th style="border: 1px solid;">STT</th>
                <th style="border: 1px solid;">Ngày giao</th>
                <th style="border: 1px solid;">Tài xế</th>
                <th style="border: 1px solid;">Đơn hàng</th>
                <th style="border: 1px solid;">Khách hàng</th>
                <th style="border: 1px solid;">Tổng tiền</th>
                <th style="border: 1px solid;">Đã Thu</th>
                <th style="border: 1px solid;">Còn nợ</th>
                <th style="border: 1px solid;">Ghi chú</th>
            </tr>
</thead>         
';
//        <th style="border: 1px solid;">TT.Lần 1</th>
//                <th style="border: 1px solid;">TT.Lần 2</th>
//                <th style="border: 1px solid;">TT.Lần 3</th>
//                <th style="border: 1px solid;">TT.Lần 4</th>
//                <th style="border: 1px solid;">TT.Lần 5</th>

        $output .= '<tbody style="border: 1px solid;">';
        $id = 0;
        $tong_total_price = 0;
        $tong_tienno = 0;
        $tong_dathu = 0;

        $list_kunden = array();
        $list_order_number = array();
        $list_time_lan1 = array();

        foreach ($data as $row) {
            //var_dump((int)$row['tongtien_no'] != 0);die;
            //if($row['tongtien_no'] != 0){
                $id++;

            $list_kunden[] = $row['customer_id'];
            $list_order_number[] = $row['order_number'];
            $list_time_lan1['ngay_dat_hang'] = $ngay_dat_hang;
            $list_time_lan1['ngay_giao_hang'] = $ngay_chuyen_hang;

                $tong_total_price += $row['total_price'];
                $tong_tienno += $row['tongtien_no'];
                if ($row['customer']) {
                    $json_customer = get_object_vars(json_decode($row['customer']));
                    if(isset($json_customer['d_first_name'])){
                        $frist_name = $json_customer['d_first_name'];
                    }elseif(isset($json_customer['first_name'])){
                        $frist_name = $json_customer['first_name'];
                    }else{
                        $frist_name = "";
                    }

                    if(isset($json_customer['d_last_name'])){
                        $last_name = $json_customer['d_last_name'];
                    }elseif(isset($json_customer['last_name'])){
                        $last_name = $json_customer['last_name'];
                    }else{
                        $last_name = "";
                    }
                    $customer = $frist_name. " " . $last_name;
                }

                if($customer == " "){
                    $customer = $row['key_word_customer'];
                }

                $dathu = $row['thanhtoan_lan1']  + $row['thanhtoan_lan2'] + $row['thanhtoan_lan3'] +$row['thanhtoan_lan4']  + $row['thanhtoan_lan5'] ;
                $tong_dathu += $dathu;
                if($dathu == 0 || $dathu == ""){
                    $dathu_print = "";
                }else{
                    $dathu_print = number_format($dathu,2) ." €";
                }

                if($tong_dathu == 0 || $tong_dathu == ""){
                    $tong_dathu_print = "";
                }else{
                    $tong_dathu_print = number_format($tong_dathu,2) ." €";
                }

//                if($row['thanhtoan_lan1'] == 0 || $row['thanhtoan_lan1'] == ""){
//                    $thanhtoan_lan1_print = "";
//                }else{
//                    $thanhtoan_lan1_print = $row['thanhtoan_lan1'] ." €";
//                }
//
//                if($row['thanhtoan_lan2'] == 0 || $row['thanhtoan_lan2'] == ""){
//                    $thanhtoan_lan2_print = "";
//                }else{
//                    $thanhtoan_lan2_print = $row['thanhtoan_lan2']." €";
//                }
//
//                if($row['thanhtoan_lan3'] == 0 || $row['thanhtoan_lan3'] == ""){
//                    $thanhtoan_lan3_print = "";
//                }else{
//                    $thanhtoan_lan3_print = $row['thanhtoan_lan3']." €";
//                }
//
//                if($row['thanhtoan_lan4'] == 0 || $row['thanhtoan_lan4'] == ""){
//                    $thanhtoan_lan4_print = "";
//                }else{
//                    $thanhtoan_lan4_print = $row['thanhtoan_lan4']." €";
//                }
//
//                if($row['thanhtoan_lan5'] == 0 || $row['thanhtoan_lan5'] == ""){
//                    $thanhtoan_lan5_print = "";
//                }else{
//                    $thanhtoan_lan5_print = $row['thanhtoan_lan5']." €";
//                }

                if($row['tongtien_no'] == "" || $row['tongtien_no'] == 0){
                    $row_tongtien_no_print = " ";
                }else{
                    $row_tongtien_no_print = number_format($row['tongtien_no'], 2). " €";
                }

                $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $id . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['shipped_at'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['shipper_name'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;">' . $row['order_number'] . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $customer . '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . number_format($row['total_price'],2)." €". '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . $dathu_print. '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;">' . $row_tongtien_no_print. '</td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;">' . $row['note']. '</td>
			</tr>
			';
            //}

        $list_kunden = array_unique($list_kunden);
        $list_time_lan1 = array_unique($list_time_lan1);
        // var_dump($list_time_lan1);die;
        }

        if($list_time_lan1){
            $data_lastime = $this->m_voxy_package_orders->get_lastime_pay($list_kunden,$list_time_lan1, $list_order_number,$shipper_id);
        }

       // var_dump($data_lastime);die;

        if(isset($data_lastime) && $data_lastime != false){
            $lastime_total_price = 0 ;
            $lastime_total_dathu = 0 ;
            $lastime_total_no = 0 ;
            foreach ($data_lastime as $item){
                $id++;
                $lastime_total_price += $item['total_price'];
                $lastime_total_dathu += $item['thanhtoan_lan1'];
                $lastime_total_no += $item['tongtien_no'];
                if(isset($item['key_word_customer']) && $item['key_word_customer'] != ""){
                    $key_word = $item['key_word_customer'];
                }else{
                    $key_word = null;
                }

                //get id khachhang
                $id_khachang = $this->m_voxy_package_kunden->get_id_khachhang($item['customer_id']);

                if (isset($json_customer['d_first_name'])) {
                    $frist_name = $json_customer['d_first_name'];
                } elseif (isset($json_customer['first_name'])) {
                    $frist_name = $json_customer['first_name'];
                }else {
                    $frist_name = "";
                }

                if (isset($json_customer['d_last_name'])) {
                    $last_name = $json_customer['d_last_name'];
                } elseif (isset($json_customer['last_name'])) {
                    $last_name = $json_customer['last_name'];
                } else {
                    $last_name = "";
                }

                if($key_word != null){
                    $customer_name = $key_word ;
                }else{
                    $customer_name = $frist_name . " " . $last_name;
                }
                if($item['thanhtoan_lan1'] == 0 || $item['thanhtoan_lan1'] == null){
                    $thanhtoan_lan1_print = " ";
                }else{
                    $thanhtoan_lan1_print = number_format($item['thanhtoan_lan1'],2)." €";
                }

                if($item['tongtien_no'] == 0 || $item['tongtien_no'] == null){
                    $tongtien_no_lan1_print = " ";
                }else{
                    $tongtien_no_lan1_print = number_format($item['tongtien_no'],2) ." €";
                }
//				<td style="text-align: right;">' . number_format($item['total_price'], 2) . " €" . '</td>
                if($item['note'] == ""){
                    $item['note'] = "Nợ cũ";
                }

                $output .= '
			<tr style="font-style: italic;">
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $id . '</span></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at'] . '</span></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipper_name'] . '</span></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['order_number'] . '</span></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: center;"><span style="color: red">' . $id_khachang . "-".$customer_name . '</span></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color: red;">' . $thanhtoan_lan1_print . '</span></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><span style="color:red;">' . $tongtien_no_lan1_print .'</span></td>
				<td style="border-right: 1px solid;border-left: 1px solid;border-bottom: 0.01mm solid;"><span style="color: red">' . $item['shipped_at']. '</span></td>
			</tr>
			';
            }
        }

        if(!isset($lastime_total_price)){
            $lastime_total_price = 0;
        }

        if(!isset($lastime_total_dathu)){
            $lastime_total_dathu = 0;
        }

        if(!isset($lastime_total_no)){
            $lastime_total_no = 0;
        }

        $id = $id +1;
        $output .= '
			<tr>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"> </td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: left;"></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid; text-align: right"><b>' . number_format($tong_total_price,2) ." €". '</b></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"><b>'.number_format($tong_dathu + $lastime_total_dathu,2).'</b></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;text-align: right;"><b>' . number_format($tong_tienno + $lastime_total_no, 2) . " €" . '</b></td>
				<td style="border-left: 1px solid;border-bottom: 0.01mm solid;"></td>
			</tr>
			';
        $output .= '</tbody>';
        $output .= '</table><br>';

        $output .= '</div>';
        //var_dump($output);die;
        return $output;
    }

    public function pdf_day_excel_tienno($list_id_to_tienno, $ngay_dat_hang, $ngay_chuyen_hang, $shipper_id, $ship_area_id){
        $this->load->model('m_voxy_package');
        $this->load->model('m_voxy_category');
        $this->load->model('m_voxy_package_orders');

        $this->db->select('id');
        $this->db->select('order_number');
        $this->db->select('total_price');
        $this->db->select('thanhtoan_lan1');
        $this->db->select('thanhtoan_lan2');
        $this->db->select('thanhtoan_lan3');
        $this->db->select('thanhtoan_lan4');
        $this->db->select('thanhtoan_lan5');
        $this->db->select('tongtien_no');
        $this->db->select('shipper_name');
        $this->db->select('shipped_at');
        $this->db->select('customer');
        $this->db->select('customer_id');
        $this->db->select('key_word_customer');

        $this->db->select('note');
        $this->db->from('voxy_package_orders');
        $this->db->where('status != ', 'red');
        //$this->db->where('tongtien_no != ', '');
        //$this->db->where('tongtien_no > 0 ', null,false);

        if($list_id_to_tienno == null || $list_id_to_tienno == ""){

            if($ngay_dat_hang && $ngay_dat_hang != ""){
                $this->db->where('shipped_at >=', $ngay_dat_hang);
            }

            if($ngay_chuyen_hang && $ngay_chuyen_hang != ""){
                $this->db->where('shipped_at <=', $ngay_chuyen_hang);
            }

            if ($shipper_id != "") {
                $this->db->where_in('shipper_id', $shipper_id);
            }

            if ($ship_area_id != "") {
                $this->db->where_in('ship_area_id', $ship_area_id);
            }

        }else{
            if($list_id_to_tienno != ""){
                $this->db->where_in('id', $list_id_to_tienno);
            }
        }

        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();
        if($data){
            return $data;
        }else{
            return "";
        }

    }
}

?>
