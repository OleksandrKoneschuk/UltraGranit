<?php

namespace core;

use Exception;
use core\Core;
use TelegramBot\Api\BotApi;

define('TOKEN', '7872489450:AAFY_H1TopVR9op3w3iHQc5ql34PwUL9p6s');

class TelegramBot
{
    public static function sendNewOrderNotification($orderId)
    {
        try {
            $db = Core::get()->db;

            $order = $db->select('orders', '*', ['id' => $orderId]);

            $order = $order[0];

            $orderProducts = $db->select('order_products', ['product_id', 'price'], ['order_id' => $order->id]);

            if (is_object($order)) {
                $order = (array)$order;
            }


            $managers = $db->select('users', ['telegram_id'], ['access_level' => 5]);
            $admins = $db->select('users', ['telegram_id'], ['access_level' => 10]);

            $managers = array_merge($managers, $admins);

            $message = "📦 *Нове замовлення #{$order['id']}*\n";
            $message .= "👤 Клієнт: *{$order['last_name']} {$order['first_name']}*\n";
            $phoneNumber = $order['phone_number'];
            if ($phoneNumber[0] !== '+') {
                $phoneNumber = '+38' . $phoneNumber;
            }
            $message .= "📞 Телефон: *{$phoneNumber}*\n";

            $message .= "📌 Доставка: *{$order['nova_poshta']}*\n";
            $message .= "📄 Коментар: *{$order['comment']}*\n";
            $message .= "🔄 Статус: *Нове*\n\n";

            if (!empty($orderProducts)) {
                $message .= "🛒 *Товари:*\n";
                foreach ($orderProducts as $orderProduct) {
                    // Отримуємо назву товару
                    $product = $db->select('product', ['name'], ['id' => $orderProduct->product_id])[0] ?? null;
                    $productName = $product ? $product->name : "❓ Невідомий товар";

                    $message .= "   🔹 $productName - *" . number_format($orderProduct->price, 2) . " грн*\n";
                }
            } else {
                $message .= "🛒 Товари не знайдені ❌\n";
            }

            $message .= "\n⚡ Оновити статус: `/update_order {$order['id']} processing|completed|cancelled`";

            $api = new BotApi(TOKEN);
            $api->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
            $api->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);

            foreach ($managers as $manager) {
                $api->sendMessage($manager->telegram_id, $message, "Markdown");
            }

        } catch (Exception $e) {
            file_put_contents('log.txt', "❌ ПОМИЛКА ВИКОНАННЯ: \n" . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}
