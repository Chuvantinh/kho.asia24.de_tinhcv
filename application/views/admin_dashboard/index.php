<!--<div class="widget-manage">-->
<!--    <div class="widget-content data_table e_data_table">-->
<!--        <div class="span8"> -->
<!--            --><?php
//            echo "Xin chào <b>" . $user_name . "</b>. Hân hạnh được phục vụ !";
//            ?>
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<?php
//{ label: "Israel", y: 178, gdp: 100, url: "israel.png" },  //top 10 ban chay nhat
if ($data_import) {
    $data_top10_import = array();
    $arr_top10 = array();
    $all_product = 0;
    $gdp = 0;
    foreach ($data_import as $item) {
        $all_product += $item['sl_nhap'];
        $gdp = round(($item['sl_nhap'] / $all_product) * 100, 2);
        $arr_top10['label'] = $item['title'] . "(" . $item['variant_title'] . ")";

        if( $this->session->userdata('selected_nhaphang_sorting') != false ){
            $sort = $this->session->userdata('selected_nhaphang_sorting');
        }else{
            $sort = $sort = "quantity";
        }

        if($sort !== "quantity"){
            $arr_top10['y'] = $item['thanhtien'];//thanhtien
        }else{
            $arr_top10['y'] = $item['sl_nhap'];//quantity
        }

        $arr_top10['sl_nhap'] = $item['sl_nhap'];
        $arr_top10['total_price'] = number_format($item['thanhtien'],2) . " €";
        $arr_top10['gdp'] = $gdp;
        $arr_top10['url'] = "germany.png";
        $arr_top10['dv'] = $item['variant_title'];
        $data_top10_import[] = $arr_top10;
    }
//    echo "<pre>";
//    var_dump($data_top10_import);
//    echo "</pre>";die;

} else {
    $data_top10_import = array();
}

if ($data_product_laixe) {
    $data_laixe = array();
    $arr_colum = array();
    foreach ($data_product_laixe as $item) {
        $arr_colum['label'] = $item['shipper_name'];
        $arr_colum['y'] = $item['total_price'];
        $data_laixe[] = $arr_colum;
    }
} else {
    $data_laixe = array();
}

//{ label: "Israel", y: 178, gdp: 100, url: "israel.png" },  //top 10 ban chay nhat
if ($data_top10_banchay) {
    $data_top10 = array();
    $arr_top10 = array();
    $all_product = 0;
    $gdp = 0;
    foreach ($data_top10_banchay as $item) {
        $all_product += $item['quantity'];
        $gdp = round(($item['quantity'] / $all_product) * 100, 2);
        $arr_top10['label'] = $item['title'] . "(" . $item['variant_title'] . ")";
        $arr_top10['y'] = $item['quantity'];
        $arr_top10['gdp'] = $gdp;
        $arr_top10['url'] = "germany.png";
        $arr_top10['dv'] = $item['variant_title'];
        $data_top10[] = $arr_top10;
    }
} else {
    $data_top10 = array();
}
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="widget e_widget">
            <div class="widget-title">
                <div class="icon"><i class="icon20 i-table"></i></div>
                <h4>Home Page</h4>
                <a href="#" class="minimize"></a>
            </div>
            <div class="widget-manage">
                <div class="widget-content">
                    <div class="general-information">
                        <div class="title">
                            <h3>Sales results today</h3>
                        </div>
                        <div class="orders chia3">
                            <div class="sub-icon">
                                <img src="<?php echo base_url()."/themes/images/currency_blue_euro.png"?>" alt="Euro" style="width: 100px; height: 100px">
                            </div>
                            <div class="infor-orders">
                                <p style="font-size: 20px; font-weight: bold; color: #0d7fb8"><?php echo $number_of_orders?$number_of_orders:0; ?>-Orders</p>
                                <p style="font-size: 20px; font-weight: bold; color: #0d7fb8"><?php echo round($sum_doanhthu,2)?number_format(round($sum_doanhthu,2)):0; ?> €</p>
                                <p style="font-size: 20px;">Revenue</p>
                            </div>
                        </div>
                        <div class="refund chia3">
                            <div class="sub-icon">
                                <img src="<?php echo base_url()."/themes/images/go-back-icon.png"?>" alt="Go back" style="width: 100px; height: 100px">
                            </div>
                            <div class="infor-orders">
                                <p style="font-size: 20px; font-weight: bold; color: #0d7fb8"><?php echo $cout_hangve?$cout_hangve:0; ?>-Orders</p>
                                <p style="font-size: 20px; font-weight: bold; color: #0d7fb8"><?php echo round($sum_doanhthu_trave,2)?number_format(round($sum_doanhthu_trave,2)):0; ?> €</p>
                                <p style="font-size: 20px;">Return</p>
                            </div>
                        </div>

                        <div class="compare chia3">
                            <div>
                                <div class="sub-icon">
                                    <?php if ($procent > 0) { ?>
                                    <img src="<?php echo base_url()."/themes/images/go_up.png"?>" alt="Go back" style="width: 100px; height: 100px">
                                    <?php } else { ?>
                                    <img src="<?php echo base_url()."/themes/images/go_down.png"?>" alt="Go back" style="width: 100px; height: 100px">
                                    <?php } ?>
                                </div>
                                <div class="infor-orders">
                                    <p style="font-size: 20px; font-weight: bold; color: #0d7fb8"><?php echo $procent?$procent:0; ?> %</p>

                                    <p style="font-size: 20px;">Compared to the same period last month</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chartcontainer-wrapper">
                        <div id="chartContainer" style="height: 370px; width: 100%;"></div>

                        <div class="select main-select1">
                            <?php
                            if($this->session->userdata('selected_nhaphang_sorting') !== false){
                                $selected_nhaphang_sorting = $this->session->userdata('selected_nhaphang_sorting');
                            }else{
                                $selected_nhaphang_sorting = "quantity";
                            }
                            ?>
                            <select name="top10-import-sorting" class="top10-import-sorting" >
                                <option value="quantity" <?php echo ($selected_nhaphang_sorting == "quantity")?"selected":""; ?>>Quantity</option>
                                <option value="money" <?php echo ($selected_nhaphang_sorting == "money")?"selected":""; ?>>Money</option>
                            </select>
                        </div>

                        <div class="select main-select">
                            <?php
                            if($this->session->userdata('selected_nhaphang') !== false){
                                $selected = $this->session->userdata('selected_nhaphang');
                            }else{
                                $selected = 1;
                            }

                            ?>
                            <select class="top10-import">
                                <option value="1" <?php echo ($selected == 1)?"selected":""; ?> >Yesterday</option>
                                <option value="0" <?php echo ($selected == 0)?"selected":""; ?> >Today</option>
                                <option value="2" <?php echo ($selected == 2)?"selected":""; ?> >Last 7 days</option>
                                <option value="3" <?php echo ($selected == 3)?"selected":""; ?> >This month</option>
                                <option value="4" <?php echo ($selected == 4)?"selected":""; ?> >Last month</option>
                            </select>
                        </div>
                    </div>
                    <hr style="color: black;    border-top: 5px solid #c9c9c9;">

                    <div class="chartcontainer-wrapper">
                        <div id="chartContainer2" style="height: 370px; width: 100%;"></div>

                        <div class="select main-select1" style="display: none;">
                            <?php
                            if($this->session->userdata('selected_banchay_sorting') !== false){
                                $selected_banchay_sorting = $this->session->userdata('selected_banchay_sorting');
                            }else{
                                $selected_banchay_sorting = "quantity";
                            }
                            ?>
                            <select name="top10-export-sorting" class="top10-export-sorting" >
                                <option value="quantity" <?php echo ($selected_banchay_sorting == "quantity")?"selected":""; ?>>Quantity</option>
                                <option value="money" <?php echo ($selected_banchay_sorting == "money")?"selected":""; ?>>Money</option>
                            </select>
                        </div>

                        <div class="select main-select">
                            <?php
                            if ($this->session->userdata('selected_banchay') !== false){
                                $selected = $this->session->userdata('selected_banchay');
                            }else{
                                $selected = 1;
                            }
                            ?>
                            <select class="top10-export">
                                <option value="1" <?php echo ($selected == 1)?"selected":""; ?> >Yesterday</option>
                                <option value="0" <?php echo ($selected == 0)?"selected":""; ?> >Today</option>
                                <option value="2" <?php echo ($selected == 2)?"selected":""; ?> >Last 7 days</option>
                                <option value="3" <?php echo ($selected == 3)?"selected":""; ?> >This month</option>
                                <option value="4" <?php echo ($selected == 4)?"selected":""; ?> >Last month</option>
                            </select>
                        </div>
                    </div>
                    <hr style="color: black;    border-top: 5px solid #c9c9c9;">

                    <div class="chartcontainer-wrapper">
                        <div id="chartContainer3" style="height: 370px; width: 100%;"></div>
                        <div class="select main-select">
                            <?php


                            if($this->session->userdata('selected_laixe') !== false){
                                $selected = $this->session->userdata('selected_laixe');
                            }else{
                                $selected = 1;
                            }

                            ?>
                            <select class="top-laixe">
                                <option value="1" <?php echo ($selected == 1)?"selected":""; ?> >Yesterday</option>
                                <option value="0" <?php echo ($selected == 0)?"selected":""; ?> >Today</option>
                                <option value="2" <?php echo ($selected == 2)?"selected":""; ?> >Last 7 days</option>
                                <option value="3" <?php echo ($selected == 3)?"selected":""; ?> >This month</option>
                                <option value="4" <?php echo ($selected == 4)?"selected":""; ?> >Last month</option>
                            </select>
                        </div>
                    </div>
                    <hr style="color: black;    border-top: 5px solid #c9c9c9;">


                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .main-select {
        position: absolute;
        left: 20px;
        top: 0px;
        width: 15%;
    }

    .main-select1{
        position: absolute;
        right: 0px;
        top: 0px;
        width: 15%;
    }
    .selector {
        width: 100% !important;
    }
    .selector span{
        width: 100% !important;
    }

    .chartcontainer-wrapper{
        position: relative;
    }

    .chia3 {
        width: 30%;
        float: left;
        margin-bottom: 50px;
    }

    .chartcontainer-wrapper{
        clear: left;
    }
    
    .sub-icon{
        width: 50%;
        float: left;
    }
    
    .infor-orders{
        width: 50%;
        float: left;
    }

</style>

<script type="text/javascript" charset="utf-8">
    $(window).load(function () {
        //so luong hang xuat nhieu nhat
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title: {
                text: "Top 10 Buying Products "
            },
            axisX: {
                interval: 1
            },
            axisY: {
                title: "<?php echo $selected_nhaphang_sorting; ?>"
            },
            data: [{
                type: "bar",
                toolTipContent: "<img src=\"https://canvasjs.com/wp-content/uploads/images/gallery/javascript-column-bar-charts/\"{url}\"\" style=\"width:40px; height:20px;\"> <b>{label}</b><br>Số lượng: {sl_nhap}-{dv}<br>Số tiền: {total_price}",
                dataPoints:<?php echo json_encode($data_top10_import, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();


        //so luong hang xuat nhieu nhat
        var chart2 = new CanvasJS.Chart("chartContainer2", {
            animationEnabled: true,
            title: {
                text: "Top 10 Selling Products"
            },
            axisX: {
                interval: 1
            },
            axisY: {
                title: "Quantity"
            },
            data: [{
                type: "bar",
                toolTipContent: "<img src=\"https://canvasjs.com/wp-content/uploads/images/gallery/javascript-column-bar-charts/\"{url}\"\" style=\"width:40px; height:20px;\"> <b>{label}</b><br>Số lượng: {y}-{dv}<br>",
                dataPoints:<?php echo json_encode($data_top10, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart2.render();

        //thong ke theo lai xe
        var chart3 = new CanvasJS.Chart("chartContainer3", {
            animationEnabled: true,
            theme: "light2", // "light1", "light2", "dark1", "dark2"
            title: {
                text: "Revenue of Drivers"
            },
            subtitles: [{
                text: "In Euro",
                fontSize: 16
            }],
            axisY: {
                prefix: "€ ",
            },
            data: [{
                type: "column",
                yValueFormatString: "€ #,##0.00",
                dataPoints: <?php echo json_encode($data_laixe, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart3.render();

        $('.canvasjs-chart-credit').css('display', 'none');

    });

    $('.top10-import').on('change',function () {
        var import_select = $('.top10-import').val();
        var import_sorting = $('.top10-import-sorting').val();
       // show_ajax_loading($('.e_data_table'));
        var url = "<?php echo base_url(); ?>admin_dashboard/ajax_baocao_top10_nhaphang.html";
        $.ajax({
            url: url,
            type: "post",
            data: {
                import_select: import_select,
                import_sorting: import_sorting
            },
            dataType: "json",
            success: function(data) {
                location.reload();
            },
            error: function(a, b, c) {
                alert(a + b + c);
                //window.location = url;
            }
        });
    });

    $('.top10-import-sorting').on('change',function () {
        var import_select = $('.top10-import').val();
        var import_sorting = $('.top10-import-sorting').val();
        // show_ajax_loading($('.e_data_table'));
        var url = "<?php echo base_url(); ?>admin_dashboard/ajax_baocao_top10_nhaphang.html";
        $.ajax({
            url: url,
            type: "post",
            data: {
                import_select: import_select,
                import_sorting: import_sorting
            },
            dataType: "json",
            success: function(data) {
                location.reload();
            },
            error: function(a, b, c) {
                alert(a + b + c);
                //window.location = url;
            }
        });
    });

    $('.top10-export').on('change',function () {
        var export_select = $('.top10-export').val();
        var export_sorting = $('.top10-export-sorting').val();
        //show_ajax_loading($('.e_data_table'));
        var url = "<?php echo base_url(); ?>admin_dashboard/ajax_baocao_top10_banchay.html";
        $.ajax({
            url: url,
            type: "post",
            data: {
                export_select: export_select,
                export_sorting: export_sorting
            },
            dataType: "json",
            success: function(data) {
                //remove_ajax_loading();
                location.reload();
                // if(data.state === 1){
                //     $("#chartContainer2").html("");
                //     var chart_new_2 = new CanvasJS.Chart("chartContainer2",{
                //         animationEnabled: true,
                //         title: {
                //             text: "Top 10 Selling Products"
                //         },
                //         axisX: {
                //             interval: 1
                //         },
                //         axisY: {
                //             title: "Quantity"
                //         },
                //         data: [{
                //             type: "bar",
                //             toolTipContent: "<img src=\"https://canvasjs.com/wp-content/uploads/images/gallery/javascript-column-bar-charts/\"{url}\"\" style=\"width:40px; height:20px;\"> <b>{label}</b><br>Số lượng: {y}-{dv}<br>",
                //             dataPoints: data.html
                //         }]
                //     });
                //     chart_new_2.render();
                //     $('.canvasjs-chart-credit').css('display', 'none');
                //     console.log('thanhcong');
                // }
            },
            error: function(a, b, c) {
                alert(a + b + c);
                //window.location = url;
            }
        });
    });

    $('.top-laixe').on('change',function () {
        var $select = $(this).val();
        //show_ajax_loading($('.e_data_table'));
        var url = "<?php echo base_url(); ?>admin_dashboard/ajax_baocao_doanhthu_laixe.html";
        $.ajax({
            url: url,
            type: "post",
            data: {
                select: $select
            },
            dataType: "json",
            success: function(data) {
                //remove_ajax_loading();
                location.reload();
                // if(data.state === 1){
                //     $("#chartContainer3").html("");
                //     //thong ke theo lai xe
                //
                //     var chart3_new = new CanvasJS.Chart("chartContainer3", {
                //         animationEnabled: true,
                //         theme: "light2", // "light1", "light2", "dark1", "dark2"
                //         title: {
                //             text: "Doanh Thu Theo Lái Xe"
                //         },
                //         subtitles: [{
                //             text: "In Euro",
                //             fontSize: 16
                //         }],
                //         axisY: {
                //             prefix: "€ ",
                //         },
                //         data: [{
                //             type: "column",
                //             yValueFormatString: "€ #,##0.00",
                //             dataPoints: data.html
                //         }]
                //     });
                //     chart3_new.render();
                //     $('.canvasjs-chart-credit').css('display', 'none');
                //     console.log('thanhcong');
                // }
            },
            error: function(a, b, c) {
                alert(a + b + c);
                //window.location = url;
            }
        });
    });
</script>
