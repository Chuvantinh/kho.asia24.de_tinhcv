<div class="span6 resizable uniform modal-content" style="width:1000px">
    <script type="text/javascript">
        $(document).ready(function () {
            var dateToday = new Date();
            $('#i_time_start').datetimepicker({
                format: 'Y-m-d H:i:00',
                lang: 'en',
                step: 20,
                changeMonth: true,
                numberOfMonths: 1,
                minDate: dateToday
            });

            var dateToday = new Date();
            $('#i_time_end').datetimepicker({
                format: 'Y-m-d H:i:00',
                lang: 'en',
                step: 20,
                changeMonth: true,
                numberOfMonths: 1,
                minDate: dateToday
            });
        });
    </script>
    <form class="form-horizontal e_ajax_submit" action="<?php echo $save_link; ?>" enctype="multipart/form-data" method="POST">
        <div class="modal-header"> 
            <span type="button" class="close" data-dismiss="modal"><i class="icon16 i-close-2"></i></span>
            <h3><?php echo $title; ?></h3>
        </div>
        <div class="modal-body bgwhite">
            <?php if (isset($list_system)): ?>
                <div class="control-group">
                    <label class="control-label" >Hệ thống</label>
                    <div class="controls controls-row">
                        <select class="select2" multiple="" name="system_id[]">
                            <?php foreach ($list_system as $one_system): ?>
                                <option value="<?php echo $one_system->id ?>"><?php echo $one_system->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
            <?php foreach ($list_input as $input) { ?>
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