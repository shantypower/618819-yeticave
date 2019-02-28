<?php
require_once('core/mysql_helper.php');
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

function check_remaintime($date) {
    $seconds = strtotime($date);
    $seconds_passed = $seconds - strtotime('now');
    $days = floor($seconds_passed / 86400);
    if ($days > 0) return true;
    return false;
}

function getAllCategories($link)
{
    $sql = 'SELECT id, cat_name, css_cl FROM categories';
    $categories = db_fetch_data($link, $sql, $categories = []);
    return $categories;
}

function getAllLots($link)
{
    $sql = 'SELECT l.id, l.lot_name, l.start_price, l.img_src, l.price_step, MAX(lr.rate), c.cat_name
    FROM lots l
    JOIN categories c
      ON l.cat_id = c.id
    LEFT OUTER JOIN lot_rates lr
      ON l.id = lr.lot_id
   WHERE l.date_end > CURRENT_DATE()
   GROUP BY l.id, l.lot_name, l.start_price, l.img_src, c.cat_name
   ORDER BY l.date_add
    DESC LIMIT 6;';
    $adverts = db_fetch_data($link, $sql, $adverts = []);
    return $adverts;
}

function showContent($categories, $page_content, $title)
{
    $show_page = include_template('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => $title
    ]);
    return $show_page;
}

function getLotById($id, $categories, $adverts, $link)
{
    $page_content = [];
    $sql = "SELECT l.id, l.lot_name, l.descr, l.start_price, l.img_src, MAX(lr.rate), l.price_step, l.author_id, l.date_end, c.cat_name
              FROM lots l
              JOIN categories c
                ON l.cat_id = c.id
              LEFT OUTER JOIN lot_rates lr
                ON l.id = lr.lot_id
             WHERE l.id  = ? GROUP BY l.id, l.lot_name, l.descr, l.start_price, l.img_src, l.price_step, l.author_id, l.date_end, c.cat_name";

    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    return $result = mysqli_stmt_get_result($stmt);
}

function getUserByEmail($user_email, $link)
{
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$user_email]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return $res;
}
