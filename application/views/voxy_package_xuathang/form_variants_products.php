<?php

if(isset($list_products)){
    foreach ($list_products as $key => $item){
        $check_variant1 = $this->m_voxy_package->check_variant1($item['variant1_id']);
        $check_variant2 = $this->m_voxy_package->check_variant2($item['variant2_id']);
        $quantity_in_ware_house = 0;
        if($check_variant1 == true){
            $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant1($item['variant1_id']);
        }

        if($check_variant2 == true){
            $quantity_in_ware_house = $this->m_voxy_package->get_quantity_now_variant2($item['variant2_id']);
        }
?>
    <div class="row2">
        <div class="title"><?php echo $item['title']; ?></div>
        <div class="variant">
            <div class="packung" style="float: left;width: 50%">
                <div class="infor-packung" style="float: left;width: 30%;padding: 0 10px;"
                     data-cat-id="<?php echo $item['cat_id']; ?>"
                     data-sku="<?php echo $item['sku1']; ?>"
                     data-title="<?php echo $item['title']; ?>"
                     data-option="<?php echo $item['option1']; ?>"
                     data-variant-id="<?php echo $item['variant1_id']; ?>"
                     data-location="<?php echo $item['location']; ?>"
                     data-product-id="<?php echo $item['id_shopify']; ?>"
                     data-sl-kho="<?php echo $quantity_in_ware_house; ?>"
                     >
                    <div class="option1"><?php echo $item['option1']; ?></div>
                    <div class="sku"><?php echo "SKU:" . $item['sku1']; ?></div>
                </div>
                <div class="quantity_packung" style="float: left;width: 30%">
                    <span>Số lượng</span>
                    <input type="number" class="quantity-packung-<?php echo $key; ?>"
                           value=""
                           style="min-width: 20px;margin-right: 10px;">
                </div>
            </div>

            <div class="infor-verpackung"
                 style="float: left;width: 50%;padding: 0 10px;"
                 data-cat-id="<?php echo $item['cat_id']; ?>"
                 data-sku="<?php echo $item['sku2']; ?>"
                 data-title="<?php echo $item['title']; ?>"
                 data-option="<?php echo $item['option2']; ?>"
                 data-variant-id="<?php echo $item['variant2_id']; ?>"
                 data-location="<?php echo $item['location']; ?>"
                 data-sl-kho="<?php echo $quantity_in_ware_house; ?>"
                 data-product-id="<?php echo $item['id_shopify']; ?>"
            >
                <div class="infor_verpackung" style="float: left;width: 30%">
                    <div class="variant_verpackung"><?php echo $item['option2']; ?></div>
                    <div class="sku_verpackung"><?php echo "SKU:" . $item['sku2']; ?></div>
                </div>
                <div class="quantity_verpackung" style="float: left;width: 30%">
                    <span>Số lượng</span>
                    <input type="number" class="quantity-verpackung-<?php echo $key; ?>"
                           value=""
                           style="min-width: 20px;margin-right: 10px;">
                </div>
            </div>
        </div>

        <br/>
        <br/>
    </div>

<?php  }
} ?>