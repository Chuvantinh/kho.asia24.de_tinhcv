<?php $data_pro = $list_input['data_pro'];

?>
<div class="span6 resizable uniform modal-content" style="width:1000px">
    <form class="form-horizontal e_ajax_submit" action="<?php echo $save_link; ?>" enctype="multipart/form-data" autocomplete="off"
          method="POST">
        <div class="modal-header">
            <span type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></span>
            <h3><?php echo $title; ?></h3>
        </div>
        <div class="modal-body bgwhite">
            <?php $_str_readonly = ' readonly=readonly style="background: #d9d9d9"'; ?>
            <?php foreach ($list_input as $key => $input) {
                if ($key != "data_pro") {
                    ?>
                    <?php
                    if (isset(${'readonly_' . $input->name}) && ${'readonly_' . $input->name} == true) {
                        $input->string_rule .= $_str_readonly;
                    }
                    ?>
                    <div class="control-group <?php echo($input->rule['type'] == 'hidden' ? $input->rule['type'] : ''); ?>">
                        <label class="control-label"
                               for="<?php echo $input->name; ?>"><?php echo $input->label; ?></label>
                        <div class="controls controls-row">
                            <?php if ($input->rule['type'] == 'textarea') { ?>
                                <textarea name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>
                                          id="i_<?php echo $input->name; ?>"></textarea>
                            <?php } elseif ($input->rule['type'] == 'select') { ?>
                                <select class="select2" name="<?php echo $input->name; ?>"
                                        id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>>
                                    <?php foreach ($input->option as $option) { ?>
                                        <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } elseif ($input->rule['type'] == 'file') { ?>
                                <?php if ($input->rule['crop'] == true) { ?>
                                    <input name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>"
                                           type="hidden"/>
                                    <div class="file_input_content e_file_input_content">
                                        <p class="image_preview"><img class="main_img" src="#"/></p>
                                        <input data-name="image_uploader[]" <?php echo $input->string_rule; ?> />
                                    </div>
                                    <div class="image_crop_content">
                                        <div class="trigger_crop" title="Ấn để crop">
                                            <span class="i-arrow-right-18 c_icon arr_icon"></span><span
                                                    class="i-crop c_icon crop_icon"></span>
                                        </div>
                                        <div class="crop_value" title="Ấn để crop">
                                            <img class="croped_img" src="#"/>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="file_input_content e_file_input_content">
                                        <p class="image_preview"><img class="main_img" src="#"/></p>
                                        <input <?php echo $input->string_rule; ?> />
                                    </div>
                                <?php } ?>
                            <?php } elseif ($input->rule['type'] == 'rich_editor') { ?>
                                <textarea class="ckeditor"
                                          data-ckfinder="<?php echo $this->path_static_file . 'plugins/ckfinder'; ?>"
                                          name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> ></textarea>
                            <?php } elseif ($input->rule['type'] == 'shipping_address') {
                                ?>
                                <div class="control_group">
                                    <input type="text" name="s_first_name" id="s_first_name" placeholder="first_name" readonly>
                                    <input type="text" name="s_last_name" id="s_last_name" placeholder="giá 10€" readonly>
                                    <input type="text" name="s_address1" id="s_address1"
                                           placeholder="pichelsdorfer str. 131" readonly>
                                    <input type="text" name="s_zip" id="s_zip" placeholder="15595 postlei zahn" readonly>
                                    <input type="text" name="s_city" id="s_city" placeholder="Berlin oder..." readonly>
                                    <input type="text" name="s_phone" id="s_phone" placeholder="0176301445..." readonly>
                                </div>
                            <?php } elseif ($input->rule['type'] == 'billing_address') { ?>
                                <div class="control_group">
                                    <input type="text" name="b_first_name" id="b_first_name" placeholder="first_name" readonly>
                                    <input type="text" name="b_last_name" id="b_last_name" placeholder="giá 10€" readonly>
                                    <input type="text" name="b_address1" id="b_address1"
                                           placeholder="pichelsdorfer str. 131" readonly>
                                    <input type="text" name="b_zip" id="b_zip" placeholder="15595 postlei zahn" readonly>
                                    <input type="text" name="b_city" id="b_city" placeholder="Berlin oder..." readonly>
                                    <input type="text" name="b_phone" id="b_phone" placeholder="0176987.." readonly>
                                </div>
                            <?php } elseif ($input->rule['type'] == 'line_items') { ?>
                                <table style="width: 100% ">

                                    <tr style="font-weight: bold">
                                        <td style="width: 50%" id="c_title">sản phẩm</td>
                                        <td style="width: 5%" id="c_price">giá</td>
                                        <td style="width: 5%">x</td>
                                        <td style="width: 5%" id="c_quantity">số lượng</td>
                                        <td style="width: 15%" id="c_total_price">tổng tiền</td>
                                        <td style="width: 10%" id="c_expri_day">ngày hết hạn</td>
                                        <td style="width: 10%" id="c_location">vị trí</td>
                                    </tr>
                                    <?php
                                    $count = 0;
                                    $total_price = 0;
                                    $data_excel = array();
                                    $oder_number = 0;
                                    foreach ($data_pro as $key => $item) {
                                        $count += $item['quantity'];
                                        $total_price += $item['price'] * $item['quantity'];
                                        ?>
                                        <tr style="border-bottom: 1px solid black ">
                                            <td style="width: 50%" id="title">
                                                <?php echo $item['title'];
                                                echo "<br/>"; ?>
                                                <?php echo "SKU: " . $item['sku'];
                                                echo "<br/>"; ?>
                                                <?php echo "Loại sp: " . $item['variant_title'];
                                                echo "<br/>"; ?>
                                            </td>
                                            <td style="width: 5%" id="price"><?php echo "€" . $item['price']; ?></td>
                                            <td style="width: 5%">X</td>
                                            <td style="width: 5%" id="quantity"><?php echo $item['quantity']; ?></td>
                                            <td style="width: 15%" id="total_price">
                                                <?php
                                                echo "€" . number_format($item['price'] * $item['quantity'], 2);
                                                ?>
                                            </td>
                                            <td style="width: 10%" id="expri_day">
                                                <?php
                                                    echo $item['expri_day'];
                                                ?>
                                            </td>
                                            <td style="width: 10%" id="location">
                                                <?php
                                                    echo $item['location'];
                                                
                                                ?>
                                        </tr>
                                        <?php
                                        $oder_number = $item['oder_number'];
                                        $data_excel[$key]['title'] = $item['title'];
                                        $data_excel[$key]['price'] = $item['price'];
                                        $data_excel[$key]['quantity'] = $item['quantity'];
                                        $data_excel[$key]['total'] = number_format($item['price'] * $item['quantity'], 2);
                                        $data_excel[$key]['location'] = $item['location'];
                                        $data_excel[$key]['expri_day'] = $item['expri_day'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo "Tổng số sản phẩm" ?></td>
                                        <td><?php echo $count . " items"; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td> <?php echo "total: €" . number_format($total_price, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><?php echo "Shipping: " ?></td>
                                        <td><?php echo "5 €: " ?></td>
                                        <td><?php echo "<b>Total: € </b>" . number_format($total_price + 5, 2); ?></td>
                                    </tr>
                                </table>
                            <?php } else { ?>
                                <input name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>
                                       id="i_<?php echo $input->name; ?>"/>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>


        <div class="modal-footer">
            <a href="<?php echo base_url('voxy_package_orders/excel')."?order_number=".$oder_number."&"."total_price=".$total_price; ?>"
               class="excel btn btn-success btn-lg" > Xuất đơn hàng với Excel</a>

            <a href="<?php echo base_url('htmltopdf/pdf_order')."?order_number=".$oder_number ?>" target="_blank"
               class="btn-danger" > Xuất đơn hàng với PDF</a>

            <button type="submit" class="b_add b_edit btn btn-primary">Lưu</button>
            <button type="reset" class="b_add btn">Nhập lại</button>
            <button type="button" class="b_view b_add b_edit btn" data-dismiss="modal">Hủy</button>
        </div>
    </form>

</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.controls-row input[type="datetime"]').datepicker({
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear : true,
            numberOfMonths: 1,
            minDate: '01-01-2016'
        });
        // $('.excel').on('click', function () {
        //     var $url = $('.excel').attr('data-href');
        //     var data = $('.excel').attr('data-order-number');
        //     var total_price = $('.excel').attr('data-total-price');
        //     $.ajax({
        //         url: $url,
        //         type: "POST",
        //         data: {
        //             order_number : data
        //         },
        //         //dataType: "json",
        //         dataType: "text",
        //         success: function(data) {
        //             console.log("xuat file thanh cong");
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             console.log(textStatus);
        //         }
        //     });
        // });
    });
</script>
