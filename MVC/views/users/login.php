<?php
/** @var string $error_message Повідомлення про помилку*/
$this->Title = 'Авторизація';
?>

<link rel="stylesheet" href="/MVC/views/css/login.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<div class="login-main">
    <div class="login-container">
        <h2>Авторизація</h2>

        <form action="" method="post">
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger d-flex p-2 align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                    <div style="padding: 0 5px 0 5px">
                        <?= $error_message ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success d-flex p-2 align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <div style="padding: 0 5px 0 5px">
                        <?= $success_message ?>
                    </div>
                </div>
            <?php endif; ?>

            <label for="inputPhoneNumber">Номер телефону:</label>
            <input name="phone_number" type="text" id="phone_number" required autocomplete="tel-national" value="<?php echo isset($registered_phone_number) ? htmlspecialchars($registered_phone_number) : ''; ?>">

            <label for="inputPassword">Пароль:</label>
            <input name="password" type="password" id="password" required autocomplete="current-password">

            <button type="submit">Увійти</button>
        </form>

        <a href="/users/register">Зареєструватись</a>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#phone_number').mask('+380-(00)-000-(00)-00');

        $('form').on('submit', function () {
            $('#phone_number').val($('#phone_number').cleanVal());
        });
    });
</script>