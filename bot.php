<?php

file_put_contents("log.txt", bot . phpfile_get_contents("php://input") . PHP_EOL, FILE_APPEND);

require_once 'vendor/autoload.php';

use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;

$botToken = "7872489450:AAFY_H1TopVR9op3w3iHQc5ql34PwUL9p6s";
$bot = new Client($botToken);

// Команди

$bot->command('start', function ($message) use ($bot) {
    $bot->sendMessage($message->getChat()->getId(), "Привіт! Я твій бот. Напиши щось, і я повторю.");
});

// Повторює будь-яке повідомлення користувача
$bot->on(function (Update $update) use ($bot) {
    $message = $update->getMessage();
    $chatId = $message->getChat()->getId();
    $text = $message->getText();

    $bot->sendMessage($chatId, "Ти написав: " . $text);
}, function () {
    return true;
});

try {
    $bot->run();
} catch (Exception $e) {
    error_log($e->getMessage());
}

?>
