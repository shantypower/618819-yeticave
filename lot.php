<?php
date_default_timezone_set('Asia/Chita');
require_once('data.php');
require('db_connection.php');
require_once('functions.php');
$categories = getAllCategories($link);

$adverts = getAllLots($link);
$lot = '';
$id = (int)$_GET['id'];

print(getLotById($id, $categories, $adverts, $link));
