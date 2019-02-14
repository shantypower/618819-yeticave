<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $value): ?>
        <li class="promo__item promo__item--boards">
            <a class="promo__link" href="pages/all-lots.html"><?=$value?></a>
        </li>
        <?php endforeach?>
    </ul>
</section>
<section class="lot-item container">
    <h2>Ошибка</h2>
    <p><?=$error?></p>
</section>
