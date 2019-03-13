<?php
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);
$user_data['is_auth'] = 0;
$user = [];

$errors = [];
$dict = [];
$search = '';

if ($isConnect === false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    if (!empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Введите корректный e-mail';
    }
    if (isset($_FILES['photo2']['name']) && (!empty($_FILES['photo2']['name']))) {
        $tmp_name = $_FILES['photo2']['tmp_name'];
        $path = $_FILES['photo2']['name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);
        if (($file_type !== "image/jpeg") && ($file_type !== "image/png")) {
            $errors['file'] = 'Загрузите картинку в формате PNG или JPG';
        }
        $path = setPathName($file_type);
        if ($path) {
            move_uploaded_file($tmp_name, 'img/' . $path);
        }
        $user['path'] = $path;
    }
    $user['path'] = '';

    if (empty($errors) && (count(getUserByEmail($user['email'], $link)) > 0)) {
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
            $page_content = includeTemplate('error.php', ['error' => mysqli_error($link)]);
        }
    }
}

$user['path'] = '';
$menu = includeTemplate('menu.php', ['menu' => $categories]);
$page_content = includeTemplate(
    'sign-up.php',
    [
        'top_menu' => $menu,
        'user' => $user,
        'errors' => $errors,
        'dict' => $dict
    ]
);

print(showContent($categories, $page_content, $user_data, $search, 'Регистрация'));
