<?php
require_once('data.php');
require('db_connection.php');
require_once('functions.php');
$categories = getAllCategories($link);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;
    var_dump($_POST);

	$required = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date', 'file'];
	$dict = ['lot-name' => 'Название', 'category' => 'Категория', 'message' => 'Описание', 'lot-rate' => 'Начальная цена', 'lot-step' => 'Шаг ставки', 'lot-date' => 'Дата окончания торгов', 'file' => 'Фото лота'];
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
        $errors['lot-rate'] = 'Введите число больше нуля';
    }
    if ((!is_numeric($_POST['lot-step']))||($_POST['lot-step'] <= 0)) {
        $errors['lot-step'] = 'Введите число больше нуля';
    }  var_dump($errors);
/*     if (!delta_day($_POST['lot-date'])) {
        $errors['lot-date'] = 'Неверная дата. Закрыть лот можно не ранее чем через сутки после добавления лота';
    } */
    if (isset($_FILES['photo2']['name'])) {
        $tmp_name = $_FILES['photo2']['tmp_name'];
        $path = $_FILES['photo2']['name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);
        if (($file_type !== "image/jpg") && ($file_type !== "image/png")) {
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
    if (count($errors)) {
        $page_content = include_template('add-lot.php', ['lot' => $lot, 'errors' => $errors, 'dict' => $dict]);

    } else {
        $sql = "INSERT INTO lots (date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id) VALUES (NOW(), ?, ?, ?, ?, ?, ?, '1', ?);";
        $stmt = db_get_prepare_stmt($link, $sql, [$lot['lot-name'], $lot['message'], 'img/' . $lot['path'], $lot['message'], $lot['lot-rate'], $lot['lot-date'], $lot['lot-step'], $lot['lot-step'], '10', $lot['category']]);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($link);

            header("Location: lot.php?id=" . $lot_id);
        } else {
            $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
        }
    }
} else {
    $page_content = include_template('add-lot.php', ['categories' => $categories]);
}

print(showContent($categories, $page_content, $is_auth, $user_name));

