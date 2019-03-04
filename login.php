<?php
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);
$adverts = getAllLots($link);
$errors = [];
$search = '';
$menu = include_template('menu.php', ['menu' => $categories]);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;

    $required = [
        'email',
        'password',
    ];

    foreach ($required as $key) {
        if (empty($form[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }
    if (filter_var($form['email'], FILTER_VALIDATE_EMAIL) == false) {
        $errors['email'] = 'Введите корректный e-mail';
    }
    $user = getUserByEmail($form['email'], $link);
    if (!count($errors) && $user) {

        if (password_verify($form['password'], $user['user_pass'])) {
            $_SESSION['id'] = $user['id'];
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Пользователь с таким e-mail не найден';
    }
    if (count($errors)) {
        $page_content = include_template('login.php', ['top_menu' => $menu, 'form' => $form, 'errors' => $errors]);
        print(showContent($categories, $page_content, $user_data, $search, 'Ошибка входа'));
        return;
    } else {
        $user_data['is_auth'] = 1;
        header("Location: /index.php");
        exit();
    }
}
if (isset($_SESSION['id'])) {
    $page_content = include_template('index.php', [
        'categories' => $categories,
        'adverts' => $adverts,
        'user_data' => $user_data]);
}
else {
    $page_content = include_template('login.php', ['top_menu' => $menu]);
}
print(showContent($categories, $page_content, $user_data, $search, 'Авторизация'));
