<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/MVC/views/css/admin.css">
</head>
<body>
<?php include '../../../templates/header.php'; ?>
<main class="dashboard-main">
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>
        <table>
            <tr>
                <th>Ім'я</th>
                <th>Прізвище</th>
                <th>По батькові</th>
                <th>Номер телефону</th>
                <th>Email</th>
                <th>Роль</th>
            </tr>
            <?php if (!empty($admin_data)): ?>
                <?php foreach ($admin_data as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['middle_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Немає даних</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</main>
<?php include '../../../templates/footer.php'; ?>
</body>
</html>
