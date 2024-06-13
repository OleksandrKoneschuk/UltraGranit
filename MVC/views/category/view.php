<?php
/** @var stdClass $category */
/** @var array $products */

/** @var array $breadcrumbs */

use MVC\models\Users;

$this->Title = $category->name;
$user = Users::GetLoggedUserData();
?>

<main>
    <link rel="stylesheet" href="/MVC/views/css/category.css">
    <div class="container">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb p-3 bg-body-tertiary rounded-3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis" href="/">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#333"
                             class="bi bi-house-fill" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                            <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                        </svg>
                        <span class="visually-hidden">Home</span>
                    </a>
                </li>
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <?php if (end($breadcrumbs) === $breadcrumb): ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= htmlspecialchars($breadcrumb['label']) ?>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a class="link-body-emphasis fw-semibold text-decoration-none"
                               href="<?= htmlspecialchars($breadcrumb['url']) ?>">
                                <?= htmlspecialchars($breadcrumb['label']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-3">
                <div class="filter-sort">
                    <h4>Сортування</h4>
                    <select id="sort" class="form-select">
                        <option value="name_asc">Назва (А-Я)</option>
                        <option value="name_desc">Назва (Я-A)</option>
                        <option value="price_asc">Ціна (за зростанням)</option>
                        <option value="price_desc">Ціна (за спаданням)</option>
                    </select>

                    <h4 class="mt-4">Фільтри</h4>
                    <div class="filter">
                        <h5>Призначення</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="family" id="family">
                            <label class="form-check-label" for="family">Сімейні</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="mother" id="mother">
                            <label class="form-check-label" for="mother">Для матері</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="father" id="father">
                            <label class="form-check-label" for="father">Для батька</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="husband" id="husband">
                            <label class="form-check-label" for="husband">Для чоловіка</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="wife" id="wife">
                            <label class="form-check-label" for="wife">Для дружини</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="child" id="child">
                            <label class="form-check-label" for="child">Дитячі</label>
                        </div>

                        <h5 class="mt-3">Камінь</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="granite" id="granite">
                            <label class="form-check-label" for="granite">Граніт</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="gabbro" id="gabbro">
                            <label class="form-check-label" for="gabbro">Габро</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="marble" id="marble">
                            <label class="form-check-label" for="marble">Мармур</label>
                        </div>

                        <h5 class="mt-3">Колір</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="green" id="green">
                            <label class="form-check-label" for="green">Зелений</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="brown" id="brown">
                            <label class="form-check-label" for="brown">Коричневий</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="gray" id="gray">
                            <label class="form-check-label" for="gray">Сірий</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="red" id="red">
                            <label class="form-check-label" for="red">Червоний</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="black" id="black">
                            <label class="form-check-label" for="black">Чорний</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <?php if (Users::isAdmin($user)) : ?>
                    <a href="/product/add/<?= $category->id ?>" class="btn btn-primary mb-3" style="margin-top: 10px">
                        Додати продукт</a>
                <?php endif; ?>

                <h1><?= htmlspecialchars($category->name) ?></h1>

                <div id="product-list" class="row">
                    <!-- Initial products will be loaded here -->
                </div>

                <div class="d-flex justify-content-center">
                    <button id="load-more" class="btn btn-secondary" style="margin-bottom: 20px">Завантажити ще товари
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const isAdmin = <?= json_encode(Users::isAdmin($user)); ?>;
</script>
<script src="/MVC/views/js/category.js" defer></script>