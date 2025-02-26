<?php
ob_start();
stream_context_set_default([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

require_once 'vendor/autoload.php';
require_once 'core/DataBase.php';

use core\Core;
use TelegramBot\Api\Client;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use core\DataBase;
use TelegramBot\Api\Types\ForceReply;
use MVC\models\Order;

define('TOKEN', '7872489450:AAFY_H1TopVR9op3w3iHQc5ql34PwUL9p6s');

$db = new DataBase('localhost', 'ultra_granit', 'admin', 'a69nlAhtUlyyyIS2');

$bot = new Client(TOKEN);
$api = new BotApi(TOKEN);

// Вимкнення перевірки SSL для `curl`
$api->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
$api->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);

/**
 * Перевірка, чи користувач очікує введення номера телефону
 */
function isUserWaitingForPhone($chatId) {
    return strpos(file_get_contents("waiting_users.txt"), (string)$chatId) !== false;
}

/**
 * Додає користувача у список очікування номера телефону
 */
function setUserWaitingForPhone($chatId) {
    file_put_contents("waiting_users.txt", "$chatId\n", FILE_APPEND);
}

/**
 * Видаляє користувача зі списку очікування номера телефону
 */
function removeUserFromWaitingList($chatId) {
    if (!file_exists("waiting_users.txt")) return;

    $lines = file("waiting_users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newLines = [];

    foreach ($lines as $line) {
        list($id, $action) = explode(":", $line);
        if ($id != $chatId) $newLines[] = $line;
    }

    file_put_contents("waiting_users.txt", implode("\n", $newLines) . "\n");
}

/**
 * Отримує користувача з БД за telegram_id
 */
function getUserByTelegramId($telegramId) {
    global $db;
    $result = $db->select('users', '*', ['telegram_id' => $telegramId]);
    return $result ? $result[0] : null;
}

/**
 * Отримує користувача з БД за номером телефону
 */
function getUserByPhoneNumber($phoneNumber) {
    global $db;

    // Видаляємо +38, якщо є
    if (strpos($phoneNumber, '+38') === 0) {
        $phoneNumber = substr($phoneNumber, 3);
    }

    file_put_contents('log.txt', "🔍 Шукаємо телефон у БД: $phoneNumber\n", FILE_APPEND);
    $result = $db->select('users', '*', ['phone_number' => $phoneNumber]);
    return $result ? $result[0] : null;
}

/**
 * Прив’язує Telegram ID до користувача
 */
function linkTelegramToUser($chatId, $userId) {
    global $db;
    file_put_contents('log.txt', "🔹 Оновлення Telegram ID для користувача: ID=$userId, Telegram ID=$chatId\n", FILE_APPEND);
    $result = $db->update("users", ["telegram_id" => $chatId], ["id" => $userId]);

    if ($result) {
        file_put_contents('log.txt', "✅ Telegram ID успішно оновлено!\n", FILE_APPEND);
    } else {
        file_put_contents('log.txt', "❌ ПОМИЛКА: Не вдалося оновити Telegram ID\n", FILE_APPEND);
    }
}

function setUserWaitingForAction($chatId, $action) {
    file_put_contents("waiting_users.txt", "$chatId:$action\n", FILE_APPEND);
}

function getUserWaitingAction($chatId) {
    if (!file_exists("waiting_users.txt")) return null;

    $lines = file("waiting_users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($id, $action) = explode(":", $line);
        if ($id == $chatId) return $action;
    }
    return null;
}

/**
 * Відправка головного меню
 */
//function sendMainMenu($chatId, $accessLevel, $api) {
//    $menuText = "📋 *Головне меню*:";
//
//    // Створюємо масив кнопок для клавіатури
//    $buttons = [
//        ["/products", "/orders"],
//        ["/get_currency"]
//    ];
//
//    if ($accessLevel >= 5) {
//        $buttons[] = ["/orders"];
//    }
//    if ($accessLevel == 10) {
//        $buttons[] = ["/admin", "/set_currency"];
//    }
//
//    // Створюємо розмітку клавіатури
//    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($buttons, true, true);
//
//    // Відправляємо меню з кнопками
//    $api->sendMessage($chatId, $menuText, "Markdown", false, null, $keyboard);
//}

function sendMainMenu($chatId, $accessLevel, $api) {
    $menu = "📋 *Головне меню:*\n\n";
    $menu .= "🛍 */products* - Переглянути товари\n";
    $menu .= "📦 */orders* - Мої замовлення\n";
    $menu .= "💵 */get_currency* - Перегляд курсу USD\n";

    if ($accessLevel >= 5) {
        $menu .= "\n👥 *Менеджерські команди:*\n";
        $menu .= "📦 */orders_list* - Список усіх замовлень\n";
    }
    if ($accessLevel == 10) {
        $menu .= "\n🛠 *Адміністративні команди:*\n";
        $menu .= "🔧 */admin* - Панель адміністратора\n";
        $menu .= "💵 */set_currency* - Налаштувати курс USD\n";
        $menu .= "💵 */enable_auto_update* - Увімкнути авто оновлення курсу USD\n";
        $menu .= "💵 */disable_auto_update* - Вимкнути авто оновлення курсу USD\n";
        $menu .= "💵 */status_auto_update* - Статус авто оновлення курсу USD\n";
    }

    // Відправляємо відформатоване меню у Markdown
    $api->sendMessage($chatId, $menu, "Markdown");
}

// 📌 Функція оновлення ролі користувача
function processRoleUpdate($chatId, $targetUserId, $newAccessLevel, $roleName, $api, $db) {
    // Перевіряємо, чи введений ID є числом
    if (!is_numeric($targetUserId) || intval($targetUserId) <= 0) {
        $api->sendMessage($chatId, "❌ Некоректний ID! Введіть ціле число.", "Markdown");
        return;
    }

    $targetUserId = intval($targetUserId);

    // Перевіряємо, чи існує користувач
    $targetUser = $db->select('users', ['id'], ['id' => $targetUserId])[0] ?? null;

    if (!$targetUser) {
        file_put_contents('log.txt', "❌ ПОМИЛКА: Користувач з ID #$targetUserId не знайдений!\n", FILE_APPEND);
        $api->sendMessage($chatId, "❌ Користувач з ID #$targetUserId не знайдений.");
        return;
    }

    // Оновлюємо рівень доступу користувача
    $db->update('users', ['access_level' => $newAccessLevel], ['id' => $targetUserId]);

    file_put_contents('log.txt', "✅ Користувач #$targetUserId отримав роль $roleName\n", FILE_APPEND);
    $api->sendMessage($chatId, "✅ Користувач #$targetUserId тепер $roleName!");
}

/**
 * Отримання рівня доступу користувача
 */
function getUserAccessLevel($userId)
{
    global $db;
    $user = $db->select('users', ['access_level'], ['id' => $userId]);
    return !empty($user) ? $user[0]->access_level : 1; // 1 - стандартний користувач
}

/**
 * Отримати статус автооновлення курсу
 */
function getAutoUpdateStatus()
{
    return file_exists('auto_update_status.txt') ? trim(file_get_contents('auto_update_status.txt')) : 'disabled';
}

/**
 * Встановити статус автооновлення курсу
 */
function setAutoUpdateStatus($status)
{
    file_put_contents('auto_update_status.txt', $status);
}

/**
 * Отримує список менеджерів та адміністраторів, які мають access_level >= 5
 */
function getManagersAndAdmins() {
    global $db;
    return $db->select('users', ['telegram_id'], ['access_level[>=]' => 5]);
}

function writeLog($message) {
    $logFile = __DIR__ . '/../../log.txt'; // Шлях до файлу логів
    $date = date("Y-m-d H:i:s"); // Дата і час
    file_put_contents($logFile, "[$date] $message" . PHP_EOL, FILE_APPEND);
}


$data = file_get_contents("php://input");
file_put_contents('log.txt', date("Y-m-d H:i:s") . " - Отримані дані: " . $data . PHP_EOL, FILE_APPEND);

if (!$data) {
    file_put_contents('log.txt', "❌ ПОМИЛКА: Дані від Telegram не отримані!" . PHP_EOL, FILE_APPEND);
    exit;
}

try {
    $bot->command('start', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $firstName = $message->getChat()->getFirstName();
        $username = $message->getChat()->getUsername() ?? 'немає';

        file_put_contents('log.txt', "ℹ️ /start: Отримано запит від $chatId ($firstName @$username)\n", FILE_APPEND);

        global $db;
        $user = getUserByTelegramId($chatId);

        if ($user) {
            file_put_contents('log.txt', "✅ Користувач знайдений: {$user->first_name} {$user->last_name} (ID: {$user->id})\n", FILE_APPEND);
            $api->sendMessage($chatId, "✅ Вітаю, {$user->first_name}! Ви вже зареєстровані.\n Використайте коданду /menu та перегляньте можливості!");
        } else {
            file_put_contents('log.txt', "❌ Користувач НЕ знайдений у БД. Запитуємо номер телефону.\n", FILE_APPEND);
            setUserWaitingForPhone($chatId);
            $replyMarkup = new ForceReply(true);
            $api->sendMessage($chatId, "📲 Введіть свій номер телефону у форматі *0123456789* (без +38):", "Markdown", false, null, $replyMarkup);
        }
    });

    $bot->on(function ($update) use ($api, $db) {
        $message = $update->getMessage();
        $chatId = $message->getChat()->getId();
        $text = trim($message->getText());
        $replyTo = $message->getReplyToMessage();

        // 📌 Якщо це команда (починається з "/"), просто логуємо та виходимо
        if (strpos($text, '/') === 0) {
            file_put_contents('log.txt', "🔍 Команда отримана: $text (не номер)\n", FILE_APPEND);
            return;
        }

        // 📌 Перевіряємо, чи користувач перебуває у стані очікування введення даних
        $waitingAction = getUserWaitingAction($chatId);

        if ($waitingAction) {
            removeUserFromWaitingList($chatId); // Видаляємо користувача зі списку очікування

            switch ($waitingAction) {
                case "assign_manager":
                    processRoleUpdate($chatId, $text, 5, "менеджер", $api, $db);
                    return;
                case "assign_admin":
                    processRoleUpdate($chatId, $text, 10, "адміністратор", $api, $db);
                    return;
                case "assign_user":
                    processRoleUpdate($chatId, $text, 1, "звичайний користувач", $api, $db);
                    return;
                case "set_currency":
                    if (!is_numeric($text) || floatval($text) <= 0) {
                        $api->sendMessage($chatId, "❌ Некоректне значення! Введіть число (наприклад `39.50`).", "Markdown");
                        return;
                    }

                    $newRate = floatval($text);
                    $db->update('currency', ['exchange_rate' => $newRate, 'updated_at' => date('Y-m-d H:i:s')], ['currency_code' => 'USD']);

                    file_put_contents('log.txt', "🔄 Курс USD оновлено до $newRate UAH (Менеджер/Адмін ID: $chatId)\n", FILE_APPEND);
                    $api->sendMessage($chatId, "✅ Курс USD оновлено до *$newRate* грн.", "Markdown");
                    return;
            }
        }

        // 📌 Перевіряємо, чи користувач очікує введення номера телефону
        if (isUserWaitingForPhone($chatId)) {
            $user = getUserByPhoneNumber($text);
            if ($user) {
                linkTelegramToUser($chatId, $user->id);
                removeUserFromWaitingList($chatId);
                $api->sendMessage($chatId, "✅ Ви успішно авторизовані! Використовуйте /menu для навігації.");
            } else {
                $api->sendMessage($chatId, "❌ Ваш номер не знайдено у базі даних.");
            }
            return;
        }

        // 📌 Якщо повідомлення не відповідає жодному очікуванню
        $api->sendMessage($chatId, "Ви написали: $text");

    }, function () {
        return true;
    });

    $bot->command('menu', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $accessLevel = $user ? $user->access_level : 1;
        sendMainMenu($chatId, $accessLevel, $api);
    });

    $bot->command('products', function ($message) use ($api, $db) {
        $chatId = $message->getChat()->getId();
        $products = $db->select('product', ['name', 'price'], null, ['LIMIT' => 5]);

        $response = "🛍 *Товари:*\n";
        foreach ($products as $product) {
            $response .= "📌 " . $product->name . " - " . $product->price . "\n";
        }

        $api->sendMessage($chatId, $response, "Markdown");
    });

    // 📌 Команда /orders (менеджери та адміністратори можуть бачити замовлення)
    $bot->command('orders', function ($message) use ($api, $db) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1; // 1 - стандартний доступ

        if ($accessLevel == 1) {
            // Отримуємо ID користувача з бази
            if (!$user) {
                $api->sendMessage($chatId, "❌ Помилка: Користувача не знайдено у базі.");
                return;
            }

            // Отримуємо замовлення користувача
            $orders = $db->select('orders', ['id', 'status'], ['user_id' => $userId], ['ORDER' => ['id' => 'DESC'], 'LIMIT' => 5]);

            if (empty($orders)) {
                $api->sendMessage($chatId, "ℹ️ У вас немає замовлень.");
                return;
            }

            // Формуємо відповідь
            $response = "📦 *Ваші останні замовлення:*\n";
            foreach ($orders as $order) {
                $response .= "\n🆔 Замовлення: *#" . $order->id . "*\n";
                $response .= "📌 Статус: *" . $order->status . "*\n";

                // Отримуємо товари в цьому замовленні
                $orderProducts = $db->select('order_products', ['product_id', 'price'], ['order_id' => $order->id]);

                if (!empty($orderProducts)) {
                    $response .= "🛒 *Товари:*\n";
                    foreach ($orderProducts as $orderProduct) {
                        // Отримуємо назву товару
                        $product = $db->select('product', ['name'], ['id' => $orderProduct->product_id])[0] ?? null;
                        $productName = $product ? $product->name : "❓ Невідомий товар";

                        $response .= "   🔹 $productName - *" . number_format($orderProduct->price, 2) . " грн*\n";
                    }
                } else {
                    $response .= "🛒 Товари не знайдені ❌\n";
                }
            }

            $api->sendMessage($chatId, $response, "Markdown");
            return;
        }

        if ($accessLevel < 5) {
            $api->sendMessage($chatId, "⛔ У вас немає прав для перегляду замовлень!");
            return;
        }

        $orders = $db->select('orders', ['id', 'user_id', 'status'], null, ['ORDER' => ['id' => 'DESC'], 'LIMIT' => 5]);

        $response = "📦 *Останні замовлення:*\n";
        foreach ($orders as $order) {
            $response .= "🆔 Замовлення: #" . $order->id . " (Статус: " . $order->status . ")\n";
        }

        $api->sendMessage($chatId, $response, "Markdown");
    });

    // 📌 Команда /admin (доступ тільки для адміністраторів)
    $bot->command('admin', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1; // 1 - стандартний доступ

        if ($accessLevel < 10) {
            $api->sendMessage($chatId, "⛔ У вас немає прав адміністратора!");
            return;
        }

        $api->sendMessage($chatId, "🔧 Панель адміністратора:\n".
            "👤 /assign_manager - Зробити користувача менеджером\n".
            "👤 /assign_admin - Зробити користувача адміністратором\n".
            "👤 /assign_user - Повернути роль звичайного користувача\n".
            "💵 /set_currency - Налаштувати курс USD");
    });

    // 📌 Команда /assign_manager (адмін може призначати менеджерів)
    $bot->command('assign_manager', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 10) {
            $api->sendMessage($chatId, "⛔ У вас немає прав!");
            return;
        }

        setUserWaitingForAction($chatId, "assign_manager");
        $api->sendMessage($chatId, "👤 Введіть *ID користувача*, якому призначити роль менеджера:", "Markdown", false, null, new \TelegramBot\Api\Types\ForceReply(true));
    });

    // 📌 Команда /assign_admin (адмін може призначати інших адміністраторів)
    $bot->command('assign_admin', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 10) {
            $api->sendMessage($chatId, "⛔ У вас немає прав!");
            return;
        }

        setUserWaitingForAction($chatId, "assign_admin");
        $api->sendMessage($chatId, "👤 Введіть *ID користувача*, якому призначити роль адміністратора:", "Markdown", false, null, new \TelegramBot\Api\Types\ForceReply(true));
    });

    // 📌 Команда /assign_user (адмін може повернути користувачеві роль звичайного користувача)
    $bot->command('assign_user', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 10) {
            $api->sendMessage($chatId, "⛔ У вас немає прав!");
            return;
        }

        setUserWaitingForAction($chatId, "assign_user");
        $api->sendMessage($chatId, "👤 Введіть *ID користувача*, щоб повернути йому стандартний рівень доступу:", "Markdown", false, null, new \TelegramBot\Api\Types\ForceReply(true));
    });

    // 📌 Команда /set_currency (менеджери та адміністратори можуть змінювати курс долара)
    $bot->command('set_currency', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 5) {
            $api->sendMessage($chatId, "⛔ У вас немає прав змінювати курс валют!");
            return;
        }

        setUserWaitingForAction($chatId, "set_currency");
        $api->sendMessage($chatId, "💵 Введіть новий курс USD → UAH (лише число, наприклад `39.50`):", "Markdown", false, null, new \TelegramBot\Api\Types\ForceReply(true));
    });

    // 📌 Команда /get_currency (будь-хто може переглянути курс)
    $bot->command('get_currency', function ($message) use ($api, $db) {
        $chatId = $message->getChat()->getId();

        // Отримуємо курс долара з БД
        $currency = $db->select('currency', ['exchange_rate', 'updated_at'], ['currency_code' => 'USD'])[0] ?? null;

        if ($currency) {
            $api->sendMessage($chatId, "💵 Поточний курс USD: *{$currency->exchange_rate}* грн\n📅 Оновлено: {$currency->updated_at}", "Markdown");
        } else {
            $api->sendMessage($chatId, "❌ Курс USD не знайдено в базі даних.", "Markdown");
        }
    });

    // 📌 Команда для увімкнення автооновлення
    $bot->command('enable_auto_update', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 10) {
            $api->sendMessage($chatId, "⛔ У вас немає прав для цієї дії!");
            return;
        }

        setAutoUpdateStatus('enabled');
        $api->sendMessage($chatId, "✅ Автоматичне оновлення курсу увімкнено.");
    });

    // 📌 Команда для вимкнення автооновлення
    $bot->command('disable_auto_update', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 10) {
            $api->sendMessage($chatId, "⛔ У вас немає прав для цієї дії!");
            return;
        }

        setAutoUpdateStatus('disabled');
        $api->sendMessage($chatId, "❌ Автоматичне оновлення курсу вимкнено.");
    });

    // 📌 Команда для перевірки статусу автооновлення
    $bot->command('status_auto_update', function ($message) use ($api) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 10) {
            $api->sendMessage($chatId, "⛔ У вас немає прав для цієї дії!");
            return;
        }

        $status = getAutoUpdateStatus();
        $api->sendMessage($chatId, "ℹ️ Статус автооновлення: " . ($status == 'enabled' ? "✅ Увімкнено" : "❌ Вимкнено"));
    });

    $bot->command('update_order', function ($message) use ($api, $db) {
        $chatId = $message->getChat()->getId();
        $user = getUserByTelegramId($chatId);
        $userId = $user ? $user->id : null;
        $accessLevel = $userId ? getUserAccessLevel($userId) : 1;

        if ($accessLevel < 5) {
            $api->sendMessage($chatId, "⛔ У вас немає прав змінювати статус замовлень!");
            return;
        }

        $params = explode(" ", trim($message->getText()));

        if (count($params) < 3) {
            $api->sendMessage($chatId, "❌ Неправильний формат! Використовуйте: `/update_order {order_id} new|processing|completed|cancelled`", "Markdown");
            return;
        }

        $orderId = intval($params[1]);
        $newStatus = $params[2];

        $validStatuses = ['new', 'processing', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            $api->sendMessage($chatId, "❌ Невірний статус! Доступні статуси: " . implode(", ", $validStatuses));
            return;
        }

        $updated = $db->update('orders', ['status' => $newStatus], ['id' => $orderId]);

        if ($updated) {
            $api->sendMessage($chatId, "✅ Статус замовлення #$orderId оновлено на *$newStatus*.", "Markdown");
        } else {
            $api->sendMessage($chatId, "❌ Помилка оновлення статусу замовлення #$orderId.");
        }
    });

    $bot->run();
} catch (Exception $e) {
    file_put_contents('log.txt', "❌ ПОМИЛКА: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
}
