<ul class="nav__list container">
<?php foreach ($menu as $key => $value): ?>
    <li class="nav__item">
        <a href="all-lots.php?category=<?=strip_tags($value['id'])?>"><?=$value['cat_name']?></a>
    </li>
<?php endforeach?>
</ul>

