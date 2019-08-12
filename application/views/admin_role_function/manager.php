<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo $title; ?></h4>
                    <a href="#" class="minimize"></a>
                </div>

                <!-- div widget manage -->
                <div class="widget-manage">
                    <!-- div form search-->
                    <div class="widget-form-search" style="display: none"></div>

                    <!-- div form ajax-->
                    <div class="clear clear-form-search"></div>

                    <!-- Noi dung se hien thi -->
                    <div class="widget-content data_table">
                    <div id='msg_a'></div>
                    <div class="accordion" id="accordion" style="margin: auto; max-width: 1200px">
                        <?php $stt = 0; foreach ($list_role_function as $controller => $list_action) { $stt++ ?>
                            <?php $_controller = ($controller == '*' ? '00' : $controller); ?>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" href="#<?php echo $_controller; ?>">
                                        <?php echo $stt . '. ' . (($_controller == '00') ? 'Tất cả các quyền' : $_controller); ?>
                                    </a>
                                </div>
                                <div id="<?php echo $_controller; ?>" class="accordion-body collapse" style="height: 0px; ">
                                    <div class="accordion-inner">
                                        <table class="table table-bordered" id="table_<?php echo $_controller; ?>">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Controller</th>
                                                    <th>Action</th>
                                                    <th>Mô tả</th>
                                                    <th>
                                                        <?php if($role_id == 1): ?>
                                                            <label></label>
                                                        <?php else: ?>
                                                            <input type="checkbox" onchange="checkAll('<?php echo $_controller; ?>', this)" name="chk<?php echo $_controller; ?>[]" />
                                                        <?php endif; ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($list_action as $key_action => $actions) {
                                                echo '<tr>';
                                                $checked = $actions['checked'] ? "checked" : "";
                                                echo '<td> '. $actions['id'] .'</td>';
                                                echo '<td> '. $actions['controller'] .'</td>';
                                                echo '<td> '. $actions['action'] .'</td>';
                                                echo '<td> '. $actions['description'] .'</td>';
                                                if($role_id == 1){
                                                    echo '<td class="center"><input class="toggle" '. $checked .' type="checkbox" disabled></td>';
                                                } else {
                                                    echo '<td class="center"><input class="toggle" name="chk'. $controller .'[]" '. $checked .' onchange="Update(' . $actions['id'] . ', this)" type="checkbox" value="'. $actions['id'] .'"></td>';
                                                }
                                                echo '</tr>';
                                            } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <script>
                            function checkAll(controller, element)
                            {
                                var checked = $(element).is(":checked");
                                $.ajax({
                                    method: "POST",
                                    url: '<?php echo $ajax_update_role_controller; ?>',
                                    data: { "controller" : controller, "role_id" : '<?php echo $role_id; ?>', 'checked' : checked },
                                    dataType: "JSON",
                                    success: function (data) {
                                        if(data) {
                                            show_top_error('success', 'Cập nhật thành công !');
                                        } else {
                                            show_top_error('error', 'Cập nhật thất bại !');
                                        }
                                    },
                                    error: function (xhr, desc, err) {
                                        show_top_error('error', err);
                                        console.log(xhr);
                                        console.log(desc);
                                    }
                                });
                                var tableID = "table_" + controller;
                                var table = document.getElementById(tableID);
                                for (var i = 1; i < table.rows.length; i++) {
                                    table.rows[i].cells[4].children[0].checked = checked;
                                }
                            }

                            function Update(function_id, element)
                            {
                                var checked = $(element).is(":checked");
                                $.ajax({
                                    method: "POST",
                                    url: '<?php echo $ajax_update_role_function; ?>',
                                    data: { "function_id" : function_id, "role_id" : '<?php echo $role_id; ?>', 'checked' : checked },
                                    success: function (data) {
                                        console.log(data);
                                        if(data) {
                                            show_top_error('success', 'Cập nhật thành công !');
                                        } else {
                                            show_top_error('error', 'Cập nhật thất bại !');
                                        }
                                    }
                                })
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        // Show Status Ajax: status: error - success
        function show_top_error(status, mgs)
        {
            $.jGrowl("<i class='icon16 i-checkmark-3'></i> " + mgs, {
                group: status,
                position: 'top-right',
                sticky: false,
                closeTemplate: '<i class="icon16 i-close-2"></i>',
                animateOpen: {
                    width: 'show',
                    height: 'show'
                }
            });
        }
    </script>