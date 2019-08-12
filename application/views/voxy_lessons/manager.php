<div class="container-fluid"> 
    <div class="row-fluid"> 
        <div class="span12"> 
            <div class="widget e_widget">
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <div class="actions_content e_actions_content">
                        <a href="<?php //echo $add_link; ?>" class="btn btn-info add_button" > Xuất Excel </a>
                    </div>
                    <a href="#" class="minimize"></a>
                </div>
                <div class="widget-content data_table e_data_table" data-url="<?php echo $ajax_data_link; ?>" data-loading_img="<?php echo $this->path_theme_file; ?>images/preloaders/loading-spiral.gif">
                    <!-- Ajax load ding content -->
                </div>
            </div>
        </div>
    </div>
</div>