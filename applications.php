<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Обработка отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['review_error'] = 'Ошибка безопасности. Обновите страницу.';
        header('Location: applications.php');
        exit;
    }

    if ($_POST['action'] === 'save_review') {
        $order_id = (int)$_POST['order_id'];
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;

        $check = $pdo->prepare("SELECT product_name FROM orders WHERE id = ? AND user_id = ? AND status = 'Доставлено'");
        $check->execute([$order_id, $user_id]);
        $order = $check->fetch();

        if (!$order) {
            $_SESSION['review_error'] = 'Нельзя оставить отзыв для этого заказа.';
            header('Location: applications.php');
            exit;
        }

        if ($rating < 1 || $rating > 5) {
            $_SESSION['review_error'] = 'Оценка должна быть от 1 до 5.';
            header('Location: applications.php');
            exit;
        }

        if (mb_strlen($comment) < 10) {
            $_SESSION['review_error'] = 'Текст отзыва должен содержать минимум 10 символов.';
            header('Location: applications.php');
            exit;
        }

        if ($review_id) {
            $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ? AND order_id = ?");
            $stmt->execute([$rating, $comment, $review_id, $user_id, $order_id]);
            $_SESSION['review_success'] = 'Отзыв обновлен.';
        } else {
            $check_review = $pdo->prepare("SELECT id FROM reviews WHERE order_id = ? AND user_id = ?");
            $check_review->execute([$order_id, $user_id]);
            if ($check_review->fetch()) {
                $_SESSION['review_error'] = 'Вы уже оставили отзыв к этому заказу.';
                header('Location: applications.php');
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO reviews (order_id, user_id, product_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $user_id, $order['product_name'], $rating, $comment]);
            $_SESSION['review_success'] = 'Отзыв добавлен. Спасибо!';
        }
        header('Location: applications.php');
        exit;
    }

    if ($_POST['action'] === 'delete_review') {
        $review_id = (int)$_POST['review_id'];
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
        $stmt->execute([$review_id, $user_id]);
        $_SESSION['review_success'] = 'Отзыв удален.';
        header('Location: applications.php');
        exit;
    }
}

// Получение заявок
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Получение отзывов
$reviews_stmt = $pdo->prepare("SELECT * FROM reviews WHERE user_id = ?");
$reviews_stmt->execute([$user_id]);
$user_reviews = [];
while ($row = $reviews_stmt->fetch(PDO::FETCH_ASSOC)) {
    $user_reviews[$row['order_id']] = $row;
}

$success_msg = $_SESSION['review_success'] ?? '';
$error_msg = $_SESSION['review_error'] ?? '';
unset($_SESSION['review_success'], $_SESSION['review_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки - Цветы - и точка</title>
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
                <li class="nav-item"><a class="nav-link px-3 active" href="applications.php">Заявки</a></li>
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

<!-- ============ ЗАЯВКИ ============ -->
<section class="py-5" style="flex: 1;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Личный кабинет</span>
            <h1 class="section-title mb-1">Мои заявки</h1>
            <p class="text-secondary">История ваших заказов и отзывы</p>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success rounded-4 mb-4 text-center"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger rounded-4 mb-4 text-center"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="text-center py-5 bg-white rounded-4" style="border: 2px dashed rgba(0,0,0,0.1);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🌷</div>
                <h3 class="fw-bold mb-2" style="font-family: 'Playfair Display', serif;">У вас пока нет заявок</h3>
                <p class="text-secondary mb-4">Оформите первый заказ и он появится здесь</p>
                <a href="order.php" class="btn-accent">Оформить заявку →</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <div class="col-lg-6">
                        <div class="order-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;"><?= htmlspecialchars($order['product_name']) ?></h5>
                                    <div class="text-secondary small">Создана: <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></div>
                                </div>
                                <?php
                                    $status_class = 'status-new';
                                    if ($order['status'] === 'В доставке') $status_class = 'status-delivery';
                                    elseif ($order['status'] === 'Доставлено') $status_class = 'status-done';
                                ?>
                                <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($order['status']) ?></span>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="text-secondary small">Дата доставки</div>
                                    <div class="fw-semibold"><?= date('d.m.Y', strtotime($order['delivery_date'])) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-secondary small">Оплата</div>
                                    <div class="fw-semibold"><?= htmlspecialchars($order['payment_method']) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-secondary small">Количество</div>
                                    <div class="fw-semibold"><?= (int)$order['quantity'] ?> шт.</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-secondary small">Итого</div>
                                    <div class="fw-bold" style="color: var(--rose-deep);"><?= number_format($order['total_price'], 0, '.', ' ') ?> ₽</div>
                                </div>
                            </div>

                            <?php if ($order['status'] === 'Доставлено'): ?>
                                <div class="pt-3 border-top">
                                    <?php if (isset($user_reviews[$order['id']])):
                                        $review = $user_reviews[$order['id']];
                                        $stars = str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']);
                                    ?>
                                        <div>
                                            <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2px;"><?= $stars ?></div>
                                            <p class="mb-3" style="font-style: italic; color: var(--ink-soft);"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn-outline-ink" style="padding: 0.4rem 1rem; font-size: 0.85rem;"
                                                        data-bs-toggle="modal" data-bs-target="#editReviewModal<?= $order['id'] ?>">
                                                    Редактировать
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Удалить отзыв?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="delete_review">
                                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                    <button type="submit" class="btn-outline-ink" style="padding: 0.4rem 1rem; font-size: 0.85rem; color: #c97b93; border-color: #c97b93;">
                                                        Удалить
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <button type="button" class="btn-accent w-100" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $order['id'] ?>">
                                            Оставить отзыв ✿
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-secondary small py-2">
                                    Отзыв будет доступен после доставки
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Модалка создания отзыва -->
                    <?php if ($order['status'] === 'Доставлено' && !isset($user_reviews[$order['id']])): ?>
                    <div class="modal fade" id="reviewModal<?= $order['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" style="font-family: 'Playfair Display', serif;">Отзыв о заказе №<?= $order['id'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-secondary small mb-3">
                                        <strong><?= htmlspecialchars($order['product_name']) ?></strong> (<?= (int)$order['quantity'] ?> шт.) — <?= date('d.m.Y', strtotime($order['delivery_date'])) ?><br>
                                        <span class="fw-bold" style="color: var(--rose-deep);">Сумма: <?= number_format($order['total_price'], 0, '.', ' ') ?> ₽</span>
                                    </p>
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="save_review">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-uppercase text-secondary">Оценка</label>
                                            <select class="form-control rounded-3" name="rating" required>
                                                <option value="5">★★★★★ Отлично</option>
                                                <option value="4">★★★★☆ Хорошо</option>
                                                <option value="3">★★★☆☆ Удовлетворительно</option>
                                                <option value="2">★★☆☆☆ Плохо</option>
                                                <option value="1">★☆☆☆☆ Ужасно</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-uppercase text-secondary">Текст отзыва</label>
                                            <textarea class="form-control rounded-3" name="comment" rows="3" placeholder="Расскажите о вашем опыте..." required minlength="10"></textarea>
                                            <small class="text-secondary">Минимум 10 символов</small>
                                        </div>
                                        <button type="submit" class="btn-accent w-100">Отправить отзыв</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Модалка редактирования отзыва -->
                    <?php if (isset($user_reviews[$order['id']])):
                        $review = $user_reviews[$order['id']];
                    ?>
                    <div class="modal fade" id="editReviewModal<?= $order['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" style="font-family: 'Playfair Display', serif;">Редактировать отзыв</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="save_review">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-uppercase text-secondary">Оценка</label>
                                            <select class="form-control rounded-3" name="rating" required>
                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                    <option value="<?= $i ?>" <?= $review['rating'] == $i ? 'selected' : '' ?>>
                                                        <?= str_repeat('★', $i) . str_repeat('☆', 5 - $i) ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-uppercase text-secondary">Текст отзыва</label>
                                            <textarea class="form-control rounded-3" name="comment" rows="3" required minlength="10"><?= htmlspecialchars($review['comment']) ?></textarea>
                                        </div>
                                        <button type="submit" class="btn-accent w-100">Сохранить изменения</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="main.php" class="btn-outline-ink">← На главную</a>
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
</body>
</html>