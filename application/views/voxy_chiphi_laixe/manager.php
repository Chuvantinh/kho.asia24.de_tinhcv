<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
                        <a href="<?php echo $add_link; ?>" class="btn i-plus-circle-2 btn-info e_ajax_link add_button" > Thêm </a>
                        <a href="<?php echo $delete_list_link; ?>"
                           class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide">Xóa </a>
                        <span class="btn i-loop-4 delete_button e_reverse_button for_select hide"> Đảo ngược </span>
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

                                <div class="chiphi_div">
                                    <span>Từ ngày:</span>
                                    <input type="datetime" name="tungay" class="tungay"
                                           style="line-height: 34px;"
                                           placeholder="từ ngày">
                                </div>

                                <div class="chiphi_div">
                                    <span>Đến ngày:</span>
                                    <input type="datetime" name="denngay" class="denngay"
                                           style="line-height: 34px;"
                                           placeholder="đến ngày">
                                </div>

                                <div class="chiphi_div taixe">
                                    <span>Tài xế:</span>
                                    <?php if(isset($shipper)) { ?>
                                        <select name="shipper_id[]" class="laixe" multiple>
                                            <?php foreach ($shipper as $item) { ?>
                                                <option value="<?php echo $item->id;?>"><?php echo $item->first_name;?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>

                                <div class="tour chiphi_div" >
                                    <span>Tour:</span>
                                    <select name="shipper_are_id[]" class="ship_areas" multiple>
                                        <?php if(isset($shipper_area_id)) {
                                            foreach ($shipper_area_id as $item) {
                                                ?>
                                                <option value="<?php echo $item->id;?>"><?php echo $item->name;?></option>
                                            <?php } } ?>
                                    </select>
                                </div>

                                <input type="hidden" class="list_id_to_nhathang" name="list_id_to_nhathang">

                                <div class="clear"></div>
                                <p style="text-align: center;">
                                    <button type="submit" id="pdf" name="htmltopdf" class="excel-chiphi-laixe btn btn-success">Excel</button>
<!--                                    <button type="submit" class= "btn-danger btn" formaction="htmltopdf/print_money_day">Übersicht</button>-->
<!--                                    <button type="submit" class= "btn-success btn" formaction="htmltopdf/print_money_day_excel">Übersicht Excel</button>-->
<!--                                    <button type="submit" class= "btn-danger btn" formaction="htmltopdf/pdf_list_giaohang">Lieferschein</button>-->
<!---->
<!--                                    <button type="submit" class="btn btn-info donkiem" formaction="htmltopdf/print_order_allready">Đơn Kiểm</button>-->
                                </p>

                            </div>
                        </form>
                    </div>

                    <!-- div widget manage -->
                    <div class="widget-manage">
                        <!-- div form ajax-->
                        <div class="clear clear-form-search" style="border-top:  1px solid #c9c9c9"></div>
                        <div class="widget-content data_table e_data_table"
                             data-url="<?php echo base_url(); ?>voxy_chiphi_laixe/ajax_list_data.html"
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
                        $('.tungay').datepicker({
                            dateFormat: "dd-mm-yy",
                            changeMonth: true,
                            changeYear : true,
                            numberOfMonths: 1,
                            minDate: '01-01-2016'
                        });

                        $('.denngay').datepicker({
                            dateFormat: "dd-mm-yy",
                            changeMonth: true,
                            changeYear : true,
                            numberOfMonths: 1,
                            minDate: '01-01-2016'
                        });
                        //end ngay bat dau

                        $(document).on("click", ".e_data_paginate_tinhcv li:not(.disabled):not(.active)", bind_ajax_change_paganation);

                        function bind_ajax_change_paganation(){
                            var tungay = $('.tungay').val();
                            var denngay = $('.denngay').val();

                            var selected=[];
                            var i = -1;
                            $('.laixe option:selected').each(function(){
                                i++;
                                selected[i]=$(this).val();
                            });
                            var laixe = selected;
                            if(laixe == ""){
                                laixe = "all";
                            }

                            var selected_area=[];
                            var i = -1;
                            $('.ship_areas option:selected').each(function(){
                                i++;
                                selected_area[i]=$(this).val();
                            });
                            var ship_areas = selected_area;
                            if(ship_areas == ""){
                                ship_areas = "all";
                            }

                            show_ajax_loading($('.e_data_table'));

                            $.ajax({
                                url: "<?php echo base_url('voxy_chiphi_laixe/ajax_list_data')?>",
                                type: "post",
                                data: {
                                    tungay: tungay,
                                    denngay: denngay,
                                    laixe: laixe,
                                    ship_areas: ship_areas
                                },
                                dataType: "text",
                                success: function(dataAll) {
                                    console.log("sent data ok");
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
                                    console.log("sent data not oke");
                                }
                            });
                        }
                        $('.tungay, .denngay, .laixe, .ship_areas').on('change',function () {
                            bind_ajax_change_paganation();
                        });

                    </script>
                </div>
            </div>
        </div>
    </div>

<style>

    .chiphi_div {
        margin: 0 !important;
        padding: 5px;
        width: 25%;
        float: left;
    }
    @media only screen and (max-width: 600px) {
        .t-nhathang {
            width: 100% !important;
        }
    }
</style>
