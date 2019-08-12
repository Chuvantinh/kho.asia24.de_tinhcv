<div class="row-fluid">
    <div class="span6">
        <div id="dataTable_length" class="dataTables_length">
            <label>
                <span>
                    Hiển thị  <input name="post" type="text" class="changer_number_record e_changer_number_record"
                                     value="<?php echo $limit ? $limit : "tất cả"; ?>"> bản ghi trên 1 trang
                </span>
            </label>
        </div>
    </div>
    <div class="span5">
        <div class="dataTables_filter" id="dataTable_filter">
            Tìm kiếm: <input type="text" name="q" class="e_search_table" autocomplete="off" value="<?php echo $search_string; ?>" style="width: 80%;" />
        </div>
    </div>
    <div class="clear"></div>
</div>

<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <?php
        foreach ($colum as $key => $value) {
            if (isset($order[$key])) {
                $temp = $order[$key];
                $class = "sorting_" . $order[$key];
            } else {
                $temp = "";
                $class = "sorting";
            }
            $order_post = array_search($key, array_keys($order));
            ?>
            <th class="<?php echo $class; ?>"
                order="<?php echo $temp; ?>" <?php echo (array_search($key, array_keys($order)) !== FALSE) ? "order_pos='" . $order_post . "'" : "" ?>
                field_name="<?php echo $key; ?>"><?php echo $value; ?></th>
        <?php } ?>
    </tr>
    </thead>
    <?php if (sizeof($record)) { ?>
        <tbody>
        <?php

        foreach ($record as $item)
        {
            if($item->tongtien_no != 0){
                $class = "baodo";
            }else{
                $class = "good";
            }
            ?>
            <tr class="gradeX <?php echo $class. " ".$item->order_number; ?>"  data-id="<?php echo $item->$key_name; ?>">
                <?php
                foreach ($colum as $key => $value) {
                    if($key == "custom_action"){
                        $custom_action = " custom_action";
                    }else{
                        $custom_action = "";
                    }

                    if($key == "m.total_price"){
                        $class_total_price = "black";
                    }else{
                        $class_total_price = "";
                    }

                    ?>
                    <td for_key="<?php echo $key; ?>" class="<?php echo $custom_action . $class_total_price; ?>">
                        <?php $key_temp = explode(".", $key);
                            echo $item->{end($key_temp)};
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    <?php } ?>
</table>
<?php if (!sizeof($record)) { ?>
    <p class="no_record">Không có bản ghi nào thỏa mãn yêu cầu</p>
<?php } else { ?>
    <div class="row-fluid no-magin">
        <div class="span6" style="margin-right: 0 !important;">
            <?php if ($to) { ?>
                <div class="dataTables_info" id="dataTable_info">Hiển thị
                    từ <?php echo $from . " tới " . $to . " trên tổng số " . $total; ?> bản ghi
                </div>
            <?php } else { ?>
                <div class="dataTables_info" id="dataTable_info">Hiển thị tất cả <?php echo $total; ?> bản ghi</div>
            <?php } ?>
        </div>

        <div class="span6">
            <div class="dataTables_paginate paging_bootstrap pagination">
                <?php echo $pagging; ?>
            </div>
        </div>

        <div class="clear"></div>
    </div>
<?php } ?>

<style>
    .baodo td{
        color:red;
        background-color: #c2efd4 !important;
    }

    .good td{
        background-color: #c2efd4 !important;
        color: blue;
    }

    .good:hover td,.baodo:hover td{
        background-color: #40cc4b !important;
    }
    .custom_action{
        display: flex;
    }
    .black{
        color: black !important;
    }
    .e_check_all{
        font-size: 100px !important;
    }
    .custom_action a {
        vertical-align: middle !important;
    }
</style>

<script type="text/javascript">
    var heigth_max = 0;
    $.each($('.custom_action').prev(),function(){
        var height = $('.custom_action').prev().height();
        if(height > heigth_max){
            heigth_max = height;
        }
    });

    $('.custom_action').height(heigth_max + 16 + 2);

    var height_for_a = $('.custom_action a').height();
    //console.log(height_for_a);
    $('.custom_action a').css('line-height',height_for_a+"px")

</script>