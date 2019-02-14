<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $key => $value): ?>

        <li class="promo__item <?=$value['css_cl']?>">
            <a class="promo__link" href="pages/all-lots.html"><?=$value['cat_name']?></a>
        </li>
        <?php endforeach?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($adverts as $key => $item):?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=text_clean($item['img_src'])?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=text_clean($item['cat_name'])?></span>
                <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=text_clean($item['lot_name'])?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount"><?=price_format(text_clean($item['start_price'])).' &#8381;'?></span>
                        <span class="lot__cost"><?=price_format(text_clean($item['MAX(lr.rate)']))?><b class="rub">р</b></span>
                    </div>
                    <div class="lot__timer timer"><?=lot_lifetime()?></div>
                </div>
            </div>
        </li>
        <?php endforeach?>
    </ul>
</section>
