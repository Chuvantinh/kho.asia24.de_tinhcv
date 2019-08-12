<div class="modal-content container chuvantinh" style="width: 70%; margin: 0px auto">
    <form class="form-horizontal e_ajax_submit" action="<?php echo $save_link; ?>" enctype="multipart/form-data" method="POST">
        <div class="title">
            <span type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></span>
            <h3><?php echo $title; ?></h3>
        </div>

        <?php $_str_readonly = ' readonly=readonly style="background: #d9d9d9"'; ?>
        <?php
        foreach ($list_input as $input) {
            ?>
            <?php
            if(isset(${'readonly_' . $input->name}) && ${'readonly_' . $input->name} == true){
                $input->string_rule .= $_str_readonly;
            }
            ?>

            <?php if($input->label != "Cài đặt hết hàng" &&  $input->label != "Cài đặt lưu trữ hàng"
                && $input->label != "Tên tìm nhanh" && $input->label != "MWST" && $input->label != "Mô tả"
                && $input->label != "Tìm nhanh Sỉ"
                && $input->label != "Loại sản phẩm"
                && $input->label != "Nhà cung cấp" && $input->label != "Trạng thái") { ?>

                <div class="row2 <?php echo ($input->rule['type'] == 'hidden' ? $input->rule['type'] : ''); ?>">

                    <label class="col-25" for="<?php echo $input->name; ?>"><?php echo $input->label; ?></label>
                    <div class="col-75">
                        <?php if ($input->rule['type'] == 'textarea') { ?>
                            <textarea name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" ></textarea>
                        <?php } elseif ($input->rule['type'] == 'select') {  if ($input->name == "location") { //var_dump($input);die;?>

                            <select class="select2" name="<?php echo $input->name."[]"; ?>" id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule;?> multiple>
                                <?php
                                foreach ($input->option as $option) {
                                    if($option->display != null) {
                                    $_location_selected = explode(",", $location_selected[0]['location']);
                                    ?>
                                    <option value="<?php echo $option->value; ?>" <?php
                                    foreach ($_location_selected as $location_ed) {
                                        if ( $option->value == $location_ed) { ?> selected="selected" <?php } } ?> ><?php echo $option->display; ?></option>
                                    <?php
                                } } ?>
                            </select>

                        <?php } else { ?>
                            <select class="select2" name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule;?>>
                                <?php foreach ($input->option as $option) { ?>
                                    <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>

                        <?php }  elseif ($input->rule['type'] == 'rich_editor') { ?>
                            <textarea class="ckeditor" data-ckfinder="<?php echo $this->path_static_file.'plugins/ckfinder'; ?>" name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> ></textarea>
                        <?php } elseif ($input->rule['type'] == 'packung_karton') { ?>
                            <div class="row2 chu_row2">
                                <div class="col" style="width: 50%; float: left;">
                                    <label class="col-25" style="display:none; text-align: left !important; margin-bottom: 0px !important;" for="id_option2">Sỉ</label>
                                    <input class="col-75" type="text" name="option2" id="id_option2" placeholder="Karton" style="display:none;margin-bottom: 0px !important;">

                                    <label class="col-25" style="display:none;text-align: left !important;">Giá mua sỉ </label>
                                    <input type="text" style="display:none;" class="col-75"name="gia_mua_si" id="id_gia_mua_si" placeholder="Giá mua si">

                                    <label class="col-25" style="display:none;text-align: left !important;">Giá mua lẻ</label>
                                    <input type="text" style="display:none;" class="col-75" name="gia_mua_le" id="id_gia_mua_le" placeholder="giá mua lẻ">

                                    <label class="col-25" style="display:none;text-align: left !important; margin-bottom: 0px !important; for="id_barcode2">Barcode Sỉ</label>
                                    <input class="col-75" type="text" name="barcode2" id="id_barcode2" placeholder="mã barcode 9856" style="display:none;margin-bottom: 0px !important;">

                                    <label class="col-25" style="display:none;text-align: left !important; margin-bottom: 0px !important; for="id_barcode1">Barcode Lẻ</label>
                                    <input class="col-75" type="text" name="barcode1" id="id_barcode1" placeholder="mã barcode 9856" style="display:none;margin-bottom: 0px !important;">

                                    <label class="col-25" style="display:none; text-align: left !important; margin-bottom: 0px !important; for="id_sku2">Mã Sản Phẩm (SKU)</label>
                                    <input class="col-75" type="text" name="sku2" id="id_sku2" placeholder="mã sp A1234" style="display:none; width: 75% !important; margin-bottom: 0px !important;">

                                    <label class="col-25" style="text-align: left !important; margin-bottom: 0px !important;" for="id_inventory_quantity2">SL Sỉ</label>
                                    <input class="col-75" type="text" name="inventory_quantity2" id="id_inventory_quantity2" placeholder="số lượng sỉ" style="text-align:center;margin-bottom: 0px !important;">

                                    <label class="col-25" style="display:none; text-align: left !important; margin-bottom: 0px !important; for="id_price2">Giá € </label>
                                    <input class="col-75" type="text" name="price2" id="id_price2" placeholder="Giá 10€" style=" display:none; width: 75% !important; margin-bottom: 0px !important;" readonly>
                                </div>

                                <div class="col" style="width: 50% ; float: left ;padding-left: 10px;">
                                    <label class="col-25" style="display:none; text-align: left !important; margin-bottom: 0px !important;" for="id_option1">Lẻ</label>
                                    <input class="col-75" type="text" name="option1" id="id_option1" placeholder="Packung" style=" display:none; width: 75% !important; margin-bottom: 0px !important;" >

                                    <label class="col-25" style=" display:none; text-align: left !important; margin-bottom: 0px !important; for="id_sku1">Mã Sản phẩm</label>
                                    <input class="col-75" type="text" name="sku1" id="id_sku1" placeholder="mã sp A1234" style="display:none; width: 75% !important; margin-bottom: 0px !important;">

                                    <label class="col-25" style="display:none; text-align: left !important; margin-bottom: 0px !important; for="id_price1">Giá €</label>
                                    <input class="col-75" type="text" name="price1" id="id_price1" placeholder="giá 10€" style="display:none; width: 75% !important; margin-bottom: 0px !important;" readonly>

                                    <label class="col-25" style="text-align: left !important; margin-bottom: 0px !important;" for="inventory_quantity1">SL Lẻ</label>
                                    <input class="col-75" type="text" name="inventory_quantity1" id="id_inventory_quantity1" placeholder="số lượng le" style="text-align:center;margin-bottom: 0px !important;">
                                </div>

                                <hr>
                            </div>
                        <?php } else { ?>
                            <input name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                        <?php } ?>
                    </div>

                </div>

            <?php } ?>

        <?php }  ?>

        <div class="row2" style="margin-top: 10px;">
            <button type="submit" class="b_add b_edit btn btn-primary">Lưu</button>
            <button type="reset" class="b_add btn">Nhập lại</button>
            <button type="button" class="b_view b_add b_edit btn" data-dismiss="modal">Hủy</button>
        </div>
    </form>
</div>

<script type="text/javascript" charset="utf-8">
    $( document ).ready(function() {
        $('input[type="datetime"]').datepicker({
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016',
            onSelect: function(dateText) {
                $("#foo").focus();
                $(".e_ajax_submit").submit();
            }
        });

        //thay doi chuot den vi tri can thiet
        $("#i_location").on("click",function () {
            $("#id_barcode2").focus();
        });

        $("#id_barcode2").on("change",function () {
                $("#id_barcode1").focus();
        });

        $("#id_barcode1").on("change",function () {
                $("#id_inventory_quantity2").focus();
        });
        $("#id_inventory_quantity2").on("change",function () {
            $("#id_inventory_quantity1").focus();
        });
        $("#id_inventory_quantity1").on("change",function () {
            $("#i_expri_day").focus();
        });

        //ngan chan may scann
        $("#i_location").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });

        $("#id_barcode2").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });

        $("#id_barcode1").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });
        $("#id_inventory_quantity2").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });
        $("#id_inventory_quantity1").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });
        //ngan chan may scann

        $(".b_edit").on("click",function () {
            $("#foo").focus();
        });

        $('input[type=search]').on('focus', function(){
            // replace CSS font-size with 16px to disable auto zoom on iOS
            $(this).data('fontSize', $(this).css('font-size')).css('font-size', '13px');
        }).on('blur', function(){
            // put back the CSS font-size
            $(this).css('font-size', $(this).data('fontSize'));
        });

    });
</script>
<style type="text/css">
    /* Style inputs, select elements and textareas */
    input[type=text], select, textarea, input:active, input:focus, input:focus-within, input:hover, input:visited {
        width: 100%;
        padding: 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        resize: vertical;
    }

    /* Style the submit button */
    input[type=submit] {
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        float: right;
    }

    /* Style the container */
    .container {
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 20px;
    }

    /* Floating column for labels: 25% width */
    .col-25 {
        float: left;
        width: 25% !important;
        margin: 0;
    }

    /* Floating column for inputs: 75% width */
    .col-75 {
        float: left;
        width: 75% !important;
        margin : 0;
    }
    label{
        margin: 0;padding: 0;
    }

    /* Clear floats after the columns */
    .row:after {
        content: "";
        display: table;
        clear: both;
    }
    .row2{
        margin: 0;padding: 0;
        float: left;
        width: 100%;
        margin-top: 3px;
    }
    .modal_content {
        margin-left: 0 !important;
        margin-top: 0 !important;
    }

    .modal_content input {
        min-width: 0 !important;
    }

    .title {
        background-color: #62aeef
    }

    /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
    @media only screen and (max-width: 800px) {
        .col-25, .col-75, input[type=submit] {
            margin-top: 0;
        }
    }

    @media only screen and (max-width: 480px) {
        .col-25, .col-75, input[type=submit] {
            margin-top: 0;
        }
        label[for="variants"]{
            display: none;
        }
        .chuvantinh {width: 100% !important;}
        .chu_row2 {width: 133% !important;}
    }
</style>