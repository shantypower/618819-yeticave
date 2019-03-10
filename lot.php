<?php
$isRate = false;
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);

$adverts = getAllLots($link);
$lot = '';
$search = '';
$page_content = '';
$current_price = 0;

if ($isConnect == false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}

$top_menu = includeTemplate('menu.php', ['menu' => $categories]);
$id = (int)$_GET['id'];
$rates = getRatesForLot($id, $link);
$rates_count = count($rates);
$lot = getLotById($id, $link);

if (!$lot) {
    print(showError($categories, $page_content, $user_data, $search, '<h2>404 Страница не найдена</h2><p>Данной страницы не существует на сайте.</p>'));
    return;
}

$current_price = $lot['MAX(lr.rate)'] ? $lot['MAX(lr.rate)'] : $lot['start_price'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rate = $_POST;
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
    } elseif ($_POST['cost'] <= 0) {
        $errors['cost'] = 'Сумма ставки должна быть больше нуля';
    } elseif ($_POST['cost'] < ($lot['MAX(lr.rate)'] + $lot['start_price'] + $lot['price_step'])) {
        $errors['cost'] = 'Сумма ставки должна быть больше текущей + шаг торгов';
    }

    if (count($errors)) {
        $page_content = includeTemplate('lot.php', [
            'top_menu' => $top_menu,
            'lot' => $lot,
            'rates' => $rates,
            'errors' => $errors,
            'dict' => $dict,
            'isRate' => $isRate,
            'user_data' => $user_data,
            'rates_count' => $rates_count,
            'current_price' => $current_price
        ]);
        print(showContent($categories, $page_content, $user_data, $search, 'Введена неверная цена'));
        return;
    } else {
        $sql = 'INSERT INTO lot_rates (date_add, rate, user_id, lot_id) VALUES (NOW(), ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [$_POST['cost'], $user_data['id'], $id]);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            header("Location: lot.php?id=" . $id);
        } else {
            $page_content = includeTemplate('error.php', ['error' => mysqli_error($link)]);
            print(showContent($categories, $page_content, $user_data, $search, mysqli_error($link)));
        }
    }
}
if (isset($user_data['id'])) {
    $isRate = checkUserRated($id, $user_data['id'], $link);
}

$page_content = includeTemplate('lot.php', [
    'top_menu' => $top_menu,
    'lot' => $lot,
    'isRate' => $isRate,
    'current_price' => $current_price,
    'user_data' => $user_data,
    'rates' => $rates,
    'rates_count' => $rates_count,
    'content' => $page_content
]);
print(showContent($categories, $page_content, $user_data, $search, $lot['lot_name']));
