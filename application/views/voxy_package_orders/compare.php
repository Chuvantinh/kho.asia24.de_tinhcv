<?php


?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo "Đối chiếu nợ"; ?></h4>

                    <a href="#" class="minimize"></a>
                </div>

                <!-- div form search-->
                <div class="widget-form-search" style="display: block">
                    <?php $custom_where = (isset($form_conds['custom_where']) ? $form_conds['custom_where'] : array()); ?>
                    <?php $custom_like = (isset($form_conds['custom_like']) ? $form_conds['custom_like'] : array()); ?>

                    <div class="widget-manage">
                        <div class="widget-content">
                            <form action="voxy_package_orders/handeln_compare" type="post">
                                <table class="table table-hover table-bordered cke_light_background">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Ngày giao</th>
                                        <th scope="col">Đơn hàng</th>
                                        <th scope="col">Tài xế</th>
                                        <th scope="col">Khách hàng</th>
                                        <th scope="col">Còn nợ</th>
                                        <th scope="col">Đối chiếu nợ</th>
                                        <th scope="col">Trạng thái</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($list_orders) {
                                        $id = 0;
                                        foreach ($list_orders as $key => $item) {
                                            $id++;
                                            if ($item['key_word_customer'] != "") {
                                                $customer = $item['key_word_customer'];
                                            } else {
                                                $json_customer = get_object_vars(json_decode($item['customer']));
                                                if (isset($json_customer['d_first_name'])) {
                                                    $frist_name = $json_customer['d_first_name'];
                                                } elseif (isset($json_customer['first_name'])) {
                                                    $frist_name = $json_customer['first_name'];
                                                } else {
                                                    $frist_name = "";
                                                }

                                                if (isset($json_customer['d_last_name'])) {
                                                    $last_name = $json_customer['d_last_name'];
                                                } elseif (isset($json_customer['last_name'])) {
                                                    $last_name = $json_customer['last_name'];
                                                } else {
                                                    $last_name = "";
                                                }
                                                $customer = $frist_name . "&nbsp" . $last_name;
                                            }
                                            ?>
                                            <tr class="information">
                                                <td><?php echo $id; ?></td>
                                                <td><?php echo $item['shipped_at']; ?></td>
                                                <td><?php echo $item['order_number']; ?></td>
                                                <td class="order_id hide"><?php echo $item['id']; ?></td>
                                                <td><?php echo $item['shipper_name']; ?></td>
                                                <td><?php echo $customer; ?></td>
                                                <td><?php echo $item['tongtien_no']; ?></td>
                                                <td>
                                                    <input type="text" class="input_compare" name="input_compare"
                                                           value="<?php echo $item['data_compare']; ?>"
                                                           data-tongtien-no="<?php echo $item['tongtien_no']; ?>">
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-info btn-lg validate deactive">
                                                        <span class="glyphicon glyphicon-ok"></span> Khớp
                                                    </a>

                                                    <a href="#" class="btn btn-danger btn-lg not-validate deactive">
                                                        <span class="glyphicon glyphicon-remove"></span> Lệch
                                                    </a>

                                                </td>
                                                <td class="check-end hide">
                                                    <input type="text" class="input-check-end"
                                                           value="<?php echo $item['status_compare']; ?>">
                                                </td>
                                            </tr>
                                        <?php }
                                    } ?>
                                    </tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <button type="submit"
                                                    class="btn-large submit-compare btn btn-full btn-success">
                                                Xác nhận
                                            </button>
                                        </td>
                                        <td></td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>

                    <div id="dialog" title="Thông báo" style="display: none">
                        <p>Số tiền nợ chưa khớp, xin hãy kiểm tra lại </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        @media only screen and (max-width: 600px) {
            .t-nhathang {
                width: 100% !important;
            }
        }

        .deactive {
            display: none;
        }

    </style>

    <script type="text/javascript">

        $('input[name="input_compare"').on('change', function () {
            var tong_tien_no = $(this).attr('data-tongtien-no');
            var value = $(this).val();
            if (value === tong_tien_no) {
                $(this).parent().next().find('.validate').css('display', 'block');
                $(this).parent().next().find('.not-validate').css('display', 'none');
                $(this).parent().next().next().find('.input-check-end').attr('value', 1);
            } else {
                $(this).parent().next().find('.validate').css('display', 'none');
                $(this).parent().next().find('.not-validate').css('display', 'block');
                $(this).parent().next().next().find('.input-check-end').attr('value', 0);
            }
        });


        $(document).on("click", ".submit-compare", function (e) {

            var list_check_end = [];
            $.each($(".input-check-end"), function (index) {
                list_check_end.push
                ({
                    value: $(this).attr('value')
                });
            });

            //check cac don hang khop het chua thi moi cho hien chu xac nhan
            for (var i = 0; i < list_check_end.length; i++) {
                if (parseFloat(list_check_end[i].value) === 0) {
                    //$('.submit-compare').css('display','none');
                    $("#dialog").css('display', 'block');
                    $("#dialog").dialog();
                }
            }

            var list_orders = [];
            $.each($(".information"), function (index) {
                list_orders.push
                ({
                    order_id: $(this).find('.order_id').text(),
                    data_compare: $(this).find('.input_compare').val(),
                    status_compare: $(this).find('.input-check-end').attr('value')
                });
            });

            e.preventDefault();
            var obj = $(".table");
            show_ajax_loading(obj);
            var $ulr = "<?php echo base_url('voxy_package_orders/handeln_compare')?>";

            $.ajax({
                url: $ulr,
                type: 'POST',
                data: {
                    list_orders: list_orders
                },
                dataType: 'json',
                success: function (data) {

                    if (data.status === 1) {
                        $('.e_loading').css('display', 'none');
                    }

                    setInterval(function () {
                        window.close();
                    }, 2000);
                },
                error: function (a, b, c) {
                    console.log("Doi chieu orders co loi");
                    //location.reload();
                },
                complete: function (jqXHR, textStatus) {

                }
            });
        });
    </script>