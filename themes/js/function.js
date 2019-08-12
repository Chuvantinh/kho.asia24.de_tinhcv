/**
 * @descreption: Tổng hợp các hàm sửa dụng
 *
 */

/*==================================*/

/**
 * Hàm xử lý sự kiện sau khi click vào link ajax trả kết quả về
 * @param {Object} data Dữ liệu trả về
 * @param {jQuery} obj Link click vào
 * @returns {undefined}
 */
function default_ajax_link(data, obj) {
    if (data.state == 1) {
        if (data.html) {
            var $modal = $("<div class='modal fade out modal_content'>");
            $modal.html(data.html);
            $modal.modal();
        }
    } else {
        if (data.redirect) {
            window.location = data.redirect;
        }
    }
}


function permission_error(data, obj) {
    if (data.state != undefined && data.state == 0) {
        $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
            group: "error",
            position: 'top-right',
            sticky: false,
            closeTemplate: '<i class="icon16 i-close-2"></i>',
            animateOpen: {
                width: 'show',
                height: 'show'
            }
        });
    }
}

function default_data_table(data, obj) {
    if (data.state !== undefined && data.state == 0) {
        $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
            group: "error",
            position: 'top-right',
            sticky: false,
            closeTemplate: '<i class="icon16 i-close-2"></i>',
            animateOpen: {
                width: 'show',
                height: 'show'
            }
        });
    } else {
        obj.html(data.html);
        obj.find("select").select2();
    }
}

/**
 * Hàm sử lý sự kiện sau khi lưu form edit
 * @param {Object} data Dữ liệu trả về
 * @param {jQuery} form Form xảy ra sự kiện
 * @param {jQuery} button   Button xảy ra sự kiện
 * @returns {undefined}
 */
function  save_form_edit_response(data, form, button) {
    button.removeAttr('disabled');
    var jgrow = "error";
    if (data.state == 1) { /* Thành công */
        button.removeClass('btn-danger');
        button.addClass('btn-success');
        button.html('Thành công ...');
        jgrow = "success";

        var for_key = data.key_name;
        $("th[field_name]").each(function() {
            var attr = $(this).attr("field_name").split(".");
            if (attr[attr.length - 1] == data.key_name) {
                for_key = $(this).attr("field_name");
            }
        });
        var tempObj = $("tr[data-id='" + data.record[data.key_name] + "'] td[for_key='" + for_key + "']").parent("tr");

        if (tempObj && tempObj.length) {
            tempObj.find("td").effect("highlight", {}, 5000);
            for (var key in data.record) {
                tempObj.children("td[for_key]").each(function() {
                    var t_key = $(this).attr("for_key");
                    if (t_key == key) {
                        $(this).html(data.record[key]);
                    } else {
                    if (t_key.split(".").length > 1) {
                        if (t_key.split(".")[1] == key) {
                            $(this).html(data.record[key]);
                        }
                    }
                }
                });
            }
        }

        form.parents(".modal_content").modal("hide");
    } else if (data.state == 0) { /* Lỗi dữ liệu không hợp lệ */
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Thất bại ...');
    } else if (data.state == 2) { /* Lỗi phía server */
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Thất bại ...');
    } else {
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Không rõ kết quả');
    }
    if (data.error) {
        show_error(data.error);
    }

    if (data.redirect === 'self') {
        window.location.reload();
    } else if (data.redirect) {
        window.location = data.redirect;
    }

    $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
        group: jgrow,
        position: 'top-right',
        sticky: false,
        closeTemplate: '<i class="icon16 i-close-2"></i>',
        animateOpen: {
            width: 'show',
            height: 'show'
        }
    });
}



/**
 * Hàm sử lý sự kiện sau khi lưu form add
 * @param {Object} data Dữ liệu trả về
 * @param {jQuery} form Form xảy ra sự kiện
 * @param {jQuery} button   Button xảy ra sự kiện
 * @returns {undefined}
 */
function  save_form_add_response(data, form, button) {
    button.removeAttr('disabled');
    var jgrow = "error";
    if (data.state == 1) { /* Thành công */
        button.removeClass('btn-danger');
        button.addClass('btn-success');
        button.html('Thành công ...');
        jgrow = "success";

        var tempObj = false;
        $("th[field_name]").each(function() {
            var attr = $(this).attr("field_name").split(".");
            if (attr[attr.length - 1] == data.key_name) {
                tempObj = $(this).parents(".data_table");
            }
        });
        if (tempObj) {
            creat_ajax_table(tempObj);
        }

        form.parents(".modal_content").modal("hide");
    } else if (data.state == 0) { /* Lỗi dữ liệu không hợp lệ */
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Thất bại ...');
    } else if (data.state == 2) { /* Lỗi phía server */
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Thất bại ...');
        location.reload();
    } else {
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Không rõ kết quả');
        location.reload();
    }
    if (data.error) {
        show_error(data.error);
    }

    if (data.redirect === 'self') {
        window.location.reload();
    } else if (data.redirect) {
        window.location = data.redirect;
    }

    $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
        group: jgrow,
        position: 'top-right',
        sticky: false,
        closeTemplate: '<i class="icon16 i-close-2"></i>',
        animateOpen: {
            width: 'show',
            height: 'show'
        }
    });
}

/**
 * Hàm xử lý dữ liệu trả về khi gọi form add
 * @param {object}  data    dữ liệu trả về dạng json.
 * @param {jQuery}  obj     Đối tượng click vào (đối tượng phát sinh ajax)
 * @returns {undefined}
 */
function get_form_add_response(data, obj) {
    a = data;
    if (data.state == 1) {
        if (data.html) {
            var $modal = $("<div class='modal fade out modal_content'>");
            $modal.html(data.html);
            $modal.find("button:not(.b_add)").remove();
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
        }
    } else {
        if (data.redirect === 'self') {
            window.location.reload();
        } else if (data.redirect) {
            window.location = data.redirect;
        }
    }
}

/**
 * Hàm xử lý dữ liệu trả về khi gọi form edit
 * @param {object}  data    dữ liệu trả về dạng json.
 * @param {jQuery}  obj     Đối tượng click vào (đối tượng phát sinh ajax)
 * @returns {undefined}
 */
function get_form_edit_response(data, obj) {
    if (data.state != "1") {
        $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
            group: "error",
            position: 'top-right',
            sticky: false,
            closeTemplate: '<i class="icon16 i-close-2"></i>',
            animateOpen: {
                width: 'show',
                height: 'show'
            }
        });
    } else {
        if (!data.record_data) {
            $.jGrowl("<i class='icon16 i-checkmark-3'></i> Id không tồn tại", {
                group: "error",
                position: 'top-right',
                sticky: false,
                closeTemplate: '<i class="icon16 i-close-2"></i>',
                animateOpen: {
                    width: 'show',
                    height: 'show'
                }
            });
        }
        if (data.html) {
            var $modal = $("<div class='modal fade out modal_content'>");
            $modal.html(data.html);
            for (var key in data.record_data) {
                var tempSelector = creat_input_selector("[name='" + key + "']");
                var inputObj = $modal.find(tempSelector);
                if (inputObj.attr("type") == "checkbox") {
                    inputObj.prop("checked", data.record_data[key] == 1 ? true : false);
                } else {
                    if(inputObj.attr("type") == "select" && inputObj.attr("readonly") == 'readonly' ){
                        inputObj.removeClass("select2");
                        inputObj.css('width', '100%');
                        inputObj.css('min-width', '300px');
                        inputObj.css('padding-top', '8px');
                        inputObj.css('padding-bottom', '8px');
                        var childCount = inputObj[0]['length'];
                        var childList = inputObj[0]['options'];
                        for (var i = 0; i < childCount; i++){
                            if(data.record_data[key] == childList[i]['value']){
                                childList[i]['selectedIndex'] = i;
                            } else {
                                childList[i]['disabled'] = 1;
                            }
                        }
                    }
                    inputObj.val(data.record_data[key]);
                }
            }
            $modal.find("button:not(.b_edit)").remove();
            $modal.modal();
        }
    }
}


/**
 * Hàm xử lý dữ liệu trả về khi gọi form view
 * @param {object}  data    dữ liệu trả về dạng json.
 * @param {jQuery}  obj     Đối tượng click vào (đối tượng phát sinh ajax)
 * @returns {undefined}
 */
function get_data_view_response(data, obj) {
    if (data.state != 1) {
        $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
            group: "error",
            position: 'top-right',
            sticky: false,
            closeTemplate: '<i class="icon16 i-close-2"></i>',
            animateOpen: {
                width: 'show',
                height: 'show'
            }
        });
    } else {
        if (!data.record_data) {
            $.jGrowl("<i class='icon16 i-checkmark-3'></i> Id không tồn tại", {
                group: "error",
                position: 'top-right',
                sticky: false,
                closeTemplate: '<i class="icon16 i-close-2"></i>',
                animateOpen: {
                    width: 'show',
                    height: 'show'
                }
            });
        }
        if (data.html) {
            var $modal = $("<div class='modal fade out modal_content'>");
            $modal.html(data.html);
            for (var key in data.record_data) {
                var tempSelector = creat_input_selector("[name='" + key + "']");
                var inputObj = $modal.find(tempSelector).attr("disabled", "disabled");
                if (inputObj.attr("type") == "checkbox") {
                    inputObj.prop("checked", data.record_data[key] == 1 ? true : false);
                } else {
                    inputObj.val(data.record_data[key]);
                }
            }
            $modal.find("button:not(.b_view)").remove();
            $modal.modal({
                keyboard: true,
                backdrop: true
            });

        }
    }
}


function delete_respone(data, obj) {
    var group = "success";

    if (data.state != 1) {
        group = "error";
    } else {
        for (var key in data.list_id) {
            obj.parents(".e_widget").find(".e_data_table table tbody tr[data-id='" + data.list_id[key] + "']").fadeOut(1000, function() {
                $(this).remove();
            });
        }
    }

    $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
        group: group,
        position: 'top-right',
        sticky: false,
        closeTemplate: '<i class="icon16 i-close-2"></i>',
        animateOpen: {
            width: 'show',
            height: 'show'
        }
    });
}



/**
 * Hàm xử lý mặc định dữ liệu trả về khi submit form
 * @param {object} data
 * @param {jQuery} form
 * @param {jQuery} button
 * @returns {undefined}
 */
function default_form_submit_respone(data, form, button) {
    button.removeAttr('disabled');
    var jgrow = "error";
    if (data.state == 1) { /* Thành công */
        button.removeClass('btn-danger');
        button.addClass('btn-success');
        button.html('Thành công ...');
        jgrow = "success";
    } else if (data.state == 0) { /* Lỗi dữ liệu không hợp lệ */
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Thất bại ...');
    } else if (data.state == 2) { /* Lỗi phía server */
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Thất bại ...');
    } else {
        button.addClass('btn-danger');
        button.removeClass('btn-success');
        button.html('Không rõ kết quả');
    }
    if (data.error) {
        show_error(data.error);
    }

    $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + data.msg, {
        group: jgrow,
        position: 'top-right',
        sticky: false,
        closeTemplate: '<i class="icon16 i-close-2"></i>',
        animateOpen: {
            width: 'show',
            height: 'show'
        },
        afterOpen: function() {
            if (data.redirect === 'self') {
                window.location.reload();
            } else if (data.redirect) {
                window.location = data.redirect;
            }
        }
    });
}

/**
 *
 * @param {object} errorList Danh sách các lỗi, dạng objetc {key:value}<br/>
 *                       key là name của form, value là text hiển thị lỗi
 * @returns {undefined}
 */
function show_error(errorList) {
    for (var key in errorList) {
        var selector = creat_input_selector("[name='" + key + "']");
        var obj = $(selector);
        obj.removeClass("valid");
        obj.addClass("error");
        var error = obj.siblings("label.error"); //length;//.children(".error");
        if (error.length) {
            error.show();
        } else {
            obj.after("<label class='error' ></label>").show();
        }
        obj.children("label.error").html(errorList[key]);
        obj.siblings("label.error").html(errorList[key]);
    }
}


/**
 * Hàm thay đổi hiển thị lỗi của các input trong form
 * @param {jQuery} obj
 * @param {bool} is_valid
 * @returns change View
 */
function change_error_state(obj, is_valid) {
    if (is_valid) {
        obj.removeClass("error");
        obj.addClass("valid");
        obj.siblings("label.error").hide();
    } else {
        obj.removeClass("valid");
        obj.addClass("error");
        var error = obj.siblings("label.error"); //length;//.children(".error");
        if (error.length) {
            error.show();
        } else {
            obj.after("<label class='error' ></label>").show();
        }
    }
}


/**
 * Hàm kiểm tra email có hợp lệ hay không
 * @param {String} email
 * @returns {boolean}
 */
function is_email(email) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(email);
}


function creat_input_selector(more) {
    var input = "input,select,radio,textarea";
    var temp = input.split(",");
    var selector = "";
    if (more.substr(more.length - 1) != ",") {
        selector = temp.join(more + ",") + more;
    } else {
        selector = temp.join(more) + more.slice(0, more.length - 1);
    }
    return selector;
}

function changer_view_form() {
    $(".resizable").resizable({handles: "e, w"});
    $(".uniform").find("select.select2").select2();
    //    $(".button").button();
    $(".uniform").find("[type='datepicker']").datepicker({dateFormat: 'dd-mm-yy'});
    $(".uniform").find("[type='checkbox'], [type='radio'], [type='file']").not('.toggle, .chosen, .multiselect').uniform();
    $(".ckeditor").each(function() {
        var name = $(this).attr("name");
        var finder = $(this).attr("data-ckfinder");
        window[name] = CKEDITOR.replace($(this).get(0),
                {
                    toolbar: [
                        // ['NewPage', 'DocProps', 'Preview', '-', 'Templates'],
                        ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
                        ['Find', 'Replace', '-', 'SelectAll'],
                        ['Link', 'Unlink', 'Anchor'],
                        ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'],
                        ['Maximize', 'ShowBlocks'],
                        // ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'],
                        // ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'],
                        ['Format', 'Font', 'FontSize'],
                        ['TextColor', 'BGColor']
                    ],
                    height: '100px',
                    width: '774px'
                });
        CKFinder.setupCKEditor(window[name], finder);
    });
}

function make_alias($title, $separator, $lowercase) {
    if ($separator === undefined) {
        $separator = "-";
    }
    if ($lowercase === undefined) {
        $lowercase = true;
    }
    $title = $.trim($title);
    $title = $title.replace(/\s+/gi, ' ');
    $title = $title.replace(/á|à|ạ|ả|ã|ă|ắ|ằ|ặ|ẳ|ẵ|â|ấ|ầ|ậ|ẩ|ẫ/g, "a");
    $title = $title.replace(/Á|À|Ạ|Ả|Ã|Â|Ấ|Ầ|Ậ|Ẩ|Ẫ|Ă|Ắ|Ằ|Ặ|Ẳ|Ẵ/g, "A");
    $title = $title.replace(/ó|ò|ọ|ỏ|õ|ô|ố|ồ|ộ|ổ|ỗ|ơ|ớ|ờ|ợ|ở|ỡ/g, "o");
    $title = $title.replace(/Ô|Ố|Ồ|Ộ|Ổ|Ỗ|Ó|Ò|Ọ|Ỏ|Õ|Ơ|Ớ|Ờ|Ợ|Ở|Ỡ/g, "O");
    $title = $title.replace(/é|è|ẹ|ẻ|ẽ|ê|ế|ề|ệ|ể|ễ/g, "e");
    $title = $title.replace(/Ê|Ế|Ề|Ệ|Ể|Ễ|É|È|Ẹ|Ẻ|Ẽ/g, "E");
    $title = $title.replace(/ú|ù|ụ|ủ|ũ|ư|ứ|ừ|ự|ử|ữ/g, "u");
    $title = $title.replace(/Ư|Ứ|Ừ|Ự|Ử|Ữ|Ú|Ù|Ụ|Ủ|Ũ/g, "U");
    $title = $title.replace(/í|ì|ị|ỉ|ĩ/g, "i");
    $title = $title.replace(/Í|Ì|Ị|Ỉ|Ĩ/g, "I");
    $title = $title.replace(/ý|ỳ|ỵ|ỷ|ỹ/g, "y");
    $title = $title.replace(/Ý|Ỳ|Ỵ|Ỷ|Ỹ/g, "Y");
    $title = $title.replace(/đ/g, "d");
    $title = $title.replace(/Đ/g, "D");

    $title = $title.replace(/\{|\}|\$|\||\\|`|!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g, $separator);
    $title = $title.replace(/\-+/g, $separator);
    $title = $title.replace(/^\-+|\-+$/g, "");
    $title = $title.replace(/[^0-9A-Za-z\-]/g, "");

    if ($lowercase) {
        $title = $title.toLowerCase();
    }
    return $title;
}

/**
 * Ham chuyen convert number -> view
 */
function convert_number() {
    var source_obj = $(this);
    if (!source_obj.hasClass('e_not_number')) {
        var unit = $(this).attr('unit');
        var val_obj = source_obj.val();
        var val_convert = $.number(val_obj, 0, ",", ".");
        var e_convert_num_value = source_obj.siblings('.e_convert_num_value');
        if (!e_convert_num_value.length) {
            source_obj.after('<div class="e_convert_num_value" style="font-weight:bold;color:#00887A"></div>')
        }
        if (unit == undefined) {
            unit = '';
        }
        e_convert_num_value.html(val_convert + ' ' + unit);
        if (!parseInt(val_convert)) {
            e_convert_num_value.hide();
        } else {
            e_convert_num_value.show();
        }
    }
}

function check_value_number(e, source_obj) {
    var obj;
    if (source_obj) {
        obj = source_obj;
    } else {
        obj = $(this);
    }
    var value = $.trim(obj.val());
    if (value.length != 0) {
        if (!(!isNaN(parseFloat(value)) && isFinite(value))) {
            change_error_state(obj, false);
            obj.siblings("label.error").html("Dữ liệu nhập vào là số");
            return false;
        } else {
            change_error_state(obj, true);
        }
    }
    return true;
}