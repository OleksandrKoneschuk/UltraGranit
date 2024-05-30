<?php
include '../templates/header.php';
?>

<main class="login-main">
    <div class="login-container">
        <h2>Авторизація</h2>
        <form action="login.php" method="post">
            <label for="username">Логін:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Увійти</button>
        </form>
        <a href="registration.php">Зареєструватись</a>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Обробка авторизації
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Тут має бути код для перевірки користувача у базі даних
            // Наприклад:
            // $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
            // if ($result->num_rows > 0) {
            //     // Успішна авторизація
            //     echo '<p>Успішно авторизовано</p>';
            // } else {
            //     echo '<p>Невірний логін або пароль</p>';
            // }
        }
        ?>
    </div>
</main>

<?php
include '../templates/footer.php';
?>
