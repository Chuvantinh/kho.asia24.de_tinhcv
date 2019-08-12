$(document).ready(function() {
    var selector = creat_input_selector(":not(.disable_validate),");
    $(document).on("click", "a[href='#']", function(e) {
        e.preventDefault();
    });
    $(document).on("change", selector, check_value);
    $(document).on("submit", "form.e_ajax_submit", check_form);
    $(document).on("click", ".e_ajax_link", do_ajax_link);
    //$(document).on("submit", ".submit_barcode", do_ajax_link);
    $(document).on("change", ".e_check_all", do_check_all);
    $(document).on("click", ".e_reverse_button", reverse_check);//nut dao nguoc
    $(document).on("change", "input[name='_e_check_all']", show_delete_button);//hien thi nut delete ben tren

    $(document).on("change", ".e_changer_number_record", bind_ajax_change_table);//thay doi number page in top10_import.php
    $(document).on("change", ".e_search_table", bind_ajax_change_table);

    $(document).on("click", ".e_data_paginate li:not(.disabled):not(.active)", bind_ajax_change_table);
    $(document).on("change", "input[type='file'][preview='true']", bind_ajax_preview_img);
    $(document).on("click", ".e_toogle_next_div", toogle_next_div);
    $(document).on("click", ".e_data_table thead tr th", bind_ajax_change_table);

    $(document).on("keyup", "input[type='number']", convert_number);
    $(document).on("keyup", "input.e_number", convert_number);
    $(document).on("change", "input.e_number", check_value_number);

    $(document).on("focus", ":input[type=number]", disable_scroll_mousewheel);

    $(".e_data_table").each(function() {
        creat_ajax_table($(this));
    });
    $("div.widget-form-search").find("div.selector").css('display','none'); // thuyvv, filter form search manage
    $("select.select2").select2();
    $('body').modalmanager({resize: false});
});

/**
 * chan viec su dung chuot lan khi nhap so
 */
function disable_scroll_mousewheel(){
    $('input[type=number]').on('mousewheel', function(e){
        e.preventDefault();
        $(this).blur();
    });
}

function toogle_next_div(e) {
    if ($(this).hasClass('minimize')) {
        $(this).removeClass('minimize').addClass('maximize').next('div').slideUp('200');
    } else {
        $(this).removeClass('maximize').addClass('minimize').next('div').slideDown('200');
    }
}

function bind_ajax_preview_img(e) {
    var url = $(this).attr("preview_url");
    var inputName = $(this).attr("data-name");
    var fileList = this.files;
    if (fileList.length) {
        for (i = 0; i < fileList.length; i++) {
            creat_file_upload(fileList[i], inputName, url);
        }
    }
}

/**
 *
 * @param {file} file           Input type=file
 * @param {string} inputName    name input đẩy lên server
 * @param {string} url          url đẩy dữ liệu lên
 * @returns {undefined}
 */
function creat_file_upload(file, inputName, url) {
    var formData = new FormData();
    formData.append(inputName, file);
    $.ajax({
        url: url, //WEB API URL
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "text",
        xhr: function() {
            var xhr = $.ajaxSettings.xhr();
            xhr.upload.addEventListener('progress', function(event) {
                var percent = 0;
                var position = event.loaded || event.position; /*event.position is deprecated*/
                var total = event.total;
                if (event.lengthComputable) {
                    percent = Math.ceil(position / total * 100);
                }
                upload_progress(event, position, total, percent, file.name);
            }, false);
            return xhr;
        },
        success: function(data) {
//            alert(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
//                alert("error");
        }
    });

}

function upload_progress(event, position, total, percent, fileName) {
    $(".crop_value").append(fileName + "-" + percent + "|");
}

function bind_ajax_change_table(e) {
    /* Xử lý riêng khu vực phân trang */
    var cancel = false;
    var obj = $(this);
    if (obj.parents(".e_data_paginate").length) {
        obj.siblings().removeClass("active");
        obj.addClass("active");
    }
    //console.log(obj);
    /* Xử lý riêng khu vực sắp xếp */
    if (obj.parents("thead").length && obj.attr("field_name")) {
        if (obj.attr("field_name") != "custom_action" && obj.attr("field_name") != "custom_check") {
            if (obj.hasClass("sorting_asc")) {
                obj.removeClass("sorting_asc");
                obj.addClass("sorting");
                obj.attr("order", "");
                obj.removeAttr("order_pos");
            } else if (obj.hasClass("sorting_desc")) {
                obj.removeClass("sorting_desc");
                obj.addClass("sorting_asc");
                obj.attr("order", "asc");

                var curent_pos = obj.attr("order_pos");
                obj.siblings("th[order_pos]").each(function() {
                    if ($(this).attr("order_pos") > curent_pos) {
                        $(this).attr("order_pos", $(this).attr("order_pos") - 1);
                    }
                });
                obj.attr("order_pos", obj.siblings("th[order_pos]").length);
            } else if (obj.hasClass("sorting")) {
                obj.removeClass("sorting");
                obj.addClass("sorting_desc");
                obj.attr("order", "desc");
                obj.attr("order_pos", obj.siblings("th[order_pos]").length);
            }
        } else {
            cancel = true;
        }
    }

    if (!cancel) {
        e.preventDefault();
        creat_ajax_table($(this).parents(".e_data_table"));
    }
}

function reverse_check(e, source_obj) {
    var obj;
    if (source_obj) {
        obj = source_obj;
    } else {
        obj = $(this);
    }
    obj.parents(".e_widget").find(".e_data_table tbody input[name='_e_check_all']").each(function() {
        $(this).prop("checked", !$(this).prop("checked"));
    });
    show_delete_button(e, obj);
}

function show_delete_button(e, source_obj) {
    e.preventDefault();
    var obj;
    if (source_obj) {
        obj = source_obj;
    } else {
        obj = $(this);
    }
    if (obj.parents(".e_widget").find(".e_data_table tbody input[name='_e_check_all']:checked").length) {
        obj.parents(".e_widget").find(".e_actions_content .for_select").show();
    } else {
        obj.parents(".e_widget").find(".e_actions_content .for_select").hide();
        obj.parents(".e_widget").find(".e_data_table thead .e_check_all").prop("checked", false);
    }

    var temp = [];
    obj.parents(".e_widget").find(".e_data_table tbody input[name='_e_check_all']:checked").each(function() {
        temp.push($(this).parents("tr").attr("data-id"));
    });
    temp = {list_id: temp};
    temp = JSON.stringify(temp);
    obj.parents(".e_widget").find(".e_actions_content .delete_list_button").attr("data", temp);

}

function do_check_all(e, source_obj) {
    var obj;
    if (source_obj) {
        obj = source_obj;
    } else {
        obj = $(this);
    }
    obj.parents("table").find("input[name='_e_check_all']").prop("checked", obj.prop("checked"));
    show_delete_button(e, obj);
}

function creat_ajax_table(obj) {
    var url = obj.attr("data-url");
    var q = obj.find(".e_search_table").val();
    var limit = obj.find(".e_changer_number_record").val();
    var page = obj.find(".e_data_paginate li.active a").attr("data-page");
    var order = [];
    temp_order = {};
    obj.find("thead tr th").each(function() {
        if ($(this).attr("order")) {
            if ($(this).attr("order") == "asc" || $(this).attr("order") == "desc") {
                //                order.push($(this).attr("field_name") + " " + $(this).attr("order"));
                temp_order[$(this).attr("order_pos")] = $(this).attr("field_name") + " " + $(this).attr("order");
            }
        }
    });
    for (var i in temp_order) {
        order.push(temp_order[i]);
    }
    order = order.reverse();
    order = order.join(",");

    var data = {
        q: q,
        limit: limit,
        page: page,
        order: order
    };

    show_ajax_loading(obj);

    $.ajax({
        url: url,
        type: "POST",
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
                window[data.callback](data, obj);
            } else {
                console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_data_table'");
                default_data_table(data, obj);
            }
        },
        error: function(a, b, c) {
            alert(a + b + c);
            window.location = url;
        }
    });

}

function show_ajax_loading(obj) {
    var img = obj.attr("data-loading_img");
    var loading = $("<div class='e_loading'>");
    loading.html("<img src='" + img + "' title='Loading'/>");
    //loading.classList.add('e_loading');
    loading.css({
        "height": obj.height() + parseInt(obj.css("padding-top")) + parseInt(obj.css("padding-bottom")),
        "width": obj.width() + parseInt(obj.css("padding-left")) + parseInt(obj.css("padding-right")),
        "position": "absolute",
        "top": "0px",
        "left": "0px",
        "background-color": "rgba(179, 179, 179, 0.3)",
        "text-align": "center"
    });
    loading.children("img").css({
        "position": "absolute",
        "top": "50%",
        "left": "50%",
        "margin-left": "-24px",
        "margin-top": "-24px"
    });
    obj.append(loading);
}

function remove_ajax_loading(){
    var loading = $(".e_loading");
    loading.css({
        "display" : "none"
    })
}

function do_ajax_link(e, source_obj) {//xoa element
    e.preventDefault();
    var obj;
    if (source_obj) {
        obj = source_obj;
    } else {
        obj = $(this);
    }
    var url = obj.attr("href");
    var data = obj.attr("data");
    if (data) {
        data = JSON.parse(obj.attr("data"));
    }
    if (obj.hasClass("e_ajax_confirm")) {
        $("<div title='Bạn chắc chắn?'>").html("Dữ liệu không thể khôi phục sau khi làm thao tác này, bạn chắc chắn sẽ làm điều này chứ?").dialog({
            resizable: false,
            height: 180,
            modal: true,
            buttons: {
                "Chắc chắn": function() {
                    call_ajax_link(url, data, obj);
                    $(this).dialog("destroy");
                },
                "Thôi": function() {
                    $(this).dialog("destroy");
                }
            },
            close: function() {
                $(this).dialog("destroy");
            }
        });
    } else {
        call_ajax_link(url, data, obj);
    }
}

function call_ajax_link(url, data, obj) {
    $('body').modalmanager('loading');
    $.ajax({
        url: url,
        type: "POST",
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
                console.log("Gọi hàm: ", data.callback, obj);
                window[data.callback](data, obj);
            } else {
                console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_ajax_link'");
                console.log(data);
                default_ajax_link(data, obj);
            }
            changer_view_form();
        },
        error: function(a, b, c) {
//            alert(a + b + c);
            window.location = url;
        },
        complete: function(jqXHR, textStatus) {
            var $modal = $("<div class='modal fade out modal_content'>");
            $modal.html("");
            $modal.modal();
            $modal.modal('hide');
            $('body').modalmanager('removeLoading');
        }
    });
}

function check_form(e) {
    var check_all = true;
    var form = $(this);
    var selector = creat_input_selector(":not(.disable_validate),");
    form.find(selector).each(function() {
        var temp = check_value(e, $(this));
        check_all = check_all && temp;
    });

    if (check_all) {
        ajax_submit_form(form);
    }
    e.preventDefault();
    return false;
}

function call_ajax_link_barcode(url, id, data, obj) {
    $('body').modalmanager('loading');
    $.ajax({
        url: url,
        type: "POST",
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
                console.log("Gọi hàm: ", data.callback, obj);
                window[data.callback](data, obj);
            } else {
                console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_ajax_link'");
                default_ajax_link(data, obj);
            }
            changer_view_form();
        },
        error: function(a, b, c) {
//            alert(a + b + c);
            window.location = url;
        },
        complete: function(jqXHR, textStatus) {
            var $modal = $("<div class='modal fade out modal_content'>");
            $modal.html("");
            $modal.modal();
            $modal.modal('hide');
            $('body').modalmanager('removeLoading');
        }
    });
}

function ajax_submit_form(form) {
    var btn = form.find("button[type='submit']");
    btn.removeClass('btn-primary');
    btn.addClass('btn-danger');
    btn.html('Loading ...');
    btn.attr('disabled', 'disabled');
    $('body').modalmanager('loading');
    form.ajaxSubmit({
        type: "POST",
        dataType: "text",
        cache: false,
        success: function(dataAll) {
            var temp = dataAll.split($("body").attr("data-barack"));
            var data = {};
            for (var i in temp) {
                temp[i] = $.parseJSON(temp[i]);
                data = $.extend({}, data, temp[i]);
            }
            if (window[data.callback]) {
                console.log("Gọi hàm: ", data.callback);
                window[data.callback](data, form, btn);
            } else {
                console.log("Không tìm thấy hàm yêu cầu:'", data.callback, "'-->Tự động gọi hàm xử lý mặc định 'default_form_submit_respone'");
                default_form_submit_respone(data, form, btn);
            }
        },
        error: function(a, b, c) {
            btn.html('Error');
            btn.removeClass('btn-success');
            btn.removeAttr('disabled');
        },
        complete: function(jqXHR, textStatus) {
            $('body').modalmanager('removeLoading');
        }
    });
}

function check_value(e, source_obj) {
    var obj;
    if (source_obj) {
        obj = source_obj;
    } else {
        obj = $(this);
    }
    var value = $.trim(obj.val());
    if (obj.attr("type") == "number") {
        if (value.length != 0) {
            if (!(!isNaN(parseFloat(value)) && isFinite(value))) {
                change_error_state(obj, false);
                obj.parent(".controls-row").children("label.error").html("Dữ liệu nhập vào là số");
                return false;
            } else {
                change_error_state(obj, true);
            }
        }
    }
    if (obj.attr("required") != undefined && obj.attr("required") != "0") {
        if (value.length == 0) {
            change_error_state(obj, false);
            obj.parent(".controls-row").children("label.error").show();
            obj.parent(".controls-row").children("label.error").html("Trường này là bắt buộc");
            return false;
        } else {
            change_error_state(obj, true);
        }
    }
    if (obj.attr("minlength") != undefined && obj.attr("minlength") > 1) {
        if (value.length < obj.attr("minlength")) {
            change_error_state(obj, false);
            obj.parent(".controls-row").children("label.error").html("Độ dài tối thiểu là " + obj.attr("minlength") + " ký tự");
            return false;
        } else {
            change_error_state(obj, true);
        }
    }
    if (obj.attr("maxlength") != undefined && obj.attr("maxlength") > 1) {
        if (value.length > obj.attr("maxlength")) {
            change_error_state(obj, false);
            obj.parent(".controls-row").children("label.error").html("Độ dài tối đa là " + obj.attr("maxlength") + " ký tự");
            return false;
        } else {
            change_error_state(obj, true);
        }
    }

    if (obj.attr("is_email") != undefined && obj.attr("is_email") != 0) {
        if (!is_email(value)) {
            change_error_state(obj, false);
            obj.parent(".controls-row").children("label.error").html("Trường này yêu cầu là email !");
            return false;
        } else {
            change_error_state(obj, true);
        }
    }
    if (obj.attr("recheck") != undefined && obj.attr("recheck") != 0) {
        var selector = creat_input_selector("[name='" + obj.attr("recheck") + "']");
        if (value != $(selector).val()) {
            change_error_state(obj, false);
            obj.parent(".controls-row").children("label.error").html("Dữ liệu nhập lại không đúng !");
            return false;
        } else {
            change_error_state(obj, true);
        }
    }

    if (obj.prop("tagName") == "SELECT" && (obj.attr("allow_null") == undefined || obj.attr("allow_null") == '0' || obj.attr("allow_null").toLowerCase() == 'false')) {
        if (value == '0') {
            change_error_state(obj, false);
            obj.parent(".controls-row").children("label.error").html("Trường này không được bỏ trống !");
            return false;
        } else {
            change_error_state(obj, true);
        }
    }
    return true;
}