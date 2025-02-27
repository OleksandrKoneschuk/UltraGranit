<?php

namespace core;

class Router
{
    protected $route;

    public function __construct($route)
    {
        $this->route = $route;
    }


    public function run() {
        $parts = explode('/', $this->route);
        if (strlen($parts[0]) == 0) {
            $parts[0] = 'Site';
            $parts[1] = 'index';
        }
        if (count($parts) == 1) {
            $parts[1] = 'index';
        }

        Core::get()->moduleName = $parts[0];
        Core::get()->actionName = $parts[1];

        $controller = 'MVC\\controllers\\'.ucfirst($parts[0]) .'Controller';
        $method = 'action'.ucfirst($parts[1]);
        if (class_exists($controller)){
            $controllerObject = new $controller();
            Core::get()->controllerObject = $controllerObject;
            $method = 'action' . str_replace('_', '', ucfirst($parts[1]));
            if (method_exists($controllerObject, $method)) {
                array_splice($parts, 0, 2);
                return $controllerObject->$method($parts);
            } else {
                return $this->error(404, 'Сторінку не знайдено...');
            }
        } else {
            return $this->error(404, 'Сторінку не знайдено...');
        }
    }

    public function done(){
    }

    public function error($code, $message = '') : void {
        http_response_code($code);
        $this->render('error', [
            'errorCode' => $code,
            'errorMessage' => $message
        ]);
    }

    protected function render($view, $data = []) {
        extract($data);
        include __DIR__ . "/../MVC/views/site/$view.php";
        exit;
    }
}
