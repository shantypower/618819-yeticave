<nav class="nav"><?=$top_menu; ?></nav>
<form class="form container" action="https://echo.htmlacademy.ru" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>
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
    <div class="form__item form__item--last <?=$classname;?>">
    <label for="password">Пароль*</label>
    <input id="password" type="password" name="password" placeholder="Введите пароль">
    <span class="form__error"><?=$error;?></span>
    </div>
    <?php if (isset($errors)): ?>
    <span class="form__error form__error--bottom">Неверное имя пользователя или пароль</span>
    <?php endif; ?>
    <button type="submit" class="button">Войти</button>
</form>
