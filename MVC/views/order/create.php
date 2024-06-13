<main>
    <div class="container mt-5">
        <h2>Оформлення замовлення</h2>
        <form id="order-form">
            <div class="mb-3">
                <label for="surname" class="form-label">Прізвище</label>
                <input type="text" class="form-control" id="surname" name="surname" value="<?= isset($user) ? htmlspecialchars($user['last_name']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Ім'я</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= isset($user) ? htmlspecialchars($user['first_name']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="patronymic" class="form-label">По батькові</label>
                <input type="text" class="form-control" id="patronymic" name="patronymic" value="<?= isset($user) ? htmlspecialchars($user['middle_name']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Номер телефону</label>
                <input type="tel" class="form-control" id="phone" name="phone" value="<?= isset($user) ? htmlspecialchars($user['phone_number']) : '' ?>" required oninput="formatPhoneNumber(this)">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Адрес електронної пошти</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= isset($user) ? htmlspecialchars($user['email']) : '' ?>" >
            </div>
            <div class="mb-3">
                <label for="novaposhta" class="form-label">Відділення Нової Пошти</label>
                <input type="text" class="form-control" id="novaposhta" name="novaposhta" required>
            </div>
            <h3>Товари в кошику</h3>
            <div id="basket-products">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Назва</th>
                        <th>Ціна</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <h4 id="total-price"></h4>
            <button type="submit" class="btn btn-primary mb-5">Оформити замовлення</button>
        </form>
    </div>
</main>

<script src="/MVC/views/js/create.js" defer></script>