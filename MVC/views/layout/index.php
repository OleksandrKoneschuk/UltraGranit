<?php
/** @var string $Title */
/** @var string $Content */

use MVC\models\Users;

if (empty($Title))
    $Title = '';
if (empty($Content))
    $Content = '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $Title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="/MVC/views/css/styles_templates.css" rel="stylesheet">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <a href="/"><img src="/public/uploads/logo3.png" alt="Ultra Granit"></a>
        </div>
        <nav>
            <ul>
                <li><a href="/">Головна</a></li>
                <li><a href="#">Каталог</a></li>
                <li><a href="#">Про нас</a></li>
                <li><a href="#">Контакти</a></li>
                <li><a href="/users/login"><img src="/public/uploads/user.png" alt="User Icon" class="user-icon"></a></li>

                <li><a href="#"><img src="/public/uploads/shopping-cart.png" alt="User Icon" class="user-icon"></a></li>
            </ul>
        </nav>
    </div>
</header>


    <?= $Content ?>


<footer>
    <div class="container">
        <div class="contact-info">
            <p>Контактна Інформація:</p>
            <p>Пн-Пт 09:00 – 19:00</p>
            <p>Сб 10:00 – 16:00</p>
        </div>
        <div class="phone-info">
            <p><i class="phone-icon"></i> +38 (097) 277 2560</p>
            <p>+38 (095) 576 8404</p>
        </div>
        <div class="address-info">
            <p><i class="address-icon"></i> Адреса</p>
            <p>м. Коростишів, вул. Гвардійська 37а</p>
        </div>
        <div class="email-info">
            <p><i class="email-icon"></i> ultra-granit@gmail.com</p>
        </div>
        <div class="map">
            <img src="/public/uploads/map.png" alt="Map">
        </div>
        <p>© 2024 Ultra Granit. Всі права захищені.</p>
    </div>
</footer>
</body>
</html>