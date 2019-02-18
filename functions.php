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
    $sql = 'SELECT l.id, l.lot_name, l.start_price, l.img_src, MAX(lr.rate), c.cat_name
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

function showContent($categories, $page_content, $is_auth, $user_name)
{
    $show_page = include_template('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'title' => 'YetiCave - Главная страница'
    ]);
    print($show_page);
}
