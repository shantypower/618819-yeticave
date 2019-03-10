<?php
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$search = '';
$categories = [];
$page_content = '';
$categories = getAllCategories($link);

if ($user_data['is_auth'] == 0) {
        $page_content = includeTemplate('error.php', ['error' => '<h2>403 Доступ запрещен</h2><p>Добавлять лот могут только зарегистрированные пользователи</p>']);
        print(showContent($categories, $page_content, $user_data, $search, '403 Доступ запрещен'));
    exit();
    }

if ($isConnect == false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}

$top_menu = includeTemplate('menu.php', ['menu' => $categories]);
$page_content = includeTemplate('add-lot.php', ['top_menu' => $top_menu, 'categories' => $categories]);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;

    $required = [
        'lot-name',
        'category',
        'message',
        'lot-rate',
        'lot-step',
        'lot-date'
    ];
	$dict = [
        'lot-name' => 'Название',
        'category' => 'Категория',
        'message' => 'Описание',
        'lot-rate' => 'Начальная цена',
        'lot-step' => 'Шаг ставки',
        'lot-date' => 'Дата окончания торгов',
        'file' => 'Фото лота'
    ];
    $errors = [];
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }
    if ((!is_numeric($_POST['category']))||($_POST['category'] <= 0)) {
        $errors['category'] = 'Выберите категорию';
      }
    if ((!is_numeric($_POST['lot-rate']))||($_POST['lot-rate'] <= 0)) {
        $errors['lot-rate'] = 'Введите число больше ноля';
    }
    if ((!is_numeric($_POST['lot-step']))||($_POST['lot-step'] <= 0)) {
        $errors['lot-step'] = 'Введите число больше ноля';
    }
    if (!checkRemainTime($_POST['lot-date'])) {
        $errors['lot-date'] = 'Неверная дата: нельзя закрыть лот менее чем через сутки после добавления';
    }

    if (isset($_FILES['photo2']['name'])) {
        if (!empty($_FILES['photo2']['name'])) {
            $tmp_name = $_FILES['photo2']['tmp_name'];
            $path = $_FILES['photo2']['name'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            if (($file_type !== "image/jpeg") && ($file_type !== "image/png")) {
                $errors['file'] = 'Загрузите картинку в формате PNG или JPG';
            } else {
                if ($file_type == "image/jpeg") $path = uniqid() . ".jpg";
                if ($file_type == "image/png") $path = uniqid() . ".png";
                move_uploaded_file($tmp_name, 'img/' . $path);
                $lot['path'] = $path;
            }
        } else {
            $errors['file'] = 'Вы не загрузили файл';
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }
    if (count($errors)) {
        $page_content = includeTemplate('add-lot.php',
            [
                'categories' => $categories,
                'top_menu' => $top_menu,
                'lot' => $lot,
                'errors' => $errors,
                'dict' => $dict
            ]);

    } else {
        $sql = "INSERT INTO lots (date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
                VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = db_get_prepare_stmt($link, $sql, [
            $lot['lot-name'],
            $lot['message'],
            'img/' . $lot['path'],
            $lot['lot-rate'],
            $lot['lot-date'],
            $lot['lot-step'],
            $user_data['id'],
            $lot['category']
        ]);
        $res = mysqli_stmt_execute($stmt);
        if ($res) {
            $lot_id = mysqli_insert_id($link);

            header("Location: lot.php?id=" . $lot_id);
        } else {
            $page_content = includeTemplate('error.php', ['error' => mysqli_error($link)]);
        }
    }
}

$content = includeTemplate('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'user_data' => $user_data,
    'title' => 'YetiCave - Добавление лота'
]);
print($content);

