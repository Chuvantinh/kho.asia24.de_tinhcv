<div class="container-fluid">
    <div class="row-fluid"> 
        <div class="span12"> 
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content"></div>
                    <a href="#" class="minimize"></a>
                </div>

                <!-- div widget manage -->
                <div class="widget-manage">
                    <!-- div form search-->
                    <div class="widget-form-search" style="display: block">
                        <?php $custom_where = (isset($form_conds['custom_where'])   ? $form_conds['custom_where']   : array()); ?>
                        <?php $custom_like  = (isset($form_conds['custom_like'])    ? $form_conds['custom_like']    : array()); ?>
                        <form id="i_form_filter" action="<?php echo $form_url; ?>">
                            <div class="e_toogle_next_div toogle_next_search"></div>
                            <a href="#" title='Ẩn/hiện Lọc' class="toggle_block minimize e_toogle_next_div"></a>

                            <div class="">
                                <div class="span4">
                                    <div class="form-group">
                                        <label for="i_student_id">User ID</label>
                                        <input id="i_student_id" type="text" name="student_id" placeholder="Voxy ID, LMS ID, Email, Contact ID" tabindex="-1">
                                    </div>
                                </div>

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_native_parent">Native Parent</label>
                                        <select name="native_parent" id='i_native_parent'>
                                            <option value="">-- Tất cả --</option>
                                            <?php if(isset($list_native_parent) && is_array($list_native_parent)) :?>
                                                <?php foreach ($list_native_parent as $key => $value): ?>
                                                    <option value="<?php echo $key; ?>" <?php echo (isset($custom_where['m.native_parent']) && ($custom_where['m.native_parent'] == $key)) ? 'selected' : ''; ?> ><?php echo $value; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_pack_type">Package Type</label>
                                        <select name="pack_type" id='i_pack_type'>
                                            <option value="">-- Tất cả --</option>
                                            <?php if(isset($list_pack_type) && is_array($list_pack_type)) :?>
                                                <?php foreach ($list_pack_type as $key => $value): ?>
                                                    <option value="<?php echo $key; ?>" <?php echo (isset($custom_where['m.pack_type']) && ($custom_where['m.pack_type'] == $key)) ? 'selected' : ''; ?> ><?php echo $value; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_package_status">Package Status</label>
                                        <select name="package_status" id='i_package_status'>
                                            <option value="">-- Tất cả --</option>
                                            <?php if(isset($list_package_status) && is_array($list_package_status)) :?>
                                                <?php foreach ($list_package_status as $key => $value): ?>
                                                    <option value="<?php echo $key; ?>" <?php echo (isset($custom_where['m.status']) && ($custom_where['m.status'] == $key)) ? 'selected' : ''; ?> ><?php echo $value; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_from_end_time">Hết hạn từ ngày</label>
                                        <input id="i_from_end_time" name="from_end_time" autocomplete="off" type="datetime">
                                    </div>
                                </div>

                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_to_end_time">Hết hạn từ ngày</label>
                                        <input id="i_to_end_time" name="to_end_time" autocomplete="off" type="datetime">
                                    </div>
                                </div>

                                <div class="span2"> <!--  style="margin: 15px 0 0 0" -->
                                    <div class="form-group">
                                        <label for=""> &nbsp; </label>
                                        <button class="e_btn_search btn btn-info add_button" id="i_btn_search_submit"> Tìm kiếm </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- div form ajax-->
                    <div class="clear clear-form-search"></div>
                    <div class="widget-content data_table e_data_table" data-url="<?php echo $ajax_data_link; ?>" data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">
                        <!-- Ajax load ding content -->
                    </div>
                </div>

                <script type="text/javascript" charset="utf-8">
                    $("#i_form_filter :input[name='from_end_time']").datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        numberOfMonths: 1,
                        onClose: function (selectedDate) {
                            $("#i_form_filter :input[name='to_end_time']").datepicker("option", "minDate", selectedDate);
                        }
                    });
                    $("#i_form_filter :input[name='to_end_time']").datepicker({
                        dateFormat: "dd-mm-yy",
                        changeMonth: true,
                        numberOfMonths: 1,
                        onClose: function (selectedDate) {
                            $("#i_form_filter :input[name='from_end_time']").datepicker("option", "maxDate", selectedDate);
                        }
                    });
                    $(function() {
                        $( "#i_student_id" ).autocomplete({
                            source: "<?php echo $ajax_link_search_voxy_user; ?>"
                        });
                    });
                </script>
                <script type="text/javascript" charset="utf-8">
                    $("div.widget-form-search").find("select").select2();
                    if($("div.widget-form-search").length > 0 && $("div.widget-form-search").css('display') == 'block'){
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
                        $("#i_form_filter :input[name='student_id']").val('');
                    });
                </script>
            </div>
        </div>
    </div>
</div>
