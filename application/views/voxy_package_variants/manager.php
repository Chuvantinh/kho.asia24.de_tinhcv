<div class="container-fluid">
    <div class="row-fluid"> 
        <div class="span12"> 
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
<!--                        <a href="--><?php //echo $get_link; ?><!--" class="btn i-plus-circle-2 btn-inverse e_ajax_link_get" > Lấy dữ liệu từ hệ thống máy chủ </a>-->
                        <a href="<?php echo $add_link; ?>" class="btn i-plus-circle-2 btn-info e_ajax_link add_button" > Thêm </a>
                        <a href="<?php echo $delete_list_link; ?>" class="btn i-cancel-circle-2 btn-danger e_ajax_link e_ajax_confirm delete_list_button for_select hide" > Xóa </a>
                        <span class="btn i-loop-4 delete_button e_reverse_button for_select hide" > Đảo ngược </span>
                    </div>
                    <a href="#" class="minimize"></a>
                </div>

                <!--<form method="post" id="import_form" enctype="multipart/form-data">
                    <p><label>Select Excel File</label>
                        <input type="file" name="file" id="file" required accept=".xls, .xlsx" /></p>
                    <br />
                    <input type="submit" name="import" value="Import" class="btn btn-info" />
                </form>-->

                <!-- div widget manage -->
                <div class="widget-manage">
                    <!-- div form search-->
<!--                    <div class="widget-form-search" style="display: block">-->
<!--                        --><?php //$custom_where = (isset($form_conds['custom_where'])   ? $form_conds['custom_where']   : array()); ?>
<!--                        --><?php //$custom_like  = (isset($form_conds['custom_like'])    ? $form_conds['custom_like']    : array()); ?>
<!--                        <form id="i_form_filter" action="--><?php //echo $form_url; ?><!--">-->
<!--                            <div class="e_toogle_next_div toogle_next_search"></div>-->
<!--                            <a href="#" title='Ẩn/hiện Lọc' class="toggle_block minimize e_toogle_next_div"></a>-->
<!---->
<!--                            <div class="e_form_search">-->
<!--                                <div class="span3">-->
<!--                                    <div class="form-group">-->
<!--                                        <label for="i_cat_id">Danh Mục</label>-->
<!--                                        <select name="cat_id" id='i_cat_id'>-->
<!--                                            <option value="">-- Tất cả --</option>-->
<!--                                            --><?php //if(isset($category) && is_array($category)) :?>
<!--                                                --><?php //foreach ($category as $key => $value): ?>
<!--                                                    <option value="--><?php //echo $value['id']; ?><!--" --><?php //echo (isset($custom_where['m.cat_id']) && ($custom_where['m.cat_id'] == $value['id'])) ? 'selected' : ''; ?><!-- >--><?php //echo $value['title']; ?><!--</option>-->
<!--                                                --><?php //endforeach; ?>
<!--                                            --><?php //endif; ?>
<!--                                        </select>-->
<!--                                    </div>-->
<!--                                </div>-->
<!---->
<!--                                <div class="span2">-->
<!--                                    <div class="form-group">-->
<!--                                        <label for="i_status">Trạng thái</label>-->
<!--                                        <select name="status" id='i_status'>-->
<!--                                            <option value="">-- Tất cả --</option>-->
<!--                                            --><?php //if(isset($list_status) && is_array($list_status)) :?>
<!--                                                --><?php //foreach ($list_status as $key => $value): ?>
<!--                                                    <option value="--><?php //echo $key; ?><!--" --><?php //echo (isset($custom_where['m.status']) && ($custom_where['m.status'] == $key)) ? 'selected' : ''; ?><!-- >--><?php //echo $value; ?><!--</option>-->
<!--                                                --><?php //endforeach; ?>
<!--                                            --><?php //endif; ?>
<!--                                        </select>-->
<!--                                    </div>-->
<!--                                </div>-->
<!---->
<!--                                <div class="span12 e_form_search">-->
<!--                                    <div class="form-group">-->
<!--                                        <label for=""> &nbsp; </label>-->
<!--                                        <button class="e_btn_search btn btn-info add_button" id="i_btn_search_submit"> Tìm kiếm </button>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </form>-->
<!--                    </div>-->

                    <!-- div form ajax-->
                    <div class="clear clear-form-search"></div>
                    <div id="status"></div>
                    <form action="" class="submit_barcode">
                        <input id="foo" data-check-id=""/>
                        <input type="submit" value="Submit" class="submit_barcode">
                    </form>
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
                            url:"<?php echo base_url(); ?>voxy_package/import",
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

                    $(".submit_barcode").submit(function(e) {
                        e.preventDefault();
                        var $barcode = $("#foo").val();
                        var id_product = null;

                        $.ajax({
                           url: "<?php echo base_url(); ?>voxy_package/getid_product",
                           async: false,
                           type: "POST",
                           dataType: "json",
                           data : {barcode: $barcode },
                           success :function (data) {
                               id_product = data;
                           },
                            error: function () {
                                console.log("loi ajax get id");
                            }
                        });

 //                       var id_product = $("#foo").attr("data-check_id").val();

                        if(id_product == null){
                            var url = "<?php echo base_url(); ?>voxy_package/add";
                        }else {

                            var url = "<?php echo base_url(); ?>voxy_package/edit/"+id_product+".html";
                        }

                        $.ajax({
                            url: url,
                            type: "POST",            // Can change this to get if required
                            data: {id_product : id_product } ,
                            success: function(data) {
                                var ketqua = JSON.parse(data);
                                var $modal = $("<div class='modal fade out modal_content'>");
                                $modal.html(ketqua.html);
                                $modal.modal();
                                $("#foo").val("");
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $("#status").text(textStatus);
                        }
                        });
                    });

                </script>
            </div>
        </div>
    </div>
</div>
