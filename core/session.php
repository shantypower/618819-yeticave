<?php
session_start();
if (isset($_SESSION['user'])){
    $is_auth = 1;
    $user_name = $_SESSION['user']['user_name'];
    $user_id = $_SESSION['user']['id'];
}
