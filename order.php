<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$success = false;
$errors = [];
$product = $date_input = $payment = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = $_POST['product'] ?? '';
    $date_input = trim($_POST['date'] ?? '');
    $payment = $_POST['payment'] ?? '';

    if (empty($product)) {
        $errors['product'] = 'Выберите товар.';
    }
    if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date_input)) {
        $errors['date'] = 'Введите дату в формате ДД.ММ.ГГГГ.';
    } else {
        $parts = explode('.', $date_input);
        $date_db = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        if (!strtotime($date_db)) {
            $errors['date'] = 'Некорректная дата.';
        }
    }
    if (empty($payment)) {
        $errors['payment'] = 'Выберите способ оплаты.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_name, delivery_date, payment_method) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product, $date_db, $payment]);
        $success = true;
        $product = $date_input = $payment = '';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка</title>
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
        <a class="nav-link" href="applications.php">Заявки</a>
        <a class="nav-link active" aria-current="page" href="order.php">Новая заявка</a>
        <a class="nav-link" href="logout.php">Выйти</a>
      </div>
    </div>
  </div>
</nav>

<div class="container d-flex align-items-center justify-content-center py-4" style="min-height:70vh;">
    <div class="row justify-content-center w-100">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4 h4">Формирование заявки на заказ</h2>

                    <?php if ($success): ?>
                        <div class="alert alert-success text-center">Ваша заявка успешно отправлена на рассмотрение администратору!</div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="product" class="form-label">Наименование товара <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['product']) ? 'is-invalid' : '' ?>" id="product" name="product" required>
                                <option value="">Выберите товар</option>
                                <option value="карандаш" <?= $product === 'карандаш' ? 'selected' : '' ?>>Карандаш</option>
                                <option value="ластик" <?= $product === 'ластик' ? 'selected' : '' ?>>Ластик</option>
                                <option value="ручка" <?= $product === 'ручка' ? 'selected' : '' ?>>Ручка</option>
                            </select>
                            <?php if (isset($errors['product'])): ?>
                                <div class="invalid-feedback"><?= $errors['product'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Желаемая дата получения <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['date']) ? 'is-invalid' : '' ?>" id="date" name="date" placeholder="ДД.ММ.ГГГГ" value="<?= htmlspecialchars($date_input) ?>" required>
                            <?php if (isset($errors['date'])): ?>
                                <div class="invalid-feedback"><?= $errors['date'] ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Формат: ДД.ММ.ГГГГ (например, 25.12.2025)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Способ оплаты <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="cash" value="наличные" <?= $payment === 'наличные' ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="cash">Наличными</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="transfer" value="перевод по номеру телефона" <?= $payment === 'перевод по номеру телефона' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="transfer">Перевод по номеру телефона</label>
                            </div>
                            <?php if (isset($errors['payment'])): ?>
                                <div class="text-danger small mt-1"><?= $errors['payment'] ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">Отправить заявку</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="applications.php" class="text-decoration-none">← Мои заявки</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-3 border-top mt-auto">
    <p class="mb-0">&copy; 2026 Канцтовары.Есть</p>
</footer>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>