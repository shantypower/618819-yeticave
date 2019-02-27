<?php
session_start();
require_once('data.php');
require('db_connection.php');
require_once('functions.php');
$categories = getAllCategories($link);
$adverts = getAllLots($link);
$errors = [];
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
    $res = getUserByEmail($form['email'], $link);
    $user = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : null;
    if (!count($errors) and $user) {

        if (password_verify($form['password'], $user[0]['user_pass'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Такой пользователь не найден';
    }
    if (count($errors)) {
        $page_content = include_template('login.php', ['top_menu' => $menu, 'form' => $form, 'errors' => $errors]);
        print(showContent($categories, $page_content, 'Ошибка входа'));
    } else {
        header("Location: /index.php");
        exit();
    }
}
if (isset($_SESSION['user'])) {
    $user_name = $_SESSION['user'][0]['user_name'];
    $page_content = include_template('index.php', [
        'categories' => $categories,
        'adverts' => $adverts,
        'user_name' => $user_name]);
}
else {
    $page_content = include_template('login.php', ['top_menu' => $menu]);
}

print(showContent($categories, $page_content, 'Авторизация'));
