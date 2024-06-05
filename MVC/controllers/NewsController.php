<?php

namespace MVC\controllers;

use core\Controller;
use core\Core;
use MVC\models\News;
use MVC\models\Users;

class NewsController extends Controller
{
    public function actionIndex()
    {
        $row = Users::findById(1);
        var_dump($row);
        die();

        return $this->render();
    }

    public function actionView()
    {
        return $this->render();
    }
}