<?php
$is_auth = 0;
$user_name = '';
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);

$adverts = getAllLots($link);
$lot = '';
$page_content = '';
$top_menu = include_template('menu.php', ['menu' => $categories]);
$id = (int)$_GET['id'];
$result = getLotById($id, $categories, $adverts, $link);

if (!$result) {
    $error = mysqli_error($link);
    $page_content = include_template('error.php', ['error' => $error]);
    print(showContent($categories, $page_content, $user_name, $is_auth, $error));
}

$lot = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (count($lot) == 0) {
    http_response_code(404);
    $page_content = include_template('error.php', ['error' => '<h2>404 Страница не найдена</h2><p>Данной страницы не существует на сайте.</p>']);
    print(showContent($categories, $page_content, $user_name, $is_auth, '404 Страница не найдена'));
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rates = $_POST;
    $required = ['cost'];
    $dict = ['cost' => 'Сумма ставки'];
    $errors = [];
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (!is_numeric($_POST['cost'])) {
        $errors['cost'] = 'Сумма ставки должна быть числом';
    } else if ($_POST['cost'] <= 0) {
        $errors['cost'] = 'Сумма ставки должна быть больше нуля';
    } else if ($_POST['cost'] < ($lot[0]['MAX(lr.rate)'] + $lot[0]['start_price'] + $lot[0]['price_step'])) {
        $errors['cost'] = 'Сумма ставки должна быть больше текущей + шаг торгов';
    }

    if (count($errors)) {
        $page_content = include_template('lot.php', [
            'top_menu' => $top_menu,
            'lot' => $lot[0],
            'rates' => $rates,
            'cost' => $rates['cost'],
            'errors' => $errors,
            'dict' => $dict,
            'is_auth' => $is_auth
        ]);
        print(showContent($categories, $page_content, $user_name, $is_auth, 'Введена неверная цена'));
        return;
    } else {
        $sql = 'INSERT INTO lot_rates (date_add, rate, user_id, lot_id) VALUES (NOW(), ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [$_POST['cost'], $_SESSION['user'][0]['id'], $id]);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            header("Location: lot.php?id=" . $id);
        } else {
            $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
            print(showContent($categories, $page_content, $user_name, $is_auth, mysqli_error($link)));
        }
    }
}
$page_content = include_template('lot.php', ['top_menu' => $top_menu, 'lot' => $lot[0], 'is_auth' => $is_auth, 'user_name' => $user_name, 'content' => $page_content]);
print(showContent($categories, $page_content, $user_name, $is_auth, $lot[0]['lot_name']));
