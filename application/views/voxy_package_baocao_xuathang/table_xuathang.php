<div class="container-fluid" style="background-color: white">
    <div class="row-fluid">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="widget-manage e_data_table_tinh"
                     data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">
                    <form action="" data-url="<?php echo base_url("voxy_package_xuathang/xuathang"); ?>" method="post"
                          class="form_add_product" style="margin-top: 10px">
                        Tìm kiếm sản phẩm :
                        <input type="text" name="search_pro" class="search_pro span12">
                        <div class="list_timkiem"></div>
                    </form>

                    <form action="minus_inventory" method="post" class="form_products"
                          data-href="<?php echo base_url("htmltopdf/phieuxuathangnew"); ?>">
                        <div class="title">
                            <span class="laixe" data-id-laixe="<?php echo $shipper_id; ?>"
                                  data-laixe="<?php echo $shipper_name; ?>">Lái xe :<?php echo $shipper_name ?></span>
                            <span class="date_time"
                                  data-date="<?php echo $date_time ?>"><?php echo $date_time; ?></span>
                            <p class="name_kho" style="text-align: center;" data-name-kho="<?php echo $name_kho; ?>"
                               data-kho="<?php echo $kho; ?>"><b>Danh sách xuất hàng <?php echo $name_kho; ?></b></p>
                        </div>
                        <div class="products">
                            <div class="pro_th">
                                <span style="display: none;width: 10%;float: left;text-align: center;text-align: left !important;">Variant ID</span>
                                <span style="width: 10%;float: left;text-align: center;text-align: left !important;">SKU</span>
                                <span style="width: 35%;float: left;text-align: center">Tên</span>
                                <span style="width: 10%;float: left;text-align: center">SL Xuất</span>
                                <span style="width: 10%;float: left;text-align: center">Chưa Xuất</span>
                                <span style="width: 5%;float: left;text-align: center">Đã Xuất</span>
                                <span style="width: 5%;float: left;text-align: center">Kho</span>
                                <span style="width: 5%;float: left;text-align: center">Đơn Vị</span>
                                <span style="width: 5%;float: left;text-align: center">Sửa</span>
                                <span style="width: 5%;float: left;text-align: center">Xóa</span>
                                <span style="width: 10%;float: left;text-align: center">Vị trí</span>
                            </div>
                            <?php if (!isset($thongbao)) { ?>
                                <div class="pro_body"
                                     data-result-catid='<?php echo json_encode($all_products["result_catid"]); ?>'
                                     data-note='<?php echo json_encode($all_products["array_note_products"]); ?>'
                                     data-list-order="<?php echo $all_products["list_order"]; ?>">
                                    <?php
                                    $id = -1;

                                    if (isset($history_xuathang_list_product) && $history_xuathang_list_product != null) {
                                        $all_products["export2"] = array();
                                        foreach ($history_xuathang_list_product as $item) {
                                            $all_products["export2"][] = get_object_vars($item);
                                        }
                                    }

                                    if ($sorting == "category") {
                                        foreach ($all_products["result_catid"] as $catid) {//category

                                            if ($kho == "AKL") {
                                                if ($catid['cat_id'] == "91459649625") { ?>
                                                    <p style="text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;"><?php echo $this->m_voxy_category->get_cat_title($catid['cat_id']); ?></p>
                                                <?php }
                                            } elseif ($kho == "lil") {
                                                foreach ($all_products["export2"] as $item2) {
                                                    if ($catid['cat_id'] === $item2["cat_id"]) {
                                                        if (strpos($item2["location"], "AH") !== false) { ?>
                                                            <p style="text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;"><?php echo $this->m_voxy_category->get_cat_title($catid['cat_id']); ?></p>
                                                            <?php break;
                                                        }
                                                    }
                                                }
                                            } elseif ($kho == "cua_hang") {//trong cua hang
                                                if ($catid['cat_id'] == false) { ?>
                                                    <b>G0: Null</b>
                                                    <?php
                                                } else {
                                                    foreach ($all_products["export2"] as $item5) {
                                                        if ($catid['cat_id'] === $item5["cat_id"]) {
                                                            if (strpos($item5["location"], "AH") !== false || strpos($item5["location"], "AKL") !== false) {

                                                            } else { ?>
                                                                <p style="text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;"><?php echo $this->m_voxy_category->get_cat_title($catid['cat_id']); ?></p>
                                                                <?php break;
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                if ($catid['cat_id'] == false) { ?>
                                                    <b style="text-align: left;margin: 0; padding: 0;width: 100%;float: left;clear: left;font-weight: bold;font-size: 15px;">Không
                                                        có dữ liệu category</b>
                                                <?php } else { ?>
                                                    <p style="text-align: left;margin: 0; padding: 0;width: 100%;float: none;clear: left;font-weight: bold;font-size: 15px;"><?php echo $this->m_voxy_category->get_cat_title($catid['cat_id']); ?></p>
                                                <?php }
                                            }
                                            ?>
                                            <?php foreach ($all_products["export2"] as $row) {//san pham
                                                //check product co thuoc san pham do khong thi moi in ra
                                                if ($catid['cat_id'] == $row["cat_id"]) {

                                                    $data_da_xuat = "nein";
                                                    $quantity_xuathang = 0;
                                                    $sl_daxuat = 0;
                                                    if ($history_xuathang != null) {
                                                        foreach ($history_xuathang as $item_xuat) {
                                                            if ($item_xuat->variant_id == $row["variant_id"]) {
                                                                $quantity_xuathang = $item_xuat->quantity;
                                                                $data_da_xuat = $item_xuat->data_da_xuat;
                                                                $sl_daxuat = $item_xuat->quantity;
                                                            }
                                                        }
                                                    }

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

                                                    if ($kho == "all") { // in tat ca k phan biet
                                                        $id++;
                                                        $value_note = "";
                                                        foreach ($all_products["array_note_products"] as $item_note) {
                                                            if ($item_note["title"] === $row["title"]) {
                                                                $value_note .= $item_note["item_note_value"];
                                                            }
                                                        }
                                                        //get quantity trong kho hang, neu ko co thi hien null
                                                        $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                        $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                        $quantity_in_ware_house = 0;
                                                        if ($check_variant1 == true) {
                                                            $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                        }

                                                        if ($check_variant2 == true) {
                                                            $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                        }
                                                        ?>
                                                        <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                            <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                 class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                            <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                 class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                            <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                 class="product_id"><?php echo $row["product_id"]; ?></div>
                                                            <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                                 class="sku"><?php echo $row["sku"]; ?></div>

                                                            <div style="width: 35%;height: auto;float: left; text-align: center"
                                                                 class="parent-result">
                                                                <input type="text" class="title"
                                                                       data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                                       value="<?php echo $row["title"]; ?>"
                                                                       style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                                <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                                <div class="note"
                                                                     style="width:100%;float:left; text-transform: uppercase; text-align: center">
                                                                    <?php if ($value_note != "" && $value_note != null) { ?>
                                                                        <b>NOTE --></b><?php echo $value_note; ?>
                                                                    <?php } else { ?>

                                                                    <?php } ?>
                                                                </div>
                                                            </div>

                                                            <div style="width: 10%;height: auto;float: left">
                                                                <input type="text" class="quantity"
                                                                       data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                                       value="<?php echo $row["quantity"]; ?>"
                                                                       data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                                       data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                                       data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                                       style="width: 60px;text-align: center;">
                                                            </div>

                                                            <div style="width: 5%;height: auto;float: left">
                                                                <div class="quantity_xuathang"
                                                                     style="width: 60px;text-align: center; line-height: 35px;">
                                                                    <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                                </div>
                                                            </div>

                                                            <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                                 class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                            <div class="sl-in-kho"
                                                                 style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                                <?php echo $quantity_in_ware_house; ?>
                                                            </div>
                                                            <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                                 class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                            <button type="button" class="edit_product"
                                                                    style="width: 5%;float: left">Sửa
                                                            </button>
                                                            <button type="button" class="remove"
                                                                    style="width: 5%;float: left">Xóa
                                                            </button>
                                                            <div style="width: 10%;height: auto;float: left"
                                                                 data-location="<?php echo $row["location"]; ?>"
                                                                 class="location"><?php echo $location_xuly; ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    } elseif ($kho == "lil") {
                                                        if (strpos($row["location"], "AH") !== false) {
                                                            $id++;
                                                            $value_note = "";
                                                            foreach ($all_products["array_note_products"] as $item_note) {
                                                                if ($item_note["title"] === $row["title"]) {
                                                                    $value_note .= $item_note["item_note_value"];
                                                                }
                                                            }
                                                            //get quantity trong kho hang, neu ko co thi hien null
                                                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                            $quantity_in_ware_house = 0;
                                                            if ($check_variant1 == true) {
                                                                $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                            }

                                                            if ($check_variant2 == true) {
                                                                $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                            }
                                                            ?>
                                                            <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="product_id"><?php echo $row["product_id"]; ?></div>
                                                                <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="sku"><?php echo $row["sku"]; ?></div>

                                                                <div style="width: 35%;height: auto;float: left; text-align: center"
                                                                     class="parent-result">
                                                                    <input type="text" class="title"
                                                                           data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                                           value="<?php echo $row["title"]; ?>"
                                                                           style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                                    <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                                    <div class="note"
                                                                         style="width:100%;float:left; text-transform: uppercase; text-align: center">
                                                                        <?php if ($value_note != "" && $value_note != null) { ?>
                                                                            <b>NOTE --></b><?php echo $value_note; ?>
                                                                        <?php } else { ?>

                                                                        <?php } ?>
                                                                    </div>
                                                                </div>

                                                                <div style="width: 10%;height: auto;float: left">
                                                                    <input type="text" class="quantity"
                                                                           data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                                           value="<?php echo $row["quantity"]; ?>"
                                                                           data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                                           data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                                           data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                                           style="width: 60px;text-align: center;">
                                                                </div>

                                                                <div style="width: 5%;height: auto;float: left">
                                                                    <div class="quantity_xuathang"
                                                                         style="width: 60px;text-align: center; line-height: 35px;">
                                                                        <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                                    </div>
                                                                </div>

                                                                <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                                     class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                                <div class="sl-in-kho"
                                                                     style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                                    <?php echo $quantity_in_ware_house; ?>
                                                                </div>
                                                                <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                                     class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                                <button type="button" class="edit_product"
                                                                        style="width: 5%;float: left">Sửa
                                                                </button>
                                                                <button type="button" class="remove"
                                                                        style="width: 5%;float: left">Xóa
                                                                </button>
                                                                <div style="width: 10%;height: auto;float: left"
                                                                     data-location="<?php echo $row["location"]; ?>"
                                                                     class="location"><?php echo $location_xuly; ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    } elseif ($kho == "AKL") {
                                                        if (strpos($row["location"], "AKL") !== false) {
                                                            $id++;
                                                            $value_note = "";
                                                            foreach ($all_products["array_note_products"] as $item_note) {
                                                                if ($item_note["title"] === $row["title"]) {
                                                                    $value_note .= $item_note["item_note_value"];
                                                                }
                                                            }
                                                            //get quantity trong kho hang, neu ko co thi hien null
                                                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                            $quantity_in_ware_house = 0;
                                                            if ($check_variant1 == true) {
                                                                $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                            }

                                                            if ($check_variant2 == true) {
                                                                $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                            }
                                                            ?>
                                                            <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="product_id"><?php echo $row["product_id"]; ?></div>
                                                                <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="sku"><?php echo $row["sku"]; ?></div>

                                                                <div style="width: 35%;height: auto;float: left; text-align: center"
                                                                     class="parent-result">
                                                                    <input type="text" class="title"
                                                                           data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                                           value="<?php echo $row["title"]; ?>"
                                                                           style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                                    <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                                    <div class="note"
                                                                         style="width:100%;float:left; text-transform: uppercase; text-align: center">
                                                                        <?php if ($value_note != "" && $value_note != null) { ?>
                                                                            <b>NOTE --></b><?php echo $value_note; ?>
                                                                        <?php } else { ?>

                                                                        <?php } ?>
                                                                    </div>
                                                                </div>

                                                                <div style="width: 10%;height: auto;float: left">
                                                                    <input type="text" class="quantity"
                                                                           data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                                           value="<?php echo $row["quantity"]; ?>"
                                                                           data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                                           data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                                           data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                                           style="width: 60px;text-align: center;">
                                                                </div>

                                                                <div style="width: 5%;height: auto;float: left">
                                                                    <div class="quantity_xuathang"
                                                                         style="width: 60px;text-align: center; line-height: 35px;">
                                                                        <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                                    </div>
                                                                </div>

                                                                <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                                     class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                                <div class="sl-in-kho"
                                                                     style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                                    <?php echo $quantity_in_ware_house; ?>
                                                                </div>
                                                                <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                                     class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                                <button type="button" class="edit_product"
                                                                        style="width: 5%;float: left">Sửa
                                                                </button>
                                                                <button type="button" class="remove"
                                                                        style="width: 5%;float: left">Xóa
                                                                </button>
                                                                <div style="width: 10%;height: auto;float: left"
                                                                     data-location="<?php echo $row["location"]; ?>"
                                                                     class="location"><?php echo $location_xuly; ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    } elseif ($kho == "cua_hang") {
                                                        if ($row["location"] == false) {
                                                            $id++;
                                                            $value_note = "";
                                                            foreach ($all_products["array_note_products"] as $item_note) {
                                                                if ($item_note["title"] === $row["title"]) {
                                                                    $value_note .= $item_note["item_note_value"];
                                                                }
                                                            }
                                                            //get quantity trong kho hang, neu ko co thi hien null
                                                            $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                            $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                            $quantity_in_ware_house = 0;
                                                            if ($check_variant1 == true) {
                                                                $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                            }

                                                            if ($check_variant2 == true) {
                                                                $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                            }
                                                            ?>
                                                            <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                                <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="product_id"><?php echo $row["product_id"]; ?></div>
                                                                <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                                     class="sku"><?php echo $row["sku"]; ?></div>

                                                                <div style="width: 35%;height: auto;float: left; text-align: center"
                                                                     class="parent-result">
                                                                    <input type="text" class="title"
                                                                           data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                                           value="<?php echo $row["title"]; ?>"
                                                                           style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                                    <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                                    <div class="note"
                                                                         style="width:100%;float:left; text-transform: uppercase; text-align: center">
                                                                        <?php if ($value_note != "" && $value_note != null) { ?>
                                                                            <b>NOTE --></b><?php echo $value_note; ?>
                                                                        <?php } else { ?>

                                                                        <?php } ?>
                                                                    </div>
                                                                </div>

                                                                <div style="width: 10%;height: auto;float: left">
                                                                    <input type="text" class="quantity"
                                                                           data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                                           value="<?php echo $row["quantity"]; ?>"
                                                                           data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                                           data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                                           data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                                           style="width: 60px;text-align: center;">
                                                                </div>

                                                                <div style="width: 5%;height: auto;float: left">
                                                                    <div class="quantity_xuathang"
                                                                         style="width: 60px;text-align: center; line-height: 35px;">
                                                                        <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                                    </div>
                                                                </div>

                                                                <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                                     class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                                <div class="sl-in-kho"
                                                                     style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                                    <?php echo $quantity_in_ware_house; ?>
                                                                </div>
                                                                <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                                     class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                                <button type="button" class="edit_product"
                                                                        style="width: 5%;float: left">Sửa
                                                                </button>
                                                                <button type="button" class="remove"
                                                                        style="width: 5%;float: left">Xóa
                                                                </button>
                                                                <div style="width: 10%;height: auto;float: left"
                                                                     data-location="<?php echo $row["location"]; ?>"
                                                                     class="location"><?php echo $location_xuly; ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    //end check in ra trong kho nao
                                                }
                                            }
                                        }
                                    } else {//select product theo location
                                        foreach ($all_products["export2"] as $row) {//san pham
                                            $data_da_xuat = "nein";
                                            $quantity_xuathang = 0;
                                            $sl_daxuat = 0;
                                            if ($history_xuathang != null) {
                                                foreach ($history_xuathang as $item_xuat) {
                                                    if ($item_xuat->variant_id == $row["variant_id"]) {
                                                        $quantity_xuathang = $item_xuat->quantity;
                                                        $data_da_xuat = $item_xuat->data_da_xuat;
                                                        $sl_daxuat = $item_xuat->quantity;
                                                    }
                                                }
                                            }

                                            if (!isset($row['location'])) {
                                                $row['location'] = "";
                                            }

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

                                            if ($kho == "all") { // in tat ca k phan biet
                                                $id++;
                                                $value_note = "";
                                                foreach ($all_products["array_note_products"] as $item_note) {
                                                    if ($item_note["title"] === $row["title"]) {
                                                        $value_note .= $item_note["item_note_value"];
                                                    }
                                                }
                                                //get quantity trong kho hang, neu ko co thi hien null
                                                $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                $quantity_in_ware_house = 0;
                                                if ($check_variant1 == true) {
                                                    $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                }

                                                if ($check_variant2 == true) {
                                                    $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                }

                                                if (!isset($row["variant_title"]) || $row["variant_title"] == "") {
                                                    $row["variant_title"] = "no infor";
                                                }
                                                ?>
                                                <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                    <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                         class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                    <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                         class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                    <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                         class="product_id"><?php echo $row["product_id"]; ?></div>
                                                    <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                         class="sku"><?php echo $row["sku"]; ?></div>

                                                    <div style="width: 35%;height: auto;float: left; text-align: center"
                                                         class="parent-result">
                                                        <input type="text" class="title"
                                                               data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                               value="<?php echo $row["title"]; ?>"
                                                               style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                        <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                        <div class="note"
                                                             style="width:100%;float:left; text-transform: uppercase; text-align: right">
                                                            <?php if ($value_note != "" && $value_note != null) { ?>
                                                                <b>NOTE --></b><?php echo $value_note; ?>
                                                            <?php } else { ?>

                                                            <?php } ?>
                                                        </div>
                                                    </div>

                                                    <div style="width: 10%;height: auto;float: left">
                                                        <input type="text" class="quantity"
                                                               data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                               value="<?php echo $row["quantity"]; ?>"
                                                               data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                               data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                               data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                               style="width: 60px;text-align: center;">
                                                    </div>

                                                    <div style="width: 5%;height: auto;float: left">
                                                        <div class="quantity_xuathang"
                                                             style="width: 60px;text-align: center; line-height: 35px;">
                                                            <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                        </div>
                                                    </div>

                                                    <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                         class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                    <div class="sl-in-kho"
                                                         style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                        <?php echo $quantity_in_ware_house; ?>
                                                    </div>
                                                    <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                         class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                    <button type="button" class="edit_product"
                                                            style="width: 5%;float: left">Sửa
                                                    </button>
                                                    <button type="button" class="remove" style="width: 5%;float: left">
                                                        Xóa
                                                    </button>
                                                    <div style="width: 10%;height: auto;float: left"
                                                         data-location="<?php echo $row["location"]; ?>"
                                                         class="location"><?php echo $location_xuly; ?>
                                                    </div>
                                                </div>
                                                <?php
                                            } elseif ($kho == "lil") {
                                                //var_dump(strpos($row["location"], "AH") !== false);
                                                if (strpos($row["location"], "AH") !== false) {
                                                    $id++;
                                                    $value_note = "";
                                                    foreach ($all_products["array_note_products"] as $item_note) {
                                                        if ($item_note["title"] === $row["title"]) {
                                                            $value_note .= $item_note["item_note_value"];
                                                        }
                                                    }
                                                    //get quantity trong kho hang, neu ko co thi hien null
                                                    $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                    $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                    $quantity_in_ware_house = 0;
                                                    if ($check_variant1 == true) {
                                                        $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                    }

                                                    if ($check_variant2 == true) {
                                                        $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                    }
                                                    ?>
                                                    <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="product_id"><?php echo $row["product_id"]; ?></div>
                                                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="sku"><?php echo $row["sku"]; ?></div>

                                                        <div style="width: 40%;height: auto;float: left; text-align: center"
                                                             class="parent-result">
                                                            <input type="text" class="title"
                                                                   data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                                   value="<?php echo $row["title"]; ?>"
                                                                   style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                            <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                            <div class="note"
                                                                 style="width:100%;float:left; text-transform: uppercase; text-align: right">
                                                                <?php if ($value_note != "" && $value_note != null) { ?>
                                                                    <b>NOTE --></b><?php echo $value_note; ?>
                                                                <?php } else { ?>

                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div style="width: 10%;height: auto;float: left">
                                                            <input type="text" class="quantity"
                                                                   data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                                   value="<?php echo $row["quantity"]; ?>"
                                                                   data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                                   data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                                   data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                                   style="width: 60px;text-align: center;">
                                                        </div>

                                                        <div style="width: 5%;height: auto;float: left">
                                                            <div class="quantity_xuathang"
                                                                 style="width: 60px;text-align: center; line-height: 35px;">
                                                                <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                            </div>
                                                        </div>

                                                        <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                             class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                        <div class="sl-in-kho"
                                                             style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                            <?php echo $quantity_in_ware_house; ?>
                                                        </div>
                                                        <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                             class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                        <button type="button" class="edit_product"
                                                                style="width: 5%;float: left">Sửa
                                                        </button>
                                                        <button type="button" class="remove"
                                                                style="width: 5%;float: left">Xóa
                                                        </button>
                                                        <div style="width: 10%;height: auto;float: left"
                                                             data-location="<?php echo $row["location"]; ?>"
                                                             class="location"><?php echo $location_xuly; ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } elseif ($kho == "AKL") {
                                                if (strpos($row["location"], "AKL") !== false) {
                                                    $id++;
                                                    $value_note = "";
                                                    foreach ($all_products["array_note_products"] as $item_note) {
                                                        if ($item_note["title"] === $row["title"]) {
                                                            $value_note .= $item_note["item_note_value"];
                                                        }
                                                    }
                                                    //get quantity trong kho hang, neu ko co thi hien null
                                                    $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                    $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                    $quantity_in_ware_house = 0;
                                                    if ($check_variant1 == true) {
                                                        $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                    }

                                                    if ($check_variant2 == true) {
                                                        $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                    }
                                                    ?>
                                                    <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="product_id"><?php echo $row["product_id"]; ?></div>
                                                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="sku"><?php echo $row["sku"]; ?></div>

                                                        <div style="width: 40%;height: auto;float: left; text-align: center"
                                                             class="parent-result">
                                                            <input type="text" class="title"
                                                                   data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                                   value="<?php echo $row["title"]; ?>"
                                                                   style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                            <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                            <div class="note"
                                                                 style="width:100%;float:left; text-transform: uppercase; text-align: right">
                                                                <?php if ($value_note != "" && $value_note != null) { ?>
                                                                    <b>NOTE --></b><?php echo $value_note; ?>
                                                                <?php } else { ?>

                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div style="width: 10%;height: auto;float: left">
                                                            <input type="text" class="quantity"
                                                                   data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                                   value="<?php echo $row["quantity"]; ?>"
                                                                   data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                                   data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                                   data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                                   style="width: 60px;text-align: center;">
                                                        </div>

                                                        <div style="width: 5%;height: auto;float: left">
                                                            <div class="quantity_xuathang"
                                                                 style="width: 60px;text-align: center; line-height: 35px;">
                                                                <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                            </div>
                                                        </div>

                                                        <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                             class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                        <div class="sl-in-kho"
                                                             style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                            <?php echo $quantity_in_ware_house; ?>
                                                        </div>
                                                        <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                             class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                        <button type="button" class="edit_product"
                                                                style="width: 5%;float: left">Sửa
                                                        </button>
                                                        <button type="button" class="remove"
                                                                style="width: 5%;float: left">Xóa
                                                        </button>
                                                        <div style="width: 10%;height: auto;float: left"
                                                             data-location="<?php echo $row["location"]; ?>"
                                                             class="location"><?php echo $location_xuly; ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } elseif ($kho == "cua_hang") {
                                                if ($row["location"] == false) {
                                                    $id++;
                                                    $value_note = "";
                                                    foreach ($all_products["array_note_products"] as $item_note) {
                                                        if ($item_note["title"] === $row["title"]) {
                                                            $value_note .= $item_note["item_note_value"];
                                                        }
                                                    }
                                                    //get quantity trong kho hang, neu ko co thi hien null
                                                    $check_variant1 = $this->m_voxy_package->check_variant1($row['variant_id']);
                                                    $check_variant2 = $this->m_voxy_package->check_variant2($row['variant_id']);
                                                    $quantity_in_ware_house = 0;
                                                    if ($check_variant1 == true) {
                                                        $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($row['variant_id']);
                                                    }

                                                    if ($check_variant2 == true) {
                                                        $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($row['variant_id']);
                                                    }
                                                    ?>
                                                    <div class="infomation <?php echo $row["variant_id"]; ?>">
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="variant_id"><?php echo $row["variant_id"]; ?></div>
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="cat_id"><?php echo $row["cat_id"]; ?></div>
                                                        <div style="display: none;width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="product_id"><?php echo $row["product_id"]; ?></div>
                                                        <div style="width: 10%;height: auto;float: left;text-align: left !important;"
                                                             class="sku"><?php echo $row["sku"]; ?></div>

                                                        <div style="width: 40%;height: auto;float: left; text-align: center"
                                                             class="parent-result">
                                                            <input type="text" class="title"
                                                                   data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                                   value="<?php echo $row["title"]; ?>"
                                                                   style="width:100%;line-height: 40px; height: 40px;text-align: center">
                                                            <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>

                                                            <div class="note"
                                                                 style="width:100%;float:left; text-transform: uppercase; text-align: right">
                                                                <?php if ($value_note != "" && $value_note != null) { ?>
                                                                    <b>NOTE --></b><?php echo $value_note; ?>
                                                                <?php } else { ?>

                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div style="width: 10%;height: auto;float: left">
                                                            <input type="text" class="quantity"
                                                                   data-variant-id ="<?php echo $row["variant_id"]; ?>"
                                                                   value="<?php echo $row["quantity"]; ?>"
                                                                   data-quantity-need="<?php echo $row["quantity"]; ?>"
                                                                   data-quantity-rest="<?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>"
                                                                   data-da-xuat="<?php echo $data_da_xuat; ?>"
                                                                   style="width: 60px;text-align: center;">
                                                        </div>

                                                        <div style="width: 5%;height: auto;float: left">
                                                            <div class="quantity_xuathang"
                                                                 style="width: 60px;text-align: center; line-height: 35px;">
                                                                <?php echo (int)$row["quantity"] - (int)$quantity_xuathang; ?>
                                                            </div>
                                                        </div>

                                                        <div style="width: 5%;height: auto;float: left;line-height: 35px;"
                                                             class="sl-daxuat"><?php echo $sl_daxuat; ?></div>
                                                        <div class="sl-in-kho"
                                                             style="width: 5%;height: auto;float: left;color: orangered;line-height: 35px;">
                                                            <?php echo $quantity_in_ware_house; ?>
                                                        </div>
                                                        <div style="width: 5%;height: auto;float: left;text-align: left !important;"
                                                             class="variant_title"><?php echo $row["variant_title"]; ?></div>
                                                        <button type="button" class="edit_product"
                                                                style="width: 5%;float: left">Sửa
                                                        </button>
                                                        <button type="button" class="remove"
                                                                style="width: 5%;float: left">Xóa
                                                        </button>
                                                        <div style="width: 10%;height: auto;float: left"
                                                             data-location="<?php echo $row["location"]; ?>"
                                                             class="location"><?php echo $location_xuly; ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            //end check in ra trong kho nao
                                        }
                                    }
                                    ?>
                                </div>
                            <?php } else { ?>
                                <div class="thongbao"
                                     style="text-align: center;margin-top:20px;font-size: 20px;text-transform: uppercase;"><?php echo $thongbao; ?></div>
                            <?php } ?>
                        </div>
                        <?php if (!isset($thongbao)) { ?>
                            <button type="submit" id="btn_xuathang">Xuất hàng</button>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .pro_th {
        font-weight: bold;
        width: 100%;
        text-align: center
    }

    .infomation {
        width: 100%;
        text-align: center;
        float: left;
    }

    .laixe {
        font-weight: bold;
        font-size: 15px;
        float: left;
    }

    .date_time {
        font-weight: bold;
        font-size: 15px;
        float: right;
    }

    .title {
        width: 100%;
    }

    #btn_xuathang {
        font-size: 30px;
        display: block;
        margin: 40px auto;
        clear: left;
    }

    .parent-result {
        position: relative;
    }

    .list_timkiem {
        position: absolute;
        left: 21%;
        right: 20%;
        z-index: 999;
        background-color: #00796a;
        color: white;
        width: 909px;
    }

    .list_timkiem ul li {
        line-height: 20px;
        width: 450px;
        list-style: none;
    }

    .result-search {
        width: 500px;
    }

    .list_timkiem ul li:hover {
        cursor: pointer;
        background-color: #CCCCCC;
    }

    .list_timkiem_title {
        position: absolute;
        left: 0;
        right: 20%;
        z-index: 999;
        background-color: #00796a;
        color: white;
        width: 500px;
    }

    .list_timkiem_title ul li {
        line-height: 20px;
        width: 450px;
        list-style: none;
    }

    .list_timkiem_title ul li:hover {
        cursor: pointer;
        background-color: #CCCCCC;
    }

</style>

<script type="text/javascript">

    $("#btn_xuathang").on("click", function (e) {
        e.preventDefault();

        var obj = $(".e_data_table_tinh");
        show_ajax_loading(obj);
        var $url = $(".form_products").attr("action");

        var list_products = [];
        $.each($(".e_data_table_tinh .infomation"), function (index) {
            list_products.push
            ({
                sku: $(this).children(".sku").text(),
                title: $(this).find(".title").val(),
                quantity: $(this).find(".quantity").attr("data-val"),
                variant_id: $(this).children(".variant_id").text(),
                variant_title: $(this).children(".variant_title").text(),
                location: $(this).find(".location").attr('data-location'),
                cat_id: $(this).children(".cat_id").text(),
                note: $(this).find(".note").text(),
                product_id: $(this).children(".product_id").text(),
            });
        });

        var date = $(".date_time").attr("data-date");
        var laixe = $(".laixe").attr("data-laixe");

        var list_id = [];
        $.each($(".quantity"), function (index) {
            list_id.push
            ({
                variant_id: $(this).attr("data-variant-id"),
                quantity: $(this).val(),
                data_da_xuat: $(this).attr("data-da-xuat"),
                quantity_need: $(this).attr("data-quantity-need")
            });
        });

        var list_order = $(".pro_body").attr("data-list-order");
        var list_cat_id = $(".pro_body").attr("data-result-catid");
        var data_note = $(".pro_body").attr("data-note");

        $.ajax({
            url: $url,
            type: "POST",
            data: {
                list_order: list_order,
                date: date,
                laixe: laixe,
                list_id: list_id,
                list_products: list_products,
            },
            dataType: "json",
            success: function (data) {
                if (data.state == 1) {
                    $(".e_data_table_tinh img[title='Loading']").parent().css({
                        "display": "none"
                    });
                    remove_ajax_loading();
                    var url_xuathang = $(".form_products").attr("data-href");
                    var input = $("<input>").attr("type", "hidden").attr("name", "laixe").val($(".laixe").attr("data-laixe"));
                    var input2 = $("<input>").attr("type", "hidden").attr("name", "date").val($(".date_time").attr("data-date"));
                    var input3 = $("<input>").attr("type", "hidden").attr("name", "shipper_id").val($(".laixe").attr("data-id-laixe"));
                    var input5 = $("<input>").attr("type", "hidden").attr("name", "kho").val($(".name_kho").attr("data-kho"));
                    var input6 = $("<input>").attr("type", "hidden").attr("name", "name_kho").val($(".name_kho").attr("data-name-kho"));
                    var input7 = $("<input>").attr("type", "hidden").attr("name", "list_products").val(JSON.stringify(list_products));
                    var input8 = $("<input>").attr("type", "hidden").attr("name", "list_cat_id").val(list_cat_id);
                    var input9 = $("<input>").attr("type", "hidden").attr("name", "data_note").val(data_note);
                    var input10 = $("<input>").attr("type", "hidden").attr("name", "sorting").val($(".sorting").val());
                    var input11 = $("<input>").attr("type", "hidden").attr("name", "data_xuathang").val(JSON.stringify(list_id));
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                    var form = $("<form method='POST' action='" + url_xuathang + "' target='_blank'>");
                    form.append(input);
                    form.append(input2);
                    form.append(input3);
                    form.append(input5);
                    form.append(input6);
                    form.append(input7);
                    form.append(input8);
                    form.append(input9);
                    form.append(input10);
                    form.append(input11);
                    $("body").append(form);
                    form.submit();

                    //hoi co in ra cai phieu xuat hang ko
                    //nhay ra trang moi va reload lai trang nay
                } else {
                    $(".e_data_table_tinh img[title='Loading']").parent().css({
                        "display": "none"
                    });
                    remove_ajax_loading();
                    alert("chua co san pham nao dc them vao inventory");
                }
            },
            error: function (a, b, c) {
                console.log("Có lỗi ");
                $(".e_data_table_tinh img[title='Loading']").parent().css({
                    "display": "none"
                });
                //location.reload();
            },
            complete: function (jqXHR, textStatus) {

            }
        });
    });

    //check_validate_input($("input[name="quantity_xuathang"]"));

    function check_validate_input(obj) {
        obj.on("change", function () {
            var value_now = parseInt($(this).val());
            var data_quantity_rest = parseInt($(this).attr("data-quantity-rest"));
            if (value_now > data_quantity_rest) {
                alert("Số lượng hàng xuất tối đa " + data_quantity_rest);
                $(this).val(data_quantity_rest);
                $(this).css("color", "red");
            }
        });
    }

    $(document).ready(function () { // stop enter for submit form
        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });

    function ConfirmDialog(message) {
        $("<div></div>").appendTo("body")
            .html("<div><h6>" + message + "?</h6></div>")
            .dialog({
                modal: true, title: "Delete message", zIndex: 10000, autoOpen: true,
                width: "auto", resizable: false,
                buttons: {
                    Yes: function () {
                        // $(obj).removeAttr("onclick");
                        // $(obj).parents(".Parent").remove();

                        $("body").append("<h1>Confirm Dialog Result: <i>Yes</i></h1>");

                        $(this).dialog("close");
                    },
                    No: function () {
                        $("body").append("<h1>Confirm Dialog Result: <i>No</i></h1>");

                        $(this).dialog("close");
                    }
                },
                close: function (event, ui) {
                    $(this).remove();
                }
            });
    };

    $("#search_pro").keypress(function (event) {
        if (event.which == "10" || event.which == "13") {
            event.preventDefault();
        }
    });

    //get variants for products
    //$("#i_products").on("change", function () {
    //    var id_products = $(this).val();
    //    var id_transfer = $("#i_id").val();
    //    //check value cua product_variants
    //    if (id_products) {
    //        $.ajax({
    //            url: "<?php //echo base_url(); ?>//voxy_package/get_product_from_id_products_xuathang",
    //            async: false,
    //            type: "POST",
    //            dataType: "json",
    //            data: {products: id_products, id_transfer: id_transfer},
    //            success: function (data) {
    //                if (data.state === 1) {
    //                    $(".infor-variant").css('display','block');
    //                    $(".infor-variant").html(data.html);
    //                } else {
    //                    $(".infor-variant").css('display','block');
    //                    $(".infor-variant").html("<h3>Không có sản phẩm ở vị trí này </h3>");
    //                }
    //            },
    //            error: function () {
    //                console.log("loi ajax get id");
    //            }
    //        });
    //
    //    }
    //});

    $(".search_pro").on("change keyup", function () {
        var request = $(this).val();
        var search = $(".list_timkiem");
        if (request.length > 2) {
            $.ajax({
                url: "<?php echo base_url(); ?>voxy_package_xuathang/search_pro",
                async: false,
                type: "POST",
                dataType: "json",
                data: {request: request},
                success: function (data) {
                    if (data.state === 1) {
                        search.css("display", "block");
                        search.html(data.html);
                    } else {
                        search.css("display", "block");
                        search.html("<h3>Không có sản phẩm ở vị trí này </h3>");
                    }
                },
                error: function () {
                    console.log("loi ajax get id");
                }
            });
        } else {
            search.css("display", "none");
        }

    });

    // $('.submit-add-product ').on('click', function (e) {
    //     e.preventDefault();
    //
    //     var list_products = [];//list product ban dau
    //     $.each($(".e_data_table_tinh .infomation"), function (index) {
    //         list_products.push
    //         ({
    //             sku: $(this).children(".sku").text(),
    //             title: $(this).find(".title").val(),
    //             quantity: $(this).find(".quantity").val(),
    //             variant_id: $(this).children(".variant_id").text(),
    //             variant_title: $(this).children(".variant_title").text(),
    //             location: $(this).find(".location").attr('data-location'),
    //             cat_id: $(this).children(".cat_id").text(),
    //             //note : $(this).children(".note").text(),
    //             product_id: $(this).children(".product_id").text(),
    //         });
    //     });
    //
    //     var list_packung = [];
    //     $.each($(".infor-packung"), function (index) {
    //         list_packung.push
    //         ({
    //             sku: $(this).attr('data-sku'),
    //             title: $(this).attr('data-title'),
    //             quantity: $('.quantity-packung-' + index).val(),
    //             variant_id: $(this).attr('data-variant-id'),
    //             variant_title: $(this).attr('data-option'),
    //             location: $(this).attr('data-location'),
    //             cat_id: $(this).attr('data-cat-id'),
    //             product_id: $(this).attr('data-product-id'),
    //             data_da_xuat: 0,
    //             sl_kho: $(this).attr('data-sl-kho'),
    //         });
    //     });
    //
    //
    //     $.each(list_packung, function (key, value) {
    //         if (!isNaN(value.quantity) && value.quantity != null && value.quantity != "") {
    //             //check in array;
    //
    //             if ($("."+value.variant_id)) {
    //                 $(".pro_body").prepend(
    //                     "<div  class='infomation " + value.variant_id + "'>" +
    //                     "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id'>" + value.variant_id + "</div>" +
    //                     "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='cat_id'>" + value.cat_id + "</div>" +
    //                     "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='product_id'>" + value.product_id + "</div>" +
    //
    //                     "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku'>" + value.sku + "</div>" +
    //                     "<div style='width: 35%;height: auto;float: left; text-align: center' class='parent-result'>" +
    //                         "<input type='text' class='title'  data-search='class_search_"+value.variant_id+"'>"+
    //                         "<div class='list_timkiem_title class_search_"+value.variant_id+"'></div>"+
    //                         "<div class='note' style='width:30%;float:left; text-transform: uppercase; text-align: right'>"+
    //                             "<span style='width: 15%'>&nbsp;</span>"+
    //                         "</div>"+
    //                     "</div>" +
    //
    //                     "<div style='width: 10%;height: auto;float: left' class='quantity'>"+
    //                         "<input type='text' class='quantity' style='width: 60px;text-align: center;'>"+
    //                     "</div>" +
    //
    //                     "<div style='width: 10%;height: auto;float: left'>" +
    //                         "<div class='quantity_xuathang xuathang_phone'" +
    //                             "value=" + value.quantity + " " +
    //                             "data-da-xuat = " + 0 + " " +
    //                             "data-variant-id = " + value.variant_id + " " +
    //                             "data-quantity-need = " + value.quantity + " " +
    //                             "data-quantity-rest = " + value.quantity + " " +
    //                                 "data-da-xuat='nein'"+
    //                             "style='width: 60px;text-align: center;'>" +
    //                                 value.quantity+
    //                         "</div>" +
    //                     "</div>" +
    //
    //                     "<div style='width: 5%;height: auto;float: left' class='sl-daxuat'>" + 0 + "</div>" +
    //
    //                     "<div class='sl-in-kho' style='width: 5%;height: auto;float: left;color: orangered;line-height: 35px;'>" +
    //                         value.sl_kho+
    //                     "</div>"+
    //
    //                     "<div style='width: 5%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + value.variant_title + "</div>" +
    //
    //                     "<button type='button' class='edit_product' style='width: 5%;float:left' >Sửa</button>"+
    //                     "<button type='button' class='remove' style='width: 5%;float:left' >Xóa</button>"+
    //                     "<div style='width: 10%;height: auto;float: left' class='location' data-location= '"+value.location+"'>" + value.location + "</div>"+
    //                     "</div>"
    //                 );
    //                 $("." + value.variant_id).find(".title").val(value.title);
    //                 $("." + value.variant_id).find(".quantity").val(value.quantity);
    //             }else{
    //                 $.each(list_products, function (key_old, value_old) {
    //                     if (parseInt(value_old.variant_id) === parseInt(value.variant_id)) {//update
    //                         var value_quantity = $("." + value_old.variant_id).children(".quantity").text();
    //                         var value_new = parseInt(value_quantity) + parseInt(value.quantity);
    //                         $("." + value_old.variant_id).children(".quantity").text(value_new);
    //                         $("." + value_old.variant_id).children("input[name='quantity_xuathang']").val(value_new);
    //                     }
    //                 });
    //             }
    //         }
    //     });
    //
    //     var list_verpackung = [];
    //     $.each($(".infor-verpackung"), function (index) {
    //         list_verpackung.push
    //         ({
    //             sku: $(this).attr('data-sku'),
    //             title: $(this).attr('data-title'),
    //             quantity: $('.quantity-verpackung-' + index).val(),
    //             variant_id: $(this).attr('data-variant-id'),
    //             variant_title: $(this).attr('data-option'),
    //             location: $(this).attr('data-location'),
    //             cat_id: $(this).attr('data-cat-id'),
    //             product_id: $(this).attr('data-product-id'),
    //             data_da_xuat: 0,
    //             sl_kho: $(this).attr('data-sl-kho'),
    //         });
    //     });
    //
    //     $.each(list_verpackung, function (key, value) {
    //         if (!isNaN(value.quantity) && value.quantity != null && value.quantity != "") {
    //             //check in array;
    //             var title = encodeURIComponent(value.title);
    //             if ($("."+value.variant_id)) {
    //                 $(".pro_body").prepend(
    //                     "<div  class='infomation " + value.variant_id + "'>" +
    //                     "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id'>" + value.variant_id + "</div>" +
    //                     "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='cat_id'>" + value.cat_id + "</div>" +
    //                     "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='product_id'>" + value.product_id + "</div>" +
    //                     "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku'>" + value.sku + "</div>" +
    //
    //                     "<div style='width: 35%;height: auto;float: left; text-align: center' class='parent-result'>" +
    //                         "<input type='text' class='title'  data-search='class_search_"+value.variant_id+"'>"+
    //                         "<div class='list_timkiem_title class_search_"+value.variant_id+"'></div>"+
    //                         "<div class='note' style='width:35%;float:left; text-transform: uppercase; text-align: right'>"+
    //                             "<span style='width: 15%'>&nbsp;</span>"+
    //                         "</div>" +
    //                     "</div>"+
    //
    //                     "<div style='width: 10%;height: auto;float: left' class='quantity'>"+
    //                     "<input type='text' class='quantity' style='width: 60px;text-align: center;'>"+
    //                     "</div>" +
    //
    //                     "<div style='width: 10%;height: auto;float: left'>" +
    //                         "<div class='quantity_xuathang xuathang_phone'" +
    //                             "value=" + value.quantity + " " +
    //                             "data-da-xuat = " + 0 + " " +
    //                             "data-variant-id = " + value.variant_id + " " +
    //                             "data-quantity-need = " + value.quantity + " " +
    //                             "data-quantity-rest = " + value.quantity + " " +
    //                             "data-da-xuat='nein'"+
    //                             "style='width: 60px;text-align: center;'>" +
    //                                 value.quantity+
    //                         "</div>" +
    //                     "</div>" +
    //
    //                     "<div style='width: 5%;height: auto;float: left' class='sl-daxuat'>" + 0 + "</div>" +
    //                     "<div class='sl-in-kho' style='width: 5%;height: auto;float: left;color: orangered;line-height: 35px;'>" +
    //                         value.sl_kho+
    //                     "</div>"+
    //                     "<div style='width: 5%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + value.variant_title + "</div>" +
    //                     "<button type='button' class='edit_product' style='width: 5%;float:left' >Sửa</button>"+
    //                     "<button type='button' class='remove' style='width: 5%;float:left' >Xóa</button>"+
    //                     "<div style='width: 10%;height: auto;float: left' class='location' data-location= '"+value.location+"'>" + value.location + "</div>"+
    //                     "</div>"
    //                 );
    //                 $("." + value.variant_id).find(".title").val(value.title);
    //                 $("." + value.variant_id).find(".quantity").val(value.quantity);
    //             }else{
    //                 $.each(list_products, function (key_old, value_old) {
    //                     if (parseInt(value_old.variant_id) == parseInt(value.variant_id)) {//update
    //                         var value_quantity = $("." + value_old.variant_id).children(".quantity").text();
    //                         var value_new = parseInt(value_quantity) + parseInt(value.quantity);
    //                         $("." + value_old.variant_id).children(".quantity").text(value_new);
    //                         $("." + value_old.variant_id).children("input[name='quantity_xuathang']").val(value_new);
    //                     }
    //                 });
    //             }
    //         }
    //     });
    //
    //
    //     $('#i_products').val('');
    //     $('.select2.select2-container').remove();
    //     $('.infor-variant').css('display','none');
    //     $('#i_products').select2();
    // });

    $("body").on("click", ".remove", function () {
        $(this).parent().remove();
    });

    $("body").on("click", ".edit_product", function () {
        //$(".edit_product").on("click", function () {
        $(this).parent().find(".title").val("");
        $(this).parent().children(".variant_id").text("");
        $(this).parent().children(".cat_id").text("");
        $(this).parent().children(".product_id").text("");
        $(this).parent().find(".quantity_xuathang").attr("data-variant-id", "");
    });

    $("body").on("change keyup", ".quantity", function () {
        var val = $(this).val();
        $(this).attr("value",val);
        $(this).attr("data-quantity-need",val);
    });

    //$(".title").on("change keyup", function () {
    $("body").on("change keyup", ".title", function () {
        var request = $(this).val();
        var search = $(this).attr('data-search');
        if (request.length > 2) {
            $.ajax({
                url: "<?php echo base_url(); ?>voxy_package_xuathang/search_pro_for_title",
                async: false,
                type: "POST",
                dataType: "json",
                data: {request: request},
                success: function (data) {
                    if (data.state === 1) {
                        $("." + search).css("display", "block");
                        $("." + search).html(data.html);
                    } else {
                        $("." + search).css("display", "block");
                        $("." + search).html("<h3>Không có sản phẩm ở vị trí này </h3>");
                    }
                },
                error: function () {
                    console.log("loi ajax get id");
                }
            });
        } else {
            $("." + search).css("display", "none");
        }
    });

</script>