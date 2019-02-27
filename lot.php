<?php
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);

$adverts = getAllLots($link);
$lot = '';
$id = (int)$_GET['id'];

print(getLotById($id, $categories, $adverts, $link));
