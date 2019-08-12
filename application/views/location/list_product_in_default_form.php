<div class="widget-content">
    <div class="table-responsive" style="width: 790px;">
        <?php if(isset($location_null) && $location_null != "") { ?>
            <div class="location_null"><b><?php echo "Vi trí ".$location_null." chưa có sản phẩm nào"; ?></b></div>
        <?php } ?>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Vị Trí</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($record_data as $item) { ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['title']; ?></td>
                    <td><?php echo $item['location']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

