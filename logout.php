<?php
include('core/session.php');
unset($_SESSION['id']);
header("Location: /");
