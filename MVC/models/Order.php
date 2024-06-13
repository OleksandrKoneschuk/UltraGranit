<?php
namespace MVC\models;

use core\Core;

class Order
{
    public static $tableName = 'orders';
    public static $orderProductsTableName = 'order_products';

    public static function createOrder($data)
    {
        $db = Core::get()->db;
        $db->beginTransaction();

        try {
            error_log("Order::createOrder - Starting transaction with data: " . json_encode($data));

            // Перевірка наявності необхідних полів
            if (empty($data['last_name']) || empty($data['first_name']) || empty($data['phone_number']) || empty($data['nova_poshta'])) {
                throw new \Exception('Не всі необхідні поля заповнені.');
            }

            // Отримуємо товари з кошика
            $userId = $data['user_id'];
            $products = Basket::getProductsInBasket($userId);
            error_log("Order::createOrder - Products in basket: " . json_encode($products));

            // Перевіряємо, чи є товари в кошику
            if (empty($products)) {
                error_log("Order::createOrder - Basket is empty, cancelling order creation.");
                $db->rollBack();
                return ['success' => false, 'error' => 'Кошик порожній.'];
            }

            $orderId = $db->insert(self::$tableName, $data);
            error_log("Order::createOrder - Order ID: " . $orderId);

            // Додаємо товари до замовлення
            foreach ($products as $product) {
                $orderProductData = [
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'price' => $product->price
                ];
                $db->insert(self::$orderProductsTableName, $orderProductData);
                error_log("Order::createOrder - Added product to order: " . json_encode($orderProductData));
            }

            // Очищуємо кошик
            Basket::clearBasket($userId);
            error_log("Order::createOrder - Cleared basket for user ID: " . $userId);

            $db->commit();
            return ['success' => true, 'order_id' => $orderId];
        } catch (\Exception $e) {
            error_log("Order::createOrder - Exception: " . $e->getMessage());
            $db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public static function getOrdersByUserId($userId)
    {
        $orders = Core::get()->db->select(self::$tableName, '*', ['user_id' => $userId]);

        foreach ($orders as &$order) {
            $order->products = self::getOrderProducts($order->id);
        }

        return $orders;
    }

    public static function getOrderProducts($orderId)
    {
        $orderProducts = Core::get()->db->select(self::$orderProductsTableName, '*', ['order_id' => $orderId]);
        $products = [];

        foreach ($orderProducts as $orderProduct) {
            $product = Core::get()->db->select('product', '*', ['id' => $orderProduct->product_id]);
            if (!empty($product)) {
                $product[0]->price = $orderProduct->price;
                $products[] = $product[0];
            }
        }

        return $products;
    }

    public static function getOrderTotalPrice($orderId)
    {
        $rows = Core::get()->db->select(self::$orderProductsTableName, 'SUM(price) as total_price', ['order_id' => $orderId]);
        return $rows[0]->total_price ?? 0;
    }
}
