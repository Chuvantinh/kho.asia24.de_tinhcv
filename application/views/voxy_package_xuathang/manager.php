<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
<!--                        <a href="--><?php //echo $get_link; ?><!--"-->
<!--                           class="btn i-plus-circle-2 btn-info e_ajax_link_get add_button"> Lấy dữ liệu</a>-->
                        <!--                        <a href="-->
                        <?php //echo $add_link; ?><!--" class="btn i-plus-circle-2 btn-info e_ajax_link add_button" > Thêm </a>-->
                        <a href="<?php echo $delete_list_link; ?>"
                           class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide">
                            Xóa </a>
                        <span class="btn i-loop-4 delete_button e_reverse_button for_select hide"> Đảo ngược </span>
                    </div>
                    <a href="#" class="minimize"></a>
                </div>

                <!-- div form search-->
                <div class="widget-form-search" style="display: block">
                    <?php $custom_where = (isset($form_conds['custom_where']) ? $form_conds['custom_where'] : array()); ?>
                    <?php $custom_like = (isset($form_conds['custom_like']) ? $form_conds['custom_like'] : array()); ?>

                    <!-- // ko lien quan tim kiem-->
                    <div class="row-fluid">
                        <form  autocomplete="off" class="form2" method="post" action="voxy_package_xuathang/xuathang" style="width:100%; float: left;padding: 0 15px; border: 1px solid #c9c9c9" target="_blank">
                                <p style="font-size: 16px !important;font-weight: bold;">Xuất hàng theo kho</p>

                                <div style="width: 33%; float:left;padding: 5px">
                                    <span>Ngày giao:</span>
                                    <input type="datetime" name="date_for_orders" class="date_for_orders" placeholder="ngày">
                                </div>

                                <div class="kho_select" style="display:none;width: 25%; float:left;padding: 5px">
                                    <span>Chọn kho:</span>
                                    <select name="kho">
                                        <option value="all">Tất Cả</option>
                                        <option value="lil">Kho Khô</option>
                                        <option value="AKL">Kho Lạnh</option>
                                        <option value="cua_hang">Cửa Hàng</option>
                                    </select>
                                </div>

                                <div class="" style="width: 33%; float:left;padding: 5px">
                                        <span>Lái Xe:</span>
                                        <select name="shipper_id" class="laixe">
                                            <?php if(isset($shipper)) {
                                                foreach ($shipper as $item) {
                                                    ?>
                                                    <option value="<?php echo $item->id;?>"><?php echo $item->first_name;?></option>
                                                <?php } } ?>
                                        </select>
                                </div>

                                <div class="" style="width: 33%; float:left;padding: 5px">
                                    <span>Lọc theo: </span>
                                    <select name="sorting">
                                        <option value="category">Danh mục</option>
                                        <option value="location">Vi trí</option>
                                    </select>
                                </div>

                            <input type="hidden" class="list_id_to_nhathang" name="list_id_to_nhathang">

                                <div style="text-align: center;margin-top: 20px;" class="span12">
                                    <button type="submit" id="pdf" name="htmltopdf" class="btn btn-danger xuathang_tong">
                                        Xuất Hàng
                                    </button>
<!--                                    <button type="submit" class= "btn-info btn" formaction="htmltopdf/print_money_day">Tổng hợp</button>-->
<!--                                    <button type="submit" class= "btn-success btn" formaction="htmltopdf/pdf_list_giaohang">Phiếu Giao Hàng</button>-->
                                    <button type="submit" class= "btn-inverse btn" formaction="htmltopdf/pdf_list_kiemhang">Báo cáo</button>
                                </div>
                        </form>
                    </div>

                    <!-- div widget manage -->
                    <div class="widget-manage">
                        <!-- div form ajax-->
                        <div class="clear clear-form-search" style="border-top:  1px solid #c9c9c9"></div>
                        <p style="padding-left: 15px; font-size: 16px !important;font-weight: bold;">Xuất kho theo đơn hàng lẻ</p>
                        <div class="widget-content data_table e_data_table" data-url="<?php echo $ajax_data_link; ?>"
                             style="border-top:0 !important;"
                             data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">

                            <!-- Ajax load ding content -->
                        </div>
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
                            //creat_ajax_table_tinhcv($(this));
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
                        $(".xuathang_tong").click(function () {
                            var value = $(".delete_list_button").attr('data');
                            $(".list_id_to_nhathang").val(value);
                            setTimeout(function () {
                                //location.reload();
                            }, 1000);
                        });
                        //week
                        // console.log($('#uniform-week_id').val());
                        //                     $('#uniform-week_id').on('click', function () {
                        //                         alert("adfdsf");
                        //                         var year = $('#uniform-week_id').selected().val();
                        //                         console.log(year);
                        //                     })

                        //xu ly class autos

                        ///*function creat_ajax_table_tinhcv(obj) {
                        //    var url = "<?php //echo base_url(); ?>//voxy_package_xuathang/ajax_list_data.html";
                        //    var q = obj.val();
                        //    var limit = $('.e_data_table').find(".e_changer_number_record").val();
                        //    var page = $('.e_data_table').find(".e_data_paginate li.active a").attr("data-page");
                        //    var order = [];
                        //    temp_order = {};
                        //    $('.e_data_table').find("thead tr th").each(function() {
                        //        if ($(this).attr("order")) {
                        //            if ($(this).attr("order") == "asc" || $(this).attr("order") == "desc") {
                        //                //                order.push($(this).attr("field_name") + " " + $(this).attr("order"));
                        //                temp_order[$(this).attr("order_pos")] = $(this).attr("field_name") + " " + $(this).attr("order");
                        //            }
                        //        }
                        //    });
                        //    for (var i in temp_order) {
                        //        order.push(temp_order[i]);
                        //    }
                        //    order = order.reverse();
                        //    order = order.join(",");
                        //    var data = {
                        //        q: q,
                        //        limit: limit,
                        //        page: page,
                        //        order: order
                        //    };
                        //
                        //    show_ajax_loading($('.e_data_table'));
                        //
                        //    $.ajax({
                        //        url: url,
                        //        type: "POST",
                        //        data: data,
                        //        dataType: "text",
                        //        success: function(dataAll) {
                        //            remove_ajax_loading();
                        //            var temp = dataAll.split($("body").attr("data-barack"));
                        //            var data = {};
                        //            for (var i in temp) {
                        //                temp[i] = $.parseJSON(temp[i]);
                        //                data = $.extend({}, data, temp[i]);
                        //            }
                        //            if (window[data.callback]) {
                        //                console.log("Gọi hàm: ", data.callback);
                        //                window[data.callback](data, obj);
                        //            } else {
                        //                console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_data_table'");
                        //                default_data_table(data, $('.e_data_table'));
                        //            }
                        //
                        //        },
                        //        error: function(a, b, c) {
                        //            alert(a + b + c);
                        //            window.location = url;
                        //        }
                        //    });
                        //
                        //}*/

                        $("body").on("change", ".date_for_orders", function () {
                            var ngaygiaohang = $(".date_for_orders");
                            var laixe = $('.laixe option:selected').val();
                            creat_ajax_table_tinhcv(ngaygiaohang, laixe);
                        });

                        $("body").on("change", ".laixe", function () {
                            var ngaygiaohang = $(".date_for_orders");

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
                            creat_ajax_table_tinhcv(ngaygiaohang, laixe);
                        });

                        function creat_ajax_table_tinhcv(date_for_orders,laixe) {
                            //var url = "<?php echo base_url(); ?>voxy_package_xuathang/ajax_list_data_xuathangtaikho.html";
                            var url = "<?php echo base_url(); ?>voxy_package_xuathang/ajax_list_data.html";
                            var $ngay_giao_hang = date_for_orders.val();
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
                                date_for_orders: $ngay_giao_hang,
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


    </script>

    <style>
        @media only screen and (max-width: 600px) {
            .xuathang_tong{
                display: block !important;
            }
        }
    </style>
