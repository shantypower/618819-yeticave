<?php
date_default_timezone_set('Asia/Chita');
require_once('functions.php');
require_once('data.php');

$page_content = include_template('index.php', [
    'adverts' => $adverts,
    'categories' => $categories
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'YetiCave - Главная страница'
]);
print($layout_content);
