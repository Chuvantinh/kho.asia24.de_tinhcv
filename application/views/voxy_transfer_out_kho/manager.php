<div class="container-fluid">
    <div class="row-fluid"> 
        <div class="span12"> 
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
                        <a href="<?php echo base_url('htmltopdf/baocao_taikho'); ?>" target="_blank" class="baocao_taikho btn i-cancel-circle-2 btn-inverse for_select hide" >Báo cáo</a>
                        <a href="<?php echo $add_link; ?>" class="btn i-plus-circle-2 btn-info e_ajax_link add_button" > Thêm </a>
                        <a href="<?php echo $delete_list_link; ?>" class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide" > Xóa </a>
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
                        <form id="i_form_filter" action="<?php echo base_url('voxy_transfer_out_kho/ajax_list_data_new'); ?>" method="POST" target="_blank">
                            <div class="e_toogle_next_div toogle_next_search"></div>
                            <a href="#" title='Ẩn/hiện Lọc' class="toggle_block minimize e_toogle_next_div"></a>

                            <div class="e_form_search">
                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_status">Trạng thái</label>
                                        <select name="status" id='i_status'>
                                            <option value="">-- Tất cả --</option>
                                            <?php if(isset($list_status) && is_array($list_status)) :?>
                                                <?php foreach ($list_status as $key => $value): ?>
                                                    <option value="<?php echo $key; ?>" <?php echo (isset($custom_where['m.status']) && ($custom_where['m.status'] == $key)) ? 'selected' : ''; ?> ><?php echo $value; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_nhacc">Từ ngày:</label>
                                            <input type="datetime" name="ngay_dat_hang" class="ngay_dat_hang date_for_orders"
                                                   autocomplete="off"
                                                   style="line-height: 34px;"
                                                   placeholder="từ ngày">
                                    </div>
                                </div>

                                <div class="span2">
                                    <label for="i_nhacc">Đến ngày:</label>
                                        <input type="datetime" name="ngay_giao_hang" class="ngay_giao_hang date_for_orders2"
                                               autocomplete="off"
                                               style="line-height: 34px;"
                                               placeholder="đến ngày">
                                </div>

                                <div class="span12 e_form_search">
                                    <div class="form-group">
                                        <button class="e_btn_search btn btn-info add_button" id="i_btn_search_submit"> Tìm kiếm </button>
                                        <button type="submit" class= "btn-success btn" formaction="htmltopdf/baocao_xuathang_taikho_pdf">In Báo Cáo Theo Ngày</button>
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

                    $(document).on("click", ".baocao_taikho", function (e) {
                        e.preventDefault();
                        var obj = $(".e_data_table");
                        //show_ajax_loading(obj);
                        var $ulr = $(this).attr('href');
                        var $list_id = $('.delete_list_button').attr('data');
                        //ngay dat hang
                        var date = $('.ngay_dat_hang').datepicker('getDate');
                        if(date){
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
                            var $ngay_dat_hang = data2;
                        }

                        //ngay giao hang
                        var date3 = $('.ngay_giao_hang').datepicker('getDate');
                        if(data3){
                            if (date3.getDate() < 10) {
                                var getdate3 = '0' + date.getDate();
                            } else {
                                getdate3 = date3.getDate();
                            }
                            if( date3.getMonth() < 10){
                                var get_month3 = '0' + (date.getMonth() +1) ;
                            }else{
                                get_month3 = date3.getMonth();
                            }
                            var data3 = date3.getFullYear() + '-' + get_month3 + '-' + getdate3;//prints expected format.
                            var $ngay_giao_hang = data3;
                        }

                        $.ajax({
                            url: $ulr,
                            type: 'POST',
                            data: {
                                list_id: $list_id,
                                ngay_dat_hang: $ngay_dat_hang,
                                ngay_giao_hang: $ngay_giao_hang
                            },
                            dataType: 'json',
                            success: function (data) {
                                // if (data.state === 1) {
                                //     $('.e_data_table img[title="Loading"]').parent().css({
                                //         "display": "none"
                                //     });
                                //     location.reload();
                                // }

                                  console.log("Get san pham tai kho thanh cong");
                                    var url_xuathang_taikho = "<?php echo base_url(); ?>htmltopdf/baocao_xuathang_taikho_pdf";
                                    //var input = $("<input>").attr("type", "hidden").attr("name", "giao_hang").val(data.giao_hang);
                                    //var input1 = $("<input>").attr("type", "hidden").attr("name", "dat_hang").val(data.dat_hang);
                                    var input2 = $("<input>").attr("type", "hidden").attr("name", "list_id").val(data.list_id);
                                    setTimeout(function () {
                                        //location.reload();
                                    }, 1000);
                                    var form = $("<form method='POST' action='" + url_xuathang_taikho + "' target='_blank'>");
                                    //form.append(input);
                                    //form.append(input1);
                                    form.append(input2);
                                    $("body").append(form);
                                    form.submit();

                            },
                            error: function (a, b, c) {
                                console.log("KHong get duoc orders tu shopify");
                                //location.reload();
                            },
                            complete: function (jqXHR, textStatus) {

                            }
                        });
                    });

                    $('.ngay_dat_hang').datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        changeYear : true,
                        numberOfMonths: 1,
                        minDate: '01-01-2016'
                    });

                    $('.ngay_giao_hang').datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        changeYear : true,
                        numberOfMonths: 1,
                        minDate: '01-01-2016'
                    });


                </script>
            </div>
        </div>
    </div>
</div>
