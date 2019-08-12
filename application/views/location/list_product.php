<?php
/**
 * Created by PhpStorm.
 * User: vantinhchu
 * Date: 10.12.18
 * Time: 16:59
 */

?>
<div class="widget-content">
    <h2><?php echo $title; ?></h2>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Barcode Le</th>
                <th>Inventory Le</th>
                <th>Barcode Si</th>
                <th>Inventory Si</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($record_data as $item) { ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['title']; ?></td>
                    <td><?php echo $item['barcode1']; ?></td>
                    <td><?php echo $item['inventory_quantity1']; ?></td>
                    <td><?php echo $item['barcode2']; ?></td>
                    <td><?php echo $item['inventory_quantity2']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

