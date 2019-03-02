<?php
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require('core/data.php');
require_once('core/functions.php');
require('core/db_connection.php');
$page_content = '';
$categories = '';
$adverts ='';
if ($isConnect == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
    print(showContent($categories, $page_content, $user_data, 'Ошибка'));
    return;
}
$categories = getAllCategories($link);
if (count($categories) == 0) {
    $error = mysqli_error($link);
    $page_content = include_template('error.php', ['error' => $error]);
    print(showContent($categories, $page_content, $user_data, 'Ошибка'));
    return;
};
$adverts = getAllLots($link);
if (count($adverts) == 0) {
    $error = mysqli_error($link);
    $page_content = include_template('error.php', ['error' => $error]);
    print(showContent($categories, $page_content, $user_data, 'Ошибка'));
    return;
};
$page_content = include_template('index.php', [
    'categories' => $categories,
    'adverts' => $adverts
]);

print(showContent($categories, $page_content, $user_data, 'YetiCave - Главная страница'));
