<div class="container-fluid">
    <div class="row-fluid"> 
        <div class="span12"> 
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
<!--                        <a href="--><?php //echo base_url('htmltopdf/baocao_nhapkho_ajax'); ?><!--" target="_blank" class="baocao_nhapkho_ajax btn i-cancel-circle-2 btn-inverse for_select hide" >Báo cáo</a>-->
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
                        <form id="i_form_filter" action="<?php echo base_url('baocao_nhaphang/ajax_list_data_new'); ?>" method="POST" target="_blank">
                            <div class="e_toogle_next_div toogle_next_search"></div>
                            <a href="#" title='Ẩn/hiện Lọc' class="toggle_block minimize e_toogle_next_div"></a>

                            <div class="e_form_search">
                                <input type="hidden" name="list_id" value="" class="list_id">
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

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_select">Tiêu chí:</label>
                                        <select name="select_baocao2" id='select_baocao'>
                                            <option value="baocao_nhaphang_taikho_pdf_admin">SP nhập vào kho</option>
                                            <option value="baocao_theo_nhaccungcap">Sp nhập theo nhacc</option>
                                            <option value="baocao_hangve_sanpham">Hàng trả về</option>
                                            <option value="baocao_hanghong_sanpham">Hàng hỏng</option>
                                            <option value="baocao_hangthem_sanpham">Hàng thêm</option>
                                            <option value="baocao_hangthieu_sanpham">Hàng thiếu</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_nhacc">Nhà cung cấp:</label>
                                        <select name="vendor" id='i_nhacc'>
                                            <option value="">-- Tất cả --</option>
                                            <?php if(isset($list_nha_cc) && is_array($list_nha_cc)) :?>

                                                <?php foreach ($list_nha_cc as $key => $value):
                                                  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo (isset($custom_where['m.vendor']) && ($custom_where['m.vendor'] == $key)) ? 'selected' : ''; ?> ><?php echo $value['name']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>


<!--                                <option value="baocao_hangve_nhaccungcap">Hàng trả về theo nhà CC</option>-->
<!--                                <option value="baocao_hanghong_nhaccungcap">Hàng hỏng theo nhà </option>-->

<!--                                <div class="span2">-->
<!--                                    <div class="form-group">-->
<!--                                        <label for="i_status">Trạng thái:</label>-->
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

                                <div class="span12 e_form_search">
                                    <div class="form-group">
                                        <button class="e_btn_search btn btn-info add_button" id="i_btn_search_submit"> Tìm kiếm </button>

                                        <button type="submit" class= "btn-danger btn in-bao-cao" formaction="htmltopdf/baocao_nhaphang_taikho_pdf">In Báo Cáo</button>
<!--                                        <button type="submit" class= "btn-danger btn" formaction="htmltopdf/baocao_theo_nhaccungcap">In báo cáo nhà cung cấp (PDF)</button>-->
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

                    $(document).on("click", ".in-bao-cao", function () {
                        var $list_id = $('.delete_list_button').attr('data');
                        $('.list_id').val($list_id);
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

                    $(".in-bao-cao").on("click",function () {
                        var value_select = $("#select_baocao").val();
                       $(".in-bao-cao").attr("formaction","htmltopdf/"+value_select);
                    });

                </script>
            </div>
        </div>
    </div>
</div>
