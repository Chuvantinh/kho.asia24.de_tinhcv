<!--<script type="text/javascript" src="themes/js/vantinh/tableExport.js"></script>-->
<!--<script type="text/javascript" src="themes/js/vantinh/jquery.base64.js"></script>-->
<?php $data_pro_old = $list_input['data_pro'];
    $data_pro = array();
    $i = 0;
    foreach ($data_pro_old as $item){
        $i++;
        $data_pro[$item['title']."-".$i] = $item;
    }
    ksort($data_pro);
?>
<div class="span6 resizable uniform modal-content" style="width:1400px">
    <form class="form-horizontal e_ajax_submit" action="<?php echo $save_link; ?>" enctype="multipart/form-data"
          method="POST">
        <div class="modal-header">
            <span type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></span>
            <h3><?php echo $title; ?></h3>
        </div>
        <div class="modal-body bgwhite">
            <?php $_str_readonly = ' readonly=readonly style="background: #d9d9d9"'; ?>
            <?php foreach ($list_input as $key => $input) {
                if ($key != "data_pro") {
                    ?>
                    <?php
                    if (isset(${'readonly_' . $input->name}) && ${'readonly_' . $input->name} == true) {
                        $input->string_rule .= $_str_readonly;
                    }
                    ?>
                    <div class="control-group <?php echo($input->rule['type'] == 'hidden' ? $input->rule['type'] : ''); ?>">
                        <label class="control-label"
                               for="<?php echo $input->name; ?>"><?php echo $input->label; ?></label>
                        <div class="controls controls-row">
                            <?php if ($input->rule['type'] == 'textarea') { ?>
                                <textarea name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>
                                          id="i_<?php echo $input->name; ?>"></textarea>
                            <?php } elseif ($input->rule['type'] == 'select') { ?>
                                <select class="select2" name="<?php echo $input->name; ?>"
                                        id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>>
                                    <?php foreach ($input->option as $option) { ?>
                                        <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } elseif ($input->rule['type'] == 'file') { ?>
                                <?php if ($input->rule['crop'] == true) { ?>
                                    <input name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>"
                                           type="hidden"/>
                                    <div class="file_input_content e_file_input_content">
                                        <p class="image_preview"><img class="main_img" src="#"/></p>
                                        <input data-name="image_uploader[]" <?php echo $input->string_rule; ?> />
                                    </div>
                                    <div class="image_crop_content">
                                        <div class="trigger_crop" title="Ấn để crop">
                                            <span class="i-arrow-right-18 c_icon arr_icon"></span><span
                                                    class="i-crop c_icon crop_icon"></span>
                                        </div>
                                        <div class="crop_value" title="Ấn để crop">
                                            <img class="croped_img" src="#"/>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="file_input_content e_file_input_content">
                                        <p class="image_preview"><img class="main_img" src="#"/></p>
                                        <input <?php echo $input->string_rule; ?> />
                                    </div>
                                <?php } ?>
                            <?php } elseif ($input->rule['type'] == 'rich_editor') { ?>
                                <textarea class="ckeditor"
                                          data-ckfinder="<?php echo $this->path_static_file . 'plugins/ckfinder'; ?>"
                                          name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> ></textarea>
                            <?php } elseif ($input->rule['type'] == 'shipping_address') {
                                ?>
                                <div class="control_group">
                                    <input type="text" name="s_first_name" id="s_first_name" placeholder="first_name" readonly>
                                    <input type="text" name="s_last_name" id="s_last_name" placeholder="giá 10€" readonly>
                                    <input type="text" name="s_address1" id="s_address1"
                                           placeholder="pichelsdorfer str. 131" readonly>
                                    <input type="text" name="s_zip" id="s_zip" placeholder="15595 postlei zahn" readonly>
                                    <input type="text" name="s_city" id="s_city" placeholder="Berlin oder..." readonly>
                                    <input type="text" name="s_phone" id="s_phone" placeholder="0176301445..." readonly>
                                </div>
                            <?php } elseif ($input->rule['type'] == 'billing_address') { ?>
                                <div class="control_group">
                                    <input type="text" name="b_first_name" id="b_first_name" placeholder="first_name" readonly>
                                    <input type="text" name="b_last_name" id="b_last_name" placeholder="giá 10€" readonly>
                                    <input type="text" name="b_address1" id="b_address1"
                                           placeholder="pichelsdorfer str. 131" readonly>
                                    <input type="text" name="b_zip" id="b_zip" placeholder="15595 postlei zahn" readonly>
                                    <input type="text" name="b_city" id="b_city" placeholder="Berlin oder..." readonly>
                                    <input type="text" name="b_phone" id="b_phone" placeholder="0176987.." readonly>
                                </div>
                            <?php } elseif ($input->rule['type'] == 'line_items') { ?>
                                <div class="products">
                                    <span>Lieferschein</span>
                                    <div class="form_add_product" style="margin-top: 10px">
                                        <span>Tìm kiếm sản phẩm :</span>
                                        <input type="text" class="search_pro span12">
                                        <div class="list_timkiem"></div>
                                    </div>

                                    <div class="pro_th">
                                        <span class="class5">STT</span>
                                        <span class="class5">SKU</span>
                                        <span class="class5">Note</span>
                                        <span class="class30">Tên</span>
                                        <span class="class5">Số lưọng</span>
                                        <span class="class5">Đơn Vị</span>
                                        <span class="class5">Giá</span>
                                        <span class="class5">H.Về</span>
                                        <span class="class5">H.Hỏng</span>
                                        <span class="class5">H.Thiếu</span>
                                        <span class="class5">H.Thêm</span>
                                        <span class="class5">Thành tiền</span>
                                        <span class="class5">Sửa</span>
                                        <span class="class5">Remove</span>
                                        <span class="class5">Restock</span>
                                    </div>

                                    <div class="pro_body">
                                        <?php
                                        $count = 0;
                                        //$total_price = 0;
                                        $data_excel = array();
                                        $oder_number = 0;
                                        foreach ($data_pro as $key => $row) {
                                            $count ++;
                                            if($count % 2 == 0){
                                                $class_row = "chan";
                                            }else{
                                                $class_row = "le";
                                            }


                                            if(!isset($row['hangve'])){
                                                $row['hangve'] = 0;
                                            }

                                            if(!isset($row['hanghong'])){
                                                $row['hanghong'] = 0;
                                            }

                                            if(!isset($row['hangthieu'])){
                                                $row['hangthieu'] = 0;
                                            }

                                            if(!isset($row['hangthem'])){
                                                $row['hangthem'] = 0;
                                            }

                                            if(!isset($row['refund'])){
                                                $row['refund'] = 1;
                                                $class_refund = "refund_active";
                                            }

                                            if(isset($row['refund']) && $row['refund'] == 1 || $row['refund'] == ""){
                                                $class_refund = "refund_active";
                                            } else{
                                                $class_refund = "refund_deactive";
                                            }

                                            if(!isset($row['variant_id']) || $row['variant_id'] == ""){
                                                $row['variant_id'] = "tinhcv_".$count;
                                            }

                                            // setup color for textbox
                                            if($row['hangve'] > 0){
                                                $color_hangve = "color_hangve";
                                            }else{
                                                $color_hangve = "";
                                            }

                                            if($row['hanghong'] > 0){
                                                $color_hanghong = "color_hanghong";
                                            }else{
                                                $color_hanghong = "";
                                            }

                                            if($row['hangthieu'] > 0){
                                                $color_hangthieu = "color_hangthieu";
                                            }else{
                                                $color_hangthieu = "";
                                            }

                                            if($row['hangthem'] > 0){
                                                $color_hangthem = "color_hangthem";
                                            }else{
                                                $color_hangthem = "";
                                            }



                                            // end setup color for textbox

                                            //$total_price += $row['price'] * ($row['quantity'] - $row['hangve'] - $row['hanghong'] - $row['hangthieu'] + $row['hangthem']);
                                            ?>


                                            <div class="infomation <?php echo $row["variant_id"]; ?> <?php echo $class_row; ?>" style="width: 100%;float:left;">

                                                <input type='hidden' class="sku_hidden" name='information[<?php echo $row['variant_id'];?>][sku]' value='<?php echo $row['sku'];?>'>
                                                <input type='hidden' class="quantity_hidden" name='information[<?php echo $row['variant_id'];?>][quantity]' value='<?php echo $row['quantity'];?>'>
                                                <input type='hidden' class="price_hidden" name='information[<?php echo $row['variant_id'];?>][price]' value='<?php echo $row['price'];?>'>
                                                <input type='hidden' class="title_hidden" name='information[<?php echo $row['variant_id'];?>][title]' value='<?php echo $row['title'];?>'>
                                                <input type='hidden' class="product_hidden" name='information[<?php echo $row['variant_id'];?>][product_id]' value='<?php echo $row['product_id'];?>'>
                                                <input type='hidden' class="variant_hidden" name='information[<?php echo $row['variant_id'];?>][variant_id]' value='<?php echo $row['variant_id'];?>'>
                                                <input type='hidden' class="variant_title_hidden" name='information[<?php echo $row['variant_id'];?>][variant_title]' value='<?php echo $row['variant_title'];?>'>
                                                <input type='hidden' class="item_note_hidden" name='information[<?php echo $row['variant_id'];?>][item_note]' value='<?php echo $row['item_note'];?>'>
                                                <input type='hidden' class="hangve_hidden" name='information[<?php echo $row['variant_id'];?>][hangve]' value='<?php echo $row['hangve'];?>'>
                                                <input type='hidden' class="hanghong_hidden" name='information[<?php echo $row['variant_id'];?>][hanghong]' value='<?php echo $row['hanghong'];?>'>
                                                <input type='hidden' class="hangthieu_hidden" name='information[<?php echo $row['variant_id'];?>][hangthieu]' value='<?php echo $row['hangthieu'];?>'>
                                                <input type='hidden' class="hangthem_hidden" name='information[<?php echo $row['variant_id'];?>][hangthem]' value='<?php echo $row['hangthem'];?>'>
                                                <input type='hidden' class="refund_hidden" name='information[<?php echo $row['variant_id'];?>][refund]' value='<?php echo $row['refund'];?>'>

                                                <div class="class5 count" style="text-align: center !important;"><?php echo $count; ?></div>
                                                <div class="class5 sku"><?php echo $row["sku"]; ?></div>
                                                <div class="class10 note">
                                                    <input type="text" class="input_note"  style="width: 122px;min-width: 105px" value="<?php echo $row["item_note"]; ?>">
                                                </div>
                                                <div class="class25 parent-result">
                                                    <input type="text" class="title"
                                                           data-search="class_search_<?php echo $row['variant_id']; ?>"
                                                           value="<?php echo $row["title"]; ?>"
                                                           style="width:100%;line-height: 36px; height: 36px;text-align: center">
                                                    <div class="list_timkiem_title class_search_<?php echo $row['variant_id']; ?>"></div>
                                                </div>
                                                <div class="class5">
                                                    <input type="text" class="quantity"
                                                           value="<?php echo $row["quantity"];?>"
                                                           style="min-width: 50px;width: 50px;text-align: center;">
                                                </div>

                                                <div class="class5 variant_title">
                                                    <?php echo $row["variant_title"]; ?>
                                                </div>

                                                <div class="class5">
                                                    <input type="text" class="price"
                                                           value="<?php echo $row["price"]; ?>"
                                                           style="min-width: 50px;width: 50px;text-align: center;">
                                                </div>

                                                <div class="class5">
                                                    <input type="text" class="hangve <?php echo $color_hangve; ?>"
                                                           value="<?php echo $row["hangve"];?>"
                                                           style="min-width: 50px;width: 50px;text-align: center;">
                                                </div>

                                                <div class="class5">
                                                    <input type="text" class="hanghong <?php echo $color_hanghong; ?>"
                                                           value="<?php echo $row["hanghong"];?>"
                                                           style="min-width: 50px;width: 50px;text-align: center;">
                                                </div>

                                                <div class="class5">
                                                    <input type="text" class="hangthieu <?php echo $color_hangthieu; ?>"
                                                           value="<?php echo $row["hangthieu"];?>"
                                                           style="min-width: 50px;width: 50px;text-align: center;">
                                                </div>

                                                <div class="class5">
                                                    <input type="text" class="hangthem <?php echo $color_hangthem; ?>"
                                                           value="<?php echo $row["hangthem"];?>"
                                                           style="min-width: 50px;width: 50px;text-align: center;">
                                                </div>

                                                <div class="class5 thanhtien">
                                                    <?php echo ($row["quantity"] - $row['hangve'] - $row['hanghong'] - $row['hangthieu']+ $row['hangthem']) * (double)$row["price"]; ?>
                                                </div>

                                                <div class="class5">
                                                    <span class="edit_product btn btn-mini">Edit<span>
                                                </div>

                                                <div class="class5">
                                                    <span class="remove btn btn-mini">Remove<span>
                                                </div>

                                                <div class="class5">
                                                    <span class="refund btn btn-mini <?php echo $class_refund; ?>">Restock<span>
                                                </div>

                                            </div>

                                            <?php
                                            $oder_number = $row['oder_number'];
                                            $data_excel[$key]['title'] = $row['title'];
                                            $data_excel[$key]['price'] = $row['price'];
                                            $data_excel[$key]['quantity'] = $row['quantity'];
                                            $data_excel[$key]['total'] = number_format($row['price'] * $row['quantity'], 2);
                                        } ?>

                                    </div>

                                    <div class="pro_footer" data-thanhtoan="3">

                                        <input type="hidden" name="thanhtoan_lan1" class="thanhtoan_lan1" value="<?php echo $thanhtoan_lan1;?>">
                                        <input type="hidden" name="thanhtoan_lan2" class="thanhtoan_lan2" value="<?php echo $thanhtoan_lan2;?>">
                                        <input type="hidden" name="thanhtoan_lan3" class="thanhtoan_lan3" value="<?php echo $thanhtoan_lan3;?>">
                                        <input type="hidden" name="thanhtoan_lan4" class="thanhtoan_lan4" value="<?php echo $thanhtoan_lan4;?>">
                                        <input type="hidden" name="thanhtoan_lan5" class="thanhtoan_lan5" value="<?php echo $thanhtoan_lan5;?>">
                                        <input type="hidden" name="tongtien_no" class="tongtien_no" value="<?php echo $tongtien_no?>">

                                        <div class="class100">
                                            <div class="class70 tongtien-text">Tổng tiền:</div>
                                            <div class="total_price class30 tongtien">
                                                <?php echo $total_price ." €"; ?></div>
                                        </div>

                                        <div class="class100">
                                            <div class="class25">Thanh toán lần 1:</div>

                                            <div class="class25 take_by_lan1">
                                                    <div class="class50" style="display: none">Thu bởi:</div>
                                                    <select name="take_by_lan1" class="select2 class50 take_by_lan1" style="width: 200px !important; float: right">
                                                        <?php foreach ($data_shippers as $item) {
                                                            if((int)$take_by_lan1 == (int)$item['id']){
                                                                $class_selected_take_lan1 = "selected";
                                                            }else{
                                                                $class_selected_take_lan1 = "";
                                                            }
                                                            ?>
                                                            <option value="<?php echo $item['id'];?>" <?php echo $class_selected_take_lan1; ?> ><?php echo $item['first_name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                            </div>

                                            <div class="class25" >
                                                Ngày:
                                                <input type="datetime" autocomplete="off" name="time_lan1" class="input-25">
                                            </div>

                                            <div class="class25 tt_lan1">
                                                Số tiền:
                                                <input type="text" value="<?php echo $thanhtoan_lan1;?>" class="input-25">
                                            </div>

                                        </div>

                                        <div class="class100">
                                            <div class="class25">Thanh toán lần 2:</div>

                                            <div class="class25 take_by_lan2">
                                                <div class="class50" style="display: none">Thu bởi:</div>
                                                <select name="take_by_lan2" class="class50 take_by_lan2 select2" style="width: 200px !important; float: right">
                                                    <?php foreach ($data_shippers as $item) {
                                                        if($take_by_lan2 == $item['id']){
                                                            $class_selected_take_lan2 = "selected";
                                                        }else{
                                                            $class_selected_take_lan2 = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $item['id'];?>" <?php echo $class_selected_take_lan2; ?> ><?php echo $item['first_name'];?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="class25" >
                                                Ngày:
                                                <input type="datetime" autocomplete="off" name="time_lan2" class="input-25">
                                            </div>

                                            <div class="class25 tt_lan2">
                                                Số tiền:
                                                <input type="text" value="<?php echo $thanhtoan_lan2;?>" class="input-25">
                                            </div>

                                        </div>

                                        <div class="class100" style="display: none">
                                            <div class="class25">Thanh toán lần 3:</div>

                                            <div class="class25 take_by_lan3">
                                                <div class="class50">Thu bởi:</div>
                                                <select name="take_by_lan3" class="class50 take_by_lan3 select2" style="width: 200px !important; float: right">
                                                    <?php foreach ($data_shippers as $item) {
                                                        if($take_by_lan3 == $item['id']){
                                                            $class_selected_take_lan3 = "selected";
                                                        }else{
                                                            $class_selected_take_lan3 = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $item['id'];?>" <?php echo $class_selected_take_lan3;?> ><?php echo $item['first_name'];?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="class25" >
                                                Ngày:
                                                <input type="datetime" autocomplete="off" name="time_lan3" class="input-25">
                                            </div>

                                            <div class="class25 tt_lan3">
                                                Số tiền:
                                                <input type="text" value="<?php echo $thanhtoan_lan3;?>" class="input-25">
                                            </div>

                                        </div>

                                        <div class="class100" style="display: none">
                                            <div class="class25">Thanh toán lần 4:</div>

                                            <div class="class25 take_by_lan4">
                                                <div class="class50">Thu bởi:</div>
                                                <select name="take_by_lan4" class="class50 take_by_lan4 select2" style="width: 200px !important; float: right">
                                                    <?php foreach ($data_shippers as $item) {
                                                        if($take_by_lan4 == $item['id']){
                                                            $class_selected_take_lan4 = "selected";
                                                        }else{
                                                            $class_selected_take_lan4 = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $item['id'];?>" <?php echo $class_selected_take_lan4;?> ><?php echo $item['first_name'];?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="class25" >
                                                Ngày:
                                                <input type="datetime" autocomplete="off" name="time_lan4" class="input-25">
                                            </div>

                                            <div class="class25 tt_lan4">
                                                Số tiền:
                                                <input type="text" value="<?php echo $thanhtoan_lan4;?>" class="input-25">
                                            </div>

                                        </div>

                                        <div class="class100" style="display: none">
                                            <div class="class25">Thanh toán lần 5:</div>

                                            <div class="class25 take_by_lan5">
                                                <div class="class50">Thu bởi:</div>
                                                <select name="take_by_lan5" class="class50 take_by_lan5 select2" style="width: 200px !important; float: right">
                                                    <?php foreach ($data_shippers as $item) {
                                                        if($take_by_lan5 == $item['id']){
                                                            $class_selected_take_lan5 = "selected";
                                                        }else{
                                                            $class_selected_take_lan5 = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $item['id'];?>" <?php echo $class_selected_take_lan5;?>><?php echo $item['first_name'];?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="class25" >
                                                Ngày:
                                                <input type="datetime" autocomplete="off" name="time_lan5" class="input-25">
                                            </div>

                                            <div class="class25 tt_lan5">
                                                Số tiền:
                                                <input type="text" value="<?php echo $thanhtoan_lan5;?>" class="input-25">
                                            </div>

                                        </div>

                                        <p style="text-align: right; font-size: 20px; color: red;padding-right: 150px;">
                                            <span style="float: left;width: 80%;">Tổng tiền nợ:</span><span style="float: left;width: 20%;" class="tt_no">
                                                <input type="text" value="<?php echo $tongtien_no;?>" style="font-size: 20px;text-align: right;min-width: 100px;width: 100px;" readonly>
                                            </span>
                                        </p>
                                    </div>

                                </div>
                            <?php } else { ?>
                                <input name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>
                                       id="i_<?php echo $input->name; ?>"/>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>

        <div class="modal-footer">
            <a href="<?php echo base_url('voxy_package_orders/excel')."?order_number=".$oder_number."&"."total_price=".$total_price; ?>"
               class="excel btn btn-success btn-lg" > Xuất đơn hàng với Excel</a>
            <a href="<?php echo base_url('voxy_package_orders/xml')."?order_number=".$oder_number."&"."total_price=".$total_price; ?>"
               class="xml btn btn-success btn-lg" > Xuất File XML</a>
            <a href="<?php echo base_url('htmltopdf/pdf_order')."?order_number=".$oder_number ?>" target="_blank"
               class="btn-danger btn btn-lg" > Xuất đơn hàng với PDF</a>



            <button type="submit" class="b_add b_edit btn btn-primary">Lưu</button>
            <button type="reset" class="b_add btn">Nhập lại</button>
            <button type="button" class="b_view b_add b_edit btn" data-dismiss="modal">Hủy</button>
        </div>
    </form>
    <div id="dialog" title="Thông báo" style="display: none">
        <p>Bạn phải thay đổi tất cả các sản phẩm linh tinh ###  => thành sản phẩm có trong kho, để có thể lưu đơn hàng thành công !</p>
    </div>

</div>

<style>

</style>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.controls-row input[type="datetime"]').datepicker({
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016'
        });

        $('.time_lan1').datepicker({
            format: "yy-mm-dd",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016'
        });
        $('.time_lan2').datepicker({
            format: "yy-mm-dd",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016'
        });
        $('.time_lan3').datepicker({
            format: "yy-mm-dd",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016'
        });

        $('.time_lan4').datepicker({
            format: "yy-mm-dd",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016'
        });

        $('.time_lan5').datepicker({
            format: "yy-mm-dd",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016'
        });

        //$('.quantity').attr('readonly', true);
        $('.price').css('color', 'red');

        $("#search_pro").keypress(function (event) {
            if (event.which == "10" || event.which == "13") {
                event.preventDefault();
            }
        });

        $(".title").keypress(function (event) {
            if (event.which == "10" || event.which == "13") {
                event.preventDefault();
            }
        });

        //them san pham
        $(".search_pro").on("change", function () {
            var request = $(this).val();
            var search = $(".list_timkiem");
            if (request.length > 2) {
                $.ajax({
                    url: "<?php echo base_url(); ?>voxy_package_orders/search_pro",
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
        //nut edit
        $("body").on("click", ".edit_product", function () {
            $(this).parent().parent().find(".title").val("");
            $(this).parent().parent().find(".variant_id").text("");
            $(this).parent().parent().find(".product_id").text("");
            $(this).parent().parent().find(".quantity").attr("data-variant-id", "");
        });
        // end nut edit

        //nut remove
        $("body").on("click", ".remove", function () {
            $(this).parent().parent().remove();
        });
        //end nut remove

        //title cua tung san pham
        $("body").on("change", ".title", function () {
            var request = $(this).val();
            var search = $(this).attr('data-search');
            if (request.length > 2) {
                $.ajax({
                    url: "<?php echo base_url(); ?>voxy_package_orders/search_pro_for_title",
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
        //end title cua tung san pham

        //thay doi input so luong
        $("body").on("change", ".quantity", function () {
           var quantity = $(this).val();
           var price = $(this).parent().parent().find('.price').val();

           var hangve = $(this).parent().parent().find('.hangve').val();
           var hanghong = $(this).parent().parent().find('.hanghong').val();
           var hangthieu = $(this).parent().parent().find('.hangthieu').val();
           var hangthem = $(this).parent().parent().find('.hangthem').val();
           var soluong_end = parseFloat(quantity) - parseFloat(hangve) - parseFloat(hanghong) - parseFloat(hangthieu) + parseFloat(hangthem);

           var thanhtien = soluong_end * price;
           //var thanhtien_dann = thanhtien.toPrecision(2);
           $(this).parent().parent().find('.thanhtien').text(number_format(thanhtien,2,'.',''));

            $(this).parent().parent().find('.quantity_hidden').val(quantity);

            //total_price
            var list_thanhtien = [];
            $.each($(".infomation .thanhtien"), function (index) {
                list_thanhtien.push
                ({
                    thanhtien: parseFloat($(this).text())
                });
            });

            //check san pham co dau #, k cho xuat hang

            var total_product = 0;
            for (var i = 0; i < list_thanhtien.length ; i++){
                total_product += list_thanhtien[i]['thanhtien'];
            }
            $(".total_price").text(number_format(total_product,2,'.',''));
            //total_price

            //change tong tien no
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();

            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }
        });

        $("body").on("change", ".price", function () {
            var price = $(this).val();
            var quantity = $(this).parent().parent().find('.quantity').val();

            var hangve = $(this).parent().parent().find('.hangve').val();
            var hanghong = $(this).parent().parent().find('.hanghong').val();
            var hangthieu = $(this).parent().parent().find('.hangthieu').val();
            var hangthem = $(this).parent().parent().find('.hangthem').val();
            var soluong_end = parseFloat(quantity) - parseFloat(hangve) - parseFloat(hanghong) - parseFloat(hangthieu) + parseFloat(hangthem);

            var thanhtien = soluong_end * price;
            //var thanhtien_dann = thanhtien.toPrecision(2);
            //$(this).parent().parent().find('.thanhtien').text(thanhtien);
            $(this).parent().parent().find('.thanhtien').text(number_format(thanhtien,2,'.',''));

            $(this).parent().parent().find('.price_hidden').val(price);

            //total_price
            var list_thanhtien = [];
            $.each($(".infomation .thanhtien"), function (index) {
                list_thanhtien.push
                ({
                    thanhtien: parseFloat($(this).text())
                });
            });

            var total_product = 0;
            for (var i = 0; i < list_thanhtien.length ; i++){
                total_product += list_thanhtien[i]['thanhtien'];
            }

            $(".total_price").text(number_format(total_product,2,'.',''));
            //total_price


            //change tong tien no
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();

            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }

        });

        $("body").on("change", ".hangve", function () {
            $(this).addClass('color_hangve');
            var hangve = $(this).val();

            var quantity = $(this).parent().parent().find('.quantity').val();

            var price = $(this).parent().parent().find('.price').val();
            var hanghong = $(this).parent().parent().find('.hanghong').val();
            var hangthieu = $(this).parent().parent().find('.hangthieu').val();
            var hangthem = $(this).parent().parent().find('.hangthem').val();
            if(typeof quantity == "undefined"){
                quantity = 0;
            }
            if(typeof price == "undefined"){
                price = 0;
            }
            if(typeof hangve == "undefined"){
                hangve = 0;
            }
            if(typeof hanghong == "undefined"){
                hanghong = 0;
            }
            if(typeof hangthieu == "undefined"){
                hangthieu = 0;
            }
            if(typeof hangthem == "undefined"){
                hangthem = 0;
            }
            var soluong_end = parseFloat(quantity) - parseFloat(hangve) - parseFloat(hanghong) - parseFloat(hangthieu) + parseFloat(hangthem);

            /*
            console.log("quantity:",quantity);
            console.log("hangve:",hangve);
            console.log("hang hong:",hanghong);
            console.log("hang thieu:",hangthieu);
            console.log("hang them:",hanghong);
            console.log("sl end:",soluong_end);
            */

            var thanhtien = soluong_end * price;
            //var thanhtien_dann = thanhtien.toPrecision(2);

            $(this).parent().parent().find('.thanhtien').text(number_format(thanhtien,2,'.',''));
            $(this).parent().parent().find('.hangve_hidden').val(hangve);

            //total_price
            var list_thanhtien = [];
            $.each($(".infomation .thanhtien"), function (index) {
                list_thanhtien.push
                ({
                    thanhtien: parseFloat($(this).text())
                });
            });

            //check san pham co dau #, k cho xuat hang

            var total_product = 0;
            for (var i = 0; i < list_thanhtien.length ; i++){
                total_product += list_thanhtien[i]['thanhtien'];
            }
            $(".total_price").text(number_format(total_product,2,'.',''));
            //total_price

            //change tong tien no
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();

            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }

        });

        $("body").on("change", ".hanghong", function () {
            $(this).addClass('color_hanghong');
            var hanghong = $(this).val();
            var quantity = $(this).parent().parent().find('.quantity').val();

            var price = $(this).parent().parent().find('.price').val();
            var hangve = $(this).parent().parent().find('.hangve').val();
            var hangthieu = $(this).parent().parent().find('.hangthieu').val();
            var hangthem = $(this).parent().parent().find('.hangthem').val();
            var soluong_end = parseFloat(quantity) - parseFloat(hangve) - parseFloat(hanghong) - parseFloat(hangthieu) + parseFloat(hangthem);

            var thanhtien = soluong_end * price;
            $(this).parent().parent().find('.thanhtien').text(number_format(thanhtien,2,'.',''));

            $(this).parent().parent().find('.hanghong_hidden').val(hanghong);

            //total_price
            var list_thanhtien = [];
            $.each($(".infomation .thanhtien"), function (index) {
                list_thanhtien.push
                ({
                    thanhtien: parseFloat($(this).text())
                });
            });

            //check san pham co dau #, k cho xuat hang

            var total_product = 0;
            for (var i = 0; i < list_thanhtien.length ; i++){
                total_product += list_thanhtien[i]['thanhtien'];
            }
            $(".total_price").text(number_format(total_product,2,'.',''));
            //total_price


            //change tong tien no
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();

            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }

        });

        $("body").on("change", ".hangthieu", function () {
            $(this).addClass('color_hangthieu');
            var hangthieu = $(this).val();
            var quantity = $(this).parent().parent().find('.quantity').val();

            var price = $(this).parent().parent().find('.price').val();
            var hangve = $(this).parent().parent().find('.hangve').val();
            var hanghong = $(this).parent().parent().find('.hanghong').val();
            var hangthem = $(this).parent().parent().find('.hangthem').val();
            var soluong_end = parseFloat(quantity) - parseFloat(hangve) - parseFloat(hanghong) - parseFloat(hangthieu) + parseFloat(hangthem);

            var thanhtien = soluong_end * price;

            $(this).parent().parent().find('.thanhtien').text(number_format(thanhtien,2,'.',''));

            $(this).parent().parent().find('.hangthieu_hidden').val(hangthieu);

            //total_price
            var list_thanhtien = [];
            $.each($(".infomation .thanhtien"), function (index) {
                list_thanhtien.push
                ({
                    thanhtien: parseFloat($(this).text())
                });
            });

            //check san pham co dau #, k cho xuat hang

            var total_product = 0;
            for (var i = 0; i < list_thanhtien.length ; i++){
                total_product += list_thanhtien[i]['thanhtien'];
            }
            $(".total_price").text(number_format(total_product,2,'.',''));
            //total_price

            //change tong tien no
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();

            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }

        });

        $("body").on("change", ".hangthem", function () {
            $(this).addClass('color_hangthem');
            var hangthem = $(this).val();
            var quantity = $(this).parent().parent().find('.quantity').val();

            var price = $(this).parent().parent().find('.price').val();
            var hangve = $(this).parent().parent().find('.hangve').val();
            var hanghong = $(this).parent().parent().find('.hanghong').val();
            var hangthieu = $(this).parent().parent().find('.hangthieu').val();
            var soluong_end = parseFloat(quantity) - parseFloat(hangve) - parseFloat(hanghong) - parseFloat(hangthieu) + parseFloat(hangthem);

            var thanhtien = soluong_end * price;
            //var thanhtien_dann = thanhtien.toPrecision(2);
            $(this).parent().parent().find('.thanhtien').text(number_format(thanhtien,2,'.',''));

            $(this).parent().parent().find('.hangthem_hidden').val(hangthem);

            //total_price
            var list_thanhtien = [];
            $.each($(".infomation .thanhtien"), function (index) {
                list_thanhtien.push
                ({
                    thanhtien: parseFloat($(this).text())
                });
            });

            //check san pham co dau #, k cho xuat hang

            var total_product = 0;
            for (var i = 0; i < list_thanhtien.length ; i++){
                total_product += list_thanhtien[i]['thanhtien'];
            }
            $(".total_price").text(number_format(total_product,2,'.',''));
            //total_price

            //change tong tien no
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();

            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }

        });

        $("body").on("change", ".input_note", function () {
            var value = $(this).val();
            $(this).parent().parent().find('.item_note_hidden').val(value);
        });

        $("body").on("click", ".refund", function () {
            var variant_id = $(this).parent().parent().find('.variant_hidden').val();
            var hangve = $(this).parent().parent().find('.hangve_hidden').val();
            var refund_class = $(this);
            $.ajax({
                url: "<?php echo base_url(); ?>voxy_package_orders/refund_product",
                async: false,
                type: "POST",
                dataType: "json",
                data: {
                    variant_id: variant_id,
                    hangve:hangve
                },
                success: function (data) {
                    if (data.state === 1) {
                        refund_class.parent().parent().find('.refund_hidden').val(0);
                        if(refund_class.hasClass('refund_active')){
                            refund_class.removeClass('refund_active');
                            refund_class.addClass('refund_deactive');
                        }
                        console.log("Hàng trả về thành công");

                    } else {
                        console.log("Hàng trả về thất bại.");
                    }
                },
                error: function () {
                    console.log("loi ajax get id");
                }
            });

        });

        number_format = function (number, decimals, dec_point, thousands_sep) {
            number = number.toFixed(decimals);

            var nstr = number.toString();
            nstr += '';
            x = nstr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? dec_point + x[1] : '';
            var rgx = /(\d+)(\d{3})/;

            //while (rgx.test(x1))
                x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');

            return x1 + x2;
        }

        $("body").on("change",".tt_lan1 input",function () {
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();
            //var $tt_no = $(".tt_no input").val();

            $(".thanhtoan_lan1").val($tt_lan1);
            $(".thanhtoan_lan2").val($tt_lan2);
            $(".thanhtoan_lan3").val($tt_lan3);
            $(".thanhtoan_lan4").val($tt_lan4);
            $(".thanhtoan_lan5").val($tt_lan5);
            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }
        });

        $("body").on("change",".tt_lan2 input",function () {
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();
            //var $tt_no = $(".tt_no input").val();

            $(".thanhtoan_lan1").val($tt_lan1);
            $(".thanhtoan_lan2").val($tt_lan2);
            $(".thanhtoan_lan3").val($tt_lan3);
            $(".thanhtoan_lan4").val($tt_lan4);
            $(".thanhtoan_lan5").val($tt_lan5);
            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }

        });

        $("body").on("change",".tt_lan3 input",function () {
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();
            //var $tt_no = $(".tt_no input").val();

            $(".thanhtoan_lan1").val($tt_lan1);
            $(".thanhtoan_lan2").val($tt_lan2);
            $(".thanhtoan_lan3").val($tt_lan3);
            $(".thanhtoan_lan4").val($tt_lan4);
            $(".thanhtoan_lan5").val($tt_lan5);
            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }
        });

        $("body").on("change",".tt_lan4 input",function () {
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();
            //var $tt_no = $(".tt_no input").val();

            $(".thanhtoan_lan1").val($tt_lan1);
            $(".thanhtoan_lan2").val($tt_lan2);
            $(".thanhtoan_lan3").val($tt_lan3);
            $(".thanhtoan_lan4").val($tt_lan4);
            $(".thanhtoan_lan5").val($tt_lan5);
            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }
        });

        $("body").on("change",".tt_lan5 input",function () {
            var $total_price = parseFloat($(".total_price").text());
            var $tt_lan1 = $(".tt_lan1 input").val();
            var $tt_lan2 = $(".tt_lan2 input").val();
            var $tt_lan3 = $(".tt_lan3 input").val();
            var $tt_lan4 = $(".tt_lan4 input").val();
            var $tt_lan5 = $(".tt_lan5 input").val();
            //var $tt_no = $(".tt_no input").val();

            $(".thanhtoan_lan1").val($tt_lan1);
            $(".thanhtoan_lan2").val($tt_lan2);
            $(".thanhtoan_lan3").val($tt_lan3);
            $(".thanhtoan_lan4").val($tt_lan4);
            $(".thanhtoan_lan5").val($tt_lan5);
            $tongtien_no = number_format($total_price - $tt_lan1 - $tt_lan2 - $tt_lan3 - $tt_lan4 - $tt_lan5,2,'.','');
            $(".tongtien_no").val($tongtien_no);
            $(".tt_no input").val($tongtien_no);

            if($tt_lan1 == "" && $tt_lan2 == "" && $tt_lan3 == "" && $tt_lan4 == "" && $tt_lan5 == ""){
                $(".tongtien_no").val("");
                $(".tt_no input").val("");
            }
        });

        $("#xml").on('click',function () {
           $("#myTable").tableExport({
              type: 'xml',
              escape: 'true'
           });
        });
        /*
        //check cac san pham co dau THang, phai sua san pham moi cho xuat hang
        $(".b_edit").on('click',function () {
            $.each($(".title"),function(){
                var value = $(this).val();
                if(value.indexOf("#") == 0){
                    $( "#dialog" ).css('display','block');
                    $( "#dialog" ).dialog();
                    return false;
                }
            });
        });

        */
    });
</script>

<style>
    .pro_th{
        width: 100%;
        float: left;
        background: chartreuse;
        color: #613737;
    }
    .le {
        background: #dce5f5;
        color: #1b1212;;
        height: 55px;
        line-height: 55px;
    }

    .chan {
        height: 55px;
        line-height: 55px;
        background: bisque;
        color: #080404;
    }

    .class5{
        width: 5%;
        float: left;
        text-align: left;
    }
    .class10{
        width: 10%;
        float: left;
        text-align: left;
    }
    .class15{
        width: 15%;
        float: left;
        text-align: left;
    }
    .class20{
        width: 20%;
        float: left;
        text-align: left;
    }
    .class25 {
        width: 25%;
        float: left;
        text-align: left;
    }
    .class30{
        width: 30%;
        float: left;
        text-align: left;
    }

    .class35{
        width: 35%;
        float: left;
        text-align: left;
    }
    .class40{
        width: 40%;
        float: left;
        text-align: left;
    }
    .class50{
        width: 50%;
        float: left;
        text-align: left;
    }
    .class70{
        width: 70%;
        float: left;
        text-align: left;
    }

    .class100{
        width: 100%;
        float: left;
        text-align: left;
        margin: 15px 15px;
        padding 20px;
        font-size: 20px;
    }

    .input-25{
        width: 150px !important;
        padding: 5px !important;
        font-size: 20px !important;
        min-width: 150px !important;
    }
    .off{
        display: none;
    }

    .parent-result {
        position: relative;
    }

    .list_timkiem {
        position: absolute;
        left: 15%;
        right: 20%;
        top: 300%;
        z-index: 999;
        background-color: #00796a;
        color: white;
        width: 909px;
    }

    .list_timkiem ul li {
        line-height: 20px;
        width: 600px;
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
    .form_add_product {
        position: relative;
    }

    .infomation{
        height: 55px;
        line-height: 55px;
    }

    .thanhtien{
        text-align: center;
    }

    .tongtien{
        padding-left: 146px;
        color: red;
        font-size: 20px;
    }

    .tongtien-text{
        text-align: center;
        color: red;
        font-size: 20px;
    }

    .refund_active{
        display: inline-block;
    }
    .refund_deactive {
        display: none;
    }

    .form-horizontal .control-label{
        width: 105px !important;
        text-align: left;
    }

    form .control-group .controls-row {
        margin-left: 105px !important;
    }

    .color_hangve{
        color: white !important;
        background: #2b8eab !important;
    }

    .color_hangthieu{
        color: white !important;
        background: #38ab41 !important;
    }

    .color_hangthem{
        color: white !important;
        background: #6a2cab !important;
    }

    .color_hanghong{
        color: white !important;
        background: #ab2362 !important;
    }

</style>
