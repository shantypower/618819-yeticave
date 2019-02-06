<?php
function price_format($price) {
    $price = ceil($price);
    return $price = number_format($price, 0, ' ', ' ');
}

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function text_clean($str) {
    $text = trim($str);
	$text = htmlspecialchars($str);
	$text = strip_tags($str);

	return $text;
}

?>
