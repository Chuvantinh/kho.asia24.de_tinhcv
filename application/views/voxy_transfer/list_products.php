
<?php
$i = 0;
foreach ($list_products as $key => $item){
    $i++;
    if($i % 2 == 0){
        $class = "gerade";
    }else{
        $class = "ungerade";
    }
    $_str_readonly = ' readonly=readonly style="background: #d9d9d9"';
    ?>
    <div  class="infomation <?php echo $class; ?>" style="padding: 5px 0; display: block;width: 100%;float: left;">
        <input type='hidden' name='information[<?php echo $item['variant_id'];?>][variant_id]' value='<?php echo $item['variant_id'];?>'>
        <input type='hidden' name='information[<?php echo $item['variant_id'];?>][cat_id]' value='<?php echo $item['cat_id'];?>'>
        <input type='hidden' name='information[<?php echo $item['variant_id'];?>][product_id]' value='<?php echo $item['product_id'];?>'>
        <!--        <input type='hidden' name='information[--><?php //echo $item['variant_id'];?><!--][sl_kho]' value='--><?php //echo $item['sl_kho'];?><!--'>-->
        <input type='hidden' name='information[<?php echo $item['variant_id'];?>][variant_title]' value='<?php echo $item['variant_title'];?>'>
        <!--        <input type='hidden' name='information[--><?php //echo $item['variant_id'];?><!--][location]' value='--><?php //echo $item['location'];?><!--'>-->
        <input type='hidden' name='information[<?php echo $item['variant_id'];?>][title]' value='<?php echo $item['title'];?>'>
        <input type='hidden' name='information[<?php echo $item['variant_id'];?>][sku]' value='<?php echo $item['sku'];?>'>

        <div style="width: 2%;float: left;text-align: left;"><?php echo $i; ?></div>
        <div class="remove" style="width: 5%;float:left">
            <i class="material-icons" title="xóa" style="color: #e47885">close</i>
        </div>
        <div style='width: 7%;height: auto;float: left;text-align: left !important;' class='sku'><?php echo $item['sku'] ?></div>
        <div style='width: 45%;height: auto;float: left; text-align: center;margin-right: 10px;' class='title'><?php echo $item['title'];?></div>
        <div style='width: 10%;height: auto;float: left' class='quantity'>
            <input type='text' name='quantity[<?php echo $item['variant_id'];?>]'
                   value="<?php echo $item['quantity'];?>"
                   class='quantity' style='min-width:50px;width: 50px;text-align: center;' <?php echo $_str_readonly; ?> >
        </div>
        <div style='width: 10%;height: auto;float: left;text-align: left !important;' class='variant_title'><?php echo $item['variant_title'];?></div>

        <div style='width: 10%;height: auto;float: left' class='gianhapnew'>
            <input type='text' name='gianhapnew[<?php echo $item['variant_id'];?>]'
                   value="<?php echo (isset($item['gianhapnew'])?$item['gianhapnew']:0);?>"
                   class='input_gianhapnew' style='min-width:50px;width: 50px;text-align: center;' <?php echo $_str_readonly; ?> >
        </div>

        <div style='width: 7%;height: auto;float: left' class='giabannew'>
            <input type='text' name='giabannew[<?php echo $item['variant_id'];?>]'
                   value="<?php echo (isset($item['giabannew'])?$item['giabannew']:0);?>"
                   class='input_giabannew' style='min-width:50px;width: 50px;text-align: center;' <?php echo $_str_readonly; ?> >
        </div>
    </div>
<?php } ?>
