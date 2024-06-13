<?php

namespace MVC\models;

use core\Core;

class Basket
{
    public static $tableName = 'basket';

    public static function addProductToBasket($userId, $productId)
    {
        if ($userId) {
            Core::get()->db->insert(self::$tableName, [
                'user_id' => $userId,
                'product_id' => $productId
            ]);
        } else {
            if (!isset($_SESSION['basket'])) {
                $_SESSION['basket'] = [];
            }
            if (!isset($_SESSION['basket'][$productId])) {
                $_SESSION['basket'][$productId] = 0;
            }
            $_SESSION['basket'][$productId]++;
        }
    }

    public static function getProductsInBasket($userId)
    {
        if ($userId) {
            $rows = Core::get()->db->select(self::$tableName, '*', [
                'user_id' => $userId
            ]);
        } else {
            $rows = [];
            if (isset($_SESSION['basket'])) {
                foreach ($_SESSION['basket'] as $productId => $quantity) {
                    $rows[] = (object)[
                        'product_id' => $productId,
                        'quantity' => $quantity
                    ];
                }
            }
        }

        $products = [];
        foreach ($rows as $row) {
            $product = Core::get()->db->select('product', '*', [
                'id' => $row->product_id
            ]);
            if (!empty($product)) {
                $product[0]->basket_id = $row->id ?? null; // Додаємо basket_id до продукту
                $product[0]->quantity = $row->quantity ?? 1;
                $products[] = $product[0];
            }
        }
        return $products;
    }

    public static function removeProductFromBasket($userId, $basketItemId)
    {
        if ($userId) {
            Core::get()->db->delete(self::$tableName, [
                'id' => $basketItemId,
                'user_id' => $userId
            ]);
        } else {
            unset($_SESSION['basket'][$basketItemId]);
        }
    }

    public static function clearBasket($userId)
    {
        if ($userId) {
            Core::get()->db->delete(self::$tableName, [
                'user_id' => $userId
            ]);
        } else {
            unset($_SESSION['basket']);
        }
    }
}
