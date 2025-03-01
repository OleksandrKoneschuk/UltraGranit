<?php

namespace MVC\controllers;

use core\Controller;
use core\Core;
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

    public function actionView($params)
    {
        $id = intval($params[0]);
        $product = Product::getProductById($id);

        if (!$product) {
            return $this->redirect("/error/404"); // Перенаправлення на 404
        }

        $photos = Product::getProductPhotos($id);
        $mainPhoto = $product->main_photo;

        // Отримуємо категорію товару
        $category = Category::getCategoryById($product->category_id);

        // Формуємо breadcrumbs
        $breadcrumbs = [
            ['label' => 'Категорії', 'url' => '/category/index'],
        ];

        if ($category) {
            $breadcrumbs[] = ['label' => $category->name, 'url' => "/category/view/{$category->id}"];
        }

        $breadcrumbs[] = ['label' => $product->name, 'url' => "/product/view/$id"];

        $user = Users::GetLoggedUserData();
        $isAdmin = $user ? Users::isAdmin($user) : false;

        return $this->render(null, [
            'product' => $product,
            'photos' => $photos,
            'mainPhoto' => $mainPhoto,
            'breadcrumbs' => $breadcrumbs,
            'isAdmin' => $isAdmin
        ]);
    }

    public function actionAdd($params)
    {
        if (!Users::isAdmin($this->user)) {
            $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на додавання товару!');
            return;
        }

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

    public function actionLoadReviews() {
        $productId = $_GET['product_id'] ?? null;

        if (!$productId) {
            echo json_encode(['reviews' => []]);
            exit;
        }

        $reviews = ProductReview::getReviewsByProductId($productId);

        echo json_encode(['reviews' => $reviews]);
        exit;
    }

    public function actionAddReview() {
        $productId = $_POST['product_id'] ?? null;
        $userName = trim($_POST['user_name'] ?? 'Анонім');
        $rating = $_POST['rating'] ?? null;
        $reviewText = trim($_POST['review_text'] ?? '');

        if (!$productId || !$rating || empty($userName)) {
            echo json_encode(['success' => false, 'message' => 'Будь ласка, заповніть всі обов’язкові поля!']);
            exit;
        }

        if (!preg_match('/^[a-zA-Zа-яА-ЯіІїЇєЄґҐ\'\- ]+$/u', $userName)) {
            echo json_encode(['success' => false, 'message' => 'Ім’я містить заборонені символи або числа!']);
            exit;
        }

        if (strlen($reviewText) > 500) {
            echo json_encode(['success' => false, 'message' => 'Текст відгуку має бути від до 500 символів.']);
            exit;
        }

        $badWords = [
            'хуй', 'хуя', 'хуле', 'хули', 'хує', 'хуяк', 'хуякати', 'хуярити', 'хуєвий', 'хуєво', 'хуйню', 'хуйнi', 'хуйнiю',
            'хуйню', 'хуйня', 'хуйнiв', 'хуячий', 'хуяч', 'хуячити', 'хуярем', 'хуярю', 'хуярити', 'хуяра', 'хуярити',
            'залупа', 'залупи', 'залупний', 'залупитися', 'залупився', 'залупилися', 'залуплю', 'залуплюся',
            'блять', 'блядь', 'бляді', 'блядський', 'блядувати', 'блядуха', 'блядота', 'блядюга', 'бляха', 'бляха-муха', 'бляха муха',
            'гандон', 'гандони', 'гандонний', 'гандонити', 'гандониться', 'гандончик', 'гандоню', 'гандонювати',
            'гніда', 'гніди', 'гнідота', 'гнида', 'гниди', 'гнидник', 'гнидота',
            'пізда', 'піздєц', 'піздець', 'піздєц', 'піздити', 'піздюля', 'піздюліна', 'піздюхи', 'піздюк', 'піздюляка',
            'пизда', 'пиздець', 'пиздєц', 'пизданути', 'пизданув', 'пизданула', 'пиздить', 'пиздити', 'пиздюк',
            'заєбали', 'заєбати', 'заїбав', 'заїбати', 'заїбись', 'заєбца', 'заєбало', 'заєбу', 'заєбун',
            'єбав', 'єбати', 'єбать', 'єбанат', 'єбанько', 'єбанутий', 'єбашити', 'єбашу', 'єбучий', 'єбуча',
            'єбуче', 'єбучі', 'єбливий', 'єбливе', 'єбліс', 'єбало', 'єбали', 'єбальник', 'єбальня', 'єбучі',
            'єбло', 'єбливий', 'єблище', 'єбальце', 'єбашу', 'єбашити', 'єбашка', 'єбашня', 'блядушнік'
        ];

        $replacement = array_fill(0, count($badWords), '***');
        $reviewText = str_ireplace($badWords, $replacement, $reviewText);
        $reviewText = preg_replace('/\b('.implode('|', $badWords).')\b/ui', '***', $reviewText);

        $result = ProductReview::addReview($productId, htmlspecialchars($userName, ENT_QUOTES), $rating, htmlspecialchars($reviewText, ENT_QUOTES));

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Помилка збереження відгуку. Спробуйте ще раз!']);
        }
        exit;
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

    public function actionSearchAjax()
    {
        $query = htmlspecialchars($_GET['query'] ?? '', ENT_QUOTES, 'UTF-8');

        if (strlen($query) < 2) {
            echo json_encode(['products' => [], 'categories' => []]);
            exit();
        }

        // Пошук категорій
        $categories = Core::get()->db->search('category', ['name'], $query);

        // Пошук товарів
        $products = Core::get()->db->search('product', ['name', 'description'], $query);

        $user = Core::get()->session->get('user');
        $isAdmin = $user ? Users::isAdmin($this->user) : false;

        echo json_encode([
            'categories' => $categories,
            'products' => $products,
            'isAdmin' => $isAdmin
        ]);
        exit();
    }

    public function actionEdit($params)
    {
        if (!Users::isAdmin($this->user)) {
            $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на додавання редагування товару!');
            return;
        }

        $productId = intval($params[0]);
        $product = Product::getProductById($productId);
        $categories = Category::getCategories();
        $materials = Product::getMaterials();

        if (!Users::isAdmin($this->user)) {
            $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на редагування товару.');
            return;
        }

        if (empty($product)) {
            $this->router->error(404, 'Неіснуюча стоірнка!', 'Продукт не знайдено.');
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
        if (!Users::isAdmin($this->user)) {
            $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на видалення товару!');
            return;
        }

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

    public function actionDeleteReview()
    {
        if (!Users::isAdmin($this->user)) {
            $this->router->error(403,  'Відмовлено в доступі!','Ви не маєте дозволу на видалення відгуку!');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Невірний запит.']);
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['review_id'])) {
            echo json_encode(['success' => false, 'message' => 'Не передано ID відгуку.']);
            exit();
        }

        $reviewId = intval($data['review_id']);

        $review = ProductReview::getReviewById($reviewId);

        if (!$review) {
            echo json_encode(['success' => false, 'message' => 'Відгук не знайдено.']);
            exit();
        }

        if (!Users::isAdmin(Core::get()->session->get('user'))) {
            echo json_encode(['success' => false, 'message' => 'У вас немає прав для видалення.']);
            exit();
        }

        if (ProductReview::deleteReview($reviewId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Помилка при видаленні.']);
        }
        exit();
    }
}