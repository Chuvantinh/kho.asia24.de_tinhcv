<div class="container-fluid">
    <div id="login">
        <div class="login-wrapper" data-active="log">
            <div id="log">
                <div class="page-header">
                    <img class="thumb thumb-rounded" src="<?php echo base_url('themes/images/patterns/logo.png'); ?>" alt="" style="border-radius: 0;border: 0;width: 100%;">
                    <h3 class="center">Đăng nhập tài khoản</h3>
                </div>
                <form id="login-form" class="form-horizontal e_ajax_submit" action="<?php echo $login_url; ?>">
                    <div class="row-fluid">
                        <div class="control-group">
                            <div class="controls-row">
                                <div class="icon"><i class="icon20 i-user"></i></div>
                                <input  style="border-radius: 3px; background: white" class="span12 valid_item" name="admin_email" id="i_admin_email" placeholder="<?php echo $form["field_form"]["user_name"]; ?>" <?php echo $this->m_login->get_display_rule($form["rule"]["user_name"]); ?> />
                                <label class="error"></label>
                            </div>
                        </div>
                        <!-- End .control-group -->
                        <div class="control-group">
                            <div class="controls-row">
                                <div class="icon"><i class="icon20 i-key"></i></div>
                                <input style="border-radius: 3px; background: white" class="span12 valid_item" type=password name="admin_password" id="i_admin_password" placeholder="<?php echo $form["field_form"]["password"]; ?>" <?php echo $this->m_login->get_display_rule($form["rule"]["password"]); ?> />
                                <label class="error"></label>
                            </div>
                        </div>
                        <!-- End .control-group -->
                        <div class="form-actions full">
                            <button id="loginBtn" type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                        </div>
                    </div>
                    <!-- End .row-fluid -->
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<style>

    html.loginPage{
        background-image: url(<?php echo base_url('images/santorini.png');?>) !important;
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;

        -webkit-background-size: cover;
        background-size:cover;
        -moz-background-size: cover;
        -o-background-size: cover;

    }
</style>