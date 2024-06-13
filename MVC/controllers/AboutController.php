<?php

namespace MVC\controllers;

use core\Controller;

class AboutController extends Controller
{
    public function actionIndex()
    {
        return $this->render();
    }
}