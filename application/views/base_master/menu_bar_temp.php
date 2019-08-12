<div class="navbar-inner">
    <div class="container-fluid">
        <a class="brand" href="<?php echo site_url(); ?>">
            <img src="<?php echo $logo; ?>" alt="Manager - Topmito" style="background-color: #fff; box-shadow: 0 0 10px 8px #fff;">
        </a>
        <div class="nav-no-collapse">
            <div id="top-search">
                <form action="#" method="post" class="form-search">
                    <div class="input-append">
                        <input type="text" name="tsearch" id="tsearch" placeholder="Search here ..." class="search-query">
                        <button type="submit" class="btn">
                            <i class="icon16 i-search-2 gap-right0"></i>
                        </button>
                    </div>
                </form>
            </div>
            <ul class="nav pull-right">
                <li class="divider-vertical"></li>
                <li class="dropdown hidden">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon24 i-bell-2"></i>
                        <span class="notification red">6</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-calendar-2"></i>
                                Admin Jenny add event</a>
                        </li>
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-file-zip"></i>
                                User Dexter attach file</a>
                        </li>
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-stack-picture"></i>
                                User Dexter attach 3 pictures</a>
                        </li>
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-cart-add"></i>
                                New orders <span class="notification green">2</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-bubbles-2"></i>
                                New comments <span class="notification red">5</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-pie-5"></i>
                                Daily stats is generated</a>
                        </li>
                    </ul>
                </li>
                <li class="divider-vertical"></li>
                <li class="dropdown hidden">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon24 i-envelop-2"></i>
                        <span class="notification red">3</span>
                    </a>
                    <ul class="dropdown-menu messages">
                        <li class="head">
                            <h4>Inbox</h4>
                            <span class="count">3 messages</span>
                            <span class="new-msg">
                                <a href="#" class="tipB" title="Write message">
                                    <i class="icon16 i-pencil-5"></i>
                                </a>
                            </span>
                        </li>
                        <li>
                            <a href="#" class="clearfix">
                                <span class="avatar">
                                    <img src="<?php echo  $this->path_static_file ?>images/default_avatar.png" alt="Manager - Topmito">
                                </span>
                                <span class="msg">Call me i need to talk with you</span>
                                <button class="btn close">
                                    <i class="icon12 i-close-2"></i>
                                </button>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="clearfix">
                                <span class="avatar">
                                    <img src="<?php echo  $this->path_static_file ?>images/default_avatar.png" alt="Manager - Topmito">
                                </span>
                                <span class="msg">Problem with registration</span>
                                <button class="btn close">
                                    <i class="icon12 i-close-2"></i>
                                </button>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="clearfix">
                                <span class="avatar">
                                    <img src="<?php echo  $this->path_static_file ?>images/default_avatar.png" alt="Manager - Topmito">
                                </span>
                                <span class="msg">
                                    I have question about ...
                                </span>
                                <button class="btn close">
                                    <i class="icon12 i-close-2"></i>
                                </button>
                            </a>
                        </li>
                        <li class="foot">
                            <a href="email.html">View all messages</a>
                        </li>
                    </ul>
                </li>
                <li class="divider-vertical"></li>
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle avatar" data-toggle="dropdown">
                        <img src="<?php echo $avatar; ?>" alt="avatar">
                        <span class="more">
                            <i class="icon16 i-arrow-down-2"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="">
                                <i class="icon16 i-cogs"></i>
                                Settings</a>
                        </li>
                        <li>
                            <a href="<?php echo '#';//$changer_info_url ?>" class="e_ajax_link_">
                                <i class="icon16 i-user"></i>
                                Profile</a>
                        </li>
                        <li>
                            <a href="<?php echo $logout_url; ?>" class="">
                                <i class="icon16 i-exit"></i>
                                Logout</a>
                        </li>
                    </ul>
                </li>
                <li class="divider-vertical"></li>
            </ul>
        </div>
        <!--/.nav-collapse --> 
    </div>
</div>