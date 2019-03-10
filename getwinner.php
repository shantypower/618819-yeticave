<?php
require_once "vendor/autoload.php";
$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");
$mailer = new Swift_Mailer($transport);
$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

if ($isConnect == false) {
    $error = mysqli_connect_error();
    print(showError($categories, $page_content, $user_data, $search, $error));
    return;
}

$win_lots = getWonLots($link);

if(!$win_lots) {
    print(showError($categories, $page_content, $user_data, $search,'<h2>Победитель не найден</h2>'));
    return;
}
foreach ($win_lots as $item) {
    $id = $item['id'];
    $lot_name = $item['lot_name'];
    $rate_winner = getRateWinner($link, $id);
    $winner = $rate_winner['user_id'];
    $sql= "UPDATE lots l
              SET l.user_winner_id = '$winner'
            WHERE l.id = '$id';";
    $result = mysqli_query($link, $sql);

    $user_winner= getWinnerContacts($link, $winner);
    $path = '';
    if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])){
        $path = 'http://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
    $letter = include_template('email.php', [
        'user_name' => $user_winner['user_name'],
        'lot_name' => $lot_name,
        'lot_id' => $id,
        'path'=> $path
    ]);
    $message = new Swift_Message();
    $message->setSubject("Ваша ставка победила");
    $message->setFrom(['keks@phpdemo.ru' => 'Yeticave']);
    $message->setBcc([$user_winner['email'] => $user_winner['user_name']]);
    $message->setBody($letter, 'text/html');
    // Отправка сообщения
    $result = $mailer->send($message);
};
