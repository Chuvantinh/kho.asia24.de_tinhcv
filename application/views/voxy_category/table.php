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
    <div class="span6">
        <div class="dataTables_filter" id="dataTable_filter">
            <label>
                <span>Lọc: </span>
                <input type="text" name="q" class="e_search_table" value="<?php echo $search_string; ?>"/>
            </label>
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

        foreach ($record as  $item) { ?>
            <tr class="gradeX" data-id="<?php echo $item->$key_name; ?>">
                <?php
                foreach ($colum as $key => $value) { ?>
                    <td for_key="<?php echo $key; ?>">
                        <?php $key_temp = explode(".", $key);
                        if($key == 'm.default_address'){
                            $array = get_object_vars(json_decode($item->default_address)); ?>
                            <p> <?php if (isset($array['d_last_name'])){echo $array['d_last_name'];}else{}; echo "&nbsp";
                                if (isset($array['d_first_name'])){echo $array['d_first_name'];}else{} ?>
                            </p>
                            <p> <?php if (isset($array['address1'])){echo $array['address1'];}else{}  ?></p>
                            <p> <?php if (isset($array['zip'])){echo $array['zip'];}else{} ; echo "&nbsp";
                                if (isset($array['city'])){echo $array['city'];}else{}  ?></p>
                            <p> <?php if (isset($array['country_name'])){echo $array['country_name'];}else{}  ?></p>
                            <p> <?php if (isset($array['phone'])){echo $array['phone'];}else{}  ?></p>
                        <?php }else {
                            echo $item->{end($key_temp)};
                        }; ?>
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
        <div class="span6">
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