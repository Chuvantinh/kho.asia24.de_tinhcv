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
                        <a href="<?php echo $get_link; ?>" class="btn i-plus-circle-2 btn-info e_ajax_link_get add_button" > Get Data </a>
                        <a href="<?php echo $delete_list_link; ?>" class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide" > Xóa </a>
                        <span class="btn i-loop-4 delete_button e_reverse_button for_select hide" > Đảo ngược </span>    
                    </div>
                    <a href="#" class="minimize"></a>
                </div>

                <!-- div widget manage -->
                <div class="widget-manage">
                    <!-- div form search-->
                    <div class="widget-form-search" style="display: none">
                        <?php $custom_where = (isset($form_conds['custom_where'])   ? $form_conds['custom_where']   : array()); ?>
                        <?php $custom_like  = (isset($form_conds['custom_like'])    ? $form_conds['custom_like']    : array()); ?>
                        <form id="i_form_filter" action="<?php echo $form_url; ?>">
                            <div class="e_toogle_next_div toogle_next_search"></div>
                            <a href="#" title='Ẩn/hiện Lọc' class="toggle_block minimize e_toogle_next_div"></a>

                            <div class="e_form_search">
                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_demo_id">Native Parent</label>
                                        <select name="native_parent" id='i_demo_id'>
                                            <option value="">-- Tất cả --</option>
                                            <?php if(isset($list_demo_id) && is_array($list_demo_id)) :?>
                                                <?php foreach ($list_demo_id as $key => $value): ?>
                                                    <option value="<?php echo $key; ?>" <?php echo (isset($custom_where['m.demo_id']) && ($custom_where['m.demo_id'] == $key)) ? 'selected' : ''; ?> ><?php echo $value; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_demo_id">Demo Search</label>
                                        <select name="demo_id" id='i_demo_id'>
                                            <option value="1">Tất cả</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_demo_id">Demo Search</label>
                                        <select name="demo_id" id='i_demo_id'>
                                            <option value="1">Tất cả</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_demo_id">Demo Search</label>
                                        <select name="demo_id" id='i_demo_id'>
                                            <option value="1">Tất cả</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_demo_id">Demo Search</label>
                                        <select name="demo_id" id='i_demo_id'>
                                            <option value="1">Tất cả</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="span2">
                                    <div class="form-group">
                                        <label for="i_demo_id">Demo Search</label>
                                        <select name="demo_id" id='i_demo_id'>
                                            <option value="1">Tất cả</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="span12 e_form_search"> <!--  style="margin: 15px 0 0 0" -->
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
                    });
                </script>
            </div>
        </div>
    </div>
</div>