
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<div class="span6 resizable uniform modal-content" style="width:1300px">
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
            <div class="t-left">
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

                                    <ul class="t-tab-kiemkho">
                                        <li class="t-li-all active" data-tab="tab1">Tất cả <span class="nummer1">(0)</span></li>
                                        <li class="t-li-match" data-tab="tab2">Khớp<span class="nummer2">(0)</span></li>
                                        <li class="t-li-not-match" data-tab="tab3">Lệch<span class="nummer3">(0)</span></li>
<!--                                        <li class="t-li-not-control">Chưa kiểm <span class="nummer4">(0)</span></li>-->
                                    </ul>

                                    <div class="wrapper_information">
                                        <div class="id" style="width: 2%;float: left;">STT</div>
                                        <div class="id" style="width: 2%;float: left;">

                                        </div>

                                        <div class="action" style="width: 5%;float: left;">&nbsp;</div>
                                        <div class="location" style="width: 20%;float: left;">Vị Trí</div>
                                        <div class="sku" style="width: 7%;float: left;">SKU</div>
                                        <div class="title" style="width: 35%;float: left;">Tên</div>
                                        <div class="variant_title" style="width: 10%;float: left;">Đơn vị</div>
                                        <div class="tonkho" style="width: 10%;float: left;text-align: left">Tồn kho</div>
                                        <div class="sl_kiem" style="width: 10%;float: left;">Thực tế</div>
                                        <div class="sailech" style="width: 10%;float: left;text-align: center">Sai lệch</div>
                                    </div>

                                    <div class="list_products_kiemkho">
                                        <?php
                                        $this->load->model('m_voxy_package');
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

                                            $title = $this->m_voxy_package->get_title_productid($item['product_id']);
                                            ?>
                                            <div  class="infomation <?php echo $class; ?>" style="padding: 5px 0; display: block;width: 100%;float: left;">
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][variant_id]' value='<?php echo $item['variant_id'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][cat_id]' value='<?php echo $item['cat_id'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][product_id]' value='<?php echo $item['product_id'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][sl_kho]' value='<?php echo $item['sl_kho'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][variant_title]' value='<?php echo $item['variant_title'];?>'>
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][location]' value='<?php echo $item['location'];?>' class='location-hidden'>
<!--                                                <input type='hidden' name='information[--><?php //echo $item['variant_id'];?><!--][title]' value='--><?php //echo $item['title'];?><!--'>-->
                                                <input type='hidden' name='information[<?php echo $item['variant_id'];?>][sku]' value='<?php echo $item['sku'];?>'>


                                                <div style="width: 2%;float: left;text-align: left;"><?php echo $i; ?></div>
<!--                                                --><?php //if($item['location'] != ""){ ?>
<!--                                                    <div style="width: 10%;float: left;text-align: left;">--><?php //echo ($item['location'] != "")?$item['location']:"null"; ?><!--</div>-->
<!--                                                --><?php //}else{ ?>
<!--                                                    <div style="width: 10%;float: left;text-align: left;">-->
<!--                                                        <input type='text' name=''-->
<!--                                                               value="--><?php //echo $item['location'];?><!--"-->
<!--                                                               class='input-location' style='min-width:30px;width: 30px;text-align: center;' --><?php //echo $_str_readonly; ?><!-- >-->
<!--                                                    </div>-->
<!--                                                --><?php //} ?>

                                                <div style='width: 20%;float: left;text-align: left;'>
                                                    <input type='text'
                                                           value="<?php echo $item['location'] ?>"
                                                           class='input-location' style='width: 100%;text-align: center;'>
                                                </div>

                                                <div class="remove" style="width: 5%;float:left">
                                                    <i class="material-icons" title="xóa" style="color: #e47885">close</i>
                                                </div>
                                                <div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'><?php echo $item['sku'] ?></div>
                                                <div style='width: 35%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'><?php echo $title;?></div>
                                                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'><?php echo $item['variant_title'];?></div>
                                                <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sl_kho'><?php echo $item['sl_kho'];?></div>
                                                <div style='width: 10%;height: auto;float: left' class='thucte'>
                                                    <input type='text' name='quantity[<?php echo $item['variant_id'];?>]'
                                                           value="<?php echo $item['sl_kiemkho'];?>"
                                                           class='quantity' style='min-width:50px;width: 50px;text-align: center;' <?php echo $_str_readonly; ?> >
                                                </div>
                                                <div style='width: 10%;height: auto;float: left;text-align: center !important;' class='sailech'><?php echo $item['sailech'];?></div>
                                            </div>
                                        <?php } }?>
                                    </div>
                                </div>

                    <?php }  ?>
                <?php } ?>
            </div>

            <div class="t-right">
                <?php
                foreach ($list_input as $input) { ?>
                    <?php if ( $input->name != "products") { ?>

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
                    <?php }
                } ?>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="b_add b_edit btn btn-primary">Xác nhận</button>
            <button type="reset" class="b_add btn">Nhập lại</button>
            <button type="button" class="b_view b_add b_edit btn" data-dismiss="modal">Thoát</button>
        </div>
    </form>
</div>

<style type="text/css">

    .t-right {
        float: left;
        width: 15%;
    }
    .t-left{
        float: left;
        width: 85%;
    }

    input {
        min-width: auto !important;
        width: 100% !important;
        text-align: left !important;
    }

    form .control-group {
        border: none;
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
    .infomation {
        line-height: 35px;
    }

    .wrapper_information{
        background-color: #dcf4fc;
        float: left;
        width: 100%;
        border: 1px solid #CCCCCC;
        height: 40px !important;
        line-height: 40px;
        font-weight:bold;
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
        padding: 6px;
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

    .t-tab-kiemkho{
        list-style: none;
        width: 100%;
        float: left;
        margin: 0;
        padding: 0;
        height: 40px;
        line-height: 40px;
    }

    .t-tab-kiemkho li {
        display: inline-block;
        height: 40px;
        background-color: #eee;
        border: 1px solid #d9d9d9;
        margin-right: 5px;
        padding: 5px;
        margin-bottom: 0;
        cursor: pointer;
        line-height: 31px;
    }

    .t-tab-kiemkho li.active {
        background: white;
    }
    .add-all {
        float: right;
        margin: 5px;
    }
    .t-tab-kiemkho,.sl_kho,.tonkho,.sailech{
        display: none;
    }



</style>

<script type="text/javascript" charset="utf-8">
    $( document ).ready(function() {
        $('input[name="date_save"]').datepicker({
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
                var id_kiemkho = $('#i_id').val();
                //check value cua product_variants
                if(id_products){
                        $.ajax({
                            url: "<?php echo base_url(); ?>voxy_package/get_product_from_id_products",
                            async: false,
                            type: "POST",
                            dataType: "json",
                            data : {products: id_products,id_kiemkho : id_kiemkho},
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
        var id_kiemkho = $('#i_id').val();
        if(id_products){
            $('.product_variants').css('display','block');
            $.ajax({
                url: "<?php echo base_url(); ?>voxy_package/get_product_from_id_products",
                async: false,
                type: "POST",
                dataType: "json",
                data : {products: id_products,id_kiemkho : id_kiemkho},
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
                    url: "<?php echo base_url(); ?>voxy_kiemkho/search_pro",
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

        $("body").on("change", ".quantity", function() {
            var sl_thucte = $(this).val();
            var sl_tonkho = $(this).parent().parent().find('.sl_kho').text();
            var sl_sailech =  parseInt(sl_thucte)-parseInt(sl_tonkho);
            $(this).parent().parent().find('.sailech').text(sl_sailech);//add text vao cho sai lech

            //for lai toan bo div information
            var sl_not_match = 0;
            var sl_match = 0;

            $.each($(".infomation"), function (index) {
                var sl_thucte = $(this).find(".quantity").val();
                var sl_tonkho = $(this).find('.sl_kho').text();
                var sl_sailech =  parseInt(sl_thucte)-parseInt(sl_tonkho);

                if(sl_sailech === 0){
                    sl_match++;
                    $(this).removeClass("tab3");
                    $(this).addClass("tab2");
                }else{
                    sl_not_match++;
                    $(this).removeClass("tab2");
                    $(this).addClass("tab3");
                }
            });

            $('.wrapper_information').attr('sl_match',sl_match);
            $('.nummer2').text("( " + sl_match + ")");

            $('.wrapper_information').attr('sl_not_match', sl_not_match);
            $('.nummer3').text("( " + sl_not_match + ")");

        });

        var sl_thucte = $(this).val();
        var sl_tonkho = $(this).parent().parent().find('.sl_kho').text();
        var sl_sailech =  parseInt(sl_thucte)-parseInt(sl_tonkho);
        $(this).parent().parent().find('.sailech').text(sl_sailech);//add text vao cho sai lech

        //for lai toan bo div information
        var sl_not_match = 0;
        var sl_match = 0;
        var count_all = 0;
        $.each($(".infomation"), function (index) {
            count_all++;
            var sl_thucte = $(this).find(".quantity").val();
            var sl_tonkho = $(this).find('.sl_kho').text();
            var sl_sailech =  parseInt(sl_thucte)-parseInt(sl_tonkho);

            if(sl_sailech === 0){
                sl_match++;
                $(this).removeClass("tab3");
                $(this).addClass("tab2");
            }else{
                sl_not_match++;
                $(this).removeClass("tab2");
                $(this).addClass("tab3");
            }
        });

        $('.wrapper_information').attr('sl_match',sl_match);
        $('.nummer2').text("( " + sl_match + ")");

        $('.wrapper_information').attr('sl_not_match', sl_not_match);
        $('.nummer3').text("( " + sl_not_match + ")");

        $('.wrapper_information').attr('sl_products', count_all);
        $('.nummer1').text("( " + count_all + ")");



        $("body").on("click", ".t-tab-kiemkho li", function() {
            $.each($(".t-tab-kiemkho li"),function () {
                    $(this).removeClass('active');
            });
            $(this).addClass('active');//add class cho tab

            var data_tab = $(this).attr("data-tab");

            if(data_tab == "tab1"){
                $.each($(".infomation"), function (index) {
                    $(this).css("display","block");
                });
            }else{
                $.each($(".infomation"), function (index) {
                    $(this).css("display","none");
                    $("."+data_tab).css("display","block");
                });
            }
        });


        $("body").on("change", ".input-location", function() {
            var value = $(this).val();
            $(this).parent().parent().find('.location-hidden').attr('value',value);
        });

    });
</script>
