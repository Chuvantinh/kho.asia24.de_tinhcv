<div class="container-fluid">
    <div class="row-fluid"> 
        <div class="span12"> 
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <a href="#" class="minimize"></a>
                </div>

                <!-- div widget manage -->
                <div class="widget-manage">
                    <!-- div form search-->
                    <div class="widget-form-search" style="display: none"></div>

                    <!-- div form ajax-->
                    <div class="clear clear-form-search"></div>

                    <!-- Noi dung se hien thi -->
                    <div class="widget-content data_table">
                        <div class="accordion" id="accordion" style="margin: auto; max-width: 1200px">

                            <!-- Hien thi danh sach Zone he thong -->
                            <!-- <div>
                                <div class="control-group">
                                    <label class="control-label" for="system_weight" style="color: red">Bước 1: Hãy lựa chọn Zone hệ thống</label>
                                    <div class="controls controls-row">
                                        <select name="system_weight" id="system_weight" class="select2">
                                            <option value="">-- Lựa chọn giá trị --</option>
                                            <?php /*if(isset($list_system_weight)): ?>
                                                <?php foreach ($list_system_weight as $value): ?>
                                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['id'] . ' - ' . $value['name']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; */?>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="" style="color: red">Bước 2: Lựa chọn hành động cần thực hiện</label>
                                </div>
                            </div>-->

                            <!-- Các hành động cần thực hiện -->

                            <!-- 1. Tạo tài khoản người dùng mới -->
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" href="#voxy_create_new_user"> 1. Tạo tài khoản người dùng mới </a>
                                </div>

                                <div id="voxy_create_new_user" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                        <?php if(isset($USER->permission_data['*']) || isset($USER->permission_data['admin_custom_tools_create_new_user'])): ?>
                                            <form class="form-horizontal" id="CreateNewUser" action="<?php echo $voxy_create_new_user; ?>" method="POST">
                                                <div class="control-group">
                                                    <label class="control-label">ID(<i style="color: red">*</i> )</label>
                                                    <div class="controls controls-row">
                                                        <input id="CreateNewUser_external_user_id" name="external_user_id" class="span12 tipB" type="number" min="1" required data-original-title="Định dạng là chữ số" >
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">Email(<i style="color: red">*</i> )</label>
                                                    <div class="controls controls-row">
                                                        <input id="CreateNewUser_email_address" name="email_address" class="span12 tip" type="email" required data-original-title="Định dạng là một Email" >
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">First Name(<i style="color: red">*</i> )</label>
                                                    <div class="controls controls-row">
                                                        <input id="CreateNewUser_first_name" name="first_name" class="span12" type="text" required maxlength="100" >
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">Last Name</label>
                                                    <div class="controls controls-row">
                                                        <input id="CreateNewUser_last_name" name="last_name" class="span12" type="text" maxlength="100" >
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">Số điện thoại</label>
                                                    <div class="controls controls-row">
                                                        <input id="CreateNewUser_phone_number" name="phone_number" class="span12 tip" type="text" maxlength="50" data-original-title="Phải đúng chuẩn định dạng SĐT quốc tế">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">Ngày hết hạn</label>
                                                    <div class="controls controls-row">
                                                        <input id="CreateNewUser_expiration_date" name="expiration_date" class="span12" type="datetime" readonly="readonly">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Native Language(<i style="color: red">*</i> )</label>
                                                    <div class="controls controls-row">
                                                        <select id="CreateNewUser_native_language" name="native_language" required>
                                                            <option value="">-- Lựa chọn giá trị --</option>
                                                            <?php if(isset($list_native_language)): ?>
                                                                <?php foreach ($list_native_language as $key => $value): ?>
                                                                    <option value="<?php echo $key; ?>"><?php echo $key . ' - ' .$value; ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Level(<i style="color: red">*</i> )</label>
                                                    <div class="controls controls-row">
                                                        <select id="CreateNewUser_level" name="level" required>
                                                            <option value="">-- Lựa chọn giá trị --</option>
                                                            <?php if(isset($list_level)): ?>
                                                                <?php foreach ($list_level as $key => $value): ?>
                                                                    <option value="<?php echo $key; ?>"><?php echo $key . ' - ' .$value; ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Can Reserve Group Sessions(<i style="color: red">*</i> )</label>
                                                    <div class="controls controls-row">
                                                        <select name="can_reserve_group_sessions" id="CreateNewUser_can_reserve_group_sessions" required>
                                                            <option value="">-- Lựa chọn giá trị --</option>
                                                            <?php if(isset($list_can_reserve_group_sessions)): ?>
                                                                <?php foreach ($list_can_reserve_group_sessions as $key => $value): ?>
                                                                    <option value="<?php echo $key; ?>"><?php echo $key . ' - ' .$value; ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label class="control-label"></label>
                                                    <div class="controls controls-row">
                                                        <button type="submit" class="span2 btn btn-primary">Tạo tài khoản</button>
                                                        <button type="reset" class="span2 btn btn-danger">Hủy</button>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <pre>Bạn chưa được cấp quyền sử dụng tính năng này !</pre>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    $("#CreateNewUser :input[name='expiration_date']").datepicker({
                                        dateFormat: "dd-mm-yy",
                                        changeMonth: true,
                                        numberOfMonths: 1,
                                        minDate: 'today'
                                    });
                                    // submit get user info
                                    $(function()
                                    {
                                        $('#CreateNewUser').submit(function(event) {
                                            event.preventDefault(); // Prevent the form from submitting via the browser
                                            var form = $(this);

                                            show_loading($('#voxy_create_new_user'));
                                            $.ajax({
                                                url: form.attr("action"),
                                                type: form.attr("method"),
                                                data: form.serialize(),
                                                dataType: "JSON",
                                                success: function (data, status) {
                                                    var _status = 'error';
                                                    var _msg    = 'Hành động thất bại !';

                                                    if(data && data.status == true){
                                                        _status = 'success';
                                                        _msg    = data.msg;
                                                    } else if(data && (data.status == false || data.state == 0)) {
                                                        _msg    = data.msg;
                                                    }

                                                    hide_loading();
                                                    console.log(data);
                                                    show_top_error( _status, _msg);
                                                },
                                                error: function (xhr, desc, err) {
                                                    hide_loading();
                                                    show_top_error('error', err);
                                                    console.log(xhr);
                                                    console.log(desc);
                                                }
                                            });
                                        });
                                    });
                                </script>
                            </div>

                            <!-- 2. Hiển thị và cập nhật thông tin người dùng -->
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" href="#voxy_show_user_info"> 2. Hiển thị và cập nhật thông tin người dùng </a>
                                </div>

                                <div id="voxy_show_user_info" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                        <form class="form-horizontal" id="ShowUser" action="<?php echo $voxy_show_user_info; ?>" method="POST">
                                            <div class="control-group">
                                                <label class="control-label" for="">LMS ID(<i style="color: red">*</i> )</label>
                                                <div class="controls controls-row">
                                                    <input class="span6 tipB" id="ShowUser_external_user_id" name="external_user_id" data-original-title="ID LMS" type="text" min="1" required>
                                                    <button type="submit" class="span2 btn btn-primary">Hiển thị</button>
                                                    <button type="reset" class="span2 btn btn-danger">Hủy</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div style="margin: 10px;">
                                        <div class="widget-content noPadding">
                                            <!-- Hien thi thon tin co ban tai khoan HV Voxy -->
                                            <table class="table table-bordered table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Email</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Phone</th>
                                                        <th>Level</th>
                                                        <th>Feature Groups</th>
                                                        <th>Date Joined</th>
                                                        <th>Expiration Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ShowTableUser"></tbody>
                                            </table>
                                            <br />

                                            <!-- Hien thi bang du lieu link dang nhap vao Voxy -->
                                            <table class="table table-bordered table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th style="min-width: 10%;">ID Học viên</th>
                                                        <th>Link Login Voxy</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ShowTableLinkLogin"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="UpdateUserInfoModel" class="modal hide fade" style="display: none; ">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></button>
                                        <h4>Chỉnh sửa thông tin tài khoản</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="paddingT15 paddingB15">
                                            <form class="form-horizontal" id="UpdateUserInfo" action="<?php echo $voxy_update_user_info; ?>" method="POST" style="margin-bottom: 0px">
                                                <div class="control-group">
                                                    <label class="control-label" for="">External User Id(<i style="color: red">*</i> )</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8" id="UpdateUserInfo_external_user_id" name="external_user_id" readonly="readonly" type="text" min="1" required>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">New External User Id</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8" id="UpdateUserInfo_new_external_user_id" name="new_external_user_id" type="text" min="1">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">First Name</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8" id="UpdateUserInfo_first_name" name="first_name" type="text"  maxlength="100">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Last Name</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8" id="UpdateUserInfo_last_name" name="last_name" type="text" maxlength="100">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Email Address</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8 tip" id="UpdateUserInfo_email_address" name="email_address" data-original-title="Định dạng là một Email" type="email" >
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Native Language</label>
                                                    <div class="controls controls-row">
                                                        <select id="UpdateUserInfo_native_language" name="native_language" class="select2">
                                                            <option value="">-- Lựa chọn giá trị --</option>
                                                            <?php if(isset($list_native_language)): ?>
                                                                <?php foreach ($list_native_language as $key => $value): ?>
                                                                    <option value="<?php echo $key; ?>"><?php echo $key . ' - ' .$value; ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Ngày VPA tiếp theo</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8" id="UpdateUserInfo_date_of_next_vpa" name="date_of_next_vpa" type="datetime" readonly="readonly">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Ngày hết hạn</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8" id="UpdateUserInfo_expiration_date" name="expiration_date" type="datetime" readonly="readonly">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Số điện thoại</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8 tip" id="UpdateUserInfo_phone_number" name="phone_number" data-original-title="Định dạng số điện thoại chuẩn quốc tế" type="text" maxlength="50">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Feature Group</label>
                                                    <div class="controls controls-row">
                                                        <input class="span8" id="UpdateUserInfo_feature_group" name="feature_group" type="number" >
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Level</label>
                                                    <div class="controls controls-row">
                                                        <select id="UpdateUserInfo_level" name="level" class="select2">
                                                            <option value="">-- Lựa chọn giá trị --</option>
                                                            <?php if(isset($list_level)): ?>
                                                                <?php foreach ($list_level as $key => $value): ?>
                                                                    <option value="<?php echo $key; ?>"><?php echo $key . ' - ' .$value; ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="">Can Reserve Group Sessions</label>
                                                    <div class="controls controls-row">
                                                        <select id="UpdateUserInfo_can_reserve_group_sessions" name="can_reserve_group_sessions" class="select2">
                                                            <option value="">-- Lựa chọn giá trị --</option>
                                                            <?php if(isset($list_can_reserve_group_sessions)): ?>
                                                                <?php foreach ($list_can_reserve_group_sessions as $key => $value): ?>
                                                                    <option value="<?php echo $key; ?>"><?php echo $key . ' - ' .$value; ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label class="control-label" for=""></label>
                                                    <div class="controls controls-row">
                                                        <button type="submit" class="span2 btn btn-primary">Cập nhật</button>
                                                        <button type="reset" class="span2 btn btn-danger" data-dismiss="modal">Hủy</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    var user_info; // bien luu tru thong tin user sau khi lay tu voxy ve

                                    $("#UpdateUserInfo :input[name='date_of_next_vpa']").datepicker({
                                        dateFormat: "dd-mm-yy",
                                        changeMonth: true,
                                        numberOfMonths: 1,
                                        minDate: 'today'
                                    });

                                    $("#UpdateUserInfo :input[name='expiration_date']").datepicker({
                                        dateFormat: "dd-mm-yy",
                                        changeMonth: true,
                                        numberOfMonths: 1,
                                        minDate: 'today'
                                    });

                                    // submit get user info
                                    $(function()
                                    {
                                        $('#ShowUser').submit(function(event) {
                                            event.preventDefault(); // Prevent the form from submitting via the browser
                                            var form = $(this);
                                            var external_user_id = $("#ShowUser :input[name='external_user_id']").val();

                                            $('#ShowTableUser').empty();
                                            $('#ShowTableLinkLogin').empty();
                                            show_loading($('#voxy_show_user_info'));
                                            $.ajax({
                                                url: form.attr("action"),
                                                type: form.attr("method"),
                                                data: form.serialize(),
                                                dataType: "JSON",
                                                success: function (data, status) {
                                                    var _status = 'error';
                                                    var _msg    = 'Hành động thất bại !';

                                                    if(data && data.status == true){
                                                        _status = 'success';
                                                        _msg    = data.msg;

                                                        user_info = data.data;
                                                        user_info.external_user_id = external_user_id;

                                                        $('#ShowTableUser').empty();
                                                        $('#ShowTableLinkLogin').empty();
                                                        $('#ShowTableUser').append(add_html_user_info(user_info));
                                                        // change_data_update_user(user_info);
                                                    } else if(data && (data.status == false || data.state == 0)) {
                                                        _msg    = data.msg;
                                                    }

                                                    hide_loading();
                                                    console.log(data);
                                                    show_top_error( _status, _msg);
                                                },
                                                error: function (xhr, desc, err) {
                                                    hide_loading();
                                                    show_top_error('error', err);
                                                    console.log(xhr);
                                                    console.log(desc);
                                                }
                                            });
                                        });
                                    });

                                    // ham chuyen du lieu vao html table thong tin user
                                    function add_html_user_info(user_info)
                                    {
                                        var html ='<tr>';
                                        html += '<td class="center vcenter">' + user_info.external_user_id + '</td>';
                                        html += '<td class="center vcenter">' + user_info.email_address + '</td>';
                                        html += '<td class="center vcenter">' + user_info.first_name + '</td>';
                                        html += '<td class="center vcenter">' + user_info.last_name + '</td>';
                                        html += '<td class="center vcenter">' + user_info.phone_number + '</td>';
                                        html += '<td class="center vcenter">' + user_info.level + '</td>';
                                        html += '<td class="center vcenter">' + (user_info.feature_group.id ? (user_info.feature_group.id + ' - ' + user_info.feature_group.name) : '') + '</td>';
                                        html += '<td class="center vcenter">' + user_info.date_joined + '</td>';
                                        html += '<td class="center vcenter">' + user_info.expiration_date + '</td>';
                                        html += '<td class="center vcenter"><div class="btn-group">';
                                        html += '<a href="#UpdateUserInfoModel" data-toggle="modal" class="btn tip" onclick="change_data_update_user(user_info)" id="btn_edit_user" title="Chỉnh sửa thông tin" data-original-title="Chỉnh sửa thông tin"><i class="icon16 i-pencil"></i></a>';
                                        html += '<a href="#" class="btn tip" onclick="get_auth_token_by_user(user_info.external_user_id)"  id="btn_get_auth_token" title="Lấy link đăng nhập với quyền người dùng này" data-original-title="Lấy link đăng nhập với quyền người dùng này"><i class="icon16 i-spinner-10"></i></a>';
                                        html += '</div></td>';
                                        html +='</tr>';
                                        return html;
                                    }

                                    // ham xu ly repalce thong tin hoc vien vao form chinh sua thong tin
                                    function change_data_update_user(user_info)
                                    {
                                        $("#UpdateUserInfo").resetForm();
                                        $("#UpdateUserInfo :input[name='external_user_id']").val(user_info.external_user_id);
                                        $("#UpdateUserInfo :input[name='first_name']").val(user_info.first_name);
                                        $("#UpdateUserInfo :input[name='last_name']").val(user_info.last_name);
                                        $("#UpdateUserInfo :input[name='email_address']").val(user_info.email_address);
                                        $("#UpdateUserInfo :input[name='native_language']").val(user_info.native_language).change();
                                        $("#UpdateUserInfo :input[name='level']").val(user_info.level).change();
                                        $("#UpdateUserInfo :input[name='can_reserve_group_sessions']").val(user_info.can_reserve_group_sessions.toString().toUpperCase()).change();
                                        $("#UpdateUserInfo :input[name='expiration_date']").val(user_info.expiration_date);
                                        $("#UpdateUserInfo :input[name='date_of_next_vpa']").val(user_info.date_of_next_vpa);
                                        $("#UpdateUserInfo :input[name='phone_number']").val(user_info.phone_number);
                                        $("#UpdateUserInfo :input[name='feature_group']").val((user_info.feature_group.id ? user_info.feature_group.id : ''));
                                    }

                                    // Ham xu ly call ajax ve controller xu ly lay duong link dang nhap
                                    function get_auth_token_by_user(external_user_id)
                                    {
                                        $('#ShowTableLinkLogin').empty();
                                        show_loading($('#voxy_show_user_info'));
                                        $.ajax({
                                            url: '<?php echo $voxy_login_by_user; ?>',
                                            type: 'POST',
                                            data: {'external_user_id':external_user_id},
                                            dataType: "JSON",
                                            success: function (data, status) {
                                                var _status = 'error';
                                                var _msg    = 'Hành động thất bại !';

                                                if(data && data.status == true){
                                                    _status = 'success';
                                                    _msg    = data.msg;

                                                    var data_link = data.data;
                                                    $('#ShowTableLinkLogin').empty();
                                                    $('#ShowTableLinkLogin').append(
                                                        '<tr><td class="center vcenter">' + external_user_id + '</td><td><a target="_blank" href="'+ data_link.actions.start +'">' + data_link.actions.start + '</a></td></tr>'
                                                    );
                                                } else if(data && (data.status == false || data.state == 0)) {
                                                    _msg    = data.msg;
                                                }

                                                hide_loading();
                                                console.log(data);
                                                show_top_error( _status, _msg);
                                            },
                                            error: function (xhr, desc, err) {
                                                hide_loading();
                                                show_top_error('error', err);
                                                console.log(xhr);
                                                console.log(desc);
                                            }
                                        });
                                    }

                                    // submit update user info
                                    $(function()
                                    {
                                        $('#UpdateUserInfo').submit(function(event) {
                                            event.preventDefault(); // Prevent the form from submitting via the browser
                                            var form = $(this);
                                            $.ajax({
                                                url: form.attr("action"),
                                                type: form.attr("method"),
                                                data: form.serialize(),
                                                dataType: "JSON",
                                                success: function (data, status) {
                                                    var _status = 'error';
                                                    var _msg    = 'Hành động thất bại !';

                                                    if(data && data.status == true){
                                                        _status = 'success';
                                                        _msg    = data.msg;

                                                        $('#UpdateUserInfo').resetForm();
                                                        $('#UpdateUserInfoModel').modal('hide');
                                                        setTimeout(function () {
                                                            $('#ShowUser').submit();
                                                        }, 300);
                                                    } else if(data && (data.status == false || data.state == 0)) {
                                                        _msg    = data.msg;
                                                    }

                                                    console.log(data);
                                                    show_top_error( _status, _msg);
                                                },
                                                error: function (xhr, desc, err) {
                                                    show_top_error('error', err);
                                                    console.log(xhr);
                                                    console.log(desc);
                                                }
                                            });
                                        });
                                    });
                                </script>
                            </div>

                            <!-- 3. Hiển thị danh sách Feature Group -->
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" href="#show_all_feature_group"> 3. Hiển thị danh sách Feature Group Voxy </a>
                                </div>

                                <div id="show_all_feature_group" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                        <form class="form-horizontal" id="ShowAllFeatureGroup" action="<?php echo $voxy_show_all_feature_group; ?>" method="POST">
                                            <div class="control-group">
                                                <label class="control-label" for=""></label>
                                                <div class="controls controls-row">
                                                    <button type="submit" class="span2 btn btn-primary">Lấy danh sách</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div style="margin: 10px;">
                                        <div class="widget-content noPadding">
                                            <!-- Hien thi thon tin co ban tai khoan HV Voxy -->
                                            <table class="table table-bordered table-hover table-striped">
                                                <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Number Of Active Users</th>
                                                    <th>Enable Admins To Set Learner Level</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="ShowTableFeatureGroup"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="ShowDetailFeatureGroupModal" class="modal hide fade" style="display: none; ">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></button>
                                        <h4>Thông tin chi tiết Feature Group</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="paddingT15 paddingB15">
                                            <form class="form-horizontal" id="ShowDetailFeatureGroup" style="margin-bottom: 0px"></form>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="#" class="btn" data-dismiss="modal">Đóng</a>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    // danh sach Group lay ve duoc
                                    var list_group;

                                    // submit get list Feature Group
                                    $(function()
                                    {
                                        $('#ShowAllFeatureGroup').submit(function(event) {
                                            event.preventDefault(); // Prevent the form from submitting via the browser
                                            var form = $(this);

                                            $('#ShowTableFeatureGroup').empty();
                                            show_loading($('#show_all_feature_group'));
                                            $.ajax({
                                                url: form.attr("action"),
                                                type: form.attr("method"),
                                                data: form.serialize(),
                                                dataType: "JSON",
                                                success: function (data, status) {
                                                    var _status = 'error';
                                                    var _msg    = 'Hành động thất bại !';

                                                    if(data && data.status == true){
                                                        _status = 'success';
                                                        _msg    = data.msg;

                                                        list_group = data.data;

                                                        $('#ShowTableFeatureGroup').empty();
                                                        $('#ShowTableFeatureGroup').append(add_html_all_feature_group(list_group));
                                                    } else if(data && (data.status == false || data.state == 0)) {
                                                        _msg    = data.msg;
                                                    }

                                                    hide_loading();
                                                    console.log(data);
                                                    show_top_error( _status, _msg);
                                                },
                                                error: function (xhr, desc, err) {
                                                    hide_loading();
                                                    show_top_error('error', err);
                                                    console.log(xhr);
                                                    console.log(desc);
                                                }
                                            });
                                        });
                                    });

                                    // ham day du lieu danh sach Features Group vao table
                                    function add_html_all_feature_group(list_feature_group)
                                    {
                                        var html = '';
                                        $.each( list_feature_group, function( key, item ) {
                                            html +='<tr>';
                                            html += '<td class="center vcenter">' + key + '</td>';
                                            html += '<td class="center vcenter">' + item.id + '</td>';
                                            html += '<td class="center vcenter">' + item.name + '</td>';
                                            html += '<td class="center vcenter">' + item.number_of_active_users + '</td>';
                                            html += '<td class="center vcenter">' + item.enable_admins_to_set_learner_level + '</td>';
                                            html += '<td class="center vcenter"><div class="btn-group">';
                                            html += '<a href="#ShowDetailFeatureGroupModal" data-toggle="modal" class="btn" onclick="show_detail_feature_group('+key+')" title="Hiển thị thông tin chi tiết"><i class="icon16 i-eye-3"></i></a>';
                                            html += '</div></td>';
                                            html +='</tr>';
                                        });

                                        return html;
                                    }

                                    // ham day du lieu chi tiet tung Features Group va modal
                                    function show_detail_feature_group(key1)
                                    {
                                        var html = '';
                                        $('#ShowDetailFeatureGroup').empty();
                                        $.each( list_group, function( key2, item2 ) {
                                            if(key1 == key2){
                                                html += '<div class="control-group">';
                                                html += '<label class="control-label" >ID</label>';
                                                html += '<div class="controls controls-row">';
                                                html += '<pre>' + item2.id + '</pre>';
                                                html += '</div>';
                                                html += '</div>';

                                                html += '<div class="control-group">';
                                                html += '<label class="control-label" >Name</label>';
                                                html += '<div class="controls controls-row">';
                                                html += '<pre>' + item2.name + '</pre>';
                                                html += '</div>';
                                                html += '</div>';

                                                html += '<div class="control-group">';
                                                html += '<label class="control-label" >Number Of Active Users</label>';
                                                html += '<div class="controls controls-row">';
                                                html += '<pre>' + item2.number_of_active_users + '</pre>';
                                                html += '</div>';
                                                html += '</div>';

                                                html += '<div class="control-group">';
                                                html += '<label class="control-label" >Enable Admins To Set Learner Level</label>';
                                                html += '<div class="controls controls-row">';
                                                html += '<pre>' + item2.enable_admins_to_set_learner_level + '</pre>';
                                                html += '</div>';
                                                html += '</div>';

                                                html += '<div class="control-group">';
                                                html += '<label class="control-label" >Features</label>';
                                                html += '<div class="controls controls-row">';
                                                $.each( item2.features, function( key3, item3 ) {
                                                    html += '<pre>' + key3 + ' : ' + item3 + '</pre>';
                                                });
                                                html += '</div>';
                                                html += '</div>';
                                            }
                                        });
                                        $('#ShowDetailFeatureGroup').append(html);
                                    }
                                </script>
                            </div>

                            <!-- 4. Chuyển danh sách người dùng vào Feature Group -->
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" href="#voxy_add_users_to_feature_group"> 4. Chuyển danh sách người dùng vào Feature Group </a>
                                </div>
                                <div id="voxy_add_users_to_feature_group" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                        <form class="form-horizontal" id="AddUsersToFeatureGroup" action="<?php echo $voxy_add_users_to_feature_group; ?>" enctype="multipart/form-data" method="POST">
                                            <div class="control-group">
                                                <label class="control-label" for=""></label>
                                                <div class="controls controls-row">
                                                    <a href="<?php echo $voxy_add_users_to_feature_group_file_temp; ?>" download><b style="color: Red">Download File Temp</b></a>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="AddUsersToFeatureGroup_fileinput">Danh sách User</label>
                                                <div class="controls controls-row">
                                                    <input type="file" name="AddUsersToFeatureGroup_fileinput" id="AddUsersToFeatureGroup_fileinput" accept=".xlsx"/>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for=""></label>
                                                <div class="controls controls-row">
                                                    <button type="submit" class="span2 btn btn-primary">Thực thi</button>
                                                    <button type="reset" class="span2 btn btn-danger" name="reset_form" >Hủy</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    // submit get list Feature Group
                                    $(function()
                                    {
                                        $("#AddUsersToFeatureGroup :button[name='reset_form']").on('click', function(e){
                                            $("#AddUsersToFeatureGroup_fileinput").val('');
                                        });

                                        $('#AddUsersToFeatureGroup').submit(function(event) {
                                            event.stopPropagation(); // Stop stuff happening
                                            event.preventDefault(); // Prevent the form from submitting via the browser

                                            var form = $(this);
                                            var fileupload = $('#AddUsersToFeatureGroup_fileinput')[0].files[0];

                                            if( fileupload == undefined){
                                                show_top_error('error', 'Bạn chưa upload File danh sách học viên !');
                                                return false;
                                            }

                                            var formData = new FormData();
                                            formData.append('fileinput', fileupload);

                                            show_loading($('#voxy_add_users_to_feature_group'));
                                            $.ajax({
                                                url: form.attr("action"),
                                                type: form.attr("method"),
                                                data: formData,
                                                cache: false,
                                                processData: false,  // tell jQuery not to process the data
                                                contentType: false,  // tell jQuery not to set contentType
                                                dataType: "JSON",
                                                success: function (data, status) {
                                                    var _status = 'error';
                                                    var _msg    = 'Hành động thất bại !';

                                                    if(data && data.status == true){
                                                        _status = 'success';
                                                        _msg    = data.msg;
                                                    } else if(data && (data.status == false || data.state == 0)) {
                                                        _msg    = data.msg;
                                                    }

                                                    hide_loading();
                                                    console.log(data);
                                                    show_top_error( _status, _msg);
                                                },
                                                error: function (xhr, desc, err) {
                                                    hide_loading();
                                                    show_top_error('error', err);
                                                    console.log(xhr);
                                                    console.log(desc);
                                                }
                                            });
                                        });
                                    });
                                </script>
                            </div>

                            <!-- 5. Chuyển danh sách người dùng về ngày hết hạn -->
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" href="#voxy_update_expiration_date_users"> 5. Chuyển danh sách người dùng về ngày hết hạn </a>
                                </div>
                                <div id="voxy_update_expiration_date_users" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                        <form class="form-horizontal" id="UpdateExpirationDateUsers" action="<?php echo $voxy_update_expiration_date_users; ?>" enctype="multipart/form-data" method="POST">
                                            <div class="control-group">
                                                <label class="control-label" for=""></label>
                                                <div class="controls controls-row">
                                                    <a href="<?php echo $voxy_update_expiration_date_users_file_temp; ?>" download><b style="color: Red">Download File Temp</b></a>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="UpdateExpirationDateUsers_fileinput">Danh sách User</label>
                                                <div class="controls controls-row">
                                                    <input type="file" name="UpdateExpirationDateUsers_fileinput" id="UpdateExpirationDateUsers_fileinput" accept=".xlsx"/>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for=""></label>
                                                <div class="controls controls-row">
                                                    <button type="submit" class="span2 btn btn-primary">Thực thi</button>
                                                    <button type="reset" class="span2 btn btn-danger" name="reset_form" >Hủy</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    // submit get list Feature Group
                                    $(function()
                                    {
                                        $("#UpdateExpirationDateUsers :button[name='reset_form']").on('click', function(e){
                                            $("#UpdateExpirationDateUsers_fileinput").val('');
                                        });

                                        $('#UpdateExpirationDateUsers').submit(function(event) {
                                            event.stopPropagation(); // Stop stuff happening
                                            event.preventDefault(); // Prevent the form from submitting via the browser

                                            var form = $(this);
                                            var fileupload = $('#UpdateExpirationDateUsers_fileinput')[0].files[0];

                                            if( fileupload == undefined){
                                                show_top_error('error', 'Bạn chưa upload File danh sách học viên !');
                                                return false;
                                            }

                                            var formData = new FormData();
                                            formData.append('fileinput', fileupload);

                                            show_loading($('#voxy_update_expiration_date_users'));
                                            $.ajax({
                                                url: form.attr("action"),
                                                type: form.attr("method"),
                                                data: formData,
                                                cache: false,
                                                processData: false,  // tell jQuery not to process the data
                                                contentType: false,  // tell jQuery not to set contentType
                                                dataType: "JSON",
                                                success: function (data, status) {
                                                    var _status = 'error';
                                                    var _msg    = 'Hành động thất bại !';

                                                    if(data && data.status == true){
                                                        _status = 'success';
                                                        _msg    = data.msg;
                                                    } else if(data && (data.status == false || data.state == 0)) {
                                                        _msg    = data.msg;
                                                    }

                                                    hide_loading();
                                                    console.log(data);
                                                    show_top_error( _status, _msg);
                                                },
                                                error: function (xhr, desc, err) {
                                                    hide_loading();
                                                    show_top_error('error', err);
                                                    console.log(xhr);
                                                    console.log(desc);
                                                }
                                            });
                                        });
                                    });
                                </script>
                            </div>

                            <!-- 6. Hiển thị danh sách người dùng -->
                            <!-- <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" href="#voxy_show_all_user"> 4. Hiển thị danh sách Người dùng trên Voxy </a>
                                </div>
                                <div id="voxy_show_all_user" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                        <form class="form-horizontal" action="<?php //echo $voxy_show_all_user; ?>">
                                            <div class="control-group">
                                                <label class="control-label" for="ShowAllUser_from_date">Từ ngày</label>
                                                <div class="controls controls-row">
                                                    <input class="span12 tip" id="ShowAllUser_from_date" name="ShowAllUser_from_date" data-original-title="Từ ngày" type="datetime" readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="ShowAllUser_to_date">Đến ngày</label>
                                                <div class="controls controls-row">
                                                    <input class="span12 tip" id="ShowAllUser_to_date" name="ShowAllUser_to_date" data-original-title="Đến ngày" type="datetime" readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="ShowAllUser_page">Page</label>
                                                <div class="controls controls-row">
                                                    <input class="span12 tip" id="ShowAllUser_page" name="ShowAllUser_page" data-original-title="Định dạng phải là số" type="number" min="1">
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for=""></label>
                                                <div class="controls controls-row">
                                                    <button type="submit" class="span2 btn btn-primary">Hiển thị</button>
                                                    <button type="reset" class="span2 btn btn-danger">Hủy</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <script type="text/javascript">
                                        $("#ShowAllUser_from_date").datepicker({
                                            dateFormat: "dd-mm-yy",
                                            changeMonth: true,
                                            numberOfMonths: 1,
                                            maxDate: 'today',
                                            onClose: function (selectedDate) {
                                                $("#ShowAllUser_to_date").datepicker("option", "minDate", selectedDate);
                                            }
                                        });
                                        $("#ShowAllUser_to_date").datepicker({
                                            dateFormat: "dd-mm-yy",
                                            changeMonth: true,
                                            numberOfMonths: 1,
                                            maxDate: 'today',
                                            onClose: function (selectedDate) {
                                                $("#ShowAllUser_from_date").datepicker("option", "maxDate", selectedDate);
                                            }
                                        });
                                    </script>
                                </div>
                            </div>-->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Show Status Ajax: status: error - success
    function show_top_error(status, mgs)
    {
        $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + mgs, {
            group: status,
            position: 'top-right',
            sticky: false,
            closeTemplate: '<i class="icon16 i-close-2"></i>',
            animateOpen: {
                width: 'show',
                height: 'show'
            }
        });
    }

    // show ajax loading image
    function show_loading(obj)
    {
        var loading = $("<div class='show_loading'>");
        loading.html("<img src='<?php echo $loading_gif; ?>' title='Loading'/>");
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

    // hide ajax loading image
    function hide_loading()
    {
        $('.show_loading').css({
            "z-index": -1
        });
    }
</script>

