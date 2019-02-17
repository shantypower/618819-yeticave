<?php
date_default_timezone_set('Asia/Chita');
require('data.php');
require('functions.php');
require_once('db_connection.php');
$page_content = '';
if ($isConnect == false) {
    $error[] = mysqli_connect_error();
    $page_content = include_template('index.php', ['error' => $error]);
} else {
    $categories = getAllCategories($link);
    if (count($categories) == 0) {
        $error = mysqli_error($link);
        $page_content = include_template('index.php', ['error' => $error]);
        return;
    };
    $adverts = getAllLots($link);
    if (count($adverts) == 0) {
        $error = mysqli_error($link);
        $page_content = include_template('index.php', ['error' => $error]);
        return;
    };
    print(showContent($categories, $adverts));
}

