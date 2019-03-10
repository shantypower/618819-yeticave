<nav class="nav"><?=$top_menu; ?></nav>
<section class="rates container">
    <table class="rates__list">
      <?php foreach ($rates as $item): ?>
          <?php $value = '';
                $classname_item = "";
                $classname_timer = "";
                $contacts="";
          if (check_remaintime($item['date_end'])) {
              $classname_item = "";
              $value = lot_lifetime();
              $classname_timer = "timer--finishing";
          } else if (($user_id === $item['winner_id'])) {
              $classname_item = "rates__item--win";
              $value = "Ставка победила";
              $classname_timer = "timer--win";
              $contacts='<p> Контакты: '.$item['contacts'].'</p>';
          } else {
              $classname_item = "rates__item--end";
              $value = "Торги закончены";
              $classname_timer = "timer--end";
          };?>
        <tr class="rates__item <?=$classname_item ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?=text_clean($item['img_src'])?>" width="54" height="40" alt="">
            </div>
            <div>
              <h3 class="rates__title"><a href="lot.php?id=<?=$item['lot_id']?>"><?=$item['lot_name']?></a></h3>
              <p><?=$item['contacts']?></p>
              <p><?=$item['email']?></p>
            </div>
          </td>
          <td class="rates__category">
            <?=$item['cat_name']?>
          </td>

          <td class="rates__timer">
            <div class="timer <?=$classname_timer ?>"><?=$value?></div>
          </td>
          <td class="rates__price">
            <?=price_format(text_clean($item['rate'])) . ' р.' ?>
          </td>
          <td class="rates__time">
            <?=humanDate($item['date_add'])?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
</section>
