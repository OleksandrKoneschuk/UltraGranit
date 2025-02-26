<?php

namespace core;

class CurrencyUpdater
{
    public static function updateIfNeeded()
    {
        if (!file_exists('auto_update_status.txt') || trim(file_get_contents('auto_update_status.txt')) !== 'enabled') {
            return;
        }

        $db = Core::get()->db;

        $lastUpdate = $db->select('currency', ['updated_at'], ['currency_code' => 'USD'])[0] ?? null;

        if (!$lastUpdate || strtotime($lastUpdate->updated_at) < strtotime('-1 day')) {
            self::updateCurrency();
        }
    }

    public static function updateCurrency()
    {
        $db = Core::get()->db;
        $apiUrl = "https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5";

        $response = file_get_contents($apiUrl);
        if (!$response) {
            return false;
        }

        $data = json_decode($response, true);
        foreach ($data as $currency) {
            if ($currency['ccy'] === 'USD') {
                $exchangeRate = floatval($currency['sale']);

                // Оновлюємо курс у БД
                $db->update('currency', [
                    'exchange_rate' => $exchangeRate,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['currency_code' => 'USD']);
                return true;
            }
        }
        return false;
    }

    public static function getCurrentUSD()
    {
        require_once 'core/Core.php';
        $db = Core::get()->db;

        $result = $db->select('currency', ['exchange_rate', 'updated_at'], ['currency_code' => 'USD']);
        return $result ? $result[0] : null;
    }
}