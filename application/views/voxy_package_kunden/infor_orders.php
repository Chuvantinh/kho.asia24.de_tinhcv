<div class="list_result">
<div class="child_title">
    <p>STT</p>
    <p class="order-number">Đơn hàng</p>
    <p class="total-price">Tổng tiền</p>
    <p class="tongtien-no">Còn nợ</p>
    <p class="tongtien-no">Ngày giao hàng</p>
</div>


<?php
if($infor){
foreach ($infor as $key => $item) {
    if($item['tongtien_no'] == ""){
        $item['tongtien_no'] = 0;
    }
    ?>
    <div class="element">
        <p><?php echo $key + 1; ?></p>
        <p class="order-number"><?php echo $item['order_number']; ?></p>
        <p class="total-price"><?php echo $item['total_price']. " €"; ?></p>
        <p class="tongtien-no"><?php echo $item['tongtien_no']." €"; ?></p>
        <p class="shipped_at"><?php echo $item['shipped_at']; ?></p>
    </div>
<?php }
}else{ ?>
    <p>Không có dữ liệu</p>
<?php } ?>
</div>

