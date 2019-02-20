<?php
date_default_timezone_set('Asia/Chita');
require('data.php');
require_once('functions.php');
require('db_connection.php');
$page_content = '';
$categories = '';
$adverts ='';
if ($isConnect == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
    print(showContent($categories, $page_content, $is_auth, $user_name));
    return;
}
$categories = getAllCategories($link);
if (count($categories) == 0) {
    $error = mysqli_error($link);
    $page_content = include_template('error.php', ['error' => $error]);
    print(showContent($categories, $page_content, $is_auth, $user_name));
    return;
};
$adverts = getAllLots($link);
var_dump($adverts);
if (count($adverts) == 0) {
    $error = mysqli_error($link);
    $page_content = include_template('error.php', ['error' => $error]);
    print(showContent($categories, $page_content, $is_auth, $user_name));
    return;
};
$page_content = include_template('index.php', [
    'categories' => $categories,
    'adverts' => $adverts
]);
print(showContent($categories, $page_content, $is_auth, $user_name));
