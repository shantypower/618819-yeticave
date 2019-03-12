<?php
require_once('core/mysql_helper.php');

/**
* Форматирует вывод цены - отступы между разрядами
* @param integer $price Число, которое нужно конверировать
* @return string Результат в виде строки
*/
function priceFormat($price)
{
    $price = ceil($price);
    return $price = number_format($price, 0, ' ', ' ');
}

/**
* Отрисовывает страницу на основании переданных параметров и шаблона
* @param string $name Имя файла-шаблона, в которой передаем параметры
* @param array $data Данные в виде массива вида ключ->значение для подстановки в шаблон
* @return string Сформированный из шаблона html-код
*/
function includeTemplate($name, $data)
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

/**
* Устраняет опасные символы в передаваемой строке
* @param string $str Строка, котороую нужно конверировать
* @return string Строка, в которые опасные символы заменены аналогами
*/
function textClean($str)
{
    $text = trim($str);
    $text = htmlspecialchars($text);
    $text = strip_tags($text);

    return $text;
}

/**
* Возвращает сколько часов и минут осталось до окончания жизни лота (в данном случае до полуночи текущего дня)
* @return string Результат в виде строки
*/
function LotLifetime()
{
    $future_time = date_create('midnight tomorrow');
    $current_time = date_create('now');
    $diff = date_diff($current_time, $future_time);
    return date_interval_format($diff, "%H:%I");
}

/**
* Проверяет дату окончания торгов для лота, чтобы она не была менее суток с момента публикации
* @param string $date Дата окончания публикации лота
* @return boolean Результат в виде значения истина, либо ложь
*/
function checkRemainTime($date)
{
    $seconds = strtotime($date);
    $seconds_passed = $seconds - strtotime('now');
    $days = floor($seconds_passed / 86400);
    if ($days > 0) {
        return true;
    }
    return false;
}

/**
* Получает из БД содержимое таблицы categories
* @param $link mysqli Ресурс соединения
* @return array Ассоциативный массив
*/
function getAllCategories($link)
{
    $sql = 'SELECT id, cat_name, css_cl FROM categories';
    $categories = db_fetch_data($link, $sql, $categories = []);
    return $categories;
}

/**
* Получает из БД самые новые, открытые лоты в порядке актуальности из таблицы lots
* @param $link mysqli Ресурс соединения
* @return array Ассоциативный массив
*/
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

/**
* Собирает html-код для показа страницы с ошибкой
* @param array $categories Ассоциативный массив категорий товаров
* @param string $page_content Фрагмент html-кода
* @param array $user_data Данные текущего пользователя
* @param string $search Строка-запрос из поля поиска
* @param string $errorText Текст ошибки
* @return string Сформированный из шаблона html-код
*/
function showError($categories, $page_content, $user_data, $search, $errorText)
{
    $page_content = includeTemplate('error.php', ['error' => $errorText]);
    return showContent($categories, $page_content, $user_data, $search, 'Не найдено');
}

/**
* Собирает html-код для показа страницы
* @param array $categories Ассоциативный массив категорий товаров
* @param string $page_content Фрагмент html-кода
* @param array $user_data Данные текущего пользователя
* @param string $search Строка-запрос из поля поиска
* @param string $title Имя страницы
* @return string Сформированный из шаблона html-код
*/
function showContent($categories, $page_content, $user_data, $search, $title)
{
    $show_page = includeTemplate('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'user_data' => $user_data,
        'search' => $search,
        'title' => $title
    ]);
    return $show_page;
}

/**
* Собирает html-код для показа пагинации на странице для результатов из поля поиска на сайте
* @param $link mysqli Ресурс соединения
* @param string $search Строка-запрос из поля поиска
* @param string $top_menu HTML-код для отрисовки меню категорий
* @return string Сформированный из шаблона html-код
*/
function showPaginationSiteSearch($link, $search, $top_menu)
{
    $current_page = 1;
    if (isset($_GET['page'])) {
        $current_page = intval($_GET['page']);
        if ($current_page <= 0) {
            $current_page = 1;
        }
    };
    $page_items = 9;
    $offset = ($current_page - 1) * $page_items;

    $items_count = getCountOfLotsBySearch($link, $search, $page_items, $offset);
    $pages_count = ceil($items_count / $page_items);
    $pages = range(1, $pages_count);

    $lots = getLotsBySiteSearch($search, $link, $page_items, $offset);
    if (!$lots) {
        return null;
    }

    $page_content = includeTemplate('search.php', [
        'search' => $search,
        'pages_count' => $pages_count,
        'current_page' => $current_page,
        'pages' => $pages,
        'top_menu' => $top_menu,
        'lots' => $lots
    ]);
    return $page_content;
}

/**
* Собирает html-код для показа пагинации на странице для результатов поиска по категориям
* @param $link mysqli Ресурс соединения
* @param string $cat Уникальный идентификатор категории
* @param string $top_menu HTML-код для отрисовки меню категорий
* @return string Сформированный из шаблона html-код
*/
function showPaginationCatSearch($link, $cat, $top_menu)
{
    $current_page = 1;
    if (isset($_GET['page'])) {
        $current_page = intval($_GET['page']);
        if ($current_page <= 0) {
            $current_page = 1;
        }
    };
    $page_items = 9;
    $items_count = getCountOfLotsByCat($link, $cat);
    $pages_count = ceil($items_count / $page_items);
    $offset = ($current_page - 1) * $page_items;
    $pages = range(1, $pages_count);

    $lots = getLotsByCategory($link, $cat, $page_items, $offset);
    if (!$lots) {
        return null;
    }

    $page_content = includeTemplate('all-lots.php', [
        'cat' => $cat,
        'pages_count' => $pages_count,
        'current_page' => $current_page,
        'pages' => $pages,
        'top_menu' => $top_menu,
        'lots' => $lots
    ]);
    return $page_content;
}

/**
* Получает из БД данные для лота по его id
* @param integer $id Уникальный идентификатор искомого лота
* @param $link mysqli Ресурс соединения
* @return array Ассоциативный массив при наличии результата иначе null
*/
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

/**
* Получает из БД данные пользователя по его email
* @param string $user_email Email пользователя
* @param $link mysqli Ресурс соединения
* @return array Ассоциативный массив при наличии результата иначе null
*/
function getUserByEmail($user_email, $link)
{
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$user_email]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $user[0] ?? null;
}

/**
* Получает из БД данные пользователя по его id
* @param integer $id Уникальный идентификатор пользователя
* @param $link mysqli Ресурс соединения
* @return array Ассоциативный массив при наличии результата иначе null
*/
function getUserByID($id, $link)
{
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $user[0] ?? null;
}

/**
* Проверяет в БД делал ли пользователь ставку для текущего лота
* @param integer $id Уникальный идентификатор текущего лота
* @param integer $user_id Уникальный идентификатор пользователя
* @param $link mysqli Ресурс соединения
* @return boolean Результат в виде значения истина, либо ложь
*/
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

/**
* Получает из БД все ставки для текущего лота
* @param integer $id Уникальный идентификатор лота
* @param $link mysqli Ресурс соединения
* @return array Ассоциативный массив
*/
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

function checkIsCategoryExist($link, $id)
{
    $sql = "SELECT id
             FROM categories
            WHERE id = ?;";
    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$result) {
        return false;
    }
    return true;
}

/**
* Получает массив лотов по искомой категории в порядке убывания по дате для вывода на страницу
* @param $link mysqli Ресурс соединения
* @param integer $cat Уникальный идентификатор категории
* @param integer $page_items Количество элементов, которое может быть выведено единовременно на страницу
* @param integer $offset Смещение выборки
* @return array Ассоциативный массив при наличии лотов, удовлетворяющим условиям поиска иначе null
*/
function getLotsByCategory($link, $cat, $page_items, $offset)
{
    $sql = "SELECT l.id, l.lot_name, l.descr, l.start_price, l.img_src, MAX(lr.rate), l.price_step, l.author_id, l.date_end, c.cat_name
              FROM lots l
              LEFT OUTER JOIN categories c
                ON l.cat_id = c.id
              LEFT OUTER JOIN lot_rates lr
                ON lr.lot_id = l.id
             WHERE cat_id = ?
             GROUP BY l.id, l.lot_name, l.descr, l.start_price, l.img_src, l.price_step, l.author_id, l.date_end, c.cat_name
             ORDER BY l.date_add
              DESC
              LIMIT ? OFFSET ?;";
    $stmt = db_get_prepare_stmt($link, $sql, [$cat, $page_items, $offset]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result ?? null;
}

/**
* Получает количество актуальных лотов по искомой категории  для вывода на страницу
* @param $link mysqli Ресурс соединения
* @param string $search Уникальный идентификатор категории
* @return integer Целое число при наличии лотов, удовлетворяющим условиям поиска иначе null
*/
function getCountOfLotsByCat($link, $search)
{
    $sql= "SELECT COUNT(*)
               AS cnt
             FROM lots
            WHERE cat_id = ?
              AND date_end > CURRENT_DATE()";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result[0]['cnt'] ?? null;
}

/**
* Получает количество лотов, удовлетворяющих запросу в поле поиска по имени или описанию для вывода на страницу
* @param $link mysqli Ресурс соединения
* @param string $search Содержимое запроса из поля поиска
* @param integer $page_items Количество элементов, которое может быть выведено единовременно на страницу
* @param integer $offset Смещение выборки
* @return integer целое число при наличии лотов, удовлетворяющим условиям поиска иначе null
*/
function getCountOfLotsBySearch($link, $search, $page_items, $offset)
{
    $sql= "SELECT COUNT(*)
               AS cnt
             FROM lots l
            WHERE MATCH(l.lot_name, l.descr) AGAINST(?)
            LIMIT ? OFFSET ?;";
    $stmt = db_get_prepare_stmt($link, $sql, [$search, $page_items, $offset]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result[0]['cnt'] ?? null;
}

/**
* Получает массив лотов, удовлетворяющих запросу из поля поиска в порядке убывания по дате для вывода на страницу
* @param string $search Содержимое запроса из поля поиска
* @param $link mysqli Ресурс соединения
* @param integer $page_items Количество элементов, которое может быть выведено единовременно на страницу
* @param integer $offset Смещение выборки
* @return array Ассоциативный массив при наличии лотов, удовлетворяющим условиям поиска иначе null
*/
function getLotsBySiteSearch($search, $link, $page_items, $offset)
{
    $sql = "SELECT l.id, l.lot_name, l.descr, l.start_price, l.img_src, MAX(lr.rate), l.price_step, l.author_id, l.date_end, c.cat_name
    FROM lots l
    LEFT OUTER JOIN lot_rates lr
      ON l.id = lr.lot_id
    JOIN categories c
      ON l.cat_id = c.id
   WHERE MATCH(l.lot_name, l.descr)
 AGAINST(?)
   GROUP BY l.id, l.lot_name, l.descr, l.start_price, l.img_src, l.cat_id
   ORDER BY l.date_add
    DESC
   LIMIT ? OFFSET ?;";
    $stmt = db_get_prepare_stmt($link, $sql, [$search, $page_items, $offset]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result ?? null;
}

/**
* Возвращает в виде строки сообщение о том сколько времени назад была сделана ставка
* @param string $time Дата ставки в виде строки
* @return string Результат в виде строки
*/
function humanDate($time)
{
    $lot_time_sec = strtotime($time);
    $secs_passed = strtotime('now') - $lot_time_sec;

    $days = floor($secs_passed / 86400);

    if ($days == 0) {
        $hours = floor($secs_passed / 3600);
        if ($hours > 0) {
            $result = $hours . ' часов назад';
            if (((($hours % 10) == 1)&&($hours != 11))||($hours == 21)) {
                $result = $hours . ' час назад';
            } elseif ((($hours > 1)&&($hours < 5))||(($hours >= 22)&&($hours <=23))) {
                $result = $hours . ' часа назад';
            } elseif (($hours >= 5)&&($hours < 21)) {
                $result = $hours . ' часов назад';
            }
        }
        $minutes = floor(($secs_passed % 3600)/60);
        if ((($minutes % 10) == 1)&&($minutes != 11)) {
            $result = $minutes . ' минуту назад';
        }
        $result = $minutes . ' минут(ы) назад';
    } else {
        $result = date_format(date_create($time), "d.m.y в H:i");
    }
    return $result;
}

/**
* Получает из БД все ставки для текущего пользователя для пока на странице "Мои ставки"
* @param $link mysqli Ресурс соединения
* @param integer $user_id Уникальный идентификатор лота
* @return array Ассоциативный массив при наличии результата иначе null
*/
function getUsersRates($link, $user_id)
{
    $sql= "SELECT MAX(lr.id) id, MAX(lr.rate) rate, u.contacts, u.email,
                  u.user_name author_name, MAX(lr.date_add) date_add,
                  c.cat_name, l.winner_id, l.lot_name,
                  l.img_src, l.id lot_id, l.date_end
             FROM lot_rates lr
             JOIN lots l
               ON lr.lot_id = l.id
             JOIN categories c
               ON l.cat_id = c.id
             JOIN users u
               ON l.author_id = u.id
            WHERE lr.user_id = ?
            GROUP BY lr.lot_id
            ORDER BY date_add DESC
            LIMIT 50;";
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result ?? null;
}

/**
* Получает из БД все победившие лоты
* @param $link mysqli Ресурс соединения
* @return array Ассоциативный массив при наличии результата иначе null
*/
function getWonLots($link)
{
    $sql= "SELECT l.id, l.lot_name, GREATEST(IFNULL(MAX(lr.rate),0),l.start_price) price
             FROM lots l
             JOIN users u
               ON u.id = l.author_id
             JOIN categories c
               ON c.id = l.cat_id
             LEFT JOIN lot_rates lr
               ON lr.lot_id = l.id
            WHERE l.winner_id
               IS NULL AND CURRENT_TIMESTAMP > l.date_end AND lr.user_id IS NOT NULL
            GROUP BY l.id
            ORDER BY l.date_add DESC
            LIMIT 100;";
    $stmt = db_get_prepare_stmt($link, $sql, []);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result ?? null;
}

/**
* Получает из таблицы ставок id пользователя, сделавшего ставку для запрашивемого лота
* @param $link mysqli Ресурс соединения
* @param integer $user_id Уникальный идентификатор лота
* @return array Ассоциативный массив при наличии результата иначе null
*/
function getRateWinner($link, $id)
{
    $sql= "SELECT user_id, id
             FROM lot_rates
            WHERE lot_id = ?
            ORDER BY date_add
             DESC
            LIMIT 1";
    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result[0] ?? null;
}

/**
* Получает контактные данные и имя пользователя (победителя лота)
* @param $link mysqli Ресурс соединения
* @param integer $winner Уникальный идентификатор пользователя
* @return array Ассоциативный массив при наличии результата иначе null
*/
function getWinnerContacts($link, $winner)
{
    $sql= "SELECT email, user_name, id
             FROM users
            WHERE id = ?;";
    $stmt = db_get_prepare_stmt($link, $sql, [$winner]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $result[0] ?? null;
}
