<!DOCTYPE html>
<html lang="vi" prefix="og: http://ogp.me/ns#">
    <head>
        <?php echo $header_base; ?>
        <?php echo $header_master_page; ?>
        <?php echo $header_page; ?>
    </head>
    <body barack="<?php echo $this->json_item_barack; ?>">
        <?php echo $content; ?>
    </body>
</html>