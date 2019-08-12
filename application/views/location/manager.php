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


                <form action="" class="submit_barcode_location" autocomplete="off" style="margin: 10px; float: left">
                    <input id="foo" data-check-id=""/>
                    <input type="submit" value="Tìm kiếm sản phẩm theo barcode Vị trí" class="submit_barcode_location">
                </form>

                <div class="col-25 t-location-excel" style="width: 25%; float: left;margin: 10px;">
                    <a href="<?php echo base_url('location/export_location_excel'); ?>" style="line-height: 21px;"
                       class="export_product_excel btn btn-success btn-lg"> Xuất Excel Vị Trí Sản Phẩm </a>
                </div>

                <!-- div widget manage -->
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

                    $('#import_form').on('submit', function(event){
                        event.preventDefault();
                        $.ajax({
                            url:"<?php echo base_url(); ?>location/import",
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

                    $(".submit_barcode_location").submit(function(e) {
                        e.preventDefault();
                        var $barcode = $("#foo").val();
                        String.prototype.replaceAll = function(search, replacement) {
                            var target = this;
                            return target.replace(new RegExp(search, 'g'), replacement);
                        };
                        var string = $barcode.replaceAll('ß','-');
                        //console.log(string);
                        $.ajax({
                            url: "<?php echo base_url(); ?>voxy_package/getid_product_from_location",
                            async: false,
                            type: "POST",
                            dataType: "json",
                            data : {location: string},
                            success :function (data) {
                                if(data.state == 1){
                                    $('.e_data_table').html(data.html);
                                }else{
                                    $('.e_data_table').html('<h3>Không có sản phẩm ở vị trí này </h3>');
                                }

                            },
                            error: function () {
                                console.log("loi ajax get id");
                            }
                        });
                    });

                </script>
            </div>
        </div>
    </div>
</div>
<style>
    @media only screen and (max-width: 600px) {
        .t-location-excel{
            width: 100% !important;
            margin: 0 !important;

        }
        .t-location-excel .export_product_excel{
            height: 60px !important;
            line-height: 60px !important;
            font-size: 20px;
        }
    }

</style>