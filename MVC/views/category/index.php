<?php
/** @var array $rows */
use MVC\models\Users;

$this->Title = 'Каталог'; ?>

<main>
    <link rel="stylesheet" href="/MVC/views/css/category.css">

    <div class="container">

        <nav aria-label="breadcrumb"  class="path">
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
                <li class="breadcrumb-item active" aria-current="page">
                    Каталог
                </li>
            </ol>
        </nav>


        <h1 class="text-center mb-5"> Каталог</h1>
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