<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем заявки пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Обработка отправки отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_order_id'])) {
    $order_id = (int)$_POST['review_order_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment'] ?? '');

    if ($rating >= 1 && $rating <= 5 && strlen($comment) >= 10) {
        // Проверяем, что заказ принадлежит пользователю и доставлен
        $check = $pdo->prepare("SELECT product_name FROM orders WHERE id = ? AND user_id = ? AND status = 'Доставлено'");
        $check->execute([$order_id, $user_id]);
        $order = $check->fetch();
        if ($order) {
            $stmt = $pdo->prepare("INSERT INTO reviews (order_id, user_id, product_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $user_id, $order['product_name'], $rating, $comment]);
            $_SESSION['review_success'] = 'Отзыв успешно добавлен!';
        } else {
            $_SESSION['review_error'] = 'Невозможно оставить отзыв для этого заказа.';
        }
    } else {
        $_SESSION['review_error'] = 'Заполните все поля корректно (оценка 1-5, текст ≥10 символов).';
    }
    header('Location: applications.php');
    exit;
}

// Получаем отзывы пользователя
$reviews_stmt = $pdo->prepare("SELECT * FROM reviews WHERE user_id = ? ORDER BY created_at DESC");
$reviews_stmt->execute([$user_id]);
$reviews = $reviews_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки</title>
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
        <a class="nav-link" href="main.php">Каталог</a>
        <a class="nav-link" href="#">О нас</a>
        <a class="nav-link active" aria-current="page" href="#">Заявки</a>
        <a class="nav-link" href="order.php">Новая заявка</a>
        <a class="nav-link" href="logout.php">Выйти</a>
      </div>
    </div>
  </div>
</nav>

<div class="container py-4">
    <h2 class="text-center mb-4">Мои заявки</h2>

    <?php if (isset($_SESSION['review_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['review_success'] ?></div>
        <?php unset($_SESSION['review_success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['review_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['review_error'] ?></div>
        <?php unset($_SESSION['review_error']); ?>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>№</th>
                    <th>Дата</th>
                    <th>Товар</th>
                    <th>Дата доставки</th>
                    <th>Способ оплаты</th>
                    <th>Статус</th>
                    <th>Отзыв</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="7" class="text-center">У вас пока нет заявок.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
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
                                <?php if ($order['status'] === 'Доставлено'): ?>
                                    <!-- Проверяем, есть ли уже отзыв для этого заказа -->
                                    <?php
                                    $review_check = $pdo->prepare("SELECT id FROM reviews WHERE order_id = ? AND user_id = ?");
                                    $review_check->execute([$order['id'], $user_id]);
                                    if ($review_check->fetch()): ?>
                                        <span class="text-success">Отзыв оставлен</span>
                                    <?php else: ?>
                                        <!-- Кнопка для открытия формы отзыва (используем details) -->
                                        <a href="#review-<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Оставить отзыв</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small">Доступно после доставки</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Блок с формами отзывов (раскрывающиеся) -->
    <div class="review-block">
        <h4 class="text-center mb-3">Оставить отзыв о товаре</h4>
        <p class="text-muted text-center small">Нажмите на ссылку «Оставить отзыв» в таблице, чтобы открыть форму для нужного заказа.</p>

        <?php foreach ($orders as $order): ?>
            <?php if ($order['status'] === 'Доставлено'): ?>
                <?php
                $review_check = $pdo->prepare("SELECT id FROM reviews WHERE order_id = ? AND user_id = ?");
                $review_check->execute([$order['id'], $user_id]);
                if (!$review_check->fetch()):
                ?>
                    <details id="review-<?= $order['id'] ?>">
                        <summary>Отзыв для заказа №<?= $order['id'] ?> (<?= htmlspecialchars($order['product_name']) ?>)</summary>
                        <form action="" method="POST">
                            <input type="hidden" name="review_order_id" value="<?= $order['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Товар</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($order['product_name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="rating_<?= $order['id'] ?>" class="form-label">Оценка <span class="text-danger">*</span></label>
                                <select class="form-select" id="rating_<?= $order['id'] ?>" name="rating" required>
                                    <option value="" disabled selected>Выберите оценку</option>
                                    <option value="5">Отлично</option>
                                    <option value="4">Хорошо</option>
                                    <option value="3">Удовлетворительно</option>
                                    <option value="2">Плохо</option>
                                    <option value="1">Ужасно</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comment_<?= $order['id'] ?>" class="form-label">Текст отзыва <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="comment_<?= $order['id'] ?>" name="comment" rows="3" placeholder="Расскажите о своём опыте с этим товаром..." required minlength="10"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Отправить отзыв</button>
                        </form>
                    </details>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
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