<nav class="nav"><?=$top_menu; ?></nav>
<section class="lot-item container">
    <h2><?=text_clean($lot['lot_name'])?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?=text_clean($lot['img_src'])?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?=text_clean($lot['cat_name'])?></span></p>
            <p class="lot-item__description"><?=text_clean($lot['descr'])?></p>
        </div>
        <div class="lot-item__right">
            <?php if (isset($_SESSION['user'])): ?>
            <div class="lot-item__state">
                <div class="lot-item__timer timer"><?=lot_lifetime()?></div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?=text_clean($lot['MAX(lr.rate)'] + $lot['start_price'])?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=text_clean($lot['MAX(lr.rate)'] + $lot['start_price'] + $lot['price_step'])?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
