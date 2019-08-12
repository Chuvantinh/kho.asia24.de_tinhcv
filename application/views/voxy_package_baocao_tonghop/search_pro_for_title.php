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
        var variant1_id = $(this).attr("data-variant1-id");
        var cat_id = $(this).attr("data-cat-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var sl_kho = $(this).attr("data-sl-kho");

        $(this).parent().parent().parent().parent().parent().find(".title").val(title);
        $(this).parent().parent().parent().parent().parent().find(".variant_title").text(donvi);
        $(this).parent().parent().parent().parent().parent().find(".sku").text(sku);
        $(this).parent().parent().parent().parent().parent().find(".sl-daxuat").text(0);
        $(this).parent().parent().parent().parent().parent().find(".quantity").val(0);
        $(this).parent().parent().parent().parent().parent().find(".variant_id").text(variant1_id);

        $(this).parent().parent().parent().parent().parent().find(".quantity").attr("data-variant-id",variant1_id);
        $(this).parent().parent().parent().parent().parent().find(".cat_id").text(cat_id);
        $(this).parent().parent().parent().parent().parent().find(".product_id").text(product_id);
        $(this).parent().parent().parent().parent().parent().find(".note").text("");
        $(this).parent().parent().parent().parent().parent().find(".variant_title").val(donvi);
        $(this).parent().parent().parent().parent().parent().find(".sl-in-kho").text(sl_kho);
        $(this).parent().parent().parent().parent().parent().attr("class","infomation "+variant1_id);

        $(".list_timkiem_title").css("display","none");
    });

    $(".verpackung_timkiem").on("click",function(){
        var product_id = $(this).attr("data-product-id");
        var variant2_id = $(this).attr("data-variant2-id");
        var cat_id = $(this).attr("data-cat-id");
        var title = $(this).attr("data-title");
        var donvi = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");
        var sl_kho = $(this).attr("data-sl-kho");

        $(this).parent().parent().parent().parent().parent().find(".title").val(title);
        $(this).parent().parent().parent().parent().parent().find(".variant_title").text(donvi);
        $(this).parent().parent().parent().parent().parent().find(".sku").text(sku);
        $(this).parent().parent().parent().parent().parent().find(".sl-daxuat").text(0);
        $(this).parent().parent().parent().parent().parent().find(".quantity").val(0);
        $(this).parent().parent().parent().parent().parent().find(".variant_id").text(variant2_id);
        $(this).parent().parent().parent().parent().parent().find(".quantity").attr("data-variant-id",variant2_id);

        $(this).parent().parent().parent().parent().parent().find(".cat_id").text(cat_id);
        $(this).parent().parent().parent().parent().parent().find(".product_id").text(product_id);
        $(this).parent().parent().parent().parent().parent().find(".note").text("");
        $(this).parent().parent().parent().parent().parent().find(".variant_title").val(donvi);
        $(this).parent().parent().parent().parent().parent().find(".sl-in-kho").text(sl_kho);
        $(this).parent().parent().parent().parent().parent().attr("class","infomation "+variant2_id);

        $(".list_timkiem_title").css("display","none")
    });
</script>
