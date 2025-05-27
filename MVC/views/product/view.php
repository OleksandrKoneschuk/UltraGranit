<?php
/** @var stdClass $product */
/** @var array $photos */
/** @var string $mainPhoto */
/** @var boolean $isAdmin */

/** @var string $error_message Повідомлення про помилку
 * @var string $success_message Повідомлення про успіх
 */

use MVC\models\Users;

/** @var array $breadcrumbs */

$reviewData = $_SESSION['review_form_data'] ?? ['user_name' => '', 'rating' => '', 'review_text' => ''];
unset($_SESSION['review_form_data']);

$this->Title = $product->name;
?>

<body data-is-admin="<?= $isAdmin ? 'true' : 'false' ?>">
<link href="/MVC/views/css/product.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="/MVC/views/js/review.js" defer></script>

<main>
    <div class="container">
        <nav aria-label="breadcrumb" class="path">
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
        <h1 class="h3 mb-3 fw-normal text-center"><?= htmlspecialchars($product->name) ?></h1>

        <div class="row">
            <div class="col-6" style="width: 40%; height: 40%">
                <?php if (!empty($photos) || $mainPhoto) : ?>
                    <div id="productCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner">
                            <?php if ($mainPhoto) : ?>
                                <div class="carousel-item active">
                                    <img src="/<?= htmlspecialchars($mainPhoto) ?>" class="img-thumbnail"
                                         alt="Основне фото">
                                </div>
                            <?php endif; ?>
                            <?php foreach ($photos as $index => $photo) : ?>
                                <div class="carousel-item <?= $index === 0 && !$mainPhoto ? 'active' : '' ?>">
                                    <img src="/<?= htmlspecialchars($photo->photo_path) ?>" class="img-thumbnail"
                                         alt="Фото продукту">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel"
                                data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel"
                                data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-6">
                <div class="row mb-3">
                    <div class="col-3">
                        Ціна товару:
                    </div>
                    <div class="col-4">
                        <?= htmlspecialchars($product->price) ?> грн.
                    </div>
                </div>
                <div>
                    <i><u>Короткий опис:</u></i>
                    <?= $product->short_description ?>
                </div>
                <div>
                    <button class="btn btn-primary add-to-cart" data-product-id="<?= $product->id ?>">Додати до кошику
                    </button>
                </div>
            </div>
        </div>
        <div>
            <br/>
            <i><u>Опис товару:</u></i>
            <?= $product->description ?>
        </div>
        <br/>

        <h3>Додати відгук</h3>
        <div id="review-messages"></div>

        <form id="review-form">
            <input type="hidden" name="product_id" value="<?= $product->id ?>">

            <div class="mb-3">
                <label for="user_name" class="form-label">Ім'я:</label>
                <input type="text" name="user_name" id="user_name" class="form-control" required>
            </div>

            <div class="rating-container">
                <label class="form-label" style="margin-bottom: 0">Моя оцінка:</label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>">
                        <label for="star<?= $i ?>"></label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="review_text" class="form-label">Текст відгуку:</label>
                <textarea name="review_text" id="review_text" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Надіслати відгук</button>
        </form>

        <br/>

        <h3>Відгуки про товар</h3>
        <div id="reviews">

        </div>
    </div>
</main>
