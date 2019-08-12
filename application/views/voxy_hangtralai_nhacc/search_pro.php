<?php

if(isset($list_products)){
    foreach ($list_products as $key => $item){
?>
    <div class="result-search">
        <ul>
            <li  class="packung_timkiem" data-sl-kho ="<?php echo $item['inventory_quantity1'];?>" data-location ="<?php echo $item['location'];?>" data-title ="<?php echo $item['title'];?>" data-sku="<?php echo $item["sku1"]?>" data-donvi="<?php echo $item["option1"]?>" data-cat-id="<?php echo $item["cat_id"]?>" data-product-id="<?php echo $item["id_shopify"]?>" data-variant1-id="<?php echo $item["variant1_id"]?>"><?php echo $item["title"]."-".$item["option1"]?></li>
            <li class="verpackung_timkiem" data-sl-kho ="<?php echo $item['inventory_quantity2'];?>" data-location ="<?php echo $item['location'];?>" data-title ="<?php echo $item['title'];?>" data-sku="<?php echo $item["sku2"]?>" data-donvi="<?php echo $item["option2"]?>" data-cat-id="<?php echo $item["cat_id"]?>" data-product-id="<?php echo $item["id_shopify"]?>" data-variant2-id="<?php echo $item["variant2_id"]?>"><?php echo $item["title"]."-".$item["option2"]?></li>
        </ul>
    </div>
<?php  }
} ?>

<script type="text/javascript">
    $(".packung_timkiem").on("click",function(){
        var product_id = $(this).attr("data-product-id");
        var variant_id = $(this).attr("data-variant1-id");
        var cat_id = $(this).attr("data-cat-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var location = $(this).attr("data-location");
        var sl_kho = $(this).attr("data-sl-kho");

        var $i = "#";
        //add vao list san pham ben duoi
        $(".list_products_transfer").prepend(
            "<div  class='infomation' style='padding: 5px 0; display: block;width: 100%;float: left;'>" +

            "<input type='hidden' name='information["+variant_id+"][variant_id]' value='" + variant_id+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][cat_id]' value='" + cat_id+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][product_id]' value='" + product_id+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][sl_kho]' value='" + sl_kho+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][variant_title]' value='" + donvi+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][location]' value='" + location+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][title]' value='" + title+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][sku]' value='" + sku+ "'>"+

            "<div style='width: 2%;float: left;text-align: left;'>"+$i+"</div>"+
            "<div class='remove' style='width: 5%;float:left'>"+
                "<i class='material-icons' title='Xóa' style='color: #e47885'>close</i>"+
            "</div>"+
            "<div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'>" + sku + "</div>" +

            "<div style='width: 45%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'>" +
                title+
            "</div>"+

            "<div style='width: 10%;height: auto;float: left' class='quantity'>"+
                "<input type='text' name='quantity["+variant_id+"]' class='quantity' style='min-width:50px;width: 50px;text-align: center;'>"+
            "</div>" +

            "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + donvi + "</div>" +

            "<div style='width: 10%;height: auto;float: left' class='gianhapnew'>"+
                "<input type='text' name='gianhapnew["+variant_id+"]' class='input_gianhapnew' style='min-width:50px;width: 50px;text-align: center;'>"+
            "</div>" +
                
            // "<div style='width: 10%;margin-left:5px;height: auto;float: left' class='location' data-location= '"+location+"'>" + location + "</div>"+
            "</div>"
        );

        $(".list_result").css("display","none");
        $(".container_list").css("display","none");
        $(".search_pro").val("");
    });

    $(".verpackung_timkiem").on("click",function(){
        var product_id = $(this).attr("data-product-id");
        var variant_id = $(this).attr("data-variant2-id");
        var cat_id = $(this).attr("data-cat-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var location = $(this).attr("data-location");
        var sl_kho = $(this).attr("data-sl-kho");

        var $i = "#";
        //add vao list san pham ben duoi
        $(".list_products_transfer").prepend(
            "<div  class='infomation' style='    background-color: #62aeef;padding: 5px 0; display: block;width: 100%;float: left;'>" +

            "<input type='hidden' name='information["+variant_id+"][variant_id]' value='" + variant_id+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][cat_id]' value='" + cat_id+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][product_id]' value='" + product_id+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][sl_kho]' value='" + sl_kho+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][variant_title]' value='" + donvi+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][location]' value='" + location+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][title]' value='" + title+ "'>"+
            "<input type='hidden' name='information["+variant_id+"][sku]' value='" + sku+ "'>"+

                "<div style='width: 2%;float: left;text-align: left;'>"+$i+"</div>"+
            "<div class='remove' style='width: 5%;float:left'>"+
                "<i class='material-icons' title='Xóa' style='color: #e47885'>close</i>"+
            "</div>"+
            "<div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'>" + sku + "</div>" +

            "<div style='width: 45%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'>" +
                title+
            "</div>"+

            "<div style='width: 10%;height: auto;float: left' class='quantity'>"+
                    "<input type='text' name='quantity["+variant_id+"]' class='quantity' style='min-width:50px;width: 50px;text-align: center;'>"+
            "</div>" +

            "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + donvi + "</div>" +

            "<div style='width: 10%;height: auto;float: left' class='gianhapnew'>"+
                "<input type='text' name='gianhapnew["+variant_id+"]' class='input_gianhapnew' style='min-width:50px;width: 50px;text-align: center;'>"+
            "</div>" +

            // "<div style='width: 10%;margin-left:5px;height: auto;float: left' class='location' data-location= '"+location+"'>" + location + "</div>"+
            "</div>"
        );

        $(".list_result").css("display","none");
        $(".container_list").css("display","none");
        $(".search_pro").val("");
    });

    $(".remove").on('click', function () {
       $(this).parent().remove();
    });
</script>
