<!DOCTYPE html>
<html lang="vi" prefix="og: http://ogp.me/ns#">
    <head>
        <?php echo $header_base; ?>
        <?php echo $header_master_page; ?>
        <?php echo $header_page; ?>
    </head>
    <body data-barack="<?php echo $this->json_item_barack; ?>">
        <div class="page-container">
            <header id="header" class="navbar navbar-inverse navbar-fixed-top">
                <?php echo $menu_bar; ?>
            </header>
            <!-- End #header --> 
            <div class="main">
                <aside id="sidebar">
                    <?php echo $left_content; ?>
                </aside>
                <!-- End #sidebar -->
                <section id="content">
                    <div class="wrapper">
                        <?php // echo $breadcrumb; ?>
                        <div class="container-fluid">
<!--                            <div id="heading" class="page-header">-->
<!--                                <h1><i class="icon20 i-file-7"></i>-->
<!--                                    --><?php //echo $title; ?>
<!--                                </h1>-->
<!--                            </div>-->
                            <div class="row-fluid">
                                <?php echo $content; ?>
                            </div>
                            <!-- End .row-fluid -->
                        </div>
                        <!-- End .container-fluid -->
                    </div>
                    <!-- End .wrapper -->
                </section>
                <div id="right_content">
                    <?php echo $right_content; ?>
                </div>
            </div>
            <!-- End .main -->
            <div id="footer">
                <?php echo $footer; ?>
            </div>
        </div>
    </body>
</html>