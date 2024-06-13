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
        $userId = $this->user->id ?? null;
        $productId = intval($params[0]);

        Basket::removeProductFromBasket($userId, $productId);

        // Повернення оновленого списку продуктів
        $products = Basket::getProductsInBasket($userId);
        header('Content-Type: application/json');
        echo json_encode($products);
        exit();
    }
}
