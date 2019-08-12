<?php

if(isset($list_products)){
    foreach ($list_products as $key => $item){
?>
    <div class="row2">
        <div class="title"><?php echo $item['title']; ?></div>
        <div class="variant">
            <div class="packung" style="float: left;width: 50%">
                <div class="info_packung" style="float: left;width: 70%;padding: 0 10px;">
                    <div class="variant_packung"><?php echo $item['option1']; ?></div>
                    <div class="sku_packung"><?php echo "SKU:" . $item['sku1']; ?></div>
                </div>
                <div class="quantity_packung" style="float: left;width: 30%">
                    <input type="number" name="quantity_packung_<?php echo $key; ?>"
                           value="<?php echo isset($item['quantity_packung']) ? $item['quantity_packung'] : "";?>"
                           style="min-width: 20px;margin-right: 10px;">
                </div>

                <div class="receive_packung" style="width: 100%; float:left;">
                    <label for="receive_packung<?php echo $key; ?>"
                           style="width: 50%;margin: 0;padding: 0 10px;float:left;">Số lượng Packung nhận</label>
                    <input type="number" name="receive_packung_<?php echo $key; ?>"
                           style="width: 50%;min-width: 150px; float:left;"
                           value="<?php echo isset($item['receive_packung']) ? $item['receive_packung'] : "";?>">
                </div>
            </div>

            <div class="verpackung" style="float: left;width: 50%;padding: 0 10px;">
                <div class="infor_verpackung" style="float: left;width: 70%">
                    <div class="variant_verpackung"><?php echo $item['option2']; ?></div>
                    <div class="sku_verpackung"><?php echo "SKU:" . $item['sku2']; ?></div>
                </div>
                <div class="quantity_verpackung" style="float: left;width: 30%">
                    <input type="number" name="quantity_verpackung_<?php echo $key; ?>"
                           value="<?php echo isset($item['quantity_verpackung'])? $item['quantity_verpackung']: "";?>"
                           style="min-width: 20px;margin-right: 10px;">
                </div>
                <div class="receive_verpackung">
                    <label for="receive_verpackung<?php echo $key; ?>"
                           style="width: 51%;margin: 0;padding: 0 10px;float:left;">Số lượng Verpackung nhận</label>
                    <input type="number" name="receive_verpackung_<?php echo $key;?>"
                           style="width: 49%;min-width: 150px;float:left;"
                           value="<?php echo isset($item['receive_verpackung']) ? $item['receive_verpackung'] : "";?>">
                </div>
            </div>
        </div>

        <br/>
        <br/>
    </div>

<?php  }

}?>