<?php

namespace MVC\controllers;

use core\Controller;
use core\Router;
use MVC\models\Category;
use MVC\models\Product;
use MVC\models\ProductReview;
use MVC\models\Users;

class ProductController extends Controller
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
        $rows = Product::getProduct();

        $viewPath = null;
        if (Users::isAdmin($this->user))
            $viewPath = 'index-admin';
        return $this->render($viewPath, [
            'rows' => $rows, 'user' => $this->user
        ]);
    }

    public function actionAdd($params)
    {
        $categoryId = isset($params[0]) ? intval($params[0]) : null;
        $categories = Category::getCategories();
        $materials = Product::getMaterials();

        if ($this->isPost) {
            $productId = Product::addProduct(
                $this->post->name,
                $categoryId,
                $this->post->material_id,
                $this->post->length_cm,
                $this->post->width_cm,
                $this->post->height_cm,
                $this->post->short_description,
                $this->post->description,
                $this->post->visible);

            $productDir = "files/products/{$productId}/";
            if (!is_dir($productDir)) {
                mkdir($productDir, 0777, true);
            }

            if (!empty($_FILES['main_photo']['tmp_name'])) {
                $mainPhotoPath = $productDir . 'main_' . uniqid() . '.jpg';
                move_uploaded_file($_FILES['main_photo']['tmp_name'], $mainPhotoPath);
                Product::updateProductMainPhoto($productId, $mainPhotoPath);
            }

            if (!empty($_FILES['additional_photos']['tmp_name'][0])) {
                foreach ($_FILES['additional_photos']['tmp_name'] as $key => $tmpName) {
                    if ($tmpName) {
                        $additionalPhotoPath = $productDir . uniqid() . '.jpg';
                        if (move_uploaded_file($tmpName, $additionalPhotoPath)) {
                            error_log("Successfully moved additional photo: " . $additionalPhotoPath);
                            Product::addProductPhoto($productId, $additionalPhotoPath);
                        } else {
                            error_log("Failed to move additional photo: " . $tmpName);
                        }
                    }
                }
            }

            return $this->redirect('/category/view/' . $categoryId);
        }

        return $this->render(null, [
            'categories' => $categories,
            'materials' => $materials,
            'category_id' => $categoryId
        ]);
    }

    public function actionLoadMore($params)
    {
        $categoryId = intval($params[0]);
        $page = intval($_GET['page']);
        $limit = intval($_GET['limit']);
        $sort = $_GET['sort'];

        $offset = ($page - 1) * $limit;

        if ($categoryId === 1) {
            $products = Product::getProducts($offset, $limit, $sort);
        } else {
            $products = Product::getProductsByCategory($categoryId, $offset, $limit, $sort);
        }

        $hasMore = count($products) === $limit;

        echo json_encode(['products' => $products, 'hasMore' => $hasMore, 'isAdmin' => Users::isAdmin($this->user)]);
        exit();
    }

    public function actionView($params)
    {
        $id = intval($params[0]);
        $product = Product::getProductById($id);
        $photos = Product::getProductPhotos($id);
        $mainPhoto = $product->main_photo;
        $reviews = ProductReview::getReviewsByProductId($id);

        if ($this->isPost) {
            $userName = trim($this->post->user_name ?? '');
            $rating = $this->post->rating ?? null;
            $reviewText = trim($this->post->review_text ?? '');

            $_SESSION['review_form_data'] = [
                'user_name' => $userName,
                'rating' => $rating,
                'review_text' => $reviewText
            ];

            // 🛑 Перевірка: Ім'я не може містити цифри
            if (empty($userName)) {
                $this->addErrorMessage('Будь ласка, введіть ваше ім\'я.');
            } elseif (!preg_match('/^[a-zA-Zа-яА-ЯіІїЇєЄґҐ\' -]+$/u', $userName)) {
                $this->addErrorMessage('Ім\'я може містити тільки літери, пробіли, апострофи або дефіси.');
            }

            // 🛑 Перевірка: Оцінка має бути числом від 1 до 5
            if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
                $this->addErrorMessage('Некоректний рейтинг. Виберіть оцінку від 1 до 5.');
            }

            // 🛑 Перевірка: Текст відгуку обов'язковий
            if (empty($reviewText)) {
                $this->addErrorMessage('Будь ласка, напишіть свій відгук.');
            }

            // ✅ Якщо немає помилок, додаємо відгук
            if (empty($this->errorMessages)) {
                ProductReview::addReview($id, $userName, $rating, $reviewText);
                unset($_SESSION['review_form_data']); // Очищаємо збережені дані після успішного додавання
                $this->addSuccessMessage('Ваш відгук додано.');
                return $this->redirect('/product/view/' . $id . '#review-form');
            }
        }

        return $this->render(null, [
            'product' => $product,
            'photos' => $photos,
            'mainPhoto' => $mainPhoto,
            'reviews' => $reviews
        ]);
    }

    public function actionEdit($params)
    {
        $productId = intval($params[0]);
        $product = Product::getProductById($productId);
        $categories = Category::getCategories();
        $materials = Product::getMaterials();

        if (!Users::isAdmin($this->user)) {
            $this->router->error(403, 'Ви не маєте дозволу на редагування товару.');
            return;
        }

        if (empty($product)) {
            $this->router->error(404, 'Продукт не знайдено.');
            return;
        }

        if ($this->isPost) {
            $name = $this->post->name ?? '';
            $price = $this->post->price ?? '';
            $category_id = $this->post->category_id ?? '';
            $material_id = $this->post->material_id ?? '';
            $length_cm = $this->post->length_cm ?? '';
            $width_cm = $this->post->width_cm ?? '';
            $height_cm = $this->post->height_cm ?? '';
            $short_description = $this->post->short_description ?? '';
            $description = $this->post->description ?? '';
            $mainPhoto = $_FILES['main_photo'] ?? null;
            $additionalPhotos = $_FILES['additional_photos'] ?? [];

            if (strlen($name) === 0) {
                $this->addErrorMessage('Назву продукту не вказано!');
            }
            if (strlen($price) === 0 || !is_numeric($price) || $price <= 0) {
                $this->addErrorMessage('Ціна продукту не вказана або вказана неправильно!');
            }
            if (strlen($category_id) === 0 || !is_numeric($category_id)) {
                $this->addErrorMessage('Категорія продукту не вказана або вказана неправильно!');
            }
            if (strlen($material_id) === 0 || !is_numeric($material_id)) {
                $this->addErrorMessage('Матеріал продукту не вказаний або вказаний неправильно!');
            }
            if (strlen($length_cm) === 0) {
                $this->addErrorMessage('Довжина не може бути 0!');
            }
            if (strlen($width_cm) === 0) {
                $this->addErrorMessage('Ширина не може бути 0!');
            }
            if (strlen($height_cm) === 0) {
                $this->addErrorMessage('Товщина не може бути 0!');
            }
            if (strlen($short_description) === 0) {
                $this->addErrorMessage('Короткий опис продукту не вказано!');
            }
            if (strlen($description) === 0) {
                $this->addErrorMessage('Опис продукту не вказано!');
            }

            if (!$this->isErrorMassageExists()) {
                $productData = [
                    'name' => $name,
                    'price' => $price,
                    'category_id' => $category_id,
                    'material_id' => $material_id,
                    'length_cm' => $length_cm,
                    'width_cm' => $width_cm,
                    'height_cm' => $height_cm,
                    'short_description' => $short_description,
                    'description' => $description,
                    'visible' => $this->post->visible ? 1 : 0
                ];

                if (!empty($mainPhoto['tmp_name'])) {
                    $photoPath = Product::addProductMainPhoto($productId, $mainPhoto['tmp_name']);
                    if ($photoPath) {
                        $productData['main_photo'] = $photoPath;
                    }
                }

                Product::updateProduct($productId, $productData);

                if (!empty($additionalPhotos['tmp_name'][0])) {
                    foreach ($additionalPhotos['tmp_name'] as $key => $tmpName) {
                        if ($additionalPhotos['error'][$key] === UPLOAD_ERR_OK) {
                            Product::addProductPhoto($productId, $tmpName);
                        }
                    }
                }

                $this->addSuccessMessage('Продукт успішно оновлено.');
                return $this->redirect('/category/view/'.$category_id);
            }
        }

        return $this->render(null, [
            'product' => $product,
            'categories' => $categories,
            'materials' => $materials
        ]);
    }

    public function actionDelete($params)
    {
        $productId = intval($params[0]);
        $confirm = $params[1] ?? null;

        if ($confirm === 'yes') {
            if (Users::isAdmin($this->user)) {
                Product::deleteProduct($productId);
                $this->addSuccessMessage('Продукт успішно видалено.');
            } else {
                $this->addErrorMessage('Ви не маєте дозволу на видалення товару.');
            }
            return $this->redirect('index');
        }

        $product = Product::getProductById($productId);
        if (!$product) {
            $this->addErrorMessage('Продукт не знайдено.');
            return $this->redirect('/category/index');
        }

        return $this->render('delete', [
            'product' => $product
        ]);
    }
}