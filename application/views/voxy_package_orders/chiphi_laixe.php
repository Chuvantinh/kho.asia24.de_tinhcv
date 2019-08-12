<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget e_widget">
                <!-- div widget title -->
                <div class="widget-title">
                    <div class="icon"><i class="icon20 i-table"></i></div>
                    <h4><?php echo "Chi Phí Lái Xe"; ?></h4>

                    <a href="#" class="minimize"></a>
                </div>

                <!-- div form search-->
                <div class="widget-form-search" style="display: inline-block; width: 100%">
                    <form action="voxy_package_orders/handeln_chiphi_laixe" metho="post" autocomplete="off">
                        <?php if (is_array($shipper_name)) {
                            $this->load->model('m_voxy_chiphi_laixe');

                            foreach ($shipper_name as $item) {
                                if ($item && $date_liefer) {
                                    $id_laixe = $this->m_voxy_shippers->get_id($item);
                                    $infor = $this->m_voxy_chiphi_laixe->get_all_infor($id_laixe, $date_liefer)[0];

                                    if ($infor != false) {
                                        $bienso = $infor['bienso'];
                                        $loaixe = $infor['loaixe'];
                                        $tien_xang = $infor['tienxang'];
                                        $tien_thue_xe = $infor['tienthuexe'];
                                        $khau_hao_xe = $infor['khauhaoxe'];
                                        $chi_phi_khac = $infor['chiphikhac'];

                                        $nopthieu_laixe = $infor['nopthieu_laixe'];
                                        $ly_do_nopthieu = $infor['ly_do_nopthieu'];

                                        $nopthua_laixe = $infor['nopthua_laixe'];
                                        $ly_do_nopthua = $infor['ly_do_nopthua'];

                                        $ly_do = $infor['lydo'];
                                        $ghi_chu = $infor['ghichu'];
                                        $chu_so_huu = $infor['chu_so_huu'];
                                    } else {
                                        $bienso = "";
                                        $loaixe = "";
                                        $tien_xang = "";
                                        $tien_thue_xe = "";
                                        $khau_hao_xe = "";
                                        $chi_phi_khac = "";

                                        $nopthieu_laixe = "";
                                        $ly_do_nopthieu = "";

                                        $nopthua_laixe = "";
                                        $ly_do_nopthua = "";

                                        $ly_do = "";
                                        $ghi_chu = "";
                                        $chu_so_huu = "";
                                    }
                                }
                                ?>

                                <div class="col-33">
                                    <div class="form-group hide">
                                        <label for="ngay_giao_hang">Ngày giao hàng</label>
                                        <input type="text" class="form-control" id="ngay_giao_hang" readonly
                                               name="ngay_giao_hang" value="<?php echo $date_liefer; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="laixe">Lái xe</label>
                                        <input type="text" class="form-control laixe" id="laixe" name="laixe" readonly
                                               style="font-weight: bold;color: red;"
                                               value="<?php echo $item; ?>">
                                    </div>

                                    <div class="form-group hide">
                                        <label for="bienso">Biển số</label>
                                        <input type="text" class="form-control bienso" id="bienso" name="bienso"
                                               value="<?php echo $bienso; ?>">
                                    </div>

                                    <div class="form-group hide">
                                        <label for="loaixe">Loại xe</label>
                                        <input type="text" class="form-control loaixe" id="loaixe" name="loaixe"
                                               value="<?php echo $loaixe; ?>">
                                    </div>

                                    <div class="bordered">
                                        <div class="form-group">
                                            <label for="tien_xang">Tiền xăng</label>
                                            <input type="text" class="form-control tien_xang" id="tien_xang"
                                                   name="tien_xang" value="<?php echo $tien_xang; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="tien_thue_xe">Tiền thuê xe</label>
                                            <input type="text" class="form-control tien_thue_xe" id="tien_thue_xe"
                                                   name="tien_thue_xe" value="<?php echo $tien_thue_xe; ?>">
                                        </div>

                                        <div class="form-group hide">
                                            <label for="khau_hao_xe">Khấu hao xe </label>
                                            <input type="text" class="form-control khau_hao_xe" id="khau_hao_xe"
                                                   name="khau_hao_xe" value="<?php echo $khau_hao_xe; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="chi_phi_khac">Chi phí khác</label>
                                            <input type="text" class="form-control chi_phi_khac" id="chi_phi_khac"
                                                   name="chi_phi_khac" value="<?php echo $chi_phi_khac; ?>">
                                        </div>

                                        <div class="form-group hide">
                                            <label for="ly_do">Lý do cho chi phí khác</label>
                                            <input type="text" class="form-control ly_do" id="ly_do" name="ly_do"
                                                   value="<?php echo $ly_do; ?>">
                                        </div>

                                    </div>

                                    <div class="bordered-laixe-thieu">
                                        <div class="form-group">
                                            <label for="nopthieu_laixe">Lái xe nộp THIẾU</label>
                                            <input type="text" class="form-control nopthieu_laixe" id="nopthieu_laixe"
                                                   name="nopthieu_laixe" value="<?php echo $nopthieu_laixe; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="ly_do_nopthieu">Lý do nộp THIẾU</label>
                                            <input type="text" class="form-control ly_do_nopthieu" id="ly_do_nopthieu"
                                                   name="ly_do_nopthieu" value="<?php echo $ly_do_nopthieu; ?>">
                                        </div>
                                    </div>

                                    <div class="bordered-laixe-thua">
                                        <div class="form-group">
                                            <label for="nopthua_laixe">Lái xe nộp THỪA</label>
                                            <input type="text" class="form-control nopthua_laixe" id="nopthua_laixe"
                                                   name="nopthua_laixe" value="<?php echo $nopthua_laixe; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="ly_do_nopthua">Lý do nộp THỪA</label>
                                            <input type="text" class="form-control ly_do_nopthua" id="ly_do_nopthua"
                                                   name="ly_do_nopthua" value="<?php echo $ly_do_nopthua; ?>">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="ghi_chu">Ghi Chú</label>
                                        <input type="text" class="form-control ghi_chu" id="ghi_chu" name="ghi_chu"
                                               value="<?php echo $ghi_chu; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="chu_so_huu">Chủ Sở hữu</label>
                                        <select name="chu_so_huu" id="chu_so_huu" class="form-control select2">
                                            <option value="xecongty" <?php echo ($chu_so_huu == "xecongty") ? "selected" : ""; ?>>
                                                Xe Công Ty
                                            </option>
                                            <option value="xerieng" <?php echo ($chu_so_huu == "xerieng") ? "selected" : ""; ?>>
                                                Xe Riêng
                                            </option>
                                            <option value="xethue"<?php echo ($chu_so_huu == "xethue") ? "selected" : ""; ?>>
                                                Xe Thuê
                                            </option>
                                        </select>
                                    </div>
                                </div>

                            <?php }
                        } ?>

                        <div class="span12" style="display: block; text-align: center; margin-top: 15px">
                            <button type="submit" id="xacnhan" name="xacnhan"
                                    class="xacnhan_chiphi_laixe btn btn-success btn-large">
                                Xác Nhận
                            </button>
                        </div>

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

    .col-33 {
        width: 20%;
        float: left;
        display: table;
        padding: 5px;
    }

    .selector {
        display: block;
    !important;
    }

    .bordered {
        color: #0a7fcc;
    }

    .bordered-laixe-thieu {
        color: #8ec835;
    }

    .bordered-laixe-thua{
        color: #c87070;
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


    $(document).on("click", ".xacnhan_chiphi_laixe", function (e) {

        var list_check_end = [];
        $.each($(".col-33"), function (index) {
            list_check_end.push
            ({
                ngay_giao_hang: $(this).find('#ngay_giao_hang').val(),
                laixe: $(this).find('#laixe').val(),
                bienso: $(this).find('#bienso').val(),
                loaixe: $(this).find('#loaixe').val(),
                tien_xang: $(this).find('#tien_xang').val(),
                tien_thue_xe: $(this).find('#tien_thue_xe').val(),
                khau_hao_xe: $(this).find('#khau_hao_xe').val(),
                chi_phi_khac: $(this).find('#chi_phi_khac').val(),

                nopthieu_laixe: $(this).find('#nopthieu_laixe').val(),
                ly_do_nopthieu: $(this).find('#ly_do_nopthieu').val(),

                nopthua_laixe: $(this).find('#nopthua_laixe').val(),
                ly_do_nopthua: $(this).find('#ly_do_nopthua').val(),

                ly_do: $(this).find('#ly_do').val(),
                ghi_chu: $(this).find('#ghi_chu').val(),
                chu_so_huu: $(this).find('#chu_so_huu option:selected').val(),
            });
        });

        e.preventDefault();
        var obj = $(".table");
        show_ajax_loading(obj);
        var $ulr = "<?php echo base_url('voxy_package_orders/handeln_chiphi_laixe')?>";

        $.ajax({
            url: $ulr,
            type: 'POST',
            data: {
                list_check_end: list_check_end
            },
            dataType: 'json',
            success: function (data) {

                if (data.status === 1) {
                    $('.e_loading').css('display', 'none');
                }

                setInterval(function () {
                    window.close();
                }, 1000);
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