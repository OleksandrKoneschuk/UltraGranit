<?php
$this->Title = 'Акаунт';
?>

    <main class="account-main">
        <div class="account-container">
            <?php if (isset($user)): ?>
                <h1>Ваш акаунт</h1>
                <p>Ім'я: <?php echo htmlspecialchars($user->first_name); ?></p>
                <p>Прізвище: <?php echo htmlspecialchars($user->last_name); ?></p>
                <p>По батькові: <?php echo htmlspecialchars($user->middle_name); ?></p>
                <p>Номер телефону: <?php echo htmlspecialchars(formatPhoneNumber($user->phone_number)); ?></p>
                <p>Email: <?php echo htmlspecialchars($user->email); ?></p>

                <h2>Ваші замовлення</h2>
                <?php if (!empty($orders)): ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Номер замовлення</th>
                            <th>Дата створення</th>
                            <th>Статус</th>
                            <th>Товари</th>
                            <th>Вартість</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order->id) ?></td>
                                <td><?= htmlspecialchars($order->created_at) ?></td>
                                <td><?= htmlspecialchars($order->status) ?></td>
                                <td><?php foreach ($order->products as $product): ?>
                                        <?= htmlspecialchars($product->name) ?>
                                </td>
                                <td><?= htmlspecialchars($product->price) ?> грн.</td>
                                    <?php endforeach; ?>

                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>У вас ще немає замовлень.</p>
                <?php endif; ?>

            <?php else: ?>
                <p>Користувач не знайдений.</p>
            <?php endif; ?>

            <a href="/users/logout">Вийти з акаунту</a>
        </div>
    </main>


<?php
function formatPhoneNumber($phoneNumber)
{
    $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

    if (strlen($phoneNumber) === 10) {
        $formatted = '+38-' . substr($phoneNumber, 0, 3) . '-(' . substr($phoneNumber, 3, 3) . ')-' . substr($phoneNumber, 6, 2) . '-(' . substr($phoneNumber, 8, 2) . ')';
        return $formatted;
    } else {
        return $phoneNumber;
    }
}

?>