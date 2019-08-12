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
        var title = $(this).attr("data-title");
        var variant_title = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");

        //add vao list san pham ben duoi
        $(".pro_body").prepend(
            "<div class='infomation "+variant_id+"' style='width: 100%;float:left;'>" +
            "<input type='hidden' class='sku_hidden' name='information["+variant_id+"][sku]' value='"+sku+"' >" +
            "<input type='hidden' class='quantity_hidden' name='information["+variant_id+"][quantity]' value='' >" +
            "<input type='hidden' class='price_hidden' name='information["+variant_id+"][price]' value=''>" +
            "<input type='hidden' class='title_hidden' name='information["+variant_id+"][title]' value='"+title+"'>" +
            "<input type='hidden' class='product_hidden' name='information["+variant_id+"][product_id]' value='"+product_id+"'>" +
            "<input type='hidden' class='variant_hidden' name='information["+variant_id+"][variant_id]' value='"+variant_id+"'>" +
            "<input type='hidden' class='variant_title_hidden' name='information["+variant_id+"][variant_title]' value='"+variant_title+"'>" +
            "<input type='hidden' class='item_note_hidden' name='information["+variant_id+"][item_note]' value=''>"+
            "<input type='hidden' class='item_note_hidden' name='information["+variant_id+"][item_note]' value=''>"+
            "<input type='hidden' class='hangve_hidden' name='information["+variant_id+"][hangve]' value=''>"+
            "<input type='hidden' class='hanghong_hidden' name='information["+variant_id+"][hanghong]' value=''>"+
            "<input type='hidden' class='hangthieu_hidden' name='information["+variant_id+"][hangthieu]' value=''>"+
            "<input type='hidden' class='hangthem_hidden' name='information["+variant_id+"][hangthem]' value=''>"+
            "<input type='hidden' class='refund_hidden' name='information["+variant_id+"][refund]' value=''>"+

            "            <div class='class5 count'>#</div>" +
            "            <div class='class5 sku'>"+sku+"</div>" +
                
            "            <div class='class10 note'>" +
            "            <input type='text' class='input_note'  " +
            "                   style='width: 122px;min-width: 105px' value=''>" +
            "            </div>" +
                
            "            <div class='class25 parent-result'>" +
            "            <input type='text' class='title'" +
                    "        data-search='class_search_"+variant_id+"'" +
                    "        value='"+title+"'" +
                    "        style='width:100%;line-height: 36px; height: 36px;text-align: center'>" +
            "                   <div class='list_timkiem_title class_search_"+variant_id+"'></div>" +
            "            </div>" +
                
            "            <div class='class5'>" +
            "            <input type='text' class='quantity'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +
            "" +
            "            <div class='class5 variant_title'>" +
            "            "+variant_title+"" +
            "            </div>" +
            "" +
            "            <div class='class5'>" +
            "            <input type='text' class='price'" +
            "        value='0'" +
            "        style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +
                
            "            <div class='class5'>" +
            "            <input type='text' class='hangve'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +

            "            <div class='class5'>" +
            "            <input type='text' class='hanghong'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +


            "            <div class='class5'>" +
            "            <input type='text' class='hangthieu'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +

            "            <div class='class5'>" +
            "            <input type='text' class='hangthem'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +

            "" +
            "            <div class='class5 thanhtien'>" +
            "#"+
            "            </div>" +
            "" +
            "            <div class='class5'>" +
            "            <span class='edit_product btn btn-mini'>Edit<span>" +
            "            </div>" +
            "" +
            "            <div class='class5'>" +
            "            <span class='remove btn btn-mini'>Remove<span>" +
            "            </div>" +

            "            <div class='class5'>" +
            "            <span class='refund btn btn-mini refund_active'>Restock<span>" +
            "            </div>" +

            "            </div>"
        );

        $(".list_timkiem").css("display","none");
        $(".search_pro").val("");
    });

    $(".verpackung_timkiem").on("click",function(){
        var product_id = $(this).attr("data-product-id");
        var variant_id = $(this).attr("data-variant2-id");
        var title = $(this).attr("data-title");
        var variant_title = $(this).attr("data-donvi");
        var sku = $(this).attr("data-sku");

        $(".pro_body").prepend(
            "<div class='infomation "+variant_id+"' style='width: 100%;float:left;'>" +
            "<input type='hidden' class='sku_hidden' name='information["+variant_id+"][sku]' value='"+sku+"'>" +
            "<input type='hidden' class='quantity_hidden' name='information["+variant_id+"][quantity]' value='' >" +
            "<input type='hidden' class='price_hidden' name='information["+variant_id+"][price]' value=''>" +
            "<input type='hidden' class='title_hidden' name='information["+variant_id+"][title]' value='"+title+"'>" +
            "<input type='hidden' class='product_hidden' name='information["+variant_id+"][product_id]' value='"+product_id+"'>" +
            "<input type='hidden' class='variant_hidden' name='information["+variant_id+"][variant_id]' value='"+variant_id+"'>" +
            "<input type='hidden' class='variant_title_hidden' name='information["+variant_id+"][variant_title]' value='"+variant_title+"'>" +
            "<input type='hidden' class='item_note_hidden' name='information["+variant_id+"][item_note]' value=''>"+
            "<input type='hidden' class='hangve_hidden' name='information["+variant_id+"][hangve]' value=''>"+
            "<input type='hidden' class='hanghong_hidden' name='information["+variant_id+"][hanghong]' value=''>"+
            "<input type='hidden' class='hangthieu_hidden' name='information["+variant_id+"][hangthieu]' value=''>"+
            "<input type='hidden' class='hangthem_hidden' name='information["+variant_id+"][hangthem]' value=''>"+
            "<input type='hidden' class='refund_hidden' name='information["+variant_id+"][refund]' value=''>"+

            "            <div class='class5 count'>#</div>" +
            "            <div class='class5 sku'>"+sku+"</div>" +
            "            <div class='class10 note'>" +
            "            <input type='text' class='input_note'  " +
            "                   style='width: 122px;min-width: 105px' value=''>" +
            "            </div>" +
            "            <div class='class25 parent-result'>" +
            "            <input type='text' class='title'" +
            "        data-search='class_search_"+variant_id+"'" +
            "        value='"+title+"'" +
            "        style='width:100%;line-height: 36px; height: 36px;text-align: center'>" +
            "                   <div class='list_timkiem_title class_search_"+variant_id+"'></div>" +
            "            </div>" +
            "            <div class='class5'>" +
            "            <input type='text' class='quantity'" +
            "        value='0'" +
            "        style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +
            "" +
            "            <div class='class5 variant_title'>" +
            "            "+variant_title+"" +
            "            </div>" +
            "" +
            "            <div class='class5'>" +
            "            <input type='text' class='price'" +
            "        value='0'" +
            "        style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +

            "            <div class='class5'>" +
            "            <input type='text' class='hangve'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +

            "            <div class='class5'>" +
            "            <input type='text' class='hanghong'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +


            "            <div class='class5'>" +
            "            <input type='text' class='hangthieu'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +

            "            <div class='class5'>" +
            "            <input type='text' class='hangthem'" +
            "               value='0'" +
            "               style='min-width: 50px;width: 50px;text-align: center;'>" +
            "            </div>" +

            "" +
            "            <div class='class5 thanhtien'>" +
                            "#"+
            "            </div>" +
            "" +
            "            <div class='class5'>" +
            "            <span class='edit_product btn btn-mini'>Edit<span>" +
            "            </div>" +
            "" +
            "            <div class='class5'>" +
            "            <span class='remove btn btn-mini'>Remove<span>" +
            "            </div>" +

            "            <div class='class5'>" +
            "            <span class='refund btn btn-mini refund_active'>Restock<span>" +
            "            </div>" +

            "            </div>"
        );

        $(".list_timkiem").css("display","none");
        $(".search_pro").val("");
    });
</script>
