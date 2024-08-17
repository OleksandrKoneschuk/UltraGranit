<?php

namespace MVC\controllers;

use core\Controller;
use core\Router;
use MVC\models\Basket;
use MVC\models\Users;

class BasketController extends Controller
{
    protected $user;
    protected $router;

    public function __construct()
    {
        parent::__construct();
        $this->user = Users::GetLoggedUserData();
        $this->router = new Router('site/error');
    }

    public function actionView()
    {
        $userId = $this->user->id ?? null;
        $products = Basket::getProductsInBasket($userId);
        header('Content-Type: application/json');
        echo json_encode($products);
        exit();
    }

    public function actionAdd($params)
    {
        $userId = $this->user->id ?? null;
        $productId = intval($params[0]);

        Basket::addProductToBasket($userId, $productId);

        $products = Basket::getProductsInBasket($userId);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'products' => $products]);
        exit();
    }

    public function actionRemove($params)
    {
        header('Content-Type: application/json');
        try {
            $basketItemId = intval($params[0]);
            $userId = $this->user ? $this->user->id : null;

            if ($userId) {
                Basket::removeProductFromBasket($userId, $basketItemId);
            } else {
                Basket::removeProductFromBasketSession($basketItemId);
            }

            $products = Basket::getProductsInBasket($userId);
            echo json_encode($products);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }
}
