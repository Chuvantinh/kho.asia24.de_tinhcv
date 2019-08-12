<div class="container-fluid">
    <div class="row-fluid"> 
        <div class="span12"> 
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
<!--                        <a href="--><?php //echo base_url('voxy_kiemkho_theongay/export_product_excel'); ?><!--" style=""-->
<!--                           class="export_product_excel btn btn-success btn-lg"> Cập nhật hệ thống </a>-->
<!--                        <a href="--><?php //echo base_url('voxy_kiemkho_theongay/update_products_rest'); ?><!--" class="btn i-plus-circle-3 btn-info btn_update_products_rest" >Cập nhật Sản Phẩm không được kiểm</a>-->
<!--                        <a href="--><?php //echo base_url('voxy_kiemkho_theongay/report_products_check'); ?><!--" class="btn i-plus-circle-3 btn-info btn_baocao_products_check" >Báo cáo sản phẩm kiểm</a>-->

<!--                        <a href="--><?php //echo base_url('voxy_kiemkho_theongay/export_product_excel'); ?><!--" style=""-->
<!--                           class="export_product_excel btn btn-success btn-lg"> Báo cáo hàng trong kho excel </a>-->
<!--                        <a href="--><?php //echo $add_link; ?><!--" class="btn i-plus-circle-2 btn-info e_ajax_link add_button" > Thêm </a>-->
<!--                        <a href="--><?php //echo $delete_list_link; ?><!--" class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide" > Xóa </a>-->
                        <span class="btn i-loop-4 delete_button e_reverse_button for_select hide" > Đảo ngược </span>
                    </div>
                    <a href="#" class="minimize"></a>
                </div>

                <!-- div widget manage -->
                <div class="widget-manage">
                    <!-- div form search-->
                    <div class="widget-form-search" style="display: block">
                        <?php $custom_where = (isset($form_conds['custom_where'])   ? $form_conds['custom_where']   : array()); ?>
                        <?php $custom_like  = (isset($form_conds['custom_like'])    ? $form_conds['custom_like']    : array()); ?>
                        <form id="i_form_filter" action="<?php echo base_url('voxy_kiemkho_theongay/ajax_list_data_new'); ?>" method="POST" target="_blank">
                            <div class="e_toogle_next_div toogle_next_search"></div>
                            <a href="#" title='Ẩn/hiện Lọc' class="toggle_block minimize e_toogle_next_div"></a>

                            <div class="e_form_search">
<!--                                <div class="span2" style="margin-top: -10px;">-->
<!--                                    <div class="form-group">-->
<!--                                        <label for="i_status">Trạng thái</label>-->
<!--                                        <select name="status" id='i_status'>-->
<!--                                            <option value="">-- Tất cả --</option>-->
<!--                                            --><?php //if(isset($list_status) && is_array($list_status)) :?>
<!--                                                --><?php //foreach ($list_status as $key => $value): ?>
<!--                                                    <option value="--><?php //echo $key; ?><!--" --><?php //echo (isset($custom_where['m.status']) && ($custom_where['m.status'] == $value['status'])) ? 'selected' : ''; ?><!-- >--><?php //echo $value; ?><!--</option>-->
<!--                                                --><?php //endforeach; ?>
<!--                                            --><?php //endif; ?>
<!--                                        </select>-->
<!--                                    </div>-->
<!--                                </div>-->
<!---->
<!--                                <div class="span2">-->
<!--                                    <span>Từ ngày:</span>-->
<!--                                    <input type="datetime" name="date_for_orders" class="date_for_orders" placeholder="từ ngày" autocomplete="off">-->
<!--                                </div>-->
<!---->
<!--                                <div class="span2">-->
<!--                                    <span>Đến ngày:</span>-->
<!--                                    <input type="datetime" name="date_for_orders_end" class="date_for_orders_end" placeholder="đến ngày" autocomplete="off">-->
<!--                                </div>-->

                                <div class="span12 e_form_search">
                                    <div class="form-group">
<!--                                        <button class="e_btn_search btn btn-info add_button" id="i_btn_search_submit">Check Kho Theo Ngay </button>-->

                                        <button type="submit" class= "btn-danger btn in-bao-cao" formaction="voxy_kiemkho_theongay/kiemkho_theongay">Ghi Data Kiểm Kho Vào Hệ Thống</button>
<!--
<button type="submit" class= "btn-danger btn" formaction="htmltopdf/baocao_theo_nhaccungcap">In báo cáo nhà cung cấp (PDF)</button>-->
<!---->
<!--                                        <button type="submit" class= "btn-success btn" formaction="htmltopdf/baocao_hangve_sanpham">Hàng trả về Sp theo ngày(PDF)</button>-->
<!--                                        <button type="submit" class= "btn-success btn" formaction="htmltopdf/baocao_hangve_nhaccungcap">Hàng trả về nhà cung cấp (PDF)</button>-->
<!---->
<!--                                        <button type="submit" class= "btn-primary btn" formaction="htmltopdf/baocao_hanghong_sanpham">Hàng hỏng theo ngày(PDF)</button>-->
<!--                                        <button type="submit" class= "btn-primary btn" formaction="htmltopdf/baocao_hanghong_nhaccungcap">Hàng hỏng nhà cung cấp (PDF)</button>-->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- div form ajax-->
                    <div class="clear clear-form-search"></div>
                    <div id="status"></div>
                    <div class="widget-content data_table e_data_table" data-url="<?php echo $ajax_data_link; ?>" data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">
                        <!-- Ajax load ding content -->
                    </div>
                </div>

                <script type="text/javascript" charset="utf-8">
                    $("div.widget-form-search").find("select").select2();
                    if($("div.widget-form-search").length > 0 && $("div.widget-form-search").css('display') == 'block'){
                        $("div.clear-form-search").css('margin-bottom', '5px');
                    }
                </script>

                <script type="text/javascript" charset="utf-8">
                    $(window).load(function () {
                        var searchInput = $("#foo");
                        searchInput.focus();//move mouse to
                    });

                    $(document).on("click", "#i_btn_search_submit", function (e) {
                        e.preventDefault();
                        var obj = $(".e_data_table");
                        show_ajax_loading(obj);
                        $("#i_form_filter").ajaxSubmit({
                            dataType: "text",
                            type: "post",
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

                    $('.date_for_orders').datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        changeYear : true,
                        numberOfMonths: 1,
                        minDate: '01-01-2016'
                    });

                    //begin ngay ket thuc
                    $('.date_for_orders_end').datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        changeYear : true,
                        numberOfMonths: 1,
                        minDate: '01-01-2016'
                    });

                    var href_report_products = $('.report_products').attr('href');
                    var href_report_values = $('.report_values').attr('href');
                    var date1 = "";
                    var data2 = "";

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

                        data1 = date.getFullYear() + '-' + get_month + '-' + getdate;//prints expected format.

                        if(typeof data2 != "undefined"){
                            $('.report_products').attr('href', href_report_products + '?date1='+data1+'&date2=' + data2);
                            $('.report_values').attr('href', href_report_values + '?date1='+data1+'&date2=' + data2);
                        }else{
                            $('.report_products').attr('href', href_report_products + '?date1=' + data1);
                            $('.report_values').attr('href', href_report_values + '?date1=' + data1);
                        }

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

                        data2 = date.getFullYear() + '-' + get_month + '-' + getdate;//prints expected format.
                        if(typeof data1 != "undefined"){
                            $('.report_products').attr('href', href_report_products + '?date1='+data1+'&date2=' + data2);
                            $('.report_values').attr('href', href_report_values + '?date1='+data1+'&date2=' + data2);
                        }else{
                            $('.report_products').attr('href', href_report_products + '?date2=' + data2);
                            $('.report_values').attr('href', href_report_values + '?date2=' + data2);
                        }

                    });

                    $(".export_product_excel").on("click", function () {
                       var list_id = $(".delete_list_button").attr("data");
                       var $_list_id = JSON.parse(list_id)["list_id"];
                       var res = $_list_id.join(",");

                       var href = $(this).attr("href");
                       var href_new = href+"?list_id="+res;
                       $(this).attr("href",href_new);
                    });

                    $('.btn_update_products_rest').on('click',function () {
                        var list_id = $(".delete_list_button").attr("data");
                        var $_list_id = JSON.parse(list_id)["list_id"];
                        var res = $_list_id.join(",");

                        var href = $(this).attr("href");
                        var href_new = href+"?list_id="+res;
                        $(this).attr("href",href_new);
                    });

                    $('.btn_baocao_products_check').on('click',function () {
                        var list_id = $(".delete_list_button").attr("data");
                        var $_list_id = JSON.parse(list_id)["list_id"];
                        var res = $_list_id.join(",");

                        var href = $(this).attr("href");
                        var href_new = href+"?list_id="+res;
                        $(this).attr("href",href_new);
                    });


                    $('.xuatfile_kiemkho').on('click',function () {
                        var list_id = $(".delete_list_button").attr("data");
                        var $_list_id = JSON.parse(list_id)["list_id"];
                        var res = $_list_id.join(",");

                        var href = $(this).attr("href");
                        var href_new = href+"?list_id="+res;
                        $(this).attr("href",href_new);
                    });



                </script>
            </div>
        </div>
    </div>
</div>
<style>
    @media only screen and (max-width: 600px) {

        .export_product_excel{
            margin: 58px 11px !important;
        }
        .actions_content{
            margin-left: 10px !important;
        }
    }

</style>