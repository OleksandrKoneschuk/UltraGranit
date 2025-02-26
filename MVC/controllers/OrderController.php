<?php

namespace MVC\controllers;

use core\Controller;
use core\Router;
use MVC\models\Order;
use MVC\models\Basket;
use MVC\models\Users;
use Exception;
use core\TelegramBot;

class OrderController extends Controller
{
    protected $user;
    protected $router;

    public function __construct()
    {
        parent::__construct();
        $this->user = Users::GetLoggedUserData();
        $this->router = new Router('site/error');
    }

    public function actionCreate()
    {
        $user = Users::GetLoggedUserData();
        $userData = null;
        if ($user) {
            $userData = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'middle_name' => $user->middle_name,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
            ];
        }

        $userId = $this->user ? $this->user->id : null;
        $products = Basket::getProductsInBasket($userId);

        return $this->render(null, [
            'products' => $products,
            'user' => $userData
        ]);
    }

    public function actionSubmit()
    {
        try {
            header('Content-Type: application/json');

            $data = [
                'user_id' => $this->user ? $this->user->id : null,
                'last_name' => $this->post->surname ?? null,
                'first_name' => $this->post->name ?? null,
                'middle_name' => $this->post->patronymic ?? null,
                'phone_number' => $this->post->phone ?? null,
                'email' => $this->post->email ?? null,
                'nova_poshta' => $this->post->novaposhta ?? null,
                'comment' => $this->post->comment ?? null
            ];

            $result = Order::createOrder($data);

            if ($result['success']) {
                $_SESSION['order_id'] = $result['order_id'];
                $orderId = $result['order_id'];

                TelegramBot::sendNewOrderNotification($orderId);

                echo json_encode(['success' => true]);
            } else {
                throw new Exception($result['error']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }
}

