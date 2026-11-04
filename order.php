<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// Генерация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = false;
$errors = [];
$product = $date_input = $payment = '';
$quantity = 1;

// Получаем товары из БД
$products = $pdo->query("SELECT id, name, price FROM products ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors['csrf'] = 'Ошибка безопасности. Обновите страницу.';
    } else {
        $product = trim($_POST['product'] ?? '');
        $quantity = max(1, (int)($_POST['quantity'] ?? 1)); // Минимум 1
        $date_input = trim($_POST['date'] ?? '');
        $payment = $_POST['payment'] ?? '';

        // Валидация товара и получение его цены
        $unit_price = 0;
        if (empty($product)) {
            $errors['product'] = 'Выберите товар.';
        } else {
            $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE name = ?");
            $stmt->execute([$product]);
            $product_data = $stmt->fetch();
            
            if (!$product_data) {
                $errors['product'] = 'Такого товара не существует.';
            } else {
                $unit_price = (float)$product_data['price'];
            }
        }

        // Валидация количества
        if ($quantity < 1) {
            $errors['quantity'] = 'Количество должно быть не менее 1.';
        }

        // Валидация даты
        if (empty($date_input)) {
            $errors['date'] = 'Введите дату.';
        } else {
            $dt = DateTime::createFromFormat('d.m.Y', $date_input);
            if (!$dt || $dt->format('d.m.Y') !== $date_input) {
                $errors['date'] = 'Некорректный формат даты. Используйте ДД.ММ.ГГГГ.';
            } else {
                $today = new DateTime();
                $today->setTime(0, 0, 0);
                if ($dt < $today) {
                    $errors['date'] = 'Дата доставки не может быть в прошлом.';
                }
                $date_db = $dt->format('Y-m-d');
            }
        }

        // Валидация оплаты
        $allowed_payments = ['наличные', 'перевод по номеру телефона'];
        if (empty($payment) || !in_array($payment, $allowed_payments)) {
            $errors['payment'] = 'Выберите способ оплаты.';
        }

        if (empty($errors)) {
            $total_price = $unit_price * $quantity;
            
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_name, quantity, total_price, delivery_date, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'Новая')");
            $stmt->execute([$_SESSION['user_id'], $product, $quantity, $total_price, $date_db, $payment]);
            
            $success = true;
            $product = $date_input = $payment = '';
            $quantity = 1;
            
            // Обновляем CSRF-токен после успешной отправки
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка - Цветы - и точка</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700;900&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=5">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- ============ ШАПКА ============ -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="main.php">
            <span class="brand-dot"></span>
            Цветы<span class="text-muted fw-normal ms-1"> - и точка</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link px-3" href="main.php">Каталог</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="about.php">О нас</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="applications.php">Заявки</a></li>
                <li class="nav-item">
                    <a class="nav-link btn-accent ms-lg-2" href="order.php">+ Новая заявка</a>
                </li>
                <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin'])): ?>
                    <li class="nav-item"><a class="nav-link px-3" href="logout.php">Выйти</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link px-3" href="auth.php">Войти</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- ============ ПРИВЕТСТВИЕ ============ -->
<?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin'])): ?>
<div class="container mt-3">
    <div class="d-flex justify-content-end">
        <span class="hero-tag">
            <?php if (isset($_SESSION['admin'])): ?>
                <span style="color: var(--rose-deep);">●</span> Администратор
            <?php else: ?>
                Здравствуйте, <strong><?= htmlspecialchars($_SESSION['login']) ?></strong>
            <?php endif; ?>
        </span>
    </div>
</div>
<?php endif; ?>

<!-- ============ ФОРМА ЗАЯВКИ ============ -->
<section class="py-5" style="flex: 1;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <span class="brand-dot mb-3" style="width: 14px; height: 14px;"></span>
                        <span class="section-label d-block mt-3">Оформление</span>
                        <h1 class="section-title mb-1">Новая заявка</h1>
                        <p class="text-secondary small mb-0">Заполните форму и мы свяжемся с вами для подтверждения</p>
                    </div>

                    <?php if ($success): ?>
                        <div class="text-center py-4">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">🌸</div>
                            <h3 class="fw-bold mb-2" style="font-family: 'Playfair Display', serif;">Заявка отправлена!</h3>
                            <p class="text-secondary mb-4">Администратор рассмотрит её и свяжется с вами в ближайшее время.</p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="applications.php" class="btn-accent">Мои заявки →</a>
                                <a href="main.php" class="btn-outline-ink">На главную</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <form action="" method="POST" novalidate id="orderForm">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <div class="mb-3">
                                <label for="product" class="form-label fw-semibold small text-uppercase text-secondary">Товар <span class="text-danger">*</span></label>
                                <select class="form-control rounded-3 <?= isset($errors['product']) ? 'is-invalid' : '' ?>" id="product" name="product" required>
                                    <option value="">Выберите товар</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= htmlspecialchars($p['name']) ?>" 
                                                data-price="<?= $p['price'] ?>" 
                                                <?= $product === $p['name'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['name']) ?> — <?= number_format($p['price'], 0, '.', ' ') ?> ₽
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['product'])): ?>
                                    <div class="invalid-feedback"><?= $errors['product'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-semibold small text-uppercase text-secondary">Количество <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3 <?= isset($errors['quantity']) ? 'is-invalid' : '' ?>"
                                       id="quantity" name="quantity" value="<?= htmlspecialchars($quantity) ?>" min="1" required>
                                <?php if (isset($errors['quantity'])): ?>
                                    <div class="invalid-feedback"><?= $errors['quantity'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Блок итоговой стоимости -->
                            <div class="mb-4 p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--cream); border: 1px solid rgba(0,0,0,0.05);">
                                <span class="fw-semibold text-uppercase text-secondary small">Итоговая стоимость:</span>
                                <span class="fw-bold fs-4" id="total_price_display" style="color: var(--rose-deep);">0 ₽</span>
                                <input type="hidden" name="total_price" id="total_price_input" value="0">
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label fw-semibold small text-uppercase text-secondary">Желаемая дата доставки <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3 <?= isset($errors['date']) ? 'is-invalid' : '' ?>"
                                       id="date" name="date" placeholder="ДД.ММ.ГГГГ"
                                       value="<?= htmlspecialchars($date_input) ?>" required>
                                <?php if (isset($errors['date'])): ?>
                                    <div class="invalid-feedback"><?= $errors['date'] ?></div>
                                <?php endif; ?>
                                <small class="text-secondary">Формат: ДД.ММ.ГГГГ (например, 25.12.2026)</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold small text-uppercase text-secondary d-block">Способ оплаты <span class="text-danger">*</span></label>
                                <div class="d-flex flex-column gap-2">
                                    <label class="payment-option" style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                        <input type="radio" name="payment" value="наличные" <?= $payment === 'наличные' ? 'checked' : '' ?> required>
                                        <span>💵 Наличными при получении</span>
                                    </label>
                                    <label class="payment-option" style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                        <input type="radio" name="payment" value="перевод по номеру телефона" <?= $payment === 'перевод по номеру телефона' ? 'checked' : '' ?>>
                                        <span>📱 Перевод по номеру телефона</span>
                                    </label>
                                </div>
                                <?php if (isset($errors['payment'])): ?>
                                    <div class="text-danger small mt-2"><?= $errors['payment'] ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn-accent w-100 py-2">Отправить заявку →</button>
                        </form>
                    <?php endif; ?>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-secondary"><a href="applications.php" class="text-dark fw-semibold text-decoration-none">← Мои заявки</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ ПОДВАЛ ============ -->
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="footer-brand">Цветы - и точка<span style="color: var(--rose-deep);">.</span></div>
                <p class="text-secondary" style="font-size: 0.95rem; line-height: 1.7;">
                    Маленькая мастерская с большой любовью к цветам.<br>
                    Работаем с 2020 года. Дарим эмоции каждый день.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Instagram">📷</a>
                    <a href="#" class="social-link" title="Telegram">✈</a>
                    <a href="#" class="social-link" title="WhatsApp">💬</a>
                    <a href="#" class="social-link" title="VK">Ⓥ</a>
                </div>
            </div>
            <div class="col-md-3">
                <h6 class="footer-title">Меню</h6>
                <ul class="footer-links">
                    <li><a href="main.php">Каталог</a></li>
                    <li><a href="about.php">О нас</a></li>
                    <li><a href="applications.php">Мои заявки</a></li>
                    <li><a href="order.php">Новая заявка</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="footer-title">Контакты</h6>
                <ul class="footer-links">
                    <li><a href="tel:+79991234567">+7 (999) 123-45-67</a></li>
                    <li><a href="mailto:hello@flowers-i-tochka.ru">hello@flowers-i-tochka.ru</a></li>
                    <li style="color: var(--ink-soft); font-size: 0.95rem;">Ежедневно с 9:00 до 21:00</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            © 2026 Цветы - и точка. Все права защищены. Сделано с ♥
        </div>
    </div>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    // Мгновенный расчёт стоимости на стороне клиента
    const productSelect = document.getElementById('product');
    const quantityInput = document.getElementById('quantity');
    const totalPriceDisplay = document.getElementById('total_price_display');
    const totalPriceInput = document.getElementById('total_price_input');

    function calculateTotal() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const quantity = parseInt(quantityInput.value) || 1;
        
        // Защита от отрицательных значений в поле ввода
        if (quantity < 1) {
            quantityInput.value = 1;
        }
        
        const total = price * (parseInt(quantityInput.value) || 1);

        // Форматируем число с пробелами (например, 15 000)
        totalPriceDisplay.textContent = total.toLocaleString('ru-RU') + ' ₽';
        totalPriceInput.value = total;
    }

    // Пересчитываем при изменении товара или количества
    productSelect.addEventListener('change', calculateTotal);
    quantityInput.addEventListener('input', calculateTotal);

    // Инициализация при загрузке страницы (если форма вернулась с ошибками)
    document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
</body>
</html>