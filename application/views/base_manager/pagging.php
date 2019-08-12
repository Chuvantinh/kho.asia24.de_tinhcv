<ul class="e_data_paginate">
    <?php
    if ($current_page == 1) {
        echo '<li data-page="1" class="disabled"><a class="to_first disable" href="#">← First</a></li>';
    } else {
        echo '<li><a data-page="1" class="to_first" href="' . $link . $key . '=1" >← First</a></li>';
    }
    if ($total_page <= $page_link_display) {
        //Hiển thị tất cả
        for ($i = 1; $i < ($total_page + 1); $i++) {
            if ($i == $current_page) {
                echo '<li class="active"><a data-page="' . $i . '" href="#">' . $i . '</a></li>';
            } else {
                echo '<li><a data-page="' . $i . '" href="' . $link . $key . '=' . $i . '">' . $i . '</a></li>';
            }
        }
    } else {
        if ($current_page > intval($page_link_display / 2) && $current_page < $total_page - intval($page_link_display / 2)) {
            //Hiển thị mỗi 1/2 ở mỗi đầu trước ($page_link_display / 2)-1 => $current_page
            for ($i = 0; $i < (intval($page_link_display / 2)); $i++) {
                echo '<li><a data-page="' . ($current_page - intval($page_link_display / 2) + $i) . '" href="' . $link . $key . '=' . ($current_page - intval($page_link_display / 2) + $i) . '">' . ($current_page - intval($page_link_display / 2) + $i) . '</a></li>';
            }
            //Hiển thị giữa
            echo '<li class="active"><a data-page=' . $current_page . ' href="#">' . $current_page . '</a></li>';
            //Hiển thị mỗi 1/2 ở mỗi đầu sau $current_page + 1 => $page_link_display / 2
            for ($i = 1; $i < (intval($page_link_display / 2) + 1); $i++) {
                echo '<li><a data-page="' . ($current_page + $i) . '" href="' . $link . $key . '=' . ($current_page + $i) . '">' . ($current_page + $i) . '</a></li>';
            }
        } elseif ($current_page <= intval($page_link_display / 2)) {
            //Hiển thị tất cả $page_link_display ở đầu trước
            for ($i = 1; $i < ($page_link_display + 1); $i++) {
                if ($i == $current_page) {
                    echo '<li class="active"><a data-page="' . $i . '" href="#">' . $i . '</a></li>';
                } else {
                    echo '<li><a data-page="' . $i . '"  href="' . $link . $key . '=' . $i . '">' . $i . '</a></li>';
                }
            }
        } elseif ($current_page >= $total_page - intval($page_link_display / 2)) {
            //Hiển thị tất cả $page_link_display ở đầu sau
            for ($i = 1; $i < ($page_link_display + 1); $i++) {
                if (($total_page - $page_link_display + $i) == $current_page) {
                    echo '<li class="active"><a data-page="' . ($total_page - $page_link_display + $i) . '" href="#">' . ($total_page - $page_link_display + $i) . '</a></li>';
                } else {
                    echo '<li><a data-page="' . ($total_page - $page_link_display + $i) . '" href="' . $link . $key . '=' . ($total_page - $page_link_display + $i) . '">' . ($total_page - $page_link_display + $i) . '</a></li>';
                }
            }
        }
    }
    if ($current_page == $total_page) {
        echo '<li class="disabled"><a data-page="' . $total_page . '" class="to_end disable">Last → </a></li>';
    } else {
        echo '<li><a data-page="' . $total_page . '" class="to_end" href="' . $link . $key . '=' . $total_page . '">Last →</a></li>';
    }
    ?>
</ul>