<?php
/** @var string $Title */

/** @var string $Content */

use MVC\models\Users;
use core\Core;

$user = Core::get()->session->get('user');
$isAdmin = $user ? Users::isAdmin($user) : false;

if (empty($Title))
    $Title = '';
if (empty($Content))
    $Content = '';

$current_page = $_SERVER['REQUEST_URI'];

require_once 'core/CurrencyUpdater.php';

$currency = \core\CurrencyUpdater::getCurrentUSD();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $Title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"/>
    <link rel="icon" href="/public/uploads/logo3.png" type="image/x-icon">
    <link href="/MVC/views/css/styles_templates.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <script src="/MVC/views/js/basket.js" defer></script>
    <script src="/MVC/views/js/search.js" defer></script>
</head>
<div>
    <div class="wrapper">
        <header>
            <div class="container">
                <div class="logo">
                    <a href="/"><img src="/public/uploads/logo3.png" alt="Ultra Granit"></a>
                </div>

                <button class="btn menu-toggle" id="mobile-menu" type="button">
                    ☰ Меню
                </button>

                <nav class="nav-menu">
                    <ul class="nav">
                        <li class="nav-item"><a class="header-link <?= $current_page == '/' ? 'active' : '' ?>"
                                                href="/">Головна</a></li>
                        <li class="nav-item"><a
                                    class="header-link <?= strpos($current_page, '/category') === 0 ? 'active' : '' ?>"
                                    href="/category/index">Каталог</a></li>
                        <li class="nav-item"><a
                                    class="header-link <?= strpos($current_page, '/about') === 0 ? 'active' : '' ?>"
                                    href="/about">Про нас</a></li>
                        <li class="nav-item"><a
                                    class="header-link <?= strpos($current_page, '/contacts') === 0 ? 'active' : '' ?>"
                                    href="/contacts">Контакти</a></li>


                        <form class="search-form d-flex align-items-center position-relative">
                            <div class="input-group">
                                <!-- Іконка пошуку -->
                                <span class="input-group-text bg-white border-end-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </span>

                                <!-- Поле введення пошуку -->
                                <input type="text" id="search-input" class="form-control search-input border-start-0"
                                       placeholder="Пошук товарів..." aria-label="Пошук" autocomplete="off">

                                <span class="input-group-text bg-white border-end-0" type="button" id="clear-search">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                      <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </span>
                            </div>

                            <!-- Випадаючий список результатів -->
                            <div id="search-results"
                                 class="search-results-list position-absolute w-100 bg-white shadow rounded d-none mt-1"></div>
                        </form>



                        <!--                        <form class="search-form d-flex align-items-center">-->
<!--                            <div class="input-group">-->
<!--                                <span class="input-group-text">-->
<!--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"-->
<!--                                         class="bi bi-search" viewBox="0 0 16 16">-->
<!--                                      <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>-->
<!--                                    </svg>-->
<!--                                </span>-->
<!--                                <input type="text" id="search-input" class="form-control search-input" placeholder="Пошук..." aria-label="Пошук">-->
<!--                                <button type="button" id="clear-search" class="btn btn-light d-none">✖</button>-->
<!--                            </div>-->
<!--                            <div id="search-results" class="search-results-list position-absolute w-100 bg-white shadow-sm d-none"></div>-->
<!--                        </form>-->

                        <?php if ($isAdmin): ?>
                            <li class="nav-item">
                                <a class="admin-btn" href="/admin/index">Адмін-панель</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item"><a
                                    class="header-link <?= strpos($current_page, '/users/login') === 0 ? 'active' : '' ?>"
                                    href="/users/login">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                                     class="bi bi-person-fill" viewBox="0 0 14 14">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                </svg>
                            </a></li>
                        <li class="nav-item"><a
                                    class="header-link <?= strpos($current_page, '/basket/view') === 0 ? 'active' : '' ?>"
                                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                                    aria-controls="offcanvasRight">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                     class="bi bi-cart3" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                                </svg>
                            </a></li>
                    </ul>
                </nav>

                <div class="mobile-menu-container d-lg-none">
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/">Головна</a></li>
                        <li><a class="dropdown-item" href="/category/index">Каталог</a></li>
                        <li><a class="dropdown-item" href="/about">Про нас</a></li>
                        <li><a class="dropdown-item" href="/contacts">Контакти</a></li>
                        <li><a class="dropdown-item" href="/users/login">Акаунт</a></li>
                        <li><a class="dropdown-item" href="/basket/view">Кошик</a></li>
                    </ul>
                </div>
            </div>


            <div class="usd-rate">
                <h7>💵 Курс долара:</h7>
                <?php if ($currency): ?>
                    <p>1 USD = <strong><?= number_format($currency->exchange_rate, 2) ?> UAH</strong></p>
                    <small>Оновлено: <?= date('d.m.Y', strtotime($currency->updated_at)) ?></small>
                <?php else: ?>
                    <p class="text-danger">❌ Дані про курс відсутні</p>
                <?php endif; ?>
            </div>
        </header>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel"
             style="width: 500px">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasRightLabel">Кошик товарів</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">

            </div>
        </div>

        <main class="content">
            <?= $Content ?>
        </main>


        <footer>
            <div class="container">
                <div class="contact-info">
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-calendar"
                             viewBox="0 0 16 16">
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                        </svg>
                        Розклад роботи
                    </p>
                    <p>Пн-Пт 08:00 – 17:00</p>
                    <p>Сб-Нд Вихідний</p>
                </div>

                <div class="phone-info">
                    <div>
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-telephone-forward" viewBox="0 0 16 16">
                                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877zm10.762.135a.5.5 0 0 1 .708 0l2.5 2.5a.5.5 0 0 1 0 .708l-2.5 2.5a.5.5 0 0 1-.708-.708L14.293 4H9.5a.5.5 0 0 1 0-1h4.793l-1.647-1.646a.5.5 0 0 1 0-.708"/>
                            </svg>
                            Контактні номери
                        </p>
                    </div>
                    <div>
                        <p><i class="phone-icon"></i> <a href="tel:+380972772560"> +38 (097) 277 2560</a></p>
                        <p><i class="phone-icon"></i> <a href="tel:+380955768404"> +38 (095) 576 8404</a></p>
                    </div>
                </div>

                <div class="address-info">
                    <div>
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-pin-map"
                                 viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                      d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/>
                                <path fill-rule="evenodd"
                                      d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
                            </svg>
                            Адреса
                        </p>
                    </div>
                    <div>
                        <a href="https://maps.google.com/maps/dir//UltraGranit+%D0%93%D0%B2%D0%B0%D1%80%D0%B4%D0%B5%D0%B9%D1%81%D0%BA%D0%B0%D1%8F+
            37+%D0%B0+%D0%9A%D0%BE%D1%80%D0%BE%D1%81%D1%82%D0%B8%D1%88%D1%96%D0%B2,+
            %D0%96%D0%B8%D1%82%D0%BE%D0%BC%D0%B8%D1%80%D1%81%D1%8C%D0%BA%D0%B0+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C+
            12500/@50.3228605,29.0399562,18z/data=!4m5!4m4!1m0!1m2!1m1!1s0x472c81996be65113:0xc331bfdb23725503">
                            <p>м. Коростишів, вул. Гвардійська 37а</p>
                        </a>
                    </div>
                </div>

                <div class="email-info">
                    <div>
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-envelope-at" viewBox="0 0 16 16">
                                <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z"/>
                                <path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648m-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z"/>
                            </svg>
                            Екектронна адреса
                        </
                        >
                    </div>
                    <div>
                        <p></p><a href="mailto:ultra-granit@gmail.com"> ultra-granit@gmail.com</a></p>
                    </div>
                </div>

                <div class="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d722.3656758476837!2d29.038428460339855!3d50.32318876424221!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x472c81996be65113%3A0xc331bfdb23725503!2sUltraGranit!5e0!3m2!1suk!2sua!4v1718027700969!5m2!1suk!2sua"
                            width="300" height="150" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            <p>© <?= date("Y") ?> Ultra Granit. Усі права захищені.</p>
    </div>
    </footer>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuToggle = document.getElementById("mobile-menu");
        const navMenu = document.querySelector(".nav-menu");

        if (menuToggle && navMenu) {
            // Відкриття / Закриття меню при натисканні на кнопку
            menuToggle.addEventListener("click", function (event) {
                event.stopPropagation(); // Запобігаємо закриттю при кліку на кнопку
                navMenu.classList.toggle("active");
            });

            // Закриваємо меню при кліку поза його межами
            document.addEventListener("click", function (event) {
                if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                    navMenu.classList.remove("active");
                }
            });

            // Закриваємо меню при скролінгу сторінки
            window.addEventListener("scroll", function () {
                navMenu.classList.remove("active");
            });
        }
    });

</script>


</body>
</html>