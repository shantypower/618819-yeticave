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
                <div class="lot-item__state">
                    <div class="lot-item__timer timer"><?=lot_lifetime()?></div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?=text_clean($current_price)?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?=text_clean($lot['MAX(lr.rate)'] + $lot['start_price'] + $lot['price_step'])?></span>
                        </div>
                    </div>
                </div>
                <?php if ($is_auth == 1 && ($user_id !== $lot['author_id']) && ($lot['date_end'] < date_create('now')) && !$isRate): ?>
                <form class="lot-item__form" action="lot.php?id=<?=$lot['id'];?>" method="post">
                    <?php $classname = isset($errors['cost']) ? "form__item--invalid" : "";
                    $value = isset($lot['cost']) ? $lot['cost'] : "";
                    $error = isset($errors['cost']) ? $errors['cost'] : "";?>
                    <p class="lot-item__form-item form__item <?=$classname;?>">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost" value="<?=$value;?>" placeholder="<?=text_clean($lot['MAX(lr.rate)'] + $lot['start_price'] + $lot['price_step'])?>">
                        <span class="form__error"><?=$error; ?></span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
            <?php endif; ?>
            <div class="history">
                <h3>История ставок (<span><?=$rates_count;?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($rates as $item): ?>
                        <tr class="history__item">
                            <td class="history__name"><?=$item['user_name'] ?></td>
                            <td class="history__price"><?=text_clean($item['rate'])?></td>
                            <td class="history__time"><?=text_clean(humanDate($item['date_add'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>
