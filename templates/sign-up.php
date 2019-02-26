<?php $classname = isset($errors) ? "form--invalid" : "";?>
<form class="form container <?=$classname;?>" action="sign-up.php" method="post" enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>
    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    $value = isset($user['email']) ? $user['email'] : "";
    $error = isset($errors['email']) ? "Введите e-mail" : "";?>
        <div class="form__item <?=$classname;?>">
            <label for="email">E-mail*</label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$value;?>">
            <span class="form__error"><?=$error;?></span>
        </div>
    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    $error = isset($errors['password']) ? "Введите пароль" : "";?>
        <div class="form__item <?=$classname;?>">
            <label for="password">Пароль*</label>
            <input id="password" type="text" name="password" placeholder="Введите пароль">
            <span class="form__error"><?=$error;?></span>
        </div>
    <?php $classname = isset($errors['name']) ? "form__item--invalid" : "";
    $value = isset($user['name']) ? $user['name'] : "";
    $error = isset($errors['name']) ? "Введите имя" : "";?>
        <div class="form__item <?=$classname;?>">
            <label for="name">Имя*</label>
            <input id="name" type="text" name="name" placeholder="Введите имя" value="<?=$value;?>">
            <span class="form__error"><?=$error;?></span>
        </div>
    <?php $classname = isset($errors['message']) ? "form__item--invalid" : "";
    $value = isset($user['message']) ? $user['message'] : "";
    $error = isset($errors['message']) ? "Напишите как с вами связаться" : "";?>
        <div class="form__item <?=$classname;?>">
            <label for="message">Контактные данные*</label>
            <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?=$value;?></textarea>
            <span class="form__error"><?=$error;?></span>
        </div>
    <?php $classname = isset($errors['file']) ? "form__item--invalid" : "form__item--uploaded";
    $value = isset($user['path']) ? 'img/' . $user['path'] : "";
    $error = isset($errors['file']) ? $errors['file'] : "";?>
        <div class="form__item form__item--file form__item--last <?=$classname;?>">
            <label>Аватар</label>
            <div class="preview">
                <button class="preview__remove" type="button">x</button>
                <div class="preview__img">
                    <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
                </div>
            </div>
        </div>
    <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" name="photo2" value="<?=$value;?>">
        <label for="photo2">
            <span>+ Добавить</span>
        </label>
    </div>
    </div>

    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php if (isset($errors)): ?>
        <ul>
            <?php foreach ($errors as $err => $val): ?>
                <li><strong><?=$dict[$err];?>:</strong><?=' ' . $val;?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
</form>
