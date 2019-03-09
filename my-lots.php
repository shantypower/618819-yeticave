<?php
date_default_timezone_set('Asia/Chita');
include('core/session.php');
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
$categories = getAllCategories($link);
$top_menu = include_template('menu.php', ['menu' => $categories]);

$adverts = getAllLots($link);
$lot = '';
$search = '';
$page_content = '';
$current_price = 0;
$user_id = $user_data['id'];

if ($isConnect == false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}
$users_rates = getUsersRates($link, $user_id);
if (!$users_rates) {
    print(showError($categories, $page_content, $user_data, $search,'<h2>У Вас нет ставок</h2>'));
    return;
}
$page_content = include_template('my-lots.php', [
    'top_menu' => $top_menu,
    'rates' => $users_rates,
    'user_id' => $user_id,
    'content' => $page_content
]);
print(showContent($categories, $page_content, $user_data, $search, 'Мои ставки'));
