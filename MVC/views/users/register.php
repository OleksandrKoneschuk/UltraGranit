<?php
/** @var string $error_message Повідомлення про помилку
 * @var string $success_message Повідомлення про успіх*/

$this->Title = 'Реєстрація';
?>

<link rel="stylesheet" href="/MVC/views/css/registration.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<main class="registration-main">
    <div class="registration-container">
        <h2>Реєстрація</h2>
        <form action="" method="post">
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger d-flex p-2 align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                    <div style="padding: 0 5px 0 5px">
                        <?= $error_message ?>
                    </div>
                </div>
            <?php endif; ?>

            <label for="first_name">Ім'я:</label>
            <input value="<?=$this->controller->post->first_name?>" type="text" id="first_name" name="first_name" pattern="[А-ЩЬЮЯҐЄІЇа-щьюяґєії' -]+">

            <label for="last_name">Прізвище:</label>
            <input value="<?=$this->controller->post->last_name?>" type="text" id="last_name" name="last_name" pattern="[А-ЩЬЮЯҐЄІЇа-щьюяґєії' -]+">

            <label for="middle_name">По батькові:</label>
            <input value="<?=$this->controller->post->middle_name?>" type="text" id="middle_name" name="middle_name" pattern="[А-ЩЬЮЯҐЄІЇа-щьюяґєії' -]+">

            <label for="phone_number">Номер телефону:</label>
            <input value="<?=$this->controller->post->phone_number?>" type="text" id="phone_number" name="phone_number">

            <label for="email">Email:</label>
            <input value="<?=$this->controller->post->email?>" type="email" id="email" name="email" aria-describedby="emailHelp">

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" >

            <label for="confirm_password">Підтвердіть пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" >

            <button type="submit">Зареєструватись</button>
        </form>
        <a href="/users/login">Увійти</a>
    </div>
</main>

<script>
    $(document).ready(function () {
        $('#phone_number').mask('+380-(00)-000-(00)-00');

        $('form').on('submit', function () {
            $('#phone_number').val($('#phone_number').cleanVal());
        });
    });
</script>
