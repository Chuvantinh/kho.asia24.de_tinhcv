<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
                        <a href="<?php echo $get_link; ?>" class="btn i-plus-circle-2 btn-inverse e_ajax_link_get" > Lấy dữ liệu từ hệ thống máy chủ </a>
                        <a href="<?php echo base_url("/admin_cronjob_chuvantinh/kunden_ghino"); ?>" class="btn i-plus-circle-2 btn-warning hide">Lấy dữ liệu nợ khách hàng</a>
                        <a href="<?php echo base_url("/voxy_package_kunden/excel_no_kunden"); ?>" target="_blank" class="btn i-plus-circle-2 btn-full btn-success excel-kunden" >Excel-Lẻ</a>
                        <a href="<?php echo base_url("/voxy_package_kunden/excel_no_kunden_all"); ?>" target="_blank" class="btn i-plus-circle-2 btn-full btn-success excel-kunden-all" >Excel-Tổng</a>
                        <a href="<?php echo $add_link; ?>" class="btn i-plus-circle-2 btn-info e_ajax_link add_button" > Thêm </a>
                        <a href="<?php echo $delete_list_link; ?>" class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide" > Xóa </a>
                        <span class="btn i-loop-4 delete_button e_reverse_button for_select hide" > Đảo ngược </span>
                    </div>
                    <a href="#" class="minimize"></a>
                </div>

<!--                <form method="post" id="import_form" enctype="multipart/form-data">-->
<!--                    <p><label>Select Excel File</label>-->
<!--                        <input type="file" name="file" id="file" required accept=".xls, .xlsx" /></p>-->
<!--                    <br />-->
<!--                    <input type="submit" name="import" value="Import" class="btn btn-info" />-->
<!--                </form>-->
<!--                <form class="span6" method="post"-->
<!--                      style="margin-top: 20px;"-->
<!--                      autocomplete="off" action="voxy_package_kunden/export_kunden">-->
<!--                                                <button type="submit" id="export_kunden" class="btn btn-danger">-->
<!--                                                    Xuất PDF Danh Sách Sản Phẩm-->
<!--                                                </button>-->
<!--                </form>-->

                <!-- div widget manage -->
                <div class="kunden-title">
                    <p>Lọc thông tin khách hàng chưa thanh toán:</p>
                </div>
                <div class="kunden-sort">
                    <div class="kunden">
                        <span>Từ ngày:</span>
                        <?php
                        $where_data = $this->session->userdata('where_data');

                        if(isset($where_data['date_liefer'])){
                            $date_liefer = $where_data['date_liefer'];
                        }else{
                            $date_liefer = "";
                        }

                        if(isset($where_data['date_liefer_end'])){
                            $date_liefer_end = $where_data['date_liefer_end'];
                        }else{
                            $date_liefer_end = "";
                        }

                        if(isset($where_data['data_shipper_id'])){
                            $data_shipper_id = $where_data['data_shipper_id'];
                        }else{
                            $data_shipper_id = "";
                        }

                        if(isset($where_data['data_shipper_are_id'])){
                            $data_shipper_are_id = $where_data['data_shipper_are_id'];
                        }else{
                            $data_shipper_are_id = "";
                        }

                        if(isset($where_data['data_sort_debt'])){
                            $select_data_sort_debt = $where_data['data_sort_debt'];
                        }else{
                            $select_data_sort_debt = "all";
                        }

                        ?>
                        <input type="datetime" name="date_liefer" class="date_liefer date" autocomplete="off"
                               style="line-height: 34px;"
                               value="<?php echo $date_liefer;?>"
                               placeholder="từ ngày">
                    </div>

                    <div class="kunden">
                        <span>Đến ngày:</span>
                        <input type="datetime" name="date_liefer_end" class="date_liefer_end date" autocomplete="off"
                               value="<?php echo $date_liefer_end;?>"
                               style="line-height: 34px;"
                               placeholder="đến ngày">
                    </div>

                    <div class="kunden">
                        <span>Tài xế:</span>
                        <?php if(isset($shipper)) { ?>
                            <select name="shipper_id[]" class="select2 shipper_id" multiple>
                                <?php foreach ($shipper as $item) {

                                    ?>
                                    <option value="<?php echo $item->id;?>"><?php echo $item->first_name;?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>

                    <div class="kunden">
                        <span>Tour:</span>
                        <select name="shipper_are_id[]" class="select2 shipper_are_id" multiple>
                            <?php if(isset($shipper_area_id)) {
                                foreach ($shipper_area_id as $item) {
                                    ?>
                                    <option value="<?php echo $item->id;?>"><?php echo $item->name;?></option>
                                <?php } } ?>
                        </select>
                    </div>
                    <div class="kunden">
                        <span>Sắp xếp Theo:</span>
                        <select name="data_sort_debt" class="select2 data_sort_debt">
                            <?php if(isset($data_sort_debt)) {
                                foreach ($data_sort_debt as $key => $item) {
                                    ?>
                                    <option value="<?php echo $key;?>" <?php echo ($key == $select_data_sort_debt)?"selected":"";?>><?php echo $item;?></option>
                                <?php } } ?>
                        </select>
                    </div>

                </div>

                <div class="widget-manage">
                    <!-- div form ajax-->
                    <div class="clear clear-form-search"></div>
                    <div class="widget-content data_table e_data_table" data-url="<?php echo $ajax_data_link; ?>" data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">
                        <!-- Ajax load ding content -->
                    </div>
                </div>

                <script type="text/javascript" charset="utf-8">

                    $("div.widget-form-search").find("select").select2();
                    if($("div.widget-form-search").length > 0 && $("div.widget-form-search").css('display') == 'block'){
                        $("div.clear-form-search").css('margin-bottom', '5px');
                    }

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
                        $.ajax({
                            url: $ulr,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                if(data.state === 1){
                                    $('.e_data_table img[title="Loading"]').parent().css({
                                        "display" : "none"
                                    });
                                    location.reload();
                                }
                            },
                            error: function(a, b, c) {
                                console.log("KHong get duoc orders tu máy chủ");
                                //location.reload();
                            },
                            complete: function(jqXHR, textStatus) {

                            }
                        });
                    });

                    $('#import_form').on('submit', function(event){
                        event.preventDefault();
                        $.ajax({
                            url:"<?php echo base_url(); ?>voxy_package_kunden/import",
                            method:"POST",
                            data:new FormData(this),
                            contentType:false,
                            cache:false,
                            processData:false,
                            success:function(data){
                                $('#file').val('');
                                //load_data();
                                //alert(data);
                            }
                        })
                    });

                    //excel ghi no khach hang
                    var href_anfang = $('.excel-kunden').attr("href");
                    $(".excel-kunden").on("click",function () {
                       var value = $(".delete_list_button").attr('data');

                       var date_liefer = $('.date_liefer').val();
                       if(! date_liefer){
                           date_liefer = "";
                       }

                       var date_liefer_end = $('.date_liefer_end').val();
                        if(! date_liefer_end){
                            date_liefer_end = "";
                        }

                        $(".excel-kunden").attr('href', href_anfang + '?id=' + value + '&date_liefer=' + date_liefer + '&date_liefer_end=' + date_liefer_end);
                    });

                    //excel ghi no khach hang in ra so tien tong theo khach hang
                    var href_anfang_all = $('.excel-kunden-all').attr("href");
                    $(".excel-kunden-all").on("click",function () {
                        var value = $(".delete_list_button").attr('data');

                        var date_liefer = $('.date_liefer').val();
                        if(! date_liefer){
                            date_liefer = "";
                        }

                        var date_liefer_end = $('.date_liefer_end').val();
                        if(! date_liefer_end){
                            date_liefer_end = "";
                        }

                        $(".excel-kunden-all").attr('href', href_anfang_all + '?id=' + value + '&date_liefer=' + date_liefer + '&date_liefer_end=' + date_liefer_end);
                    });

                    //sorting
                    $('.date_liefer').datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        changeYear : true,
                        numberOfMonths: 1,
                        minDate: '01-01-2016'
                    });


                    $('.date_liefer_end').datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        changeYear : true,
                        numberOfMonths: 1,
                        minDate: '01-01-2016'
                    });
                    //end sorting

                    $(document).on('change','.date_liefer, .date_liefer_end,.shipper_id, .shipper_are_id, .data_sort_debt',function () {
                        creat_ajax_table_tinhcv();
                    });

                    //$(document).on("click", ".e_data_paginate_tinhcv li:not(.disabled):not(.active)",  creat_ajax_table_tinhcv);
                    //$(document).on("change", ".e_changer_number_record", creat_ajax_table_tinhcv);

                    function creat_ajax_table_tinhcv() {

                        var date_liefer = $('.date_liefer').val();
                        var date_liefer_end = $('.date_liefer_end').val();

                        var data_sort_debt =  $('.data_sort_debt option:selected').val();

                        var shipper_id=[];
                        var i = -1;
                        $('.shipper_id option:selected').each(function(){
                            i++;
                            shipper_id[i]=$(this).val();
                        });

                        var shipper_are_id=[];
                        var i = -1;
                        $('.shipper_are_id option:selected').each(function(){
                            i++;
                            shipper_are_id[i]=$(this).val();
                        });

                        //console.log(shipper_id);

                        var url = $('.e_data_table').attr("data-url");
                        var data = {
                            date_liefer: date_liefer,
                            date_liefer_end: date_liefer_end,
                            data_shipper_id: shipper_id,
                            data_shipper_are_id: shipper_are_id,
                            data_sort_debt: data_sort_debt
                        };

                        //console.log(data);

                        show_ajax_loading($('.e_data_table'));

                        $.ajax({
                            url: url,
                            type: "GET",
                            data: data,
                            dataType: "text",
                            success: function(dataAll) {
                                var temp = dataAll.split($("body").attr("data-barack"));
                                var data = {};
                                for (var i in temp) {
                                    temp[i] = $.parseJSON(temp[i]);
                                    data = $.extend({}, data, temp[i]);
                                }
                                if (window[data.callback]) {
                                    console.log("Gọi hàm: ", data.callback);
                                    window[data.callback](data, $('.e_data_table'));
                                } else {
                                    console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_data_table'");
                                    default_data_table(data, $('.e_data_table'));
                                }
                            },
                            error: function(a, b, c) {
                                alert(a + b + c);
                                window.location = url;
                            }
                        });


                    }

                </script>
            </div>
        </div>
    </div>
</div>
<style>
    .kunden{
        width: 20%; float:left;padding: 5px
    }
    .kunden span {
        width: 100%;
        float:left;
        color: blue;
    }

    .kunden input{
        width: 100%;
        float:left;
    }
    .kunden-sort{
        padding: 15px;
        width: 100%;
    }
    .kunden-title{
        padding: 0 20px;
        font-size: 30px;
    }

    @media only screen and (max-width: 600px) {
        .kunden{
            width: 100%; float:left;padding: 5px
        }
    }

</style>