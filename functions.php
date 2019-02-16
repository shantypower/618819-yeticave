<?php
require_once('mysql_helper.php');
function price_format($price)
{
    $price = ceil($price);
    return $price = number_format($price, 0, ' ', ' ');
}

function include_template($name, $data)
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function text_clean($str)
{
    $text = trim($str);
    $text = htmlspecialchars($text);
    $text = strip_tags($text);

    return $text;
}

function lot_lifetime()
{
    $future_time = date_create('midnight tomorrow');
    $current_time = date_create('now');
    $diff = date_diff($current_time, $future_time);
    return date_interval_format($diff, "%H:%i");
}

function getAllCategories($link)
{
    $sql = 'SELECT cat_name, css_cl FROM categories';
    $categories = db_fetch_data($link, $sql, $categories = []);
    return $categories;
}

function getAllLots($link)
{
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
    return $adverts;
}

function dataOutput($categories, $adverts, $link)
{
    if ($categories) {
        $error_flag1 = 'index';
    } else {
        $error1 = mysqli_error($link);
        $error_flag1 = 'error';
    }

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
    $page_content = include_template('index.php', [
        'categories' => $categories,
        'adverts' => $adverts
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'title' => 'YetiCave - Главная страница'
    ]);
    return $layout_content;
}
