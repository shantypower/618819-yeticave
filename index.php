<?php
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require('core/data.php');
require_once('core/functions.php');
require('core/db_connection.php');
$page_content = '';
$categories =  [];
$adverts = [];
$search = '';
if ($isConnect == false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}

$categories = getAllCategories($link);
if (!$categories) {
    $error = mysqli_error($link);
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
};

$adverts = getAllLots($link);
if (!$adverts) {
    $error = mysqli_error($link);
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
};

$page_content = include_template('index.php', [
    'categories' => $categories,
    'adverts' => $adverts
]);
require_once('getwinner.php');
print(showContent($categories, $page_content, $user_data, $search, 'YetiCave - Главная страница'));
