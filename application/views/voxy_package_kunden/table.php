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
        <td style="width: 20px !important;"></td>
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
                field_name="<?php echo $key; ?>"><?php echo $value; ?>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <?php if (sizeof($record)) { ?>
        <tbody>
        <?php

        foreach ($record as $item) {
            if($item->conno != 0){
                $class = "baodo";
            }else{
                $item->conno = "";
                $class = "good";
            }
            ?>
            <tr class="gradeX <?php echo $class; ?>" data-id="<?php echo $item->id; ?>" data-id-customer="<?php echo $item->id_customer; ?>">
                <td style="width: 20px !important;">
                    <i class="plus">+</i>
                    <i style="display: none;" class="minus">-</i>
                </td>
                <?php
                foreach ($colum as $key => $value) {

                    ?>
                    <td for_key="<?php echo $key; ?>">
                        <?php $key_temp = explode(".", $key);
                        if ($key == 'm.default_address') {
                            if (isset($item->default_address) && $item->default_address != null) {
                                $array = get_object_vars(json_decode($item->default_address));
                                ?>
                                <p> <?php if (isset($array['d_last_name'])) {
                                        echo $array['d_last_name'];
                                    } else {
                                    };
                                    echo "&nbsp";
                                    if (isset($array['d_first_name'])) {
                                        echo $array['d_first_name'];
                                    } else {
                                    } ?>
                                </p>
                                <p> <?php if (isset($array['address1'])) {
                                        echo $array['address1'];
                                    } else {
                                    } ?></p>
                                <p> <?php if (isset($array['zip'])) {
                                        echo $array['zip'];
                                    } else {
                                    };
                                    echo "&nbsp";
                                    if (isset($array['city'])) {
                                        echo $array['city'];
                                    } else {
                                    } ?></p>
                                <p> <?php if (isset($array['country_name'])) {
                                        echo $array['country_name'];
                                    } else {
                                    } ?></p>
                                <p> <?php if (isset($array['phone'])) {
                                        echo $array['phone'];
                                    } else {
                                    } ?></p>
                            <?php }
                        } else {
                            echo $item->{end($key_temp)};
                        } ?>
                    </td>
                <?php } ?>

            </tr>
            <tr class="gradeX parent-child parent-child-<?php echo $item->id_customer; ?>" data-id="<?php echo $item->$key_name; ?>" data-id-customer="<?php echo $item->id_customer; ?>">

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

<style type="text/css">
    .plus{
        height: 20px;
        width: 20px;
        line-height: 20px;
        display: block;
        color: white;
        box-shadow: rgb(68, 68, 68) 0px 0px 3px;
        box-sizing: content-box;
        text-align: center;
        background-color: rgb(49, 177, 49);
        text-indent: 0px !important;
        border-width: 2px;
        border-style: solid;
        border-color: white;
        border-image: initial;
        font-size: 20px;
        border-radius: 20px;
    }

    .plus:hover{
        cursor: pointer;
    }
    .minus:hover{
        cursor: pointer;
    }
    .minus{
        height: 20px;
        width: 20px;
        display: block;
        color: white;
        box-shadow: rgb(68, 68, 68) 0px 0px 3px;
        box-sizing: content-box;
        text-align: center;
        line-height: 20px;
        text-indent: 0px !important;
        border-width: 2px;
        border-style: solid;
        border-color: white;
        border-image: initial;
        border-radius: 20px;
        background-color: rgb(211, 51, 51);
        font-size: 20px
    }

    .child_title{
        width: 100%;
    }
    .child_title p {
        width: 20%;
        float: left;
        background-color: #1a8eed;
        color: white;
    }

    .parent-child {
        background-color: azure;
        position: absolute;
        border: solid 1px #0093ff;
        height: 300px;
        width: 800px;
        overflow: auto;
        display: none;
        z-index: 9999;
        border-radius: 3em;
    }

    .element{
        width: 100%;
        float: left;
    }

    .element p {
        width: 20%;
        background-color: #c2efd4;
        color: #17161d;
        float: left;
    }
    .child.active{
        display: table-row;
    }

    .element p:first-child{
        text-align: center;
    }
    .child_title p:first-child{
        text-align: center;
    }

    .list_result {
        position: absolute;
        left: 0;
        right: 0;
        z-index: 999;
        background-color: white;
        color: white;
        width: 800px;
    }

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

</style>

<script type="text/javascript">
    $('.plus').on('click',function () {
        var data_id_customer = $(this).parent().parent().attr('data-id-customer');

        $(this).css('display','none');
        $(this).next().css('display','block');

        var date_liefer = $('.date_liefer').val();
        var date_liefer_end = $('.date_liefer_end').val();

        //var obj = $(".e_data_table");
        //show_ajax_loading(obj);
        $.ajax({
            url: "<?php echo base_url(); ?>voxy_package_kunden/get_infor_customer_orders",
            type: "post",
            dataType: "json",
            data: {
                id_customer : data_id_customer,
                date_liefer: date_liefer,
                date_liefer_end : date_liefer_end
            },
            success: function(data) {
                if(data.status === 1){
                    $('.parent-child-'+data_id_customer).css('display','block');
                    $('.parent-child-'+data_id_customer).html("");
                    $('.parent-child-'+data_id_customer).append(data.record);
                }
            },
            error: function(a, b, c) {
                console.log("KHong get duoc orders tu máy chủ");
                //location.reload();
            },
            complete: function(jqXHR, textStatus) {

            }
        });
    });

    $('.minus').on('click',function () {
        var data_id_customer = $(this).parent().parent().attr('data-id-customer');
        $('.parent-child-'+data_id_customer).css('display','none');
        $(this).css('display','none');
        $(this).prev().css('display','block');
    });
</script>
