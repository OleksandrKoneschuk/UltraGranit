<?php
/** @var array $rows */
use MVC\models\Users;

$this->Title = 'Категорії'; ?>

<main>
    <h1 class="text-center mb-5"> Категорії</h1>

    <link rel="stylesheet" href="/MVC/views/css/category.css">
    <div class="container">
        <div class="row">
            <?php foreach ($rows as $row) : ?>
                <div class="col-md-3 mb-4">
                    <a href="/category/view/<?= $row['id'] ?>">
                        <div class="card text-center h-100">
                            <img src="/files/category/<?= $row['photo'] ?>" class="card-img-top" alt="Фото не знайдено">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['name'] ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
