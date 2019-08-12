<div class="span6 resizable uniform modal-content container chuvantinh" style="width:70%;margin: 0 auto;    padding: 10px;">
    <form class="form-horizontal e_ajax_submit" action="<?php echo $save_link; ?>" enctype="multipart/form-data" method="POST">

        <div class="title">
            <span type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></span>
            <h3><?php echo $title; ?></h3>
        </div>

        <?php $_str_readonly = ' readonly=readonly style="background: #d9d9d9"'; ?>
        <?php foreach ($list_input as $input) { ?>
            <?php
            if(isset(${'readonly_' . $input->name}) && ${'readonly_' . $input->name} == true){
                $input->string_rule .= $_str_readonly;
            }
            ?>
            <div class="row2 <?php echo ($input->rule['type'] == 'hidden' ? $input->rule['type'] : ''); ?>">
                <label class="col-25" for="<?php echo $input->name; ?>"><?php echo $input->label; ?></label>
                <div class="col-75">
                    <?php if ($input->rule['type'] == 'textarea') { ?>
                        <textarea name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" ></textarea>
                    <?php } elseif ($input->rule['type'] == 'select') {  if ($input->name == "location") { ?>

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
                            <div class="columns_2" style="width: 50%; float: left;padding-right: 5px;">
                                <label class="col-30" style="text-align: left !important;">Sỉ</label>
                                <input type="text" class="col-70" name="option2" id="id_option2" placeholder="Karton">

                                <?php if($this->USER->user_name == "maianh"){
                                    $class_maianh = "readonly";
                                }else{
                                    $class_maianh = "";
                                }

                                ?>
                                <label class="col-30" style="background: #72b110;text-align: left !important; for="id_price2">G.bán sỉ </label>
                                <input type="text" style=""class="col-70"name="price2" id="id_price2" placeholder="Giá bán si" <?php echo $class_maianh ; ?>>

                                <?php if($this->USER->user_name == "tinhcv" || $this->USER->user_name == "long" || $this->USER->user_name == "maianh" || $this->USER->user_name == "lanasia24"){
                                    if($this->USER->user_name == "maianh"){
                                        $class_maianh = "readonly";
                                    }else{
                                        $class_maianh = "";
                                    }
                                    ?>
                                    <label class="col-30" style="background: aquamarine;text-align: left !important;">Gb.Min Sỉ </label>
                                    <input type="text" class="col-70"name="si_midest_price" id="id_si_mindest_price" placeholder="Giá min sỉ" <?php echo $class_maianh ; ?>>

                                    <label class="col-30" style="background: red;color: white;text-align: left !important;">G.mua sỉ </label>
                                    <input type="text" class="col-70"name="gia_mua_si" id="id_gia_mua_si" placeholder="Giá mua si" <?php echo $class_maianh ; ?>>

                                    <label class="col-30" style="background: #62aeef;color: white;text-align: left !important;">GW. si</label>
                                    <input type="text" class="col-70" name="heso_gb_si" id="id_heso_gb_si" placeholder="hệ số gb lẻ" <?php echo $class_maianh ; ?>>
                                <?php } ?>

                                <label class="col-30" style="text-align: left !important; for="id_barcode2">UPC Si </label>
                                <input type="text" class="col-70"name="barcode2" id="id_barcode2" placeholder="mã barcode 9856">
                                <label class="col-30" style="text-align: left !important; for="id_sku2">SKU Sỉ</label>
                                <input type="text" class="col-70"name="sku2" id="id_sku2" placeholder="mã sp A1234">
                                <label class="col-30" style="text-align: left !important; for="id_inventory_quantity2">SL Sỉ</label>
                                <input type="text" class="col-70"name="inventory_quantity2" id="id_inventory_quantity2" placeholder="số lượng tổng sau khi nhập hàng">
                            </div>

                            <div class="columns_2" style="width: 50%; float: left;">
                                <label class="col-30" style="text-align: left !important;" for="id_option1">Lẻ</label>
                                <input type="text" class="col-70" name="option1" id="id_option1" placeholder="Packung" >
                                <?php if($this->USER->user_name == "maianh"){
                                    $class_maianh = "readonly";
                                }else{
                                    $class_maianh = "";
                                }
                                ?>
                                <label class="col-30" style="background: #72b110;text-align: left !important; for="id_price1">G.bán lẻ</label>
                                <input type="text" style="" class="col-70" name="price1" id="id_price1" placeholder="giá 10€" <?php echo $class_maianh ; ?>>

                                <?php if($this->USER->user_name == "tinhcv" || $this->USER->user_name == "long" || $this->USER->user_name == "maianh" || $this->USER->user_name == "lanasia24"){
                                    if($this->USER->user_name == "maianh"){
                                        $class_maianh = "readonly";
                                    }else{
                                        $class_maianh = "";
                                    }
                                    ?>
                                    <label class="col-30" style="background: aquamarine;text-align: left !important;">Gb.Min Lẻ</label>
                                    <input type="text" class="col-70" name="le_midest_price" id="id_le_mindest_price" placeholder="giá min lẻ" <?php echo $class_maianh ; ?>>

                                    <label class="col-30" style="background: red;color: white;text-align: left !important;">G.mua lẻ</label>
                                    <input type="text" class="col-70" name="gia_mua_le" id="id_gia_mua_le" placeholder="giá mua lẻ" <?php echo $class_maianh ; ?>>

                                    <label class="col-30" style="background: #62aeef;color: white;text-align: left !important;">GW. le</label>
                                    <input type="text" class="col-70" name="heso_gb_le" id="id_heso_gb_le" placeholder="hệ số gb lẻ" <?php echo $class_maianh ; ?>>

                                <?php } ?>
                                <label class="col-30" style="text-align: left !important; for="id_barcode1">UPC Lẻ</label>
                                <input type="text" class="col-70" name="barcode1" id="id_barcode1" placeholder="mã barcode 9856">
                                <label class="col-30" style="text-align: left !important; for="id_sku1">SKU Lẻ</label>
                                <input type="text" class="col-70" name="sku1" id="id_sku1" placeholder="mã sp A1234">
                                <label class="col-30" style="text-align: left !important; for="inventory_quantity1">Sl lẻ</label>
                                <input type="text" class="col-70" name="inventory_quantity1" id="id_inventory_quantity1" placeholder="số lượng 100">
                            </div>

                        </div>
                    <?php } elseif ($input->rule['type'] == 'default_address') { ?>
                        <div class="control_group">
                            <input type="text" name="d_first_name" id="id_first_name" placeholder="Van Tinh">
                            <input type="text" name="d_last_name" id="id_first_name" placeholder="Chu">
                            <input type="text" name="address1" id="id_address1" placeholder="pichelsdorfer strasse 131" >
                            <input type="text" name="zip" id="id_zip" placeholder="Postlei zahn 13595">
                            <input type="text" name="city" id="id_city" placeholder="Berlin">
                            <input type="hidden" name="country_code" id="id_country_code" placeholder="DE">
                            <input type="text" name="country_name" id="id_country_name" placeholder="Germany">
                            <input type="text" name="phone" id="id_phone" placeholder="0987 654 1234">
                            <input type="hidden" name="default" id="id_default" placeholder="true" value="true">
                        </div>
                    <?php } else { ?>
                        <input name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <div class="row2">
            <button type="submit" class="b_add b_edit btn btn-primary">Lưu</button>
            <button type="reset" class="b_add btn">Nhập lại</button>
            <button type="button" class="b_view b_add b_edit btn" data-dismiss="modal">Hủy</button>
        </div>
    </form>
</div>

<style type="text/css">
    .select2-container {
        margin: 5px 0;
    }
</style>

<script type="text/javascript" charset="utf-8">
    $( document ).ready(function() {
        $('input[type="datetime"]').datepicker({
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016',
        });

        //xu ly add them vao so luong
        $("#them_vao").on('click',function () {
            var value = $("#inventory_sl").val();
            if(value){
                if(value.match(/^\d+$/) && 2000 > parseInt(value) > 0) {
                    var value_vorne =  $("#id_inventory_quantity2").val();
                    if( !value_vorne ) {
                        $("#id_inventory_quantity2").val(parseInt(value));
                    }else {
                        var value_new2 = parseInt(value) + parseInt(value_vorne) ;
                        $("#id_inventory_quantity2").val(value_new2);
                        $("#inventory_sl").val('');
                    }
                }else {
                    alert('Bạn phải nhập số tự nhiên lớn hơn 0  nho hon 2000 va ko phai barcode');
                }
            }

            var value_scanner = $("#inventory_sc").val();

            if (value_scanner) {
                var value_before =  $("#id_inventory_quantity2").val();
                if( !value_before ) {
                    $("#id_inventory_quantity2").val(1);
                }else {
                    var value_new2 = 1 + parseInt(value_before) ;
                    $("#id_inventory_quantity2").val(value_new2);
                    $("#inventory_sc").val('');
                }
            }
        });

        $("#i_location").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });

        $("#id_heso_gb_si").on("change",function () {
            var heso_si = $(this).val();
            var gia_mua_si = $("#id_gia_mua_si").val();
            var si_mindest_price = $("#id_si_mindest_price");
            var value = gia_mua_si / (1 - (heso_si / 100));
            var value = value.toPrecision(3);
            si_mindest_price.val(value);
        });

        $("#id_heso_gb_le").on("change",function () {
            var heso_le = $(this).val();
            var gia_mua_le = $("#id_gia_mua_le").val();
            var le_mindest_price = $("#id_le_mindest_price");
            var value = gia_mua_le / (1 - (heso_le / 100));
            var value = value.toPrecision(3);
            le_mindest_price.val(value);
        });

    });
</script>

<style type="text/css">
    /* Style inputs, select elements and textareas */
    input[type=text], select, textarea,input:active, input:focus, input:focus-within, input:hover, input:visited {
        padding: 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        resize: none;
        padding-left: 3px;
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
    .col-30 {
        float: left;
        width: 40% !important;
        margin: 0;
    }
    .col-70 {
        float: left;
        width: 60% !important;
        margin : 0;
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
    }
    .modal_content {
        margin-top: 0 !important;
    }

    .modal_content input {
        min-width: 0 !important;
    }

    .title {
        background-color: #62aeef
    }

    /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
    @media only screen and (max-width: 480px) {
        .col-25, .col-75, input[type=submit] {
            margin-top: 0;
        }
        label[for="variants"]{
            display: none;
        }
        .chuvantinh {width: 100% !important;}
        .ui-resizable {
            padding: 15px;
        }

        .chu_row2 {
            width: 133% !important;
        }
    }
</style>