<?php
/** @var stdClass $product */
/** @var array $photos */
/** @var string $mainPhoto */

$this->Title = $product->name;
?>

<link href="/MVC/views/css/product.css" rel="stylesheet">
<main>
    <h1 class="h3 mb-3 fw-normal text-center"><?= htmlspecialchars($product->name) ?></h1>
    <div class="container">
        <div class="row">
            <div class="col-6" style="width: 40%; height: 40%">
                <?php if (!empty($photos) || $mainPhoto) : ?>
                    <div id="productCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner">
                            <?php if ($mainPhoto) : ?>
                                <div class="carousel-item active">
                                    <img src="/<?= htmlspecialchars($mainPhoto) ?>" class="img-thumbnail" alt="Основне фото">
                                </div>
                            <?php endif; ?>
                            <?php foreach ($photos as $index => $photo) : ?>
                                <div class="carousel-item <?= $index === 0 && !$mainPhoto ? 'active' : '' ?>">
                                    <img src="/<?= htmlspecialchars($photo->photo_path) ?>" class="img-thumbnail" alt="Фото продукту">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
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
                    <button class="btn btn-primary add-to-cart" data-product-id="<?= $product->id ?>">Додати до кошику</button>
                </div>
            </div>
        </div>
        <div>
            <br/>
            <?= $product->description ?>
        </div>
    </div>
</main>