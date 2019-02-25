<?php
require_once('data.php');
require('db_connection.php');
require_once('functions.php');
$categories = getAllCategories($link);
$user = [];
$errors = [];
$dict = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;

    $required = [
        'email',
        'password',
        'name',
        'message'
    ];
	$dict = [
        'email' => 'E-mail',
        'password' => 'Пароль',
        'name' => 'Имя',
        'message' => 'Контактные данные',
        'file' => 'Аватар пользователя'
    ];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        $errors['email'] = 'Введите корректный e-mail';
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
                $user['path'] = $path;
            }
        } else {
            $errors['file'] = 'Вы не загрузили файл';
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }
    if (empty($errors)) {
        $email = mysqli_real_escape_string($link, $user['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($link, $sql);
        var_dump($res);
        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        } else {
            $password = password_hash($user['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (reg_date, email, user_pass, user_name, avatar_src, contacts)
                    VALUES (NOW(), ?, ?, ?, ?, ?);";
            $stmt = db_get_prepare_stmt($link, $sql, [$user['email'], $password, $user['name'], 'img/' . $user['path'],  $user['message']]);
            $res = mysqli_stmt_execute($stmt);
            if ($res && empty($errors)) {
                header("Location: login.php");
                exit();
            } else {
                $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
            }
        }
    }

}

$page_content = include_template('sign-up.php',
    [
        'categories' => $categories,
        'user' => $user,
        'errors' => $errors,
        'dict' => $dict
    ]);

print(showContent($categories, $page_content, $is_auth, $user_name));
