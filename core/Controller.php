<?php

namespace core;

class Controller
{
    protected $template;
    protected $errorMessages;
    protected $successMessages;
    public $isPost = false;
    public $isGet = false;
    public $post;
    public $get;
    public $route;

    public function __construct()
    {
        $action = Core::get()->actionName;
        $module = Core::get()->moduleName;
        $path = "MVC/views/{$module}/{$action}.php";
        $this->template = new Template($path);
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST' :
                $this->isPost = true;
                break;
            case 'GET' :
                $this->isGet = true;
                break;
        }
        $this->post = new Post();
        $this->get = new Get();
        $this->errorMessages = [];
        $this->successMessages = [];
    }

    public function render($pathToView = null, $data = []) :array
    {
        if (!empty($pathToView)) {
            $module = Core::get()->moduleName;
            $pathToView = __DIR__ . "/../MVC/views/{$module}/{$pathToView}.php";
            $this->template->setTemplateFilePath($pathToView);
        }
        $this->template->setParams($data);
        return [
            'Content' => $this->template->getHTML()
        ];
    }

    public function redirect($path) :void
    {
        header("Location: {$path}");
        die;
    }

    public function getJsonResponse() {
        return [
            'error_message' => !empty($this->errorMessages) ? implode('<br/>', $this->errorMessages) : null,
            'success_message' => !empty($this->successMessages) ? implode('<br/>', $this->successMessages) : null
        ];
    }

    public function addErrorMessage($massage = null) :void
    {
        $this->errorMessages [] = $massage;
        $this->template->setParam('error_message', implode('<br/>', $this->errorMessages));
    }

    public function addSuccessMessage($massage = null) :void
    {
        $this->successMessages [] = $massage;
        $this->template->setParam('success_message', implode('<br/>', $this->successMessages));
    }

    public function clearErrorMessage() :void
    {
        $this->errorMessages = [];
        $this->template->setParam('error_message', null);
    }

    public function clearSuccessMessage() :void
    {
        $this->successMessages = [];
        $this->template->setParam('success_message', null);
    }

    public function isErrorMassageExists() : bool
    {
        return count($this->errorMessages) > 0;
    }

    public function isSuccessMessageExists() : bool
    {
        return count($this->successMessages) > 0;
    }
}