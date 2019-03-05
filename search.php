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
$top_menu = include_template('menu.php', ['menu' => $categories]);
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $lots = siteSearch($search, $link);
    if (count($lots) > 0) {
        $page_content = include_template('search.php', [
            'top_menu' => $top_menu,
            'lots' => $lots,
            'search' => $search
        ]);
        print(showContent($categories, $page_content, $user_data, $search, 'Результаты поиска'));
        return;
    }
}
$page_content = include_template('error.php', [
    'top_menu' => $top_menu,
    'error' => 'Ничего не найдено по вашему запросу'
]);
print(showContent($categories, $page_content, $user_data, $search, 'Ничего не найдено'));
    return;
