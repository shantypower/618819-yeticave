<?php
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$search = '';
$categories = [];
$page_content = '';

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

$top_menu = include_template('menu.php', ['menu' => $categories]);
$cat = $_GET['category'];
$lots = getLotsByCategory($link, $cat);
if (!$lots) {
    print(showError($categories, $page_content, $user_data, $search,'<h2>Нет товаров в выбранной категории</h2>'));
    return;
}

$page_content = include_template('all-lots.php', [
    'top_menu' => $top_menu,
    'lots' => $lots,
    'categories' => $categories
]);

print(showContent($categories, $page_content, $user_data, $search, $cat));
