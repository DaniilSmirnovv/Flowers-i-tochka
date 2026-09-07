<?php
require_once 'config.php';

// Проверка, что админ авторизован
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: auth.php');
    exit;
}

// Обработка изменения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'] ?? '';
    $allowed = ['Новая', 'В доставке', 'Доставлено'];
    if (in_array($new_status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $_SESSION['admin_success'] = 'Статус обновлён.';
    } else {
        $_SESSION['admin_error'] = 'Некорректный статус.';
    }
    header('Location: admin.php');
    exit;
}

// Получение всех заявок с информацией о пользователе
$stmt = $pdo->query("
    SELECT orders.*, users.login, users.full_name 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    ORDER BY orders.created_at DESC
");
$orders = $stmt->fetchAll();

$success_msg = $_SESSION['admin_success'] ?? '';
$error_msg = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_success'], $_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="main.php">Канцтовары.Есть</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="#">Админка</a>
        <a class="nav-link" href="logout.php">Выйти</a>
      </div>
    </div>
  </div>
</nav>

<div class="container py-4">
    <h2 class="text-center mb-4">Все заявки пользователей</h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>№</th>
                    <th>Пользователь</th>
                    <th>Товар</th>
                    <th>Дата доставки</th>
                    <th>Способ оплаты</th>
                    <th>Статус</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="7" class="text-center">Заявок пока нет.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['login']) ?> (<?= htmlspecialchars($order['full_name']) ?>)</td>
                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                            <td><?= date('d.m.Y', strtotime($order['delivery_date'])) ?></td>
                            <td><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td>
                                <?php
                                $status_class = 'secondary';
                                if ($order['status'] === 'Новая') $status_class = 'primary';
                                elseif ($order['status'] === 'В доставке') $status_class = 'warning';
                                elseif ($order['status'] === 'Доставлено') $status_class = 'success';
                                ?>
                                <span class="badge bg-<?= $status_class ?>"><?= $order['status'] ?></span>
                            </td>
                            <td>
                                <form action="" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm">
                                        <option value="Новая" <?= $order['status'] === 'Новая' ? 'selected' : '' ?>>Новая</option>
                                        <option value="В доставке" <?= $order['status'] === 'В доставке' ? 'selected' : '' ?>>В доставке</option>
                                        <option value="Доставлено" <?= $order['status'] === 'Доставлено' ? 'selected' : '' ?>>Доставлено</option>
                                    </select>
                                    <button type="submit" name="change_status" class="btn btn-sm btn-primary">Обновить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="main.php" class="btn btn-outline-secondary">На главную</a>
    </div>
</div>

<footer class="text-center py-3 border-top mt-auto">
    <p class="mb-0">&copy; 2026 Канцтовары.Есть</p>
</footer>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>