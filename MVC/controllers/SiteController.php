<?php

namespace MVC\controllers;
use core\Controller;
use core\Template;
class SiteController extends Controller
{
    public function actionIndex(){
        return $this->render();
    }

    public function actionPrivacy()
    {
        return $this->render('privacy');
    }

    public function actionDocs() {
        return $this->render('docs');
    }

}

