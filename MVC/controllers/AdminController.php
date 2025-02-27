<?php

namespace MVC\controllers;

use core\Controller;
use core\Core;
use MVC\models\Order;
use MVC\models\Product;
use core\Router;
use MVC\models\Users;
use core\CurrencyUpdater;

class AdminController extends Controller
{
    protected $user;
    protected $router;
    public function __construct()
    {
        parent::__construct();
        $this->user = Users::GetLoggedUserData();
        $this->router = new Router('site/error');

    }

    public function actionIndex()
    {
        if (!Users::isAdmin($this->user)) {
            $this->router->error(403, 'Відмовлено в доступі!','Ви не маєте дозволу доступу до сторінки адміністратора!');
            return;
        }

        $db = Core::get()->db;

        // Отримуємо матеріали
        $materials = $db->select('materials', '*');

        // Отримуємо всі замовлення
        $orders = $db->select('orders', '*');

        // Формуємо дані про замовлення (додаємо ім'я клієнта)
        foreach ($orders as &$order) {
            $order->customer_name = trim("{$order->last_name} {$order->first_name} {$order->middle_name}");
            $order->total_price = $this->getOrderTotalPrice($order->id);
        }

        // Отримуємо поточний курс валют через CurrencyUpdater
        $currencyData = CurrencyUpdater::getCurrentUSD();
        $currentExchangeRate = $currencyData ? (float) $currencyData->exchange_rate : 0;

        // Чи включене автооновлення?
        $autoUpdateEnabled = file_exists('auto_update_status.txt') && trim(file_get_contents('auto_update_status.txt')) === 'enabled';

        return $this->render('/admin_panel', [
            'materials' => $materials,
            'orders' => $orders,
            'currentExchangeRate' => $currentExchangeRate,
            'autoUpdateEnabled' => $autoUpdateEnabled
        ]);
    }

    private function getOrderTotalPrice($orderId)
    {
        $db = Core::get()->db;
        $result = $db->select('order_products', 'SUM(price) as total_price', ['order_id' => $orderId]);
        return $result[0]->total_price ?? 0;
    }


    public function actionUpdatePrice()
    {
        if ($this->isPost) {
            $id = $this->post->id;
            $newPrice = $this->post->price;

            if ($id && $newPrice) {
                $db = Core::get()->db;

                $updated = $db->update('materials', ['price_per_m3' => $newPrice], ['id' => $id]);

                if ($updated) {
                    Core::get()->session->set('success_message', 'Ціну оновлено!');
                } else {
                    Core::get()->session->set('error_message', 'Помилка оновлення ціни');
                }

                return $this->redirect('/admin/index');
            }
        }
        Core::get()->session->set('error_message', 'Помилка оновлення ціни');
        return $this->redirect('/admin/index');
    }


    public function actionUpdateOrderStatus()
    {
        if ($this->isPost) {
            $id = $this->post->order_id;
            $newStatus = $this->post->status;

            if ($id && $newStatus) {
                $db = Core::get()->db;

                $updated = $db->update('orders', ['status' => $newStatus], ['id' => $id]);

                if ($updated) {
                    Core::get()->session->set('success_message', 'Статус замовлення оновлено!');
                } else {
                    Core::get()->session->set('error_message', 'Помилка оновлення статусу');
                }

                return $this->redirect('/admin/index');
            }
        }
        Core::get()->session->set('error_message', 'Помилка оновлення статусу');
        return $this->redirect('/admin/index');
    }






    public function actionUpdateExchangeRate()
    {
        if ($this->isPost) {
            $newRate = $this->post->exchange_rate;

            if ($newRate) {
                Core::get()->db->update('currency', ['exchange_rate' => $newRate], ['currency_code' => 'USD']);
                Core::get()->session->set('success_message', 'Курс оновлено!');
                return $this->redirect('/admin/index');
            }
        }
        Core::get()->session->set('error_message', 'Помилка оновлення курсу');
        return $this->redirect('/admin/index');
    }

    public function actionToggleAutoUpdate()
    {
        if ($this->isPost) {
            $status = $this->post->status;
            file_put_contents('auto_update_status.txt', $status);
            Core::get()->session->set('success_message', 'Статус автооновлення змінено!');
        }
        return $this->redirect('/admin/index');
    }

}
