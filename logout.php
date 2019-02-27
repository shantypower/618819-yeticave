<?php
include('session.php');
unset($_SESSION['user']);
header("Location: /");
