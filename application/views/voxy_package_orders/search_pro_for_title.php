<?php
/**
 * Created by PhpStorm.
 * User: vantinhchu
 * Date: 06.04.19
 * Time: 17:00
 */

if(isset($list_products)){
    foreach ($list_products as $key => $item){
?>
    <div class="result-search">
        <ul>
            <li  class="packung_timkiem" data-sl-kho ="<?php echo $item['inventory_quantity1'];?>" data-title ="<?php echo $item['title'];?>" data-sku="<?php echo $item["sku1"]?>" data-donvi="<?php echo $item["option1"]?>" data-cat-id="<?php echo $item["cat_id"]?>" data-product-id="<?php echo $item["id_shopify"]?>" data-variant1-id="<?php echo $item["variant1_id"]?>"><?php echo $item["title"]."-".$item["option1"]?></li>
            <li class="verpackung_timkiem" data-sl-kho ="<?php echo $item['inventory_quantity2'];?>" data-title ="<?php echo $item['title'];?>" data-sku="<?php echo $item["sku2"]?>" data-donvi="<?php echo $item["option2"]?>" data-cat-id="<?php echo $item["cat_id"]?>" data-product-id="<?php echo $item["id_shopify"]?>" data-variant2-id="<?php echo $item["variant2_id"]?>"><?php echo $item["title"]."-".$item["option2"]?></li>
        </ul>
    </div>
<?php  }
} ?>

<script type="text/javascript">
    $(".packung_timkiem").on("click",function(){
        var product_id = $(this).attr("data-product-id");
        var variant_id = $(this).attr("data-variant1-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");

        $(this).parent().parent().parent().parent().parent().find(".sku").text(sku);
        $(this).parent().parent().parent().parent().parent().find(".quantity").val(0);
        $(this).parent().parent().parent().parent().parent().find(".quantity").attr("data-variant-id",variant_id);
        $(this).parent().parent().parent().parent().parent().find(".price").val(0);
        $(this).parent().parent().parent().parent().parent().find(".title").val(title);
        $(this).parent().parent().parent().parent().parent().find(".variant_title").text(donvi);
        $(this).parent().parent().parent().parent().parent().find(".variant_id").text(variant_id);
        $(this).parent().parent().parent().parent().parent().find(".product_id").text(product_id);
        $(this).parent().parent().parent().parent().parent().find(".note").val("");

        $(this).parent().parent().parent().parent().parent().attr("class","infomation "+variant_id);

        //class hidden
        $(this).parent().parent().parent().parent().parent().find(".sku_hidden").val(sku);
        $(this).parent().parent().parent().parent().parent().find(".quantity_hidden").val(0);
        $(this).parent().parent().parent().parent().parent().find(".price_hidden").val(0);
        $(this).parent().parent().parent().parent().parent().find(".title_hidden").val(title);
        $(this).parent().parent().parent().parent().parent().find(".product_hidden").val(product_id);
        $(this).parent().parent().parent().parent().parent().find(".variant_hidden").val(variant_id);
        $(this).parent().parent().parent().parent().parent().find(".variant_title_hidden").val(donvi);
        $(this).parent().parent().parent().parent().parent().find(".item_note_hidden").val();

        $(".list_timkiem_title").css("display","none");
    });

    $(".verpackung_timkiem").on("click",function(){
        var product_id = $(this).attr("data-product-id");
        var variant_id = $(this).attr("data-variant2-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");

        $(this).parent().parent().parent().parent().parent().find(".sku").text(sku);
        $(this).parent().parent().parent().parent().parent().find(".quantity").val(0);
        $(this).parent().parent().parent().parent().parent().find(".quantity").attr("data-variant-id",variant_id);
        $(this).parent().parent().parent().parent().parent().find(".price").val(0);
        $(this).parent().parent().parent().parent().parent().find(".title").val(title);
        $(this).parent().parent().parent().parent().parent().find(".variant_title").text(donvi);
        $(this).parent().parent().parent().parent().parent().find(".variant_id").text(variant_id);
        $(this).parent().parent().parent().parent().parent().find(".product_id").text(product_id);
        $(this).parent().parent().parent().parent().parent().find(".note").val("");

        $(this).parent().parent().parent().parent().parent().attr("class","infomation "+variant_id);

        //class hidden
        $(this).parent().parent().parent().parent().parent().find(".sku_hidden").val(sku);
        $(this).parent().parent().parent().parent().parent().find(".quantity_hidden").val(0);
        $(this).parent().parent().parent().parent().parent().find(".price_hidden").val(0);
        $(this).parent().parent().parent().parent().parent().find(".title_hidden").val(title);
        $(this).parent().parent().parent().parent().parent().find(".product_hidden").val(product_id);
        $(this).parent().parent().parent().parent().parent().find(".variant_hidden").val(variant_id);
        $(this).parent().parent().parent().parent().parent().find(".variant_title_hidden").val(donvi);
        $(this).parent().parent().parent().parent().parent().find(".item_note_hidden").val();

        $(".list_timkiem_title").css("display","none")
    });
</script>
