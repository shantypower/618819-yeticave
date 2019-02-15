<?php
date_default_timezone_set('Asia/Chita');
require_once('functions.php');
require_once('mysql_helper.php');
require_once('data.php');

$link = mysqli_init();
mysqli_options($link, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
mysqli_real_connect($link, $db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");

$page_content = '';

if (!$link) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
       // Запрос на получение списка категорий
    $sql = 'SELECT cat_name, css_cl FROM categories';
    $categories = db_fetch_data($link, $sql, $categories = []);
    if ($categories) {
        $error_flag1 = 'index';
    } else {
        $error1 = mysqli_error($link);
        $error_flag1 = 'error';
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
    $adverts = db_fetch_data($link, $sql, $adverts = []);
    if ($adverts) {
        $error_flag2 = 'index';
    } else {
        $error2 = mysqli_error($link);
        $error_flag2 = 'error';
    }

    if ($error_flag1 == 'error' || $error_flag2 == 'error') {
        $page_content = include_template('error.php', [
            'error' => $error1.'<br>'.$error2
        ]);
    }
    if ($error_flag1 == 'index' && $error_flag2 == 'index') {
        $page_content = include_template('index.php', [
            'categories' => $categories,
            'adverts' => $adverts,
        ]);
    }
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'YetiCave - Главная страница'
]);
print($layout_content);
