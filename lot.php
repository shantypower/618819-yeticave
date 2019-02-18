<?php
date_default_timezone_set('Asia/Chita');
require_once('data.php');
require('db_connection.php');
require('functions.php');
$page_content = '';
$categories = getAllCategories($link);
$id = mysqli_real_escape_string($link, $_GET['id']);

$sql = "SELECT l.id, l.lot_name, l.descr, l.start_price, l.img_src, MAX(lr.rate), MIN(lr.rate), c.cat_name
          FROM lots l
          JOIN categories c
            ON l.cat_id = c.id
          JOIN lot_rates lr
            ON l.id = lr.lot_id
         WHERE l.id  = '%s'
      GROUP BY lr.lot_id";

$sql = sprintf($sql, $id);
if ($result = mysqli_query($link, $sql)) {
    if (!mysqli_num_rows($result)) {
        http_response_code(404);
        $page_content = include_template('error.php', ['error' => '<h2>404 Страница не найдена</h2><p>Данной страницы не существует на сайте.</p>']);
        showContent($categories, $page_content, $is_auth, $user_name);
        return;
    }
    $lot = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $page_content = include_template('lot.php', ['lot' => $lot]);
    showContent($categories, $page_content, $is_auth, $user_name);
    return;
}
$error = mysqli_error($link);
$page_content = include_template('error.php', ['error' => $error]);
showContent($categories, $page_content, $is_auth, $user_name);
