<?php

namespace MVC\controllers;

use core\Controller;
use core\Core;
use http\Client\Curl\User;
use MVC\models\Order;
use MVC\models\Users;

class UsersController extends Controller
{
    public function actionLogin()
    {
        $successMessage = Core::get()->session->get('success_message');
        $registeredPhoneNumber = Core::get()->session->get('registered_phone_number');

        if ($successMessage) {
            $this->template->setParam('success_message', $successMessage);
            Core::get()->session->remove('success_message');
        }

        if ($registeredPhoneNumber) {
            $this->template->setParam('registered_phone_number', $registeredPhoneNumber);
            Core::get()->session->remove('registered_phone_number');
        }

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
        $user = Users::GetLoggedUserData();
        if ($user) {
            $orders = Order::getOrdersByUserId($user->id);
            return $this->render('account', ['user' => $user, 'orders' => $orders]);
        } else {
            return $this->redirect('/users/login');
        }
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
                Core::get()->session->set('success_message', 'Ви успішно зареєстровані! <br/> Увійдіть в акаунт');
                Core::get()->session->set('registered_phone_number', $this->post->phone_number);

                return $this->redirect('/users/login');
            }
        }
        return $this->render();
    }
}