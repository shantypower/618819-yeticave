<?php
$link = mysqli_init();
mysqli_options($link, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
mysqli_real_connect($link, $db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");



