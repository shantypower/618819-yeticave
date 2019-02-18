<?php
date_default_timezone_set('Asia/Chita');
require('data.php');
require('functions.php');
require_once('db_connection.php');
$page_content = '';
if ($isConnect == false) {
    $error = mysqli_connect_error();
    showError($error);
    return;
}
$categories = getAllCategories($link);
if (count($categories) == 0) {
    $error = mysqli_error($link);
    showError($error);
    return;
};
$adverts = getAllLots($link);
if (count($adverts) == 0) {
    $error = mysqli_error($link);
    showError($error);
    return;
};
print(showContent($categories, $adverts));


