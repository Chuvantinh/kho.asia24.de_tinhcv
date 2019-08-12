<div class="row-fluid">
    <div class="span6" style="margin-right: 0;">
        <div id="dataTable_length" class="dataTables_length">
            <label>
                <span>
                    Hiển thị  <input name="post" type="text" class="changer_number_record e_changer_number_record"
                                     value="<?php echo $limit ? $limit : "tất cả"; ?>"> bản ghi trên 1 trang
                </span>
            </label>
        </div>
    </div>
    <div class="span6" style="margin-right: 0;">
        <div class="dataTables_filter" id="dataTable_filter">
            <label>
                <span>Tìm kiếm: </span>
                <input type="text" name="q" class="e_search_table" value="<?php echo $search_string; ?>" autocomplete="off"/>
            </label>
        </div>
    </div>
    <div class="clear"></div>
</div>

<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover display nowrap">
    <thead>
    <tr>
        <?php
        $i = 0;
        foreach ($colum as $key => $value) {
            $i++;
            $class_new = " class-".$i;
                if (isset($order[$key])) {
                    $temp = $order[$key];
                    $class = "sorting_" . $key;
                } else {
                    $temp = "";
                    $class = "sorting_" . $key;
                }
                $order_post = array_search($key, array_keys($order));
                ?>
                <th class="<?php echo $class. $class_new?>"
                    order="<?php echo $temp; ?>" <?php echo (array_search($key, array_keys($order)) !== FALSE) ? "order_pos='" . $order_post . "'" : "" ?>
                    field_name="<?php echo $key; ?>"><?php echo $value; ?></th>
            <?php } ?>
    </tr>
    </thead>
    <?php if (sizeof($record)) { ?>
        <tbody>
        <?php foreach ($record as $item) { ?>
            <tr class="gradeX" data-id="<?php echo $item->$key_name; ?>">
                <?php foreach ($colum as $key => $value) {
                        ?>
                        <td for_key="<?php echo $key; ?>">
                            <?php $key_temp = explode(".", $key);
                            echo $item->{end($key_temp)}; ?>
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
        <div class="span6" style="margin-right: 0;">
            <?php if ($to) { ?>
                <div class="dataTables_info" id="dataTable_info">Hiển thị
                    từ <?php echo $from . " tới " . $to . " trên tổng số " . $total; ?> bản ghi
                </div>
            <?php } else { ?>
                <div class="dataTables_info" id="dataTable_info">Hiển thị tất cả <?php echo $total; ?> bản ghi</div>
            <?php } ?>
        </div>
        <div class="span6" style="margin-right: 0;">
            <div class="dataTables_paginate paging_bootstrap pagination">
                <?php echo $pagging; ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php } ?>

<style>

    @media only screen and (max-width: 600px) {
        .sorting_m.id
        {
            display: none !important;
        }
        .export_product_excel {display:none}

        .table-bordered  thead tr th.class-1,
        .table-bordered  thead tr th.class-2,
        .table-bordered  thead tr th.class-8,
        .table-bordered  thead tr th.class-9,
        .table-bordered  tbody tr td[for_key="m.sku1"],
        .table-bordered  tbody tr td[for_key="m.sku2"],
        .table-bordered  tbody tr td[for_key="m.price1"],
        .table-bordered  tbody tr td[for_key="m.price2"]
        {
            display:none !important;
        }

    }
    .table-bordered thead:first-child tr:first-child th[field_name='custom_check'] {
        width: 2% !important;
    }
</style>
