<?php
/** @var stdClass $product */
/** @var array $photos */
/** @var string $mainPhoto */

/** @var string $error_message Повідомлення про помилку
 * @var string $success_message Повідомлення про успіх
 */


$reviewData = $_SESSION['review_form_data'] ?? ['user_name' => '', 'rating' => '', 'review_text' => ''];
unset($_SESSION['review_form_data']); // Щоб після оновлення сторінки не зберігалось зайвий раз


$this->Title = $product->name;
?>


<link href="/MVC/views/css/product.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<main>
    <br/>
    <h1 class="h3 mb-3 fw-normal text-center"><?= htmlspecialchars($product->name) ?></h1>
    <div class="container">
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
        <form method="post" id="review-form">
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger d-flex p-2 align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                    <div style="padding: 0 5px 0 5px">
                        <?= $error_message ?>
                    </div>
                </div>
            <?php endif; ?>

            <input type="hidden" name="product_id" value="<?= $product->id ?>">

            <div class="mb-3">
                <label for="user_name" class="form-label">Ім'я:</label>
                <input type="text" name="user_name" id="user_name" class="form-control"
                       value="<?= htmlspecialchars($reviewData['user_name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="rating-container">
                <label class="form-label" style="margin-bottom: 0">Моя оцінка:</label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>"
                            <?= ($reviewData['rating'] == $i) ? 'checked' : '' ?>>
                        <label for="star<?= $i ?>"></label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="review_text" class="form-label">Текст відгуку:</label>
                <textarea name="review_text" id="review_text"
                          class="form-control"><?= htmlspecialchars($reviewData['review_text'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Надіслати відгук</button>
        </form>
        <br/>

        <h3>Відгуки про товар</h3>
        <div id="reviews">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="border p-3 mb-2 rounded">
                        <strong><?= htmlspecialchars($review->user_name) ?></strong>

                        <div class="review-stars">
                            <?php
                            $fullStars = intval($review->rating);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $fullStars) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>

                        <p class="mt-2"><?= nl2br(htmlspecialchars($review->review_text ?? '')) ?></p>
                        <small><?= date('d.m.Y', strtotime($review->created_at)) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Поки що немає відгуків. Будьте першим!</p>
            <?php endif; ?>
        </div>
    </div>
</main>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (window.location.hash === "#review-form") {
            document.getElementById("review-form").scrollIntoView({behavior: "smooth"});
        }
    });
</script>
