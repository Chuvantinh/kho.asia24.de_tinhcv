<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4 style="margin-right: 20px"><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
<!--                        <a href="--><?php //echo $get_link; ?><!--" class="btn i-plus-circle-2 btn-inverse e_ajax_link_get" > Lấy dữ liệu </a>-->
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

                <div class="check_and_excel" style="margin-top: 30px;">
                    <form action="" class="submit_barcode" style="margin: 10px; float: left">
                        <input id="foo" data-check-id=""/>
                        <input type="submit" value="Nhập sản phẩm bằng máy quét barcode" class="submit_barcode barcode_input">
                    </form>
                </div>

                <!-- div widget manage -->
                <div class="widget-manage" style="clear: both;">
                    <div class="clear clear-form-search"></div>
                    <div class="widget-content data_table e_data_table chuvantinh_widget" data-url="<?php echo $ajax_data_link; ?>" data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">
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

                    $(".submit_barcode").submit(function(e) {
                        e.preventDefault();
                        var $barcode = $("#foo").val();
                        String.prototype.replaceAll = function(search, replacement) {
                            var target = this;
                            return target.replace(new RegExp(search, 'g'), replacement);
                        };
                        var $barcode2 = $barcode.replaceAll('ß','-');
                        var id_product = null;
                        var check_add = 0;
                        if($barcode != null && $barcode !== ""){
                            $.ajax({
                                url: "<?php echo base_url(); ?>voxy_package_checkhang/getid_product",
                                async: false,
                                type: "POST",
                                dataType: "json",
                                data : {barcode: $barcode2 },
                                success :function (data) {
                                    id_product = data;
                                },
                                error: function () {
                                    console.log("loi ajax get id getid_product");
                                }
                            });
                        }else {

                        }


                        if(id_product === false || id_product === null){
                            var url = "<?php echo base_url(); ?>voxy_package_checkhang/add_barcode_checkhang";
                            check_add = 1;
                        }else {
                            var url = "<?php echo base_url(); ?>voxy_package_checkhang/edit_barcode_checkhang/"+id_product+".html";
                            check_add = 0;
                        }

                        $.ajax({
                            url: url,
                            type: "POST",            // Can change this to get if required
                            data: {
                                id_product : id_product,
                                check_add : check_add,
                            } ,
                            success: function(data) {
                                var ketqua = JSON.parse(data);
                                if(check_add === 0) { //sua
                                    var $modal = $("<div class='modal_content'>");

                                    $modal.html(ketqua.html);
                                    for (var key in ketqua.record_data) {
                                        var tempSelector = creat_input_selector("[name='" + key + "']");
                                        var tempSelector2 = creat_input_selector("[name='" + key + "[]"+"']");//location[]
                                        var inputObj = $modal.find(tempSelector);
                                        var inputObj2 = $modal.find(tempSelector2);
                                        if (inputObj.attr("type") === "checkbox") {
                                            inputObj.prop("checked", ketqua.record_data[key] == 1 ? true : false);
                                        } else {
                                            inputObj2.select2();// thang 2 la thang location
                                            if(inputObj.attr("type") === "select" ){
                                                inputObj.select2();//on thi bi mat selectedIndex
                                                inputObj.removeClass("select2");
                                                inputObj.css('width', '100%');
                                                inputObj.css('min-width', '300px');
                                                inputObj.css('padding-top', '8px');
                                                inputObj.css('padding-bottom', '8px');
                                                var childCount = inputObj[0]['length'];
                                                var childList = inputObj[0]['options'];
                                                for (var i = 0; i < childCount; i++){
                                                    if(ketqua.record_data[key] == childList[i]['value']){
                                                        childList[i]['selectedIndex'] = i;
                                                        inputObj.prop('selectedIndex', i).change();//set for jquery select2
                                                    } else {
                                                        //childList[i]['disabled'] = 1;
                                                    }
                                                }
                                            }
                                            inputObj.val(ketqua.record_data[key]);
                                        }
                                    }
                                    $modal.find("button:not(.b_edit)").remove();
                                    $modal.modal();
                                    $("#i_title").focus();
                                    if(isiPhone()){
                                        $(".modal-content").css('width','100%');
                                    }
                                    //$(".modal-content").css('max-width','866px');
                                    $("#foo").val("");
                                }else { //check_add = 1,them
                                    var $modal = $("<div class='modal_content'>");
                                    $modal.html(ketqua.html);
                                    $modal.find("button:not(.b_add)").remove();
                                    var tempSelector = creat_input_selector("[class='select2']");
                                    var inputObj = $modal.find(tempSelector);
                                    inputObj.select2();
                                    var selector = creat_input_selector("[alias_for]");
                                    $modal.find(selector).each(function() {
                                        var aliasObj = $(this);
                                        selector = creat_input_selector("[name='" + aliasObj.attr("alias_for") + "']");
                                        var sourceObj = $modal.find(selector);
                                        if (sourceObj && sourceObj.length) {
                                            sourceObj.on("keyup", function() {
                                                aliasObj.val(make_alias(sourceObj.val()));
                                            });
                                        }
                                        aliasObj.on("change", function() {
                                            aliasObj.val(make_alias(aliasObj.val()));ƒ
                                        });
                                    });
                                    $modal.modal();
                                    $('select.select2').removeClass('select2');
                                    $("#i_title").focus();
                                    $("#foo").val("");
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                $("#status").text(textStatus);
                            }
                        });
                    });

                    function isiPad(){
                        return (navigator.platform.indexOf("iPad") != -1);
                    }

                    function isiPhone(){
                        return (
                            (navigator.platform.indexOf("iPhone") != -1) ||
                            (navigator.platform.indexOf("iPod") != -1)
                        );
                    }

                </script>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    input[type='text']:focus,
    input[type='number']:focus,
    textarea:focus {
        font-size: 16px;
    }

    /* disable auto-zoom on iphone input field focus */
    @media screen and (-webkit-min-device-pixel-ratio:0) {
        select:focus,
        textarea:focus,
        input:focus {
            font-size: 16px;
        }
    }
</style>
