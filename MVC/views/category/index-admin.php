<?php
/** @var array $rows */
use MVC\models\Users;

$this->Title = 'Категорії'; ?>

<main>
<h1 class="text-center  mb-5"> Категорії</h1>
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
                            <?php if (Users::isAdmin($user)) : ?>
                                <div class="card-body">
                                    <a href="/category/edit/<?= $row['id'] ?>" class="btn btn-warning mb-1">Редагувати категорію</a>
                                    <a href="/category/delete/<?= $row['id'] ?>" class="btn btn-danger">Видалити категорію</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if (Users::isAdmin($user)) : ?>
        <div class="d-flex justify-content-center">
            <a href="/category/add" class="btn btn-primary  mb-5">Додати категорію</a>
        </div>
    <?php endif; ?>
</main>