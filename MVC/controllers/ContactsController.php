<?php

namespace MVC\controllers;

use core\Controller;

class ContactsController extends Controller
{
    public function actionIndex()
    {
        return $this->render();
    }
}