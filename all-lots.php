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

$top_menu = includeTemplate('menu.php', ['menu' => $categories]);
$id = $_GET['category'];
$isCatExist = checkIsCategoryExist($link, $id);
if (!$isCatExist) {
    print(showError($categories, $page_content, $user_data, '', '<h2>404 Страница не найдена</h2><p>Данной страницы не существует на сайте.</p>'));
    return;
}
$page_content = showPaginationCatSearch($link, $id, $top_menu);
if (!$page_content) {
    print(showError($categories, $page_content, $user_data, '', '<h2>Нет товаров в выбранной категории</h2>'));
    return;
}

print(showContent($categories, $page_content, $user_data, $search, 'Товары категории'));
