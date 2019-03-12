<?php
$user_data['is_auth'] = 0;
require_once('core/data.php');
require('core/db_connection.php');
require_once('core/functions.php');
session_start();
if (isset($_SESSION['id'])) {
    $user_data = getUserByID($_SESSION['id'], $link);
    if ($user_data) {
        $user_data['is_auth'] = 1;
    }
}

