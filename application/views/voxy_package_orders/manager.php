<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
                        <a href="<?php echo $get_link; ?>"
                           class="btn i-plus-circle-2 btn-info e_ajax_link_get add_button">Lấy dữ liệu</a>
                        <!--                        <a href="-->
                        <?php //echo $add_link; ?><!--" class="btn i-plus-circle-2 btn-info e_ajax_link add_button" > Thêm </a>-->

                        <a href="<?php echo base_url('voxy_package_orders/xml'); ?>"
                           class="btn i-cancel-circle-2 btn-success for_select hide xml-button">
                            XML </a>

                        <a href="<?php echo $delete_list_link; ?>"
                           class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide">
                            Xóa </a>
                        <span class="btn i-loop-4 delete_button e_reverse_button for_select hide"> Đảo ngược </span>

                        <a href="#" class="btn i-cancel-circle-2 btn-warning for_select hide ghino-all-button">
                            Ghi Nợ Tất Cả  </a>
                        <a href="#" class="btn i-cancel-circle-2 btn-success for_select hide thanhtoan-all-button">
                            Thanh toán tất cả</a>
                    </div>
                    <a href="#" class="minimize"></a>
                </div>

                <!-- div form search-->
                <div class="widget-form-search" style="display: block">
                    <?php $custom_where = (isset($form_conds['custom_where']) ? $form_conds['custom_where'] : array()); ?>
                    <?php $custom_like = (isset($form_conds['custom_like']) ? $form_conds['custom_like'] : array()); ?>

                    <!--                    // ko lien quan tim kiem-->
                    <div class="row-fluid">
                        <form class="form2" method="post" action="htmltopdf" style="width:100%; float: left;" target="_blank" autocomplete="off">
                            <div class="kho_tong" style="padding: 0 15px; border: 1px solid #c9c9c9">
                                <p style="font-size: 16px !important;font-weight: bold;">Đơn tổng theo Tour</p>

<!--                                <div style="width: 20%;float:left;padding: 5px">-->
<!--                                    <span>Ngày đặt hàng:</span>-->
<!--                                    <input type="datetime" name="date_for_orders" class="ngay_dat_hang"-->
<!--                                           style="line-height: 34px;"-->
<!--                                           placeholder="ngày tao don">-->
<!--                                </div>-->

                                <div class="t-nhathang"style="width: 25%; float:left;padding: 5px">
                                    <span>Ngày giao hàng:</span>
                                    <input type="datetime" name="date_liefer" class="ngay_giao_hang date_for_orders2"
                                           style="line-height: 34px;"
                                           placeholder="ngày xe chạy">
                                </div>

                                <div class="taixe t-nhathang" style="float: left; width: 25%;padding: 5px">
                                    <span>Tài xế:</span>
                                    <?php if(isset($shipper)) { ?>
                                        <select name="shipper_id[]" class="laixe" multiple>
                                            <?php foreach ($shipper as $item) { ?>
                                                <option value="<?php echo $item->id;?>"><?php echo $item->first_name;?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>

                                <div class="category t-nhathang" style="float: left; width: 25%;padding: 5px">
                                    <span>Danh mục:</span>
                                    <?php if(isset($category)) {
                                        ?>
                                        <select name="category_id[]" class="category-select" multiple>
                                            <?php foreach ($category as $item) { ?>
                                                <option value="<?php echo $item['cat_id'];?>"><?php echo $item['title'];?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>

                                <div style="display:none;float: left; width: 25%;padding: 5px" class="kho_select t-nhathang">
                                    <span>Chọn kho:</span>
                                    <select name="kho">
                                        <option value="all">Tất Cả</option>
                                        <option value="cua_hang">Cửa Hàng</option>
                                        <option value="lil">Kho Khô</option>
                                        <option value="AKL">Kho Lạnh</option>
                                    </select>
                                </div>

                                <div style="float: left; width: 25%;padding: 5px;" class="sorting t-nhathang">
                                    <span>Sắp xếp:</span>
                                    <select name="sorting">
                                        <option value="category">Danh mục</option>
                                        <option value="location">Vị trí</option>
                                    </select>
                                </div>

                                <input type="hidden" class="list_id_to_nhathang" name="list_id_to_nhathang">

                                <div class="clear"></div>
                                <p style="text-align: center;">

                                    <button type="submit" id="pdf" name="htmltopdf" class="nhat-hang-pdf btn btn-danger">
                                        Đơn Tổng
                                    </button>
                                    <button type="submit" class= "btn-danger btn" formaction="htmltopdf/print_money_day">Übersicht</button>
                                    <button type="submit" class= "btn-inverse btn" formaction="htmltopdf/print_money_day_history">Übersicht Lịch Sử</button>
                                    <button type="submit" class= "btn-success btn" formaction="htmltopdf/print_money_day_excel">Übersicht Excel</button>
                                    <button type="submit" class= "btn-danger btn" formaction="htmltopdf/pdf_list_giaohang">Lieferschein</button>
<!--                                    <a href="--><?php //echo base_url('voxy_package_orders/excel_day'); ?><!--"-->
<!--                                       class="excel_day btn btn-success btn-lg"> Excel </a>-->

                                    <button type="submit" class="btn btn-info donkiem" formaction="htmltopdf/print_order_allready">Đơn Kiểm</button>
                                    <button type="submit" class="btn btn-warning orders-compare" formaction="voxy_package_orders/orders_compare">Đối chiếu nợ</button>
                                    <button type="submit" class="btn btn-warning chiphi_laixe" formaction="voxy_package_orders/add_chiphi_laixe">Chi Phí Lái Xe</button>
                                </p>

                            </div>
                        </form>
                    </div>

                    <!--                    // ko lien quan tim kiem-->

                    <!-- div widget manage -->
                    <div class="widget-manage">
                        <!-- div form ajax-->
                        <div class="clear clear-form-search" style="border-top:  1px solid #c9c9c9"></div>
                        <p style="padding-left: 15px; font-size: 16px !important;font-weight: bold;">Đơn lẻ</p>
                        <div class="widget-content data_table e_data_table"
                             data-url="<?php echo base_url(); ?>voxy_package_orders/ajax_list_data_new.html"
                             style="border-top:0 !important;"
                             data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">

                            <!-- Ajax load ding content -->
                        </div>
                    </div>

                    <div id="acknowledged-dialog" title="Thông báo ghi nợ" style="display: none">
                        <p>GHI NỢ cho đơn hàng thành công.</p>
                    </div>

                    <div id="acknowledged-dialog-thanhtoan" title="Thông báo thanh toán" style="display: none">
                        <p>THANH TOÁN cho đơn hàng thành công.</p>
                    </div>

                    <script type="text/javascript" charset="utf-8">
                        $("div.widget-form-search").find("select").select2();
                        if ($("div.widget-form-search").length > 0 && $("div.widget-form-search").css('display') == 'block') {
                            $("div.clear-form-search").css('margin-bottom', '5px');
                        }
                    </script>

                    <script type="text/javascript" charset="utf-8">

                        $(document).on("click", "#i_btn_search_submit", function (e) {
                            e.preventDefault();
                            var obj = $(".e_data_table");
                            show_ajax_loading(obj);
                            $("#i_form_filter").ajaxSubmit({
                                dataType: "text",
                                success: function (dataAll) {
                                    var temp = dataAll.split($("body").attr("data-barack"));
                                    var data = {};
                                    for (var i in temp) {
                                        temp[i] = $.parseJSON(temp[i]);
                                        data = $.extend({}, data, temp[i]);
                                    }
                                    if (window[data.callback]) {
                                        console.log("Gọi hàm: ", data.callback);
                                        window[data.callback](data, obj);
                                    } else {
                                        console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_data_table'");
                                        default_data_table(data, obj);
                                    }
                                }
                            });
                        });

                        $(document).on("click", ".e_ajax_link_get", function (e) {
                            e.preventDefault();
                            var obj = $(".e_data_table");
                            show_ajax_loading(obj);
                            var $ulr = $('.e_ajax_link_get').attr('href');
                            var $list_id = $('.delete_list_button').attr('data');
                            $.ajax({
                                url: $ulr,
                                type: 'POST',
                                data: {
                                    list_id: $list_id,
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if (data.state === 1) {
                                        $('.e_data_table img[title="Loading"]').parent().css({
                                            "display": "none"
                                        });
                                        location.reload();
                                    }
                                },
                                error: function (a, b, c) {
                                    console.log("KHong get duoc orders tu shopify");
                                    //location.reload();
                                },
                                complete: function (jqXHR, textStatus) {

                                }
                            });
                        });
                        // begin ngay bat dau
                        $('.date_for_orders').datepicker({
                            dateFormat: "dd-mm-yy",
                            changeMonth: true,
                            changeYear : true,
                            numberOfMonths: 1,
                            minDate: '01-01-2016'
                        });

                        $('.date_for_orders2').datepicker({
                            dateFormat: "dd-mm-yy",
                            changeMonth: true,
                            changeYear : true,
                            numberOfMonths: 1,
                            minDate: '01-01-2016'
                        });


                        var href = $('.excel_day').attr('href');
                        $('.date_for_orders').on('change', function () {
                            var date = $('.date_for_orders').datepicker('getDate');
                            if (date.getDate() < 10) {
                                var getdate = '0' + date.getDate();
                            } else {
                                getdate = date.getDate();
                            }

                            if( date.getMonth() < 10){
                                var get_month = '0' + (date.getMonth() +1) ;
                            }else{
                                get_month = date.getMonth();
                            }

                            var data2 = date.getFullYear() + '-' + get_month + '-' + getdate;//prints expected format.
                            $('.date_export').val(data2);
                            $('.excel_day').attr('href', href + '?date=' + data2);
                            //var $theoxe = "";
                            $('input[type=checkbox]').on('click', function () {
                                //$theoxe = $(this).val();
                                $('.excel_day').attr('href', href + '?date=' + data2);
                            });
                        });
                        //end ngay bat dau

                        //begin ngay ket thuc
                        $('.date_for_orders_end').datepicker({
                            dateFormat: "dd-mm-yy",
                            changeMonth: true,
                            changeYear : true,
                            numberOfMonths: 1,
                            minDate: '01-01-2016'
                        });

                        $('.date_for_orders_end').on('change', function () {
                            var date = $('.date_for_orders_end').datepicker('getDate');
                            if (date.getDate() < 10) {
                                var getdate = '0' + date.getDate();
                            } else {
                                getdate = date.getDate();
                            }

                            if( date.getMonth() < 10){
                                var get_month = '0' + (date.getMonth() +1) ;
                            }else{
                                get_month = date.getMonth();
                            }

                            var data2 = date.getFullYear() + '-' + get_month + '-' + getdate;//prints expected format.
                            $('.date_time_end').val(data2);
                            $('.excel_day').attr('href', href + '?date=' + data2);
                        });
                        //end ngay ket thuc

                        //xu ly href xuat noch mal
                        var global_ngay_dat_hang ;
                        var href = $('.excel_day').attr('href');
                        $('.date_for_orders').on('change', function () {
                            var date = $('.date_for_orders').datepicker('getDate');
                            if (date.getDate() < 10) {
                                var getdate = '0' + date.getDate();
                            } else {
                                getdate = date.getDate();
                            }

                            if( date.getMonth() < 10){
                                var get_month = '0' + (date.getMonth() +1) ;
                            }else{
                                get_month = date.getMonth();
                            }

                            var data2 = date.getFullYear() + '-' + get_month + '-' + getdate;//prints expected format.
                            global_ngay_giao_hang = data2;

                            $('.date_export').val(data2);
                            $('.excel_day').attr('href', href + '?date=' + data2);
                            $('input[type=checkbox]').on('click', function () {
                                $('.excel_day').attr('href', href + '?date=' + data2);
                            });

                        });
                        //end ngay ket thuc
                        $(".nhat-hang-pdf").click(function () {
                                var value = $(".delete_list_button").attr('data');
                                $(".list_id_to_nhathang").val(value);
                        });
                        $(".donkiem").click(function () {
                            var value = $(".delete_list_button").attr('data');
                            $(".list_id_to_nhathang").val(value);
                        });

                        // thay doi table theo cac option ben tren
                        $("body").on("change", ".ngay_dat_hang", function () {
                            var ngaydathang = $(".ngay_dat_hang");
                            var ngaygiaohang = $(".ngay_giao_hang");
                            //var laixe = $(".laixe option:selected").val();
                            var selected=[];
                            var i = -1;
                            $('.laixe option:selected').each(function(){
                                i++;
                                selected[i]=$(this).val();
                            });

                            //var laixe = $(".laixe option:selected").val();
                            var laixe = selected;
                            if(laixe == ""){
                                laixe = "all";
                            }
                            creat_ajax_table_tinhcv(ngaydathang,ngaygiaohang, laixe);
                        });

                        $("body").on("change", ".ngay_giao_hang", function () {
                            var ngaydathang = $(".ngay_dat_hang");
                            var ngaygiaohang = $(".ngay_giao_hang");

                            var selected=[];
                            var i = -1;
                            $('.laixe option:selected').each(function(){
                                i++;
                                selected[i]=$(this).val();
                            });

                            //var laixe = $(".laixe option:selected").val();
                            var laixe = selected;
                            if(laixe == ""){
                                laixe = "all";
                            }
                            creat_ajax_table_tinhcv(ngaydathang,ngaygiaohang, laixe);
                        });

                        $("body").on("change", ".laixe", function () {
                            var ngaydathang = $(".ngay_dat_hang");
                            var ngaygiaohang = $(".ngay_giao_hang");

                            var selected=[];
                            var i = -1;
                            $('.laixe option:selected').each(function(){
                                i++;
                                selected[i]=$(this).val();
                            });

                            //var laixe = $(".laixe option:selected").val();
                            var laixe = selected;
                            if(laixe == ""){
                                laixe = "all";
                            }
                            creat_ajax_table_tinhcv(ngaydathang,ngaygiaohang, laixe);
                        });

                        function creat_ajax_table_tinhcv(ngay_dat_hang, ngay_giao_hang,laixe) {
                            var url = "<?php echo base_url(); ?>voxy_package_orders/ajax_list_data_new.html";
                            var $ngay_dat_hang = ngay_dat_hang.val();
                            var $ngay_giao_hang = ngay_giao_hang.val();
                            var $laixe = laixe;

                            var limit = $('.e_data_table').find(".e_changer_number_record").val();
                            var page = $('.e_data_table').find(".e_data_paginate li.active a").attr("data-page");
                            var order = [];
                            temp_order = {};
                            $('.e_data_table').find("thead tr th").each(function() {
                                if ($(this).attr("order")) {
                                    if ($(this).attr("order") == "asc" || $(this).attr("order") == "desc") {
                                        //                order.push($(this).attr("field_name") + " " + $(this).attr("order"));
                                        temp_order[$(this).attr("order_pos")] = $(this).attr("field_name") + " " + $(this).attr("order");
                                    }
                                }
                            });
                            for (var i in temp_order) {
                                order.push(temp_order[i]);
                            }

                            var data = {
                                ngay_dat_hang: $ngay_dat_hang,
                                ngay_giao_hang: $ngay_giao_hang,
                                laixe: $laixe,
                                limit: limit
                            };

                            show_ajax_loading($('.e_data_table'));

                            $.ajax({
                                url: url,
                                type: "GET",
                                data: data,
                                dataType: "text",
                                success: function(dataAll) {
                                    remove_ajax_loading();
                                    var temp = dataAll.split($("body").attr("data-barack"));
                                    var data = {};
                                    for (var i in temp) {
                                        temp[i] = $.parseJSON(temp[i]);
                                        data = $.extend({}, data, temp[i]);
                                    }
                                    if (window[data.callback]) {
                                        console.log("Gọi hàm: ", data.callback);
                                        window[data.callback](data, obj);
                                    } else {
                                        console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_data_table'");
                                        default_data_table(data, $('.e_data_table'));
                                    }

                                },
                                error: function(a, b, c) {
                                    alert(a + b + c);
                                    //window.location = url;
                                }
                            });

                        }

                        $(".xml-button").on("click",function () {
                            var value = $(".delete_list_button").attr('data');
                            var href = $(this).attr("href");
                            $(".xml-button").attr('href', href + '?id=' + value);
                        });



                        //2 nut o cho action
                        $(document).on("click", ".do_ghino", function (e) {
                            var order_number = $(this).attr('data-order-number');
                            //show_ajax_loading($('.e_data_table'));
                            $.ajax({
                                url: "<?php echo base_url('voxy_package_orders/do_ghino'); ?>",
                                type: "post",
                                data: {
                                    order_number: order_number
                                },
                                dataType: "json",
                                success: function(dataAll) {
                                    //remove_ajax_loading();
                                    if(dataAll.state == 1){
                                        $("#acknowledged-dialog").dialog({
                                            height: 140,
                                            modal: true,
                                            open: function(event, ui){
                                                setTimeout("$('#acknowledged-dialog').dialog('close')",700);
                                            }
                                        });
                                    }

                                    $("."+order_number).removeClass('good');
                                    $("."+order_number).addClass('baodo');
                                    $("."+order_number+ ' td[for_key="m.tongtien_no"]').text(dataAll.tongtien_no);
                                },
                                error: function(a, b, c) {
                                    alert(a + b + c);
                                    //window.location = url;
                                }
                            });
                        });

                        $(document).on('click', '.do_thanhtoan',function(){
                            var order_number = $(this).attr('data-order-number');
                            //show_ajax_loading($('.e_data_table'));
                            $.ajax({
                                url: "<?php echo base_url('voxy_package_orders/do_thanhtoan'); ?>",
                                type: "post",
                                data: {
                                    order_number: order_number
                                },
                                dataType: "json",
                                success: function(dataAll) {
                                    //remove_ajax_loading();
                                    if(dataAll.state == 1){
                                        $("#acknowledged-dialog-thanhtoan").dialog({
                                            height: 140,
                                            modal: true,
                                            open: function(event, ui){
                                                setTimeout("$('#acknowledged-dialog-thanhtoan').dialog('close')",700);
                                            }
                                        });
                                    }

                                    $("."+order_number).removeClass('baodo');
                                    $("."+order_number).addClass('good');
                                    $("."+order_number+ ' td[for_key="m.tongtien_no"]').text(dataAll.tongtien_no);
                                },
                                error: function(a, b, c) {
                                    alert(a + b + c);
                                    //window.location = url;
                                }
                            });
                        });
                        //2 nut o cho action


                        //2 nut ben tren, ghi no cho tat ca, va thanh toan cho tat ca
                        $(document).on("click", ".ghino-all-button", function (e) {
                            var list_id = $(".delete_list_button").attr('data');

                            //show_ajax_loading($('.e_data_table'));
                            $.ajax({
                                url: "<?php echo base_url('voxy_package_orders/do_ghino'); ?>",
                                type: "post",
                                data: {
                                    list_id: list_id
                                },
                                dataType: "json",
                                success: function(dataAll) {
                                    //remove_ajax_loading();
                                    if(dataAll.state == 1){
                                        $("#acknowledged-dialog").dialog({
                                            height: 140,
                                            modal: true,
                                            open: function(event, ui){
                                                setTimeout("$('#acknowledged-dialog').dialog('close')",700);
                                            }
                                        });
                                    }

                                    $.each(dataAll.list_orders,function (index, item) {
                                        $("."+item.order_number).removeClass('good');
                                        $("."+item.order_number).addClass('baodo');
                                        $("."+item.order_number+ ' td[for_key="m.tongtien_no"]').text(item.tongtien_no);
                                    });
                                },
                                error: function(a, b, c) {
                                    alert(a + b + c);
                                    //window.location = url;
                                }
                            });
                        });

                        $(document).on("click", ".thanhtoan-all-button", function (e) {
                            var list_id = $(".delete_list_button").attr('data');

                            //show_ajax_loading($('.e_data_table'));
                            $.ajax({
                                url: "<?php echo base_url('voxy_package_orders/do_thanhtoan'); ?>",
                                type: "post",
                                data: {
                                    list_id: list_id
                                },
                                dataType: "json",
                                success: function(dataAll) {
                                    //remove_ajax_loading();
                                    if(dataAll.state == 1){
                                        $("#acknowledged-dialog-thanhtoan").dialog({
                                            height: 140,
                                            modal: true,
                                            open: function(event, ui){
                                                setTimeout("$('#acknowledged-dialog-thanhtoan').dialog('close')",700);
                                            }
                                        });
                                    }

                                    $.each(dataAll.list_orders,function (index,order_number) {
                                        $("."+order_number).removeClass('baodo');
                                        $("."+order_number).addClass('good' );
                                        $("."+order_number+ ' td[for_key="m.tongtien_no"]').text("");
                                    });
                                },
                                error: function(a, b, c) {
                                    alert(a + b + c);
                                    //window.location = url;
                                }
                            });
                        });
                        //end 2 nut ben tren


                    </script>
                </div>
            </div>
        </div>
    </div>

    <script type="text/css">

        .kho_select{
            width: 150px;
        }

        @media only screen and (max-width: 600px) {
            .kho_select {
                margin-top: 21px; margin-left: 20px;
            }
        }

        .select2-choice {
            height: 40px !important;
        }



    </script>

<style>
    @media only screen and (max-width: 600px) {
        .t-nhathang {
            width: 100% !important;
        }
    }
</style>
