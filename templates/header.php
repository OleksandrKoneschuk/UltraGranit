<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ultra Granit</title>
    <link rel="stylesheet" href="../public/css/styles_templates.css">
    <?php
    $current_file = basename($_SERVER['PHP_SELF']);
    if ($current_file == 'login.php') {
        echo '<link rel="stylesheet" href="css/login.css">';
    } elseif ($current_file == 'registration.php') {
        echo '<link rel="stylesheet" href="css/registration.css">';
    }
    ?>
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <a href="../public/index.php"><img src="../public/uploads/logo3.png" alt="Ultra Granit"></a>
        </div>
        <nav>
            <ul>
                <li><a href="#">Головна</a></li>
                <li><a href="#">Каталог</a></li>
                <li><a href="#">Про нас</a></li>
                <li><a href="#">Контакти</a></li>
                <li><a href="../public/login.php"><img src="../public/uploads/user.png" alt="User Icon" class="user-icon"></a></li>
                <li><a href="#"><img src="../public/uploads/shopping-cart.png" alt="User Icon" class="user-icon"></a></li>
            </ul>
        </nav>
    </div>
</header>
