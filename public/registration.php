<?php
include '../templates/header.php';
?>

<main class="registration-main">
    <div class="registration-container">
        <h2>Реєстрація</h2>
        <form action="registration.php" method="post">
            <label for="username">Логін:</label>
            <input type="text" id="username" name="username" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password">Підтвердіть пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Зареєструватись</button>
        </form>
        <a href="login.php">Увійти</a>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Обробка реєстрації
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password === $confirm_password) {
                // Тут має бути код для збереження користувача у базі даних
                // Наприклад:
                // $conn->query("INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')");
                // echo '<p>Успішно зареєстровано</p>';
            } else {
                echo '<p>Паролі не співпадають</p>';
            }
        }
        ?>
    </div>
</main>

<?php
include '../templates/footer.php';
?>
