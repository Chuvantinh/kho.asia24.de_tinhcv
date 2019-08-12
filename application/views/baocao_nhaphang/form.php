
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<div class="span6 resizable uniform modal-content" style="width:1000px">
    <form class="form-horizontal e_ajax_submit" autocomplete="off" action="<?php echo $save_link; ?>" enctype="multipart/form-data" method="POST">

        <div class="modal-header">
            <span type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></span>
            <h3><?php echo $title; ?></h3>
        </div>

        <div class="modal-body bgwhite new-abc">
            <?php
            if($status == 1){
                $_str_readonly = ' readonly=readonly style="background: #d9d9d9"';
            }else{
                $_str_readonly = "";
            }
            ?>
            <div class="left">
                <?php foreach ($list_input as $input) { ?>
                    <?php
                    if(isset(${'readonly_' . $input->name}) && ${'readonly_' . $input->name} == true){
                        $input->string_rule .= $_str_readonly;
                    }
                    ?>

                    <?php
                    if ($input->rule['type'] == "products") { ?>
                                <div class="products">
                                        <p><b>Products</b></p>
                                    <input type="text" name="search_pro" class="search_pro span12">
                                    <div class="container_list">
                                        <div class="list_result"></div>
                                    </div>
                                    <div class="wrapper_information" style="font-weight:bold; text-align: center;    height: 20px;">
                                        <div class="id" style="width: 2%;float: left;">STT</div>
                                        <div class="action" style="width: 5%;float: left;">&nbsp;</div>
                                        <div class="sku" style="width: 7%;float: left;">SKU</div>
                                        <div class="title" style="width: 45%;float: left;">Tên</div>
                                        <div class="sl_nhap" style="width: 10%;float: left;">SL</div>
                                        <div class="variant_title" style="width: 10%;float: left;">Đơn vị</div>
                                        <div class="gianhap" style="width: 10%;float: left;text-align: right">Giá Nhập</div>
                                        <div class="giaban" style="width: 7%;float: left;text-align: right">G. Bán</div>
<!--                                        <div class="location" style="width: 10%;float: left;">Vị trí</div>-->
                                    </div>
                                    <div class="list_products_transfer">
                                        <?php
                                        if(isset($products_history) && $products_history != null ){
                                            $i = 0;
                                        foreach (json_decode($products_history) as $item) {
                                            $item = get_object_vars($item);
                                            $i++;
                                            if($i % 2 == 0){
                                                $class = "gerade";
                                            }else{
                                                $class = "ungerade";
                                            }
                                            ?>
                                            <div  class="infomation <?php echo $class; ?>" style="padding: 5px 0; display: block;width: 100%;float: left;">
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][variant_id]' value='<?php echo $item['variant_id'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][cat_id]' value='<?php echo $item['cat_id'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][product_id]' value='<?php echo $item['product_id'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][sl_kho]' value='<?php echo $item['sl_kho'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][variant_title]' value='<?php echo $item['variant_title'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][location]' value='<?php echo $item['location'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][title]' value='<?php echo $item['title'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][sku]' value='<?php echo $item['sku'];?>'>

                                                <div style="width: 2%;float: left;text-align: left;"><?php echo $i; ?></div>
                                                <div class="remove" style="width: 5%;float:left">
                                                    <i class="material-icons" title="xóa" style="color: #e47885">close</i>
                                                </div>
                                                <div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'><?php echo $item['sku'] ?></div>
                                                <div style='width: 45%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'><?php echo $item['title'];?></div>
                                                <div style='width: 10%;height: auto;float: left' class='quantity'>
                                                    <input type='text' name='quantity[<?php echo $item['variant_id'];?>]'
                                                           value="<?php echo $item['sl_nhap'];?>"
                                                           class='quantity' style='min-width:50px;width: 50px;text-align: center;' <?php echo $_str_readonly; ?> >
                                                </div>
                                                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'><?php echo $item['variant_title'];?></div>

                                                <div style='width: 10%;height: auto;float: left' class='gianhapnew'>
                                                    <input type='text' name='gianhapnew[<?php echo $item['variant_id'];?>]'
                                                           value="<?php echo (isset($item['gianhapnew'])?$item['gianhapnew']:0);?>"
                                                           class='input_gianhapnew' style='min-width:50px;width: 50px;text-align: center;' <?php echo $_str_readonly; ?> >
                                                </div>

                                                <div style='width: 7%;height: auto;float: left' class='giabannew'>
                                                    <input type='text' name='giabannew[<?php echo $item['variant_id'];?>]'
                                                           value="<?php echo (isset($item['giabannew'])?$item['giabannew']:0);?>"
                                                           class='input_giabannew' style='min-width:50px;width: 50px;text-align: center;' <?php echo $_str_readonly; ?> >
                                                </div>
<!--                                                <div style='width: 10%;margin-left:5px;height: auto;float: left' class='location' data-location= '--><?php //echo $item['location'];?><!--'>--><?php //echo $item['location'];?><!--</div>-->
                                            </div>
                                        <?php } }?>
                                    </div>
                                </div>

                    <?php }  ?>

                    <?php if ($input->name == "note") { ?>
                        <div class="note" style="clear: left">
                            <label class="" for="<?php echo $input->name; ?>"><?php echo $input->label; ?></label>
                            <input name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <div class="right col-lg-4">
                <?php foreach ($list_input as $input) { ?>
                <?php if ($input->name != "note" && $input->name != "products") { ?>

                    <?php if ($input->name == "adresse") { ?>
                        <div class="adresse">
                            <p><span class="label-right"><?php echo $input->label; ?></span></p>
                            <?php if ($input->rule['type'] == 'select') { ?>
                                <select class="select2" name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>>
                                    <?php foreach ($input->option as $option) { ?>
                                        <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <input  class="text-right" name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                            <?php } ?>
                        </div>
                    <?php }elseif ($input->name == "datetime") { ?>
                        <div class="datetime">
                            <p><span class="label-right"><?php echo $input->label; ?></span></p>
                            <?php if ($input->rule['type'] == 'select') { ?>
                                <select class="select2" name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>>
                                    <?php foreach ($input->option as $option) { ?>
                                        <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <input  class="text-right" name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                            <?php } ?>
                        </div>

                    <?php } elseif ($input->name == "datetime") { ?>
                        <div class="datetime">
                            <p><span class="label-right"><?php echo $input->label; ?></span></p>
                            <?php if ($input->rule['type'] == 'select') { ?>
                                <select class="select2" name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>>
                                    <?php foreach ($input->option as $option) { ?>
                                        <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <input  class="text-right" name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="control-group <?php echo ($input->rule['type'] == 'hidden' ? $input->rule['type'] : ''); ?>">
                            <p><span class="label-right"><?php echo $input->label; ?></span></p>
                            <?php if ($input->rule['type'] == 'select') { ?>
                                <select class="select2" name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>>
                                    <?php foreach ($input->option as $option) { ?>
                                        <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <input  class="text-right" name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php } ?>
                <?php } ?>
            </div>

        </div>

        <div class="modal-footer">
            <?php if($status != 1 && $status != 3){ ?>
                <button type="submit" class="b_add b_edit btn btn-primary">Xác nhận</button>
            <?php } ?>
            <button type="reset" class="b_add btn">Nhập lại</button>
            <button type="button" class="b_view b_add b_edit btn" data-dismiss="modal">Thoát</button>
        </div>
    </form>
</div>

<style type="text/css">

    .left {
        width: 80% !important;
    }

    .right {
        width: 20% !important;
    }

    .modal_content input.text-right{
        min-width: 100px !important;
    }
    .container_list{
        position: absolute;
        border: solid 1px red;
        height: 300px;
        width: 669px;
        overflow: auto;
        display: none;
    }
    .list_result {
        position: absolute;
        left: 0;
        right: 0;
        z-index: 999;
        background-color: #00796a;
        color: white;
        width: 669px;
    }

    .list_result ul li {
        line-height: 20px;
        width: 600px;
        list-style: none;
    }

    .result-search {
        width: 500px;
    }

    .list_result ul li:hover{
        cursor: pointer;
        background-color: #CCCCCC;
    }

    .ungerade {
        background-color: #f9f9f9;
    }

    .wrapper_information{
        background-color: #dcf4fc;
    }

    .modal_content input {
        min-width: 292px;
        padding-left: 10px !important;
        margin-bottom: 5px !important;
    }
    .right {
        margin-top: 9px;
    }
    .control-group:before,.control-group:after {
        content: "";
        display: none !important;
    }

    .remove {
        text-shadow: none;
        min-width: 20px !important;
        width: 20px!important;
        height: 20px!important;
        font-size: 14px;
        line-height: 20px;
        padding: 0;
        color: #e47885;
        margin-right: 20px;
        text-align: center;
        padding-left: 7px;
    }
    .remove i:hover{
        cursor: pointer;
        background-color: #e47885;
        color: white !important;
    }

    .add-all {
        float: right;
        margin: 5px;
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

        $("#i_products").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });

        //get variants for products
        $('#i_products').on('change',function (e) {
                var id_products = $(this).val();
                var id_transfer = $('#i_id').val();
                //check value cua product_variants
                if(id_products){
                        $.ajax({
                            url: "<?php echo base_url(); ?>voxy_package/get_product_from_id_products",
                            async: false,
                            type: "POST",
                            dataType: "json",
                            data : {products: id_products,id_transfer : id_transfer},
                            success :function (data) {
                                if(data.state === 1){
                                    $('.info_products').html(data.html);
                                }else{
                                    $('.info_products').html('<h3>Không có sản phẩm ở vị trí này </h3>');
                                }
                            },
                            error: function () {
                                console.log("loi ajax get id");
                            }
                        });

                }
        });
        //lan dau hien thi o form
        var id_products = $('#i_products').val();
        var id_transfer = $('#i_id').val();
        if(id_products){
            $('.product_variants').css('display','block');
            $.ajax({
                url: "<?php echo base_url(); ?>voxy_package/get_product_from_id_products",
                async: false,
                type: "POST",
                dataType: "json",
                data : {products: id_products,id_transfer : id_transfer},
                success :function (data) {
                    if(data.state === 1){
                        $('.info_products').html(data.html);
                    }else{
                        $('.info_products').html('<h3>Không có sản phẩm ở vị trí này </h3>');
                    }
                },
                error: function () {
                    console.log("loi ajax get id");
                }
            });
        }else{
            $('.product_variants').css('display','none');
        }

        $(".search_pro").on("change",function(e){
            var request = $(this).val();
            var search = $(".list_result");
            if(request.length > 2){
                $.ajax({
                    url: "<?php echo base_url(); ?>voxy_transfer/search_pro",
                    async: false,
                    type: "POST",
                    dataType: "json",
                    data: {request: request},
                    success: function (data) {
                        if (data.state === 1) {
                            search.css("display","block");
                            search.html(data.html);
                            $(".container_list").css("display","block");
                            var keyCode = e.keyCode || e.which;

                            if (keyCode == 9) {
                                e.preventDefault();
                                $('html, body').animate({ scrollTop: $('.list_result').offset().top }, 'slow');
                            }
                        } else {
                            search.css("display","block");
                            $(".container_list").css("display","block");
                            search.html("<h3>Không có sản phẩm ở vị trí này </h3>");
                        }
                    },
                    error: function () {
                        console.log("loi ajax get id");
                    }
                });
            }

            if(request.length == 0){
                $(".container_list").css("display","none");
                search.css("display","none");
            }

        });

        //remove element
        $("body").on("click", ".remove", function() {
            $(this).parent().remove();
        });

        //prevent enter to submit
        $('.form-horizontal'). keydown(function (e) {
            if (e. keyCode == 13) {
                e. preventDefault();
                return false;
            }
        });

    });
</script>
