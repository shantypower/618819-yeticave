<?php
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);
$adverts = getAllLots($link);
$errors = [];
$search = '';
if ($isConnect === false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}
$menu = includeTemplate('menu.php', ['menu' => $categories]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    if (!empty($form['email']) && filter_var($form['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Введите корректный e-mail';
    }

    if (!count($errors)) {
        $user = getUserByEmail($form['email'], $link);
        if ($user && password_verify($form['password'], $user[0]['user_pass'])) {
            $_SESSION['id'] = $user[0]['id'];
            $user_data['is_auth'] = 1;
            header("Location: /index.php");
            exit();
        }
        $errors['password'] = 'Вы ввели неверный пароль';
        if (!$user) {
            $errors['email'] = 'Пользователь с таким e-mail не найден';
        }
    }

$page_content = includeTemplate('login.php', ['top_menu' => $menu, 'form' => $form, 'errors' => $errors]);
print(showContent($categories, $page_content, $user_data, $search, 'Ошибка входа'));
return;
}
if (isset($_SESSION['id'])) {
    $page_content = includeTemplate('index.php', [
        'categories' => $categories,
        'adverts' => $adverts,
        'user_data' => $user_data]);
} else {
    $page_content = includeTemplate('login.php', ['top_menu' => $menu]);
}
print(showContent($categories, $page_content, $user_data, $search, 'Авторизация'));
