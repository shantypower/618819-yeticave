<?php
include('core/session.php');
unset($_SESSION['user']);
$is_auth = 0;
$user_name = '';
header("Location: /");
