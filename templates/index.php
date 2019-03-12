<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $key => $value): ?>

        <li class="promo__item <?=$value['css_cl']?>">
            <a class="promo__link" href="all-lots.php?category=<?=strip_tags($value['id'])?>"><?=$value['cat_name']?></a>
        </li>
        <?php endforeach?>
    </ul>
</section>

<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div><!--  -->
    <ul class="lots__list">
        <?php if (isset($adverts)) :?>
            <?php foreach ($adverts as $key => $item):?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=textClean($item['img_src'])?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=textClean($item['cat_name'])?></span>
                <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?=$item['id']; ?>"><?=textClean($item['lot_name'])?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount"><?=priceFormat(textClean($item['MAX(lr.rate)'] + $item['start_price'])).' &#8381;'?></span>
                        <span class="lot__cost"><?=priceFormat(textClean($item['MAX(lr.rate)'] + $item['start_price'] + $item['price_step']))?><b class="rub">р</b></span>
                    </div>
                    <div class="lot__timer timer"><?=LotLifetime()?></div>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
        <?php endif;?>
    </ul>
</section>
