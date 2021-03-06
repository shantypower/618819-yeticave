<nav class="nav"><?=$top_menu; ?></nav>
<section class="lots">
    <h2>Результаты поиска по запросу «<span><?=$search?></span>»</h2>
    <ul class="lots__list">
        <?php foreach ($lots as $key => $item):?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=textClean($item['img_src'])?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=textClean($item['cat_name'])?></span>
                <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?=$item['id'];?>"><?=textClean($item['lot_name'])?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount"><?=priceFormat(textClean($item['MAX(lr.rate)'] + $item['start_price'])).' &#8381;'?></span>
                        <span class="lot__cost"><?=priceFormat(textClean($item['MAX(lr.rate)'] + $item['start_price'] + $item['price_step']))?><b class="rub">р</b></span>
                    </div>
                    <div class="lot__timer timer"><?=LotLifetime($item['date_end'])?></div>
                </div>
            </div>
        </li>
        <?php endforeach?>
    </ul>
</section>
<?php if ($pages_count > 1):
$href_prev = ($current_page-1 > 0) ? "search.php?search=".$search."&page=".($current_page - 1) : "";
$href_next = ($current_page+1 <= count($pages)) ? "search.php?search=".$search."&page=".($current_page + 1) : "";
    ?>
<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev"><a href=<?=$href_prev;?>>Назад</a></li>
    <?php foreach ($pages as $page): ?>
        <li class="pagination__item <?php if ((int) $page === (int) $current_page): ?>pagination__item--active<?php endif; ?>">
            <a href="search.php?search=<?=$search.'&page='.$page;?>"><?=$page;?></a>
        </li>
    <?php endforeach; ?>
    <li class="pagination-item pagination-item-next"><a href=<?=$href_next;?>>Вперед</a></li>
</ul>
<?php endif; ?>
