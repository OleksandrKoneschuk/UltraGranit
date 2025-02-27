<?php

namespace MVC\controllers;

use core\Controller;
use core\Core;
use core\Router;
use MVC\models\Category;
use MVC\models\Product;
use MVC\models\Users;


class CategoryController extends Controller
{
    protected $user;
    protected $router;
    public function __construct()
    {
        parent::__construct();
        $this->user = Users::GetLoggedUserData();
        $this->router = new Router('site/error');
    }

    public function actionIndex()
    {
        $rows = Category::getCategories();

        $viewPath = null;
        if (Users::isAdmin($this->user))
            $viewPath = 'index-admin';

        return $this->render($viewPath, [
            'rows'=>$rows, 'user' => $this->user
        ]);
    }

    public function actionView($params)
    {
        $categoryId = intval($params[0]);

        if ($categoryId === 1) {
            $category = (object)['name' => 'Усі товари', 'id' => 1];
        } else {
            $category = Category::getCategoryById($categoryId);
        }

        $breadcrumbs = [
            ['label' => 'Категорії', 'url' => '/category/index'],
            ['label' => $category->name, 'url' => "/category/view/$categoryId"]
        ];

        return $this->render(null, [
            'category' => $category,
            'user' => Users::GetLoggedUserData(),
            'breadcrumbs' => $breadcrumbs
        ]);
    }
    protected function generateBreadcrumbs($route)
    {
            $parts = explode('/', trim($route, '/'));
            $breadcrumbs = [];
            $url = '';

            $categoryNames = [
                '1' => 'Усі товари',
                '2' => 'Одинарні',
                '3' => 'Подвійні',
                '4' => 'Комплекси'
            ];

            foreach ($parts as $part) {
                // Пропускаємо 'View'
                if ($part === 'view') {
                    continue;
                }

                $url .= '/' . $part;

                if (strtolower($part) === 'category') {
                    $breadcrumbs[] = [
                        'label' => 'Категорії',
                        'url' => $url
                    ];
                } elseif (is_numeric($part) && isset($categoryNames[$part])) {
                    $breadcrumbs[] = [
                        'label' => $categoryNames[$part],
                        'url' => $url
                    ];
                } else {
                    $breadcrumbs[] = [
                        'label' => ucfirst($part),
                        'url' => $url
                    ];
                }
            }

            return $breadcrumbs;
    }

    public function actionLoadMore()
    {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 9);
        $sort = $_GET['sort'] ?? 'name_asc';
        $filters = explode(',', $_GET['filters'] ?? '');

        $offset = ($page - 1) * $limit;

        $sortColumn = 'name';
        $sortDirection = 'ASC';

        switch ($sort) {
            case 'name_desc':
                $sortDirection = 'DESC';
                break;
            case 'price_asc':
                $sortColumn = 'price';
                break;
            case 'price_desc':
                $sortColumn = 'price';
                $sortDirection = 'DESC';
                break;
        }

        $products = Product::getProducts($offset, $limit, $sortColumn, $sortDirection, $filters);

        header('Content-Type: application/json');
        echo json_encode(['products' => $products, 'hasMore' => count($products) === $limit]);
        exit();
    }

    public function actionAdd()
    {
        if (!Users::isAdmin($this->user)) {
            $this->router->error(403, 'Відмовлено в доступі!', 'Ви не маєте дозволу на додавання категорії.');
            return;
        }

        if ($this->isPost) {
            if (strlen($this->post->name) === 0)
                $this->addErrorMessage('Назву категорії не вказано!');

            if (!$this->isErrorMassageExists()){
                Category::addCategory($this->post->name, $_FILES['file']['tmp_name']);
                return $this->redirect('index');
            }
        }
        return $this->render();
    }

    public function actionEdit($params)
    {
        $id = intval($params[0]);
        if (!Users::isAdmin($this->user)) {
            return $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на редагування категорії.');
        }

        if ($id > 0) {
            if ($this->isPost) {
                Category::updateCategory($id, $this->post->name);
                if (!empty($_FILES['file']['tmp_name']))
                    Category::changePhoto($id, $_FILES['file']['tmp_name']);
                return $this->redirect('/category/index');
            }

            $category = Category::getCategoryById($id);
            return $this->render(null, [
                'category' => $category
            ]);

        } else
            return $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на редагування категорії.');
    }

    public function actionDelete($params)
    {
        $id = intval($params[0]);
        $yes = isset($params[1]) && $params[1] === 'yes';

        if (!Users::isAdmin($this->user)) {
            return $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на видалення категорії.');
        }

        if ($id > 0) {
            $category = Category::getCategoryById($id);
            if ($yes) {
                $filePath = 'files/category/' . $category->photo;
                if (is_file($filePath)) {
                    unlink($filePath);
                }
                Category::deleteCategory($id);
                return $this->redirect('/category/index');
            }

            return $this->render(null, [
                'category' => $category
            ]);
        } else
            return $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на видалення категорії.');
    }

    protected function error($code, $message)
    {
        http_response_code($code);
        $router = new Router($this->route);
        $router->error($code, $message);
        exit;
    }
}