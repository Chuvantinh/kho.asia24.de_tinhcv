<div class="navbar-inner">
    <div class="container-fluid">
        <div class="nav-no-collapse">
            <div class="ban_quyen" style="margin-top:8px; color: white; float: left;">

            </div>

            <ul class="nav pull-right">
                <li class="divider-vertical"></li>
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle avatar" data-toggle="dropdown">
                        <img src="<?php echo $avatar; ?>" alt="avatar">
                        <?php echo(isset($user_name) ? $user_name : ''); ?>
                        <span class="more">
                            <i class="icon16 i-arrow-down-2"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-cogs"></i>
                                Cài đặt
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo '#';//$changer_info_url ?>" class="e_ajax_link_">
                                <i class="icon16 i-user"></i>
                                Hồ sơ cá nhân
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $logout_url; ?>" class="">
                                <i class="icon16 i-exit"></i>
                                Đăng xuất
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="divider-vertical"></li>
            </ul>

        </div>
        <!--/.nav-collapse -->
    </div>
</div>
