<?php
/** @var array $materials */
/** @var array $orders */
/** @var float $currentExchangeRate */

/** @var bool $autoUpdateEnabled */

use MVC\controllers\AdminController;
$this->Title = 'Адмін-панель';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Адмін-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 900px;
            margin-top: 20px;
        }

        h1, h2 {
            color: #343a40;
        }

        .btn {
            margin-top: 5px;
        }

        table {
            background: #fff;
        }

        th, td {
            vertical-align: middle !important;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center my-4">Адмін-панель</h1>
    <a href="/" class="btn btn-secondary mb-3">⬅️ На головну</a>

    <h2>Зміна цін на матеріали</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>Матеріал</th>
            <th>Ціна за м³</th>
            <th>Дія</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($materials as $material): ?>
            <tr>
                <td><?= htmlspecialchars($material->name) ?></td>

                <form method="POST" action="/admin/updatePrice" class="d-flex">
                    <td>
                        <input type="hidden" name="id" value="<?= $material->id ?>">
                        <input type="number" class="form-control me-2" name="price"
                               value="<?= $material->price_per_m3 ?>" step="50">
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary">Зберегти</button>
                    </td>
                </form>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    </br>

    <h2>Зміна курсу долара</h2>
    <p>Поточний курс: <strong><?= number_format($currentExchangeRate, 2) ?> грн</strong></p>
    <form method="POST" action="/admin/updateExchangeRate" class="d-flex">
        <input type="number" class="form-control me-2" name="exchange_rate"
               value="<?= number_format($currentExchangeRate, 2) ?>" step="0.5">
        <button type="submit" class="btn btn-success">Оновити</button>
    </form>

    <h2>Автооновлення курсу</h2>
    <p>Статус: <strong
                class="<?= $autoUpdateEnabled ? 'text-success' : 'text-danger' ?>"><?= $autoUpdateEnabled ? "Увімкнено" : "Вимкнено" ?></strong>
    </p>
    <form method="POST" action="/admin/toggleAutoUpdate" class="d-flex">
        <button type="submit" name="status" value="enabled" class="btn btn-outline-success me-2">Увімкнути</button>
        <button type="submit" name="status" value="disabled" class="btn btn-outline-danger">Вимкнути</button>
    </form>

    </br>

    <h2>Список замовлень</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Клієнт</th>
            <th>Сума</th>
            <th>Статус</th>
            <th>Дія</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order->id ?></td>
                <td><?= htmlspecialchars("{$order->last_name} {$order->first_name} {$order->middle_name}") ?></td>
                <td><?= number_format($order->total_price, 2) ?> грн</td>

                <form method="POST" action="/admin/updateOrderStatus" class="d-flex">
                    <td>
                        <input type="hidden" name="order_id" value="<?= $order->id ?>">
                        <select name="status" class="form-select me-2">
                            <option value="new" <?= $order->status === 'new' ? 'selected' : '' ?>>Нове</option>
                            <option value="processing" <?= $order->status === 'processing' ? 'selected' : '' ?>>
                                Обробляється
                            </option>
                            <option value="completed" <?= $order->status === 'completed' ? 'selected' : '' ?>>Виконано
                            </option>
                            <option value="cancelled" <?= $order->status === 'cancelled' ? 'selected' : '' ?>>
                                Скасовано
                            </option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-warning">Змінити</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </br>
</div>

<!-- Підключення Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
