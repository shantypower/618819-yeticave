<?php
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);
$page_content = '';
$lots = [];
$search = '';
if ($isConnect == false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}

$top_menu = include_template('menu.php', ['menu' => $categories]);
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $page_content = showPaginationSiteSearch($link, $search, $top_menu);

    if (!$page_content) {
        print(showError($categories, $page_content, $user_data, $search,'<h2>Нет товаров в выбранной категории</h2>'));
        return;
    }
/*     $lots = siteSearch($search, $link);
    if (!$lots) {
        print(showError($categories, $page_content, $user_data, $search,'<h2>Ничего не найдено по вашему запросу</h2>'));
        return;
    }

    $page_content = include_template('search.php', [
        'top_menu' => $top_menu,
        'lots' => $lots,
        'search' => $search
    ]); */
    print(showContent($categories, $page_content, $user_data, $search, 'Результаты поиска'));
    return;
}
print(showError($categories, $page_content, $user_data, $search,'<h2>Ничего не найдено по вашему запросу</h2>'));
