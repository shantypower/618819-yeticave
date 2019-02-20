<?php
date_default_timezone_set('Asia/Chita');
require_once('data.php');
require('db_connection.php');
require_once('functions.php');
$page_content = '';
$categories = getAllCategories($link);
$lot = '';
$id = (int)$_GET['id'];
print(getLotById($id, $categories, $is_auth, $user_name, $link));
