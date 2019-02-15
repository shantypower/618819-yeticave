<?php
date_default_timezone_set('Asia/Chita');
require_once('functions.php');
require_once('data.php');

$link = mysqli_init();
mysqli_options($link, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
mysqli_real_connect($link, $db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");

$categories = [];
$adverts = [];
$page_content = '';

if (!$link) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
       // Запрос на получение списка категорий
    $sql = 'SELECT cat_name, css_cl FROM categories';
    $result = mysqli_query($link, $sql);
    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    }
    // запрос на показ девяти самых последних лотов
    $sql = 'SELECT l.lot_name, l.start_price, l.img_src, MAX(lr.rate), c.cat_name
              FROM lots l
              JOIN categories c
                ON l.cat_id = c.id
              JOIN lot_rates lr
                ON l.id = lr.lot_id
             WHERE l.date_end > CURRENT_DATE()
          GROUP BY lr.lot_id
          ORDER BY l.date_add
              DESC LIMIT 6;';
    $result = mysqli_query($link, $sql);
    if ($result) {
        $adverts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    }
    $page_content = include_template('index.php', [
        'categories' => $categories,
        'adverts' => $adverts
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'YetiCave - Главная страница'
]);
print($layout_content);
