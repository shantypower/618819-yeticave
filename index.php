<?php
date_default_timezone_set('Asia/Chita');
require('data.php');
require('functions.php');
require_once('db_connection.php');
$page_content = '';
if (!$link) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
    $categories = getAllCategories($link);
    $adverts = getAllLots($link);
    print(dataOutput($categories, $adverts, $link));
}
