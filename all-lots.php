<?php
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$search = '';
$categories = [];
$lots = [];
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
$page_content = showPagination($link, $cat, $top_menu);
if (!$page_content) {
    print(showError($categories, $page_content, $user_data, $cat,'<h2>Нет товаров в выбранной категории</h2>'));
    return;
}

print(showContent($categories, $page_content, $user_data, $search, $cat));
