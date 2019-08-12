<div class="side-options">
    <ul>
        <li>
            <a href="#" id="collapse-nav" class="act act-primary tip" title="Ẩn/Hiện">
                <i class="icon16 i-arrow-left-7"></i>
            </a>
        </li>
    </ul>
</div>
<?php //echo '<pre>';var_dump($menu_data);die;?>
<div class="sidebar-wrapper">
    <nav id="mainnav">
        <ul class="nav nav-list">
            <?php renderCategory($menu_data); ?>
            <?php function renderCategory($categoryList = array()){ ?>
                <?php foreach ($categoryList as $item) { ?>
                    <?php $isChild = (isset($item["child"]) && $item["child"] && count($item["child"]) > 0) ;?>
                    <li>
                        <a class="<?php echo isset($item["class"]) ? $item["class"] : ''; ?>"
                            href="<?php echo ($isChild) ? '#' : $item['url']; ?>"
                            title="<?php echo $item["text"] ?>">
                            <span class="icon">
                                <i class="icon20 <?php echo $item["icon"] ?>"></i>
                            </span>
                            <span class="txt"><?php echo $item["text"] ?></span>
                        </a>
                        <?php if ($isChild) { ?>
                            <ul class="sub">
                                <?php renderCategory($item["child"]); ?>
                            </ul>
                        <?php } ?>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </nav>
</div>