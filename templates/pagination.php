<?php if ($pages_count > 1):
$href_prev = ($current_page-1 > 0) ? "all-lots.php?category=".$cat."&page=".($current_page - 1) : "";
$href_next = ($current_page+1 <= count($pages)) ? "all-lots.php?category=".$cat."&page=".($current_page + 1) : "";
    ?>
<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev"><a href=<?=$href_prev;?>>Назад</a></li>
    <?php foreach ($pages as $page): ?>
        <li class="pagination__item <?php if ($page == $current_page): ?>pagination__item--active<?php endif; ?>">
            <a href="all-lots.php?category=<?=$cat.'&page='.$page;?>"><?=$page;?></a>
        </li>
    <?php endforeach; ?>
    <li class="pagination-item pagination-item-next"><a href=<?=$href_next;?>>Вперед</a></li>
</ul>
<?php endif; ?>
