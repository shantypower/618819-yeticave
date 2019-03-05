<nav class="nav"><?=$top_menu; ?></nav>
<section class="lots">
<h2>Все лоты в категории <span>«<?=$lots[0]['cat_name'];?>»</span></h2>
    <ul class="lots__list">
        <?php foreach ($lots as $key => $item):?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=text_clean($item['img_src'])?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=text_clean($item['cat_name'])?></span>
                <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?=$item['id'];?>"><?=text_clean($item['lot_name'])?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount"><?=price_format(text_clean($item['MAX(lr.rate)'] + $item['start_price'])).' &#8381;'?></span>
                        <span class="lot__cost"><?=price_format(text_clean($item['MAX(lr.rate)'] + $item['start_price'] + $item['price_step']))?><b class="rub">р</b></span>
                    </div>
                    <div class="lot__timer timer"><?=lot_lifetime()?></div>
                </div>
            </div>
        </li>
        <?php endforeach?>
    </ul>
</section>
