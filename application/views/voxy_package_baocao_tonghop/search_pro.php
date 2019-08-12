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
        var variant_title = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var location = $(this).attr("data-location");
        var sl_kho = $(this).attr("data-sl-kho");
        var quantity = 0;

        //add vao list san pham ben duoi
        $(".pro_body").prepend(
            "<div  class='infomation " + variant_id + "'>" +
            "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id'>" + variant_id + "</div>" +
            "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='cat_id'>" +cat_id + "</div>" +
            "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='product_id'>" + product_id + "</div>" +
            "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku'>" + sku + "</div>" +

            "<div style='width: 40%;height: auto;float: left; text-align: center' class='parent-result'>" +
            "<input type='text' class='title' value='"+title+"' data-search='class_search_"+variant_id+"'>"+
            "<div class='list_timkiem_title class_search_"+variant_id+"'></div>"+
            "<div class='note' style='width:35%;float:left; text-transform: uppercase; text-align: right'>"+
            "<span style='width: 15%'>&nbsp;</span>"+
            "</div>" +
            "</div>"+

            "<div style='width: 10%;height: auto;float: left' data-variant-id ='"+variant_id+"'>"+
                "<input type='text' class='quantity' value='0'" +
                        "data-da-xuat = " + 0 + " " +
                        "data-variant-id = " + variant_id + " " +
                        "data-quantity-need = " + 0 + " " +
                        "data-quantity-rest = " + 0 + " " +
                        "data-da-xuat='nein'"+
                        "style='width: 60px;text-align: center;'>"+
            "</div>"+

            "<div style='width: 5%;height: auto;float: left'>" +
            "<div class='quantity_xuathang'" +
                    "style='width: 60px;text-align: center;'>" +
                    quantity+
            "</div>" +
            "</div>" +

            "<div style='width: 5%;height: auto;float: left' class='sl-daxuat'>" + 0 + "</div>" +
            "<div class='sl-in-kho' style='width: 5%;height: auto;float: left;color: orangered;line-height: 35px;'>" +
                sl_kho+
            "</div>"+
            "<div style='width: 5%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + variant_title + "</div>" +
            "<button type='button' class='edit_product' style='width: 5%;float:left' >Sửa</button>"+
            "<button type='button' class='remove' style='width: 5%;float:left' >Xóa</button>"+
            "<div style='width: 10%;height: auto;float: left' class='location' data-location= '"+location+"'>" + location + "</div>"+
            "</div>"
        );
        //$("." + variant_id).find(".title").val(title);

        $(".list_timkiem").css("display","none");
        $(".search_pro").val("");
    });

    $(".verpackung_timkiem").on("click",function(){
        var product_id = $(this).attr("data-product-id");
        var variant_id = $(this).attr("data-variant2-id");
        var cat_id = $(this).attr("data-cat-id");
        var title = $(this).attr("data-title");
        var variant_title = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var location = $(this).attr("data-location");
        var sl_kho = $(this).attr("data-sl-kho");
        var quantity = 0;

        $(".pro_body").prepend(
            "<div  class='infomation " + variant_id + "'>" +
            "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='variant_id'>" + variant_id + "</div>" +
            "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='cat_id'>" +cat_id + "</div>" +
            "<div style='display: none;width: 10%;height: auto;float: left;text-align: left !important;' class='product_id'>" + product_id + "</div>" +

            "<div style='width: 10%;height: auto;float: left;text-align: left !important;' class='sku'>" + sku + "</div>" +
            "<div style='width: 40%;height: auto;float: left; text-align: center' class='parent-result'>" +
                "<input type='text' class='title' value='"+title+"' data-search='class_search_"+variant_id+"'>"+
            "<div class='list_timkiem_title class_search_"+variant_id+"'></div>"+
                "<div class='note' style='width:35%;float:left; text-transform: uppercase; text-align: right'>"+
                    "<span style='width: 15%'>&nbsp;</span>"+
                "</div>" +
            "</div>"+

            "<div style='width: 10%;height: auto;float: left' data-variant-id ='"+variant_id+"'>"+
                "<input type='text' class='quantity' value='0'" +
                "data-da-xuat = " + 0 + " " +
                "data-variant-id = " + variant_id + " " +
                "data-quantity-need = " + 0 + " " +
                "data-quantity-rest = " + 0 + " " +
                "data-da-xuat='nein'"+
                "style='width: 60px;text-align: center;'>"+
            "</div>"+

            "<div style='width: 5%;height: auto;float: left'>" +
                "<div class='quantity_xuathang'" +
                    "style='width: 60px;text-align: center;'>" +
                    quantity+
                "</div>" +
            "</div>" +

            "<div style='width: 5%;height: auto;float: left' class='sl-daxuat'>" + 0 + "</div>" +
            "<div class='sl-in-kho' style='width: 5%;height: auto;float: left;color: orangered;line-height: 35px;'>" +
            sl_kho+
            "</div>"+
            "<div style='width: 5%;height: auto;float: left;text-align: left !important;' class='variant_title'>" + variant_title + "</div>" +
            "<button type='button' class='edit_product' style='width: 5%;float:left' >Sửa</button>"+
            "<button type='button' class='remove' style='width: 5%;float:left' >Xóa</button>"+
            "<div style='width: 10%;height: auto;float: left' class='location' data-location= '"+location+"'>" + location + "</div>"+
            "</div>"
        );
        //$("." + variant_id).find(".title").val(title);

        $(".list_timkiem").css("display","none")
        $(".search_pro").val("");
    });

    $(".remove").on('click', function () {
        $(this).parent().remove();
    });
</script>
