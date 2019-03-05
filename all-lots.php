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
$current_page = 1;

if ($isConnect == false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}

if (isset($_GET['page'])) {
    $current_page = intval($_GET['page']);
    if ($current_page <= 0 ) {
        $current_page = 1;
    }
};

$categories = getAllCategories($link);
if (!$categories) {
    $error = mysqli_error($link);
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
};

$top_menu = include_template('menu.php', ['menu' => $categories]);
$cat = $_GET['category'];
$page_items = 9;
$items_count = getCountOfLotsByCat($link, $cat);
$pages_count = ceil($items_count / $page_items);
$offset = ($current_page - 1) * $page_items;
$pages = range(1, $pages_count);

$lots = getLotsByCategory($link, $cat, $page_items, $offset);
if (!$lots) {
    print(showError($categories, $page_content, $user_data, $search,'<h2>Нет товаров в выбранной категории</h2>'));
    return;
}

$page_content = include_template('all-lots.php', [
    'top_menu' => $top_menu,
    'lots' => $lots,
    'cat' => $cat,
    'pages_count' => $pages_count,
    'current_page' => $current_page,
    'pages' => $pages
]);

print(showContent($categories, $page_content, $user_data, $search, $cat));
