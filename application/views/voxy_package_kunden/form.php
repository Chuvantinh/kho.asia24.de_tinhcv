<div class="span6 resizable uniform modal-content" style="width:1000px">
    <form class="form-horizontal e_ajax_submit" action="<?php echo $save_link; ?>" enctype="multipart/form-data" method="POST">
        <div class="modal-header"> 
            <span type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></span>
            <h3><?php echo $title; ?></h3>
        </div>
        <div class="modal-body bgwhite">
            <?php $_str_readonly = ' readonly=readonly style="background: #d9d9d9"'; ?>
            <?php foreach ($list_input as $input) { ?>
                <?php
                    if(isset(${'readonly_' . $input->name}) && ${'readonly_' . $input->name} == true){
                        $input->string_rule .= $_str_readonly;
                    }
                ?>
                <div class="control-group <?php echo ($input->rule['type'] == 'hidden' ? $input->rule['type'] : ''); ?>">
                    <label class="control-label" for="<?php echo $input->name; ?>"><?php echo $input->label; ?></label>
                    <div class="controls controls-row">
                        <?php if ($input->rule['type'] == 'textarea') { ?>
                            <textarea name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" ></textarea>
                        <?php } elseif ($input->rule['type'] == 'select') { ?>
                            <select class="select2" name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>" <?php echo $input->string_rule; ?>>
                                <?php foreach ($input->option as $option) { ?>
                                    <option value="<?php echo $option->value; ?>"><?php echo $option->display; ?></option>
                                <?php } ?>
                            </select>
                        <?php } elseif ($input->rule['type'] == 'file'){ ?>
                            <?php if ($input->rule['crop'] == true){ ?>
                                <input name="<?php echo $input->name; ?>" id="i_<?php echo $input->name; ?>" type="hidden" />
                                <div class="file_input_content e_file_input_content">
                                    <p class="image_preview"><img class="main_img" src="#" /></p>
                                    <input data-name="image_uploader[]" <?php echo $input->string_rule; ?> />
                                </div>
                                <div class="image_crop_content">
                                    <div class="trigger_crop" title="Ấn để crop">
                                        <span class="i-arrow-right-18 c_icon arr_icon"></span><span class="i-crop c_icon crop_icon"></span>
                                    </div><div class="crop_value" title="Ấn để crop">
                                        <img class="croped_img" src="#" />
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="file_input_content e_file_input_content">
                                    <p class="image_preview"><img class="main_img" src="#" /></p>
                                    <input <?php echo $input->string_rule; ?> />
                                </div>
                            <?php } ?>
                        <?php } elseif ($input->rule['type'] == 'rich_editor') { ?>
                            <textarea class="ckeditor" data-ckfinder="<?php echo $this->path_static_file.'plugins/ckfinder'; ?>" name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> ></textarea>
                        <?php } elseif ($input->rule['type'] == 'packung_karton') {
                            ?>
                                <div class="control_group">
                                    <label class="control-label" for="Packung">Packung</label>
                                    <input type="text" name="option1" id="id_option1" placeholder="Packung" >
                                    <input type="text" name="price1" id="id_price1" placeholder="giá 10€">
                                    <input type="text" name="barcode1" id="id_barcode1" placeholder="mã barcode 9856">
                                    <input type="text" name="sku1" id="id_sku1" placeholder="mã sp A1234">
                                    <input type="text" name="inventory_quantity1" id="id_inventory_quantity1" placeholder="số lượng 100">

                                    <label class="control-label" for="Karton">Karton</label>
                                    <input type="text" name="option2" id="id_option2" placeholder="Karton" >
                                    <input type="text" name="price2" id="id_price2" placeholder="Giá 10€">
                                    <input type="text" name="barcode2" id="id_barcode2" placeholder="mã barcode 9856">
                                    <input type="text" name="sku2" id="id_sku2" placeholder="mã sp A1234">
                                    <input type="text" name="inventory_quantity2" id="id_inventory_quantity2" placeholder="số lượng 100">
                                </div>
                        <?php } elseif ($input->rule['type'] == 'default_address') { ?>
                            <div class="control_group">
                                <input type="text" name="d_first_name" id="id_first_name" placeholder="Vorname">
                                <input type="text" name="d_last_name" id="id_first_name" placeholder="Nachname">
                                <input type="text" name="address1" id="id_address1" placeholder="Roeder Platz" >
                                <input type="text" name="zip" id="id_zip" placeholder="13595">
                                <input type="text" name="city" id="id_city" placeholder="Berlin">
                                <input type="text" name="d_phone" id="id_phone" placeholder="0987 654 1234">
                                <input type="hidden" name="default" id="id_default" placeholder="true" value="true">
                            </div>
                        <?php } else { ?>
                            <input name="<?php echo $input->name; ?>" <?php echo $input->string_rule; ?> id="i_<?php echo $input->name; ?>" />
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="modal-footer"> 
            <button type="submit" class="b_add b_edit btn btn-primary">Lưu</button>
            <button type="reset" class="b_add btn">Nhập lại</button>
            <button type="button" class="b_view b_add b_edit btn" data-dismiss="modal">Hủy</button>
        </div>
    </form>
</div>

<script type="text/javascript" charset="utf-8">
    $( document ).ready(function() {
        $('.controls-row input[type="datetime"]').datepicker({
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            numberOfMonths: 1
        });
    });
</script>