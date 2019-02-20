<?php
require_once('data.php');
require('db_connection.php');
require_once('functions.php');
$categories = getAllCategories($link);
$page_content = include_template('add-lot.php', [
    'categories' => $categories
]);
print(showContent($categories, $page_content, $is_auth, $user_name));
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;
	$required = ['lot-name', 'category', 'message', 'description', 'lot-rate', 'lot-step', 'lot-date'];
	$dict = ['lot-name' => 'Название', 'message' => 'Описание', 'lot-rate' => 'Начальная цена', 'lot-step' => 'Шаг ставки', 'lot-date' => 'Дата окончания торгов'];
	$errors = [];
    foreach ($required as $key) {
		if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
		}
    }
    if (isset($_FILES['lot_img']['name'])) {
		$tmp_name = $_FILES['lot_img']['tmp_name'];
        $path = $_FILES['lot_img']['name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type !== "img/jpg") {
			$errors['file'] = 'Загрузите картинку в формате IMG или JPG';
		} else {
			move_uploaded_file($tmp_name, 'img/' . $path);
			$lot['img_src'] = $path;
		}


    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }
    if (count($errors)) {
        $page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'dict' => $dict]);
        print(showContent($categories, $page_content, $is_auth, $user_name));
	}	else {
        $page_content = include_template('lot.php', ['lot' => $lot]);
        print(showContent($categories, $page_content, $is_auth, $user_name));
	}
}


    /* $filename = uniqid() . '.jpg';
    $lot['img_src'] = $filename;
    move_uploaded_file($_FILES['lot_img']['tmp_name'], 'uploads/' . $filename); */

$sql = "INSERT INTO lots(NOW(), lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
        VALUES ('?', '?', '?', '?', '?', '?', '?', '1', '?');";

$stmt = db_get_prepare_stmt($link, $sql, [$lot['cat_id'], $lot['lot_name'], $lot['desc'], $lot['img_src']]);
$res = mysqli_stmt_execute($stmt);

if ($res) {
    $lot_id = mysqli_insert_id($link);

    header("Location: add.php?id=" . $lot_id);
} else {
    $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
    print(showContent($categories, $page_content, $is_auth, $user_name));
}

