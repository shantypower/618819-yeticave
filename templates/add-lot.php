<form class="form form--add-lot container form--invalid" action="add.php" method="POST" enctype="multipart/form-data">
      <h2>Добавление лота</h2>
      <div class="form__container-two">
      <?php $classname = isset($errors['lot-name']) ? "form__item--invalid" : "";
       $value = isset($lot['lot-name']);
       $error = isset($errors['lot-name']) ? $errors['lot-name'] : "";?>
        <div class="form__item <?=$classname;?>">
          <label for="lot-name">Наименование</label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=$value;?>" >
          <span class="form__error"><?=$error?></span>
        </div>
        <?php $classname = isset($errors['category']) ? "form__item--invalid" : "";
        $value = isset($lot['category']);
        $error = isset($errors['category']) ? $errors['category'] : ""; ?>
        <div class="form__item">
          <label for="category">Категория</label>
          <select id="category" name="category" >
            <option>Выберите категорию</option>
            <?php foreach ($categories as $item): ?>
            <option value="<?=$item['id']?>"<?php $select = ($value == $item['id']) ? 'selected' : ""; ?><?=$select?>><?=$item['cat_name'];?></option>
            <?php endforeach?>
          </select>
          <span class="form__error"><?=$error?></span>
        </div>
      </div>
      <div class="form__item form__item--wide <?=$classname;?>">
      <?php $classname = isset($errors['description']) ? "form__item--invalid" : "";
      $value = isset($lot['description']);
      $error = isset($errors['message']) ? $errors['message'] : ""; ?>
        <label for="message">Описание</label>
        <textarea id="message" name="message" placeholder="Напишите описание лота" ><?=$value;?></textarea>
        <span class="form__error"><?=$error?></span>
      </div>
      <?php $classname = isset($errors['file']) ? "form__item--invalid" : "form__item--uploaded";
      $value = isset($lot['path']) ? 'img/' . $lot['path'] : "";
      $error = isset($errors['file']) ? $errors['file'] : "";?>
      <div class="form__item form__item--file <?=$classname;?>">
        <label>Изображение</label>
        <div class="preview">
          <button class="preview__remove" type="button">x</button>
          <div class="preview__img">
            <img src="<?=$value;?>" width="113" height="113" alt="Изображение лота">
          </div>
        </div>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="photo2" name="lot_img" value="">
          <label for="photo2">
            <span>+ Добавить</span>
          </label>
        </div>
      </div>
      <div class="form__container-three">
      <?php $classname = isset($errors['lot-rate']) ? "form__item--invalid" : "";
      $value = isset($lot['lot-rate']);
      $error = isset($errors['lot-rate']) ? $errors['lot-rate'] : "";?>
        <div class="form__item form__item--small <?=$classname;?>">
          <label for="lot-rate">Начальная цена</label>
          <input id="lot-rate" type="number" name="lot-rate" placeholder="0" value="<?=$value;?>">
          <span class="form__error"><?=$error?></span>
        </div>
        <?php $classname = isset($errors['lot-step']) ? "form__item--invalid" : "";
        $value = isset($lot['lot-step']);
        $error = isset($errors['lot-step']) ? $errors['lot-step'] : "";?>
        <div class="form__item form__item--small <?=$classname;?>">
          <label for="lot-step">Шаг ставки</label>
          <input id="lot-step" type="number" name="lot-step" placeholder="0" value="<?=$value;?>" >
          <span class="form__error"><?=$error?></span>
        </div>
        <?php $classname = isset($errors['lot-date']) ? "form__item--invalid" : "";
        $value = isset($lot['lot-date']);
        $error = isset($errors['lot-date']) ? $errors['lot-date'] : "";?>
        <div class="form__item <?=$classname?>">
          <label for="lot-date">Дата окончания торгов</label>
          <input class="form__input-date" id="lot-date" type="date" name="lot-date" value="<?=$value;?>" >
          <span class="form__error"><?=$error?></span>
        </div>
      </div>
      <?php if (isset($errors)): ?>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <ul>
        <?php foreach($errors as $err => $val): ?>
        <li><strong><?=$dict[$err];?>:</strong><?=' ' . $val;?></li>
        <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <button type="submit" class="button">Добавить лот</button>
    </form>
