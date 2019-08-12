<?php

if(isset($list_products)){ ?>
    <div class="add-all">
        <button class="btn-add-all">Add all</button>
    </div>
<?php
    foreach ($list_products as $key => $item2){
            foreach ($item2 as $item){
                if($text != ""){
                    if (strpos($item['location'], $text) !== false) {
?>
    <div class="result-search">
        <ul>
            <li  class="packung_timkiem"
                 data-sl-kho ="<?php echo $item['inventory_quantity1'];?>"
                 data-location ="<?php echo $item['location'];?>"
                 data-title ="<?php echo $item['title'];?>"
                 data-sku="<?php echo $item["sku1"]?>"
                 data-donvi="<?php echo $item["option1"]?>"
                 data-cat-id="<?php echo $item["cat_id"]?>"
                 data-product-id="<?php echo $item["id_shopify"]?>"
                 data-variant-id="<?php echo $item["variant1_id"]?>">
                <?php echo $item["title"]."-".$item["option1"]?>
            </li>

            <li class="verpackung_timkiem"
                data-sl-kho ="<?php echo $item['inventory_quantity2'];?>"
                data-location ="<?php echo $item['location'];?>"
                data-title ="<?php echo $item['title'];?>"
                data-sku="<?php echo $item["sku2"]?>"
                data-donvi="<?php echo $item["option2"]?>"
                data-cat-id="<?php echo $item["cat_id"]?>"
                data-product-id="<?php echo $item["id_shopify"]?>"
                data-variant-id="<?php echo $item["variant2_id"]?>">
                <?php echo $item["title"]."-";?><b><?php echo $item["option2"]; ?></b>
            </li>
        </ul>
    </div>
<?php  } } else{ //case text normal ?>
                    <div class="result-search">
                        <ul>
                            <li  class="packung_timkiem"
                                 data-sl-kho ="<?php echo $item['inventory_quantity1'];?>"
                                 data-location ="<?php echo $item['location'];?>"
                                 data-title ="<?php echo $item['title'];?>"
                                 data-sku="<?php echo $item["sku1"]?>"
                                 data-donvi="<?php echo $item["option1"]?>"
                                 data-cat-id="<?php echo $item["cat_id"]?>"
                                 data-product-id="<?php echo $item["id_shopify"]?>"
                                 data-variant-id="<?php echo $item["variant1_id"]?>">
                                <?php echo $item["title"]."-".$item["option1"]?>
                            </li>

                            <li class="verpackung_timkiem"
                                data-sl-kho ="<?php echo $item['inventory_quantity2'];?>"
                                data-location ="<?php echo $item['location'];?>"
                                data-title ="<?php echo $item['title'];?>"
                                data-sku="<?php echo $item["sku2"]?>"
                                data-donvi="<?php echo $item["option2"]?>"
                                data-cat-id="<?php echo $item["cat_id"]?>"
                                data-product-id="<?php echo $item["id_shopify"]?>"
                                data-variant-id="<?php echo $item["variant2_id"]?>">
                                <?php echo $item["title"]."-";?><b><?php echo $item["option2"]; ?></b>
                            </li>
                        </ul>
                    </div>
        <?php } ?>
           <?php }
}
} ?>

<script type="text/javascript">
    //add tat ca cac san pham thoi
    $(".btn-add-all").on("click",function () {

        $.each($(".result-search ul li"), function (index) {
            var product_id = $(this).attr("data-product-id");
            var variant1_id = $(this).attr("data-variant-id");
            var cat_id = $(this).attr("data-cat-id");
            var title = $(this).attr("data-title");
            var donvi = $(this).attr("data-donvi");
            var sku = $(this).attr("data-sku");
            var location = $(this).attr("data-location");
            var sl_kho = $(this).attr("data-sl-kho");

            var sl_not_match = 1;
            var sl_match = 0;
            var count = 1;
            $.each($(".infomation"), function (index) {
                count++;
                var sl_thucte = $(this).find(".quantity").val();
                var sl_tonkho = $(this).find('.sl_kho').text();
                var sl_sailech =  parseInt(sl_thucte)-parseInt(sl_tonkho);

                if(sl_sailech === 0) {
                    sl_match++;
                    $(this).removeClass("tab3");
                    $(this).addClass("tab2");
                } else {
                    sl_not_match++;
                    $(this).removeClass("tab2");
                    $(this).addClass("tab3");
                }
            });
            $('.wrapper_information').attr('sl_products',count);
            $('.t-tab-kiemkho .t-li-all .nummer1').text("( "+ count + ")");

            $('.wrapper_information').attr('sl_match',sl_match);
            $('.nummer2').text("( " + sl_match + ")");

            $('.wrapper_information').attr('sl_not_match', sl_not_match);
            $('.nummer3').text("( " + sl_not_match + ")");


            //"<input type='hidden' name='information["+variant1_id+"][title]' value='" + title+ "'>"+

            //add vao list san pham ben duoi
            $(".list_products_kiemkho").append(
                "<div  class='infomation' style='padding: 5px 0; display: block;width: 100%;float: left;'>" +

                "<input type='hidden' name='information["+variant1_id+"][variant_id]' value='" + variant1_id+ "'>"+
                "<input type='hidden' name='information["+variant1_id+"][cat_id]' value='" + cat_id+ "'>"+
                "<input type='hidden' name='information["+variant1_id+"][product_id]' value='" + product_id+ "'>"+
                "<input type='hidden' name='information["+variant1_id+"][sl_kho]' value='" + sl_kho+ "'>"+
                "<input type='hidden' name='information["+variant1_id+"][variant_title]' value='" + donvi+ "'>"+
                "<input type='hidden' name='information["+variant1_id+"][location]' value='" + location+ "' class='location-hidden'>"+
                "<input type='hidden' name='information["+variant1_id+"][sku]' value='" + sku+ "'>"+

                "<div style='width: 2%;float: left;text-align: left;'>"+count+"</div>"+

                "<div style='width: 20%;float: left;text-align: left;'>"+
                    "<input type='text' value='" + location+ "' class='input-location' style='width: 100%;text-align: center;'>"+
                "</div>"+

                "<div class='remove' style='width: 5%;float:left'>"+
                "<i class='material-icons' title='Xóa' style='color: #e47885'>close</i>"+
                "</div>"+
                "<div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'>" + sku + "</div>" +

                "<div style='width: 35%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'>" +
                title+
                "</div>"+
                "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + donvi + "</div>" +
                "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sl_kho'>" + sl_kho + "</div>" +

                "<div style='width: 10%;height: auto;float: left' >"+
                "<input type='text' name='quantity["+variant1_id+"]' class='quantity' style='min-width:50px;width: 50px;text-align: center;'>"+
                "</div>" +
                "<div style='width: 10%;height: auto;float: left;text-align: center !important;' class='sailech'>" +  + "</div>" +

                // "<div style='width: 10%;margin-left:5px;height: auto;float: left' class='location' data-location= '"+location+"'>" + location + "</div>"+
                "</div>"
            );
        });

        $(".list_result").css("display","none");
        $(".container_list").css("display","none");
        $(".search_pro").val("");
    });


    $(".packung_timkiem").on("click",function(e){
        var product_id = $(this).attr("data-product-id");
        var variant1_id = $(this).attr("data-variant-id");
        var cat_id = $(this).attr("data-cat-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var location = $(this).attr("data-location");
        var sl_kho = $(this).attr("data-sl-kho");

        var sl_not_match = 1;
        var sl_match = 0;
        var count = 1;
        $.each($(".infomation"), function (index) {
            count++;
            var sl_thucte = $(this).find(".quantity").val();
            var sl_tonkho = $(this).find('.sl_kho').text();
            var sl_sailech =  parseInt(sl_thucte)-parseInt(sl_tonkho);

            if(sl_sailech === 0) {
                sl_match++;
                $(this).removeClass("tab3");
                $(this).addClass("tab2");
            } else {
                sl_not_match++;
                $(this).removeClass("tab2");
                $(this).addClass("tab3");
            }
        });
        $('.wrapper_information').attr('sl_products',count);
        $('.t-tab-kiemkho .t-li-all .nummer1').text("( "+ count + ")");

        $('.wrapper_information').attr('sl_match',sl_match);
        $('.nummer2').text("( " + sl_match + ")");

        $('.wrapper_information').attr('sl_not_match', sl_not_match);
        $('.nummer3').text("( " + sl_not_match + ")");

//"<input type='hidden' name='information["+variant1_id+"][title]' value='" + title+ "'>"+
        //add vao list san pham ben duoi
        $(".list_products_kiemkho").append(
            "<div  class='infomation' style='padding: 5px 0; display: block;width: 100%;float: left;'>" +

            "<input type='hidden' name='information["+variant1_id+"][variant_id]' value='" + variant1_id+ "'>"+
            "<input type='hidden' name='information["+variant1_id+"][cat_id]' value='" + cat_id+ "'>"+
            "<input type='hidden' name='information["+variant1_id+"][product_id]' value='" + product_id+ "'>"+
            "<input type='hidden' name='information["+variant1_id+"][sl_kho]' value='" + sl_kho+ "'>"+
            "<input type='hidden' name='information["+variant1_id+"][variant_title]' value='" + donvi+ "'>"+
            "<input type='hidden' name='information["+variant1_id+"][location]' value='" + location+ "' class='location-hidden'>"+
            "<input type='hidden' name='information["+variant1_id+"][sku]' value='" + sku+ "'>"+

            "<div style='width: 2%;float: left;text-align: left;'>"+count+"</div>"+

                "<div style='width: 20%;float: left;text-align: left;'>"+
                "<input type='text' value='" + location+ "' class='input-location' style='width: 100%;text-align: center;'>"+
                "</div>"+

            "<div class='remove' style='width: 5%;float:left'>"+
                "<i class='material-icons' title='Xóa' style='color: #e47885'>close</i>"+
            "</div>"+
            "<div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'>" + sku + "</div>" +

            "<div style='width: 35%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'>" +
                title+
            "</div>"+
            "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + donvi + "</div>" +
            "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sl_kho'>" + sl_kho + "</div>" +

            "<div style='width: 10%;height: auto;float: left' >"+
                "<input type='text' name='quantity["+variant1_id+"]' class='quantity' style='min-width:50px;width: 50px;text-align: center;'>"+
            "</div>" +
            "<div style='width: 10%;height: auto;float: left;text-align: center !important;' class='sailech'>" +  + "</div>" +

            // "<div style='width: 10%;margin-left:5px;height: auto;float: left' class='location' data-location= '"+location+"'>" + location + "</div>"+
            "</div>"
        );

        $(".list_result").css("display","none");
        $(".container_list").css("display","none");
        $(".search_pro").val("");
    });

    $(".verpackung_timkiem").on("click",function(e){
        var product_id = $(this).attr("data-product-id");
        var variant2_id = $(this).attr("data-variant-id");
        var cat_id = $(this).attr("data-cat-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var location = $(this).attr("data-location");
        var sl_kho = $(this).attr("data-sl-kho");

        var sl_not_match = 1;
        var sl_match = 0;
        var count = 1;
        $.each($(".infomation"), function (index) {
            count++;
            var sl_thucte = $(this).find(".quantity").val();
            var sl_tonkho = $(this).find('.sl_kho').text();
            var sl_sailech =  parseInt(sl_thucte)-parseInt(sl_tonkho);

            if(sl_sailech === 0) {
                sl_match++;
                $(this).removeClass("tab3");
                $(this).addClass("tab2");
            } else {
                sl_not_match++;
                $(this).removeClass("tab2");
                $(this).addClass("tab3");
            }
        });
        $('.wrapper_information').attr('sl_products',count);
        $('.t-tab-kiemkho .t-li-all .nummer1').text("( "+ count + ")");

        $('.wrapper_information').attr('sl_match',sl_match);
        $('.nummer2').text("( " + sl_match + ")");

        $('.wrapper_information').attr('sl_not_match', sl_not_match);
        $('.nummer3').text("( " + sl_not_match + ")");

        //add vao list san pham ben duoi
        $(".list_products_kiemkho").append(
            "<div  class='infomation' style='    background-color: #62aeef;padding: 5px 0; display: block;width: 100%;float: left;'>" +

            "<input type='hidden' name='information["+variant2_id+"][variant_id]' value='" + variant2_id+ "'>"+
            "<input type='hidden' name='information["+variant2_id+"][cat_id]' value='" + cat_id+ "'>"+
            "<input type='hidden' name='information["+variant2_id+"][product_id]' value='" + product_id+ "'>"+
            "<input type='hidden' name='information["+variant2_id+"][sl_kho]' value='" + sl_kho+ "'>"+
            "<input type='hidden' name='information["+variant2_id+"][variant_title]' value='" + donvi+ "'>"+
            "<input type='hidden' name='information["+variant2_id+"][location]' value='" + location+ "' class='location-hidden'>"+
            "<input type='hidden' name='information["+variant2_id+"][title]' value='" + title+ "'>"+
            "<input type='hidden' name='information["+variant2_id+"][sku]' value='" + sku+ "'>"+

                "<div style='width: 2%;float: left;text-align: left;'>"+count+"</div>"+

            "<div style='width: 20%;float: left;text-align: left;'>"+
                "<input type='text' value='" + location+ "' class='input-location' style='width: 100%;text-align: center;'>"+
            "</div>"+

            "<div class='remove' style='width: 5%;float:left'>"+
                "<i class='material-icons' title='Xóa' style='color: #e47885'>close</i>"+
            "</div>"+

            "<div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'>" + sku + "</div>" +

            "<div style='width: 35%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'>" +
                title+
            "</div>"+

            "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + donvi + "</div>" +
            "<div style='width: 10%;height: auto;float: left;text-align: center !important;' class='sl_kho'>" + sl_kho + "</div>" +

            "<div style='width: 10%;height: auto;float: left'>"+
                    "<input type='text' name='quantity["+variant2_id+"]' class='quantity' style='min-width:50px;width: 50px;text-align: center;'>"+
            "</div>" +
            "<div style='width: 10%;height: auto;float: left;text-align: center !important;' class='sailech'>" +  + "</div>" +
            // "<div style='width: 10%;margin-left:5px;height: auto;float: left' class='location' data-location= '"+location+"'>" + location + "</div>"+

        "</div>"
        );

        $(".list_result").css("display","none");
        $(".container_list").css("display","none");
        $(".search_pro").val("");
    });

    $(".remove").on('click', function (e) {
       $(this).parent().remove();
    });
</script>
