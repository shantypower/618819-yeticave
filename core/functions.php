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

function showContent($categories, $page_content, $user_data, $search, $title)
{
    $show_page = include_template('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'user_data' => $user_data,
        'search' => $search,
        'title' => $title
    ]);
    return $show_page;
}

function getLotById($id, $link)
{
    $sql = "SELECT l.id, l.lot_name, l.descr, l.start_price, l.img_src, MAX(lr.rate), l.price_step, l.author_id, l.date_end, c.cat_name
              FROM lots l
              JOIN categories c
                ON l.cat_id = c.id
              LEFT OUTER JOIN lot_rates lr
                ON l.id = lr.lot_id
             WHERE l.id  = ? GROUP BY l.id, l.lot_name, l.descr, l.start_price, l.img_src, l.price_step, l.author_id, l.date_end, c.cat_name";

    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $lot = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $lot[0] ?? null;
}

function getUserByEmail($user_email, $link)
{
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$user_email]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $user[0] ?? null;
}

function getUserByID($id, $link)
{
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $user[0] ?? null;
}

function checkUserRated($id, $user_id, $link)
{
    $sql = 'SELECT count(*) as cnt
          FROM lot_rates
         WHERE lot_id = ? AND user_id = ?;';
    $stmt = db_get_prepare_stmt($link, $sql, [$id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if ($res[0]['cnt'] > 0) {
        return true;
    }
    return false;
}

function getRatesForLot($id, $link)
{
    $sql = 'SELECT lr.user_id, lr.rate, lr.date_add, lr.lot_id, u.user_name, u.id
             FROM lot_rates lr
             LEFT OUTER JOIN users u
               ON lr.user_id = u.id
             LEFT OUTER JOIN lots l
               ON lr.lot_id = l.id
            WHERE lr.lot_id = ?
            ORDER BY lr.date_add DESC;';
    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function siteSearch($search, $link)
{
    $sql = "SELECT l.id, l.lot_name, l.descr, l.start_price, l.img_src, MAX(lr.rate), l.price_step, l.author_id, l.date_end, c.cat_name
    FROM lots l
    LEFT OUTER JOIN lot_rates lr
      ON l.id = lr.lot_id
    JOIN categories c
      ON l.cat_id = c.id
   WHERE MATCH(l.lot_name, l.descr)
 AGAINST(?)
 GROUP BY l.id, l.lot_name, l.descr, l.start_price, l.img_src, l.cat_id;";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result ?? null;
}

function humanDate($time)
{
    $lot_time_sec = strtotime($time);
    $secs_passed = strtotime('now') - $lot_time_sec;

    $days = floor($secs_passed / 86400);

    if ($days == 0) {
        $hours = floor($secs_passed / 3600);
        if ($hours > 0) {
            $result = $hours . ' часов назад';
            if (((($hours % 10) == 1 )&&($hours != 11 ))||($hours == 21)) {
                $result = $hours . ' час назад';
            } elseif ((($hours > 1 )&&($hours < 5))||(( $hours >= 22)&&( $hours <=23 ))){
                $result = $hours . ' часа назад';
            } elseif (($hours >= 5)&&($hours < 21)){
                $result = $hours . ' часов назад';
            }
        }
        $minutes = floor(($secs_passed % 3600)/60);
        if ((($minutes % 10) == 1)&&($minutes != 11)) {
            $result = $minutes . ' минуту назад';
        }
        $result = $minutes . ' минут(ы) назад';
    } else $result = date_format(date_create($time), "d.m.y в H:i");
    return $result;
}
