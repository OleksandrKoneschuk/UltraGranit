<?php

namespace MVC\controllers;

use core\Controller;
use core\Core;
use http\Client\Curl\User;
use MVC\models\Users;

class UsersController extends Controller
{
    public function actionLogin()
    {
        if (Users::IsUserLogged())
            return $this->redirect('/users/account');
        if ($this->isPost) {
            $user = Users::verification($this->post->phone_number, $this->post->password);
            if (!empty($user)) {
                Users::LoginUser($user);
                return $this->redirect('/users/account');
            } else
                $this->addErrorMessage('Неправильний логін та/або пароль!');
        }

        return $this->render();
    }

    public function actionAccount()
    {
        return $this->render();
    }

    public function actionLogout()
    {
        Users::LogoutUser();
        return $this->redirect('/users/login');
    }

    public function actionRegister()
    {
        if ($this->isPost) {
            $user = Users::FindByPhoneNumber($this->post->phone_number);
            if (!empty($user)){
                $this->addErrorMessage('Користувач з таким номером телефону вже існує!');
            }

            if (strlen($this->post->first_name) === 0)
                $this->addErrorMessage('Ім\'я не вказано!');
            if (strlen($this->post->last_name) === 0)
                $this->addErrorMessage('Прізвище не вказано!');
            if (strlen($this->post->middle_name) === 0)
                $this->addErrorMessage('По батькові не вказано!');
            if (strlen($this->post->phone_number) === 0)
                $this->addErrorMessage('Номер телефону не вказано!');
            if (strlen($this->post->email) === 0)
                $this->addErrorMessage('Електрону пошту не вказано!');
            if (strlen($this->post->password) === 0)
                $this->addErrorMessage('Пароль не вказано!');
            if (strlen($this->post->confirm_password) === 0)
                $this->addErrorMessage('Підтвердження паролю не вказано!');
            if ($this->post->password != $this->post->confirm_password)
                $this->addErrorMessage('Паролі не співпадають!');

            if (!$this->isErrorMassageExists()){
                Users::RegisterUser($this->post->first_name, $this->post->last_name, $this->post->middle_name, $this->post->phone_number, $this->post->email, $this->post->password);

            }
        }
        return $this->render();
    }
}