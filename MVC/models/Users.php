<?php

namespace MVC\models;

use core\Core;
use core\Model;

/**
 * @property int $id Id
 * @property string $first_name Ім'я
 * @property string $last_name Прізвище
 * @property string middle_name По батькові
 * @property string $phone_number Номер телефону(логін)
 * @property string $email Електронна пошта
 * @property string $password Пароль
 */
class Users extends Model
{
    public static $tableName = 'users';

    public static function verification($phone_number, $password)
    {
        $rows = self::findByCondition(['phone_number' => $phone_number]);
        if (!empty($rows)) {
            $user = $rows[0];
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }
        return null;
    }

    public static function isAdmin($user)
    {
        if ($user === null || $user->access_level === 1) {
            return false;
        } else {
            return $user->access_level === 10;
        }
    }

    public static function FindByPhoneNumber($phone_number)
    {
        $rows = self::findByCondition(['phone_number' => $phone_number]);
        if (!empty($rows))
            return $rows[0];
        else
            return null;
    }

    public static function IsUserLogged()
    {
        return !empty(Core::get()->session->get('user'));
    }

    public static function LoginUser($user)
    {
        Core::get()->session->set('user', $user);
    }

    public static function LogoutUser()
    {
        Core::get()->session->remove('user');
    }

    public static function GetLoggedUserData()
    {
        return Core::get()->session->get('user');
    }

    public static function RegisterUser($first_name, $last_name, $middle_name, $phone_number, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $user = new Users();
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->middle_name = $middle_name;
        $user->phone_number = $phone_number;
        $user->email = $email;
        $user->password = $hashedPassword;
        $user->save();
    }
}