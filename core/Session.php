<?php

namespace core;
use MVC\models\Users;
class Session
{
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function get($name)
    {
        if (empty($_SESSION[$name]))
            return null;
        return $_SESSION[$name];
    }

    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    public function getUser() {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }
}