<?php
include('core/session.php');
unset($_SESSION['user']);
header("Location: /");
