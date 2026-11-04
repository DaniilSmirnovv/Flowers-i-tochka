<?php
require_once 'config.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: auth.php');
    exit;
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Изменение статуса заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['admin_error'] = 'Ошибка безопасности.';
        header('Location: admin.php');
        exit;
    }
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'] ?? '';
    $allowed = ['Новая', 'В доставке', 'Доставлено'];
    if (in_array($new_status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $_SESSION['admin_success'] = 'Статус обновлен.';
    }
    header('Location: admin.php');
    exit;
}

// Добавление товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['admin_error'] = 'Ошибка безопасности.';
        header('Location: admin.php');
        exit;
    }
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['admin_error'] = 'Недопустимый формат изображения.';
            header('Location: admin.php');
            exit;
        }
        if ($file_size > 5 * 1024 * 1024) {
            $_SESSION['admin_error'] = 'Файл слишком большой (макс. 5 МБ).';
            header('Location: admin.php');
            exit;
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_name = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $image = 'img/' . $new_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
            $_SESSION['admin_error'] = 'Ошибка загрузки файла.';
            header('Location: admin.php');
            exit;
        }
    }

    if ($name !== '' && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $description, $image]);
        $_SESSION['admin_success'] = 'Товар добавлен.';
    } else {
        $_SESSION['admin_error'] = 'Название и цена обязательны.';
    }
    header('Location: admin.php');
    exit;
}

// Удаление товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['admin_error'] = 'Ошибка безопасности.';
        header('Location: admin.php');
        exit;
    }
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists($img)) {
        unlink($img);
    }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['admin_success'] = 'Товар удален.';
    header('Location: admin.php');
    exit;
}

// Статистика
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$new_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Новая'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Пагинация заявок
$limit = 5;
$filter_status = $_GET['status'] ?? '';
$page_orders = isset($_GET['page_orders']) ? (int)$_GET['page_orders'] : 1;
if ($page_orders < 1) $page_orders = 1;

$count_sql = "SELECT COUNT(*) FROM orders JOIN users ON orders.user_id = users.id";
$count_params = [];
if ($filter_status !== '') {
    $count_sql .= " WHERE orders.status = ?";
    $count_params[] = $filter_status;
}
$stmt = $pdo->prepare($count_sql);
$stmt->execute($count_params);
$total_count = $stmt->fetchColumn();
$total_pages_orders = max(1, ceil($total_count / $limit));
if ($page_orders > $total_pages_orders) $page_orders = $total_pages_orders;
$offset_orders = ($page_orders - 1) * $limit;

$sql = "SELECT orders.*, users.login, users.full_name FROM orders JOIN users ON orders.user_id = users.id";
$params = [];
if ($filter_status !== '') {
    $sql .= " WHERE orders.status = ?";
    $params[] = $filter_status;
}
$sql .= " ORDER BY orders.created_at DESC LIMIT $limit OFFSET $offset_orders";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Пагинация товаров
$page_products = isset($_GET['page_products']) ? (int)$_GET['page_products'] : 1;
if ($page_products < 1) $page_products = 1;
$total_pages_products = max(1, ceil($total_products / $limit));
if ($page_products > $total_pages_products) $page_products = $total_pages_products;
$offset_products = ($page_products - 1) * $limit;
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT $limit OFFSET $offset_products");
$stmt->execute();
$products = $stmt->fetchAll();

$success_msg = $_SESSION['admin_success'] ?? '';
$error_msg = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_success'], $_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Цветы - и точка</title>
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
        <a class="navbar-brand d-flex align-items-center gap-2" href="admin.php">
            <span class="brand-dot"></span>
            Цветы<span class="text-muted fw-normal ms-1"> - и точка</span>
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link px-3 active" href="admin.php">Админ-панель</a>
            <a class="nav-link btn-accent ms-2" href="logout.php">Выйти</a>
        </div>
    </div>
</nav>

<!-- ============ ПРИВЕТСТВИЕ ============ -->
<div class="container mt-3">
    <div class="d-flex justify-content-end">
        <span class="hero-tag">
            <span style="color: var(--rose-deep);">●</span> Режим администратора
        </span>
    </div>
</div>

<!-- ============ КОНТЕНТ ============ -->
<section class="py-4" style="flex: 1;">
    <div class="container">

        <?php if ($success_msg): ?>
            <div class="alert alert-success rounded-4 mb-4 text-center"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger rounded-4 mb-4 text-center"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <!-- Статистика -->
        <div class="row g-3 mb-5">
            <div class="col-6 col-md-3">
                <div class="feature-card text-center">
                    <div class="stat-num"><?= $total_orders ?></div>
                    <div class="stat-label mt-2">всего заявок</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-card text-center">
                    <div class="stat-num" style="color: #1976d2;"><?= $new_orders ?></div>
                    <div class="stat-label mt-2">новых</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-card text-center">
                    <div class="stat-num"><?= $total_products ?></div>
                    <div class="stat-label mt-2">товаров</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-card text-center">
                    <div class="stat-num"><?= $total_users ?></div>
                    <div class="stat-label mt-2">пользователей</div>
                </div>
            </div>
        </div>

        <!-- ЗАЯВКИ -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <span class="section-label">Управление</span>
                    <h2 class="section-title mb-0">Заявки клиентов</h2>
                </div>
                <form method="GET" class="d-flex gap-2">
                    <select name="status" class="form-control rounded-3" style="width: auto;">
                        <option value="">Все статусы</option>
                        <option value="Новая" <?= $filter_status === 'Новая' ? 'selected' : '' ?>>Новая</option>
                        <option value="В доставке" <?= $filter_status === 'В доставке' ? 'selected' : '' ?>>В доставке</option>
                        <option value="Доставлено" <?= $filter_status === 'Доставлено' ? 'selected' : '' ?>>Доставлено</option>
                    </select>
                    <button class="btn-accent">Фильтр</button>
                    <a href="admin.php" class="btn-outline-ink">Сброс</a>
                </form>
            </div>

            <?php if (empty($orders)): ?>
                <div class="text-center py-5 bg-white rounded-4" style="border: 2px dashed rgba(0,0,0,0.1);">
                    <p class="text-secondary mb-0">Заявок не найдено</p>
                </div>
            <?php else: ?>
                <div class="table-responsive modern-table">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Клиент</th>
                                <th>Товар</th>
                                <th class="text-center">Кол-во</th>
                                <th>Сумма</th>
                                <th>Доставка</th>
                                <th>Оплата</th>
                                <th>Статус</th>
                                <th class="text-end">Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td class="fw-semibold">#<?= $o['id'] ?></td>
                                    <td>
                                        <div class="fw-medium"><?= htmlspecialchars($o['login']) ?></div>
                                        <small class="text-secondary"><?= htmlspecialchars($o['full_name']) ?></small>
                                    </td>
                                    <td class="fw-medium"><?= htmlspecialchars($o['product_name']) ?></td>
                                    <td class="text-center fw-semibold"><?= (int)$o['quantity'] ?> шт.</td>
                                    <td class="fw-bold" style="color: var(--rose-deep);"><?= number_format($o['total_price'], 0, '.', ' ') ?> ₽</td>
                                    <td class="text-secondary"><?= date('d.m.Y', strtotime($o['delivery_date'])) ?></td>
                                    <td class="text-secondary small"><?= htmlspecialchars($o['payment_method']) ?></td>
                                    <td>
                                        <?php
                                            $status_class = 'status-new';
                                            if ($o['status'] === 'В доставке') $status_class = 'status-delivery';
                                            elseif ($o['status'] === 'Доставлено') $status_class = 'status-done';
                                        ?>
                                        <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($o['status']) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" class="d-flex gap-2 justify-content-end">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                            <select name="new_status" class="form-select form-select-sm" style="width: auto; font-size: 0.85rem;">
                                                <option value="Новая" <?= $o['status'] === 'Новая' ? 'selected' : '' ?>>Новая</option>
                                                <option value="В доставке" <?= $o['status'] === 'В доставке' ? 'selected' : '' ?>>В доставке</option>
                                                <option value="Доставлено" <?= $o['status'] === 'Доставлено' ? 'selected' : '' ?>>Доставлено</option>
                                            </select>
                                            <button name="change_status" class="btn-accent" style="padding: 0.4rem 1rem; font-size: 0.85rem;">OK</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages_orders > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center mb-0">
                            <?php for ($i = 1; $i <= $total_pages_orders; $i++): ?>
                                <li class="page-item <?= $i == $page_orders ? 'active' : '' ?>">
                                    <a class="page-link rounded-3 border-0" href="?page_orders=<?= $i ?><?= $filter_status !== '' ? '&status=' . urlencode($filter_status) : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- ТОВАРЫ -->
        <div>
            <div class="text-center mb-4">
                <span class="section-label">Каталог</span>
                <h2 class="section-title mb-0">Управление товарами</h2>
            </div>

            <form method="POST" enctype="multipart/form-data" class="auth-card mb-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Название *" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="Цена *" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="description" class="form-control rounded-3" placeholder="Описание">
                    </div>
                    <div class="col-md-2">
                        <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="col-md-2">
                        <button name="add_product" class="btn-accent w-100">Добавить</button>
                    </div>
                </div>
                <small class="text-secondary mt-2 d-block">JPG, PNG, WebP. Макс. 5 МБ.</small>
            </form>

            <?php if (empty($products)): ?>
                <div class="text-center py-5 bg-white rounded-4" style="border: 2px dashed rgba(0,0,0,0.1);">
                    <p class="text-secondary mb-0">Товаров пока нет</p>
                </div>
            <?php else: ?>
                <div class="table-responsive modern-table">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Фото</th>
                                <th>Название</th>
                                <th>Цена</th>
                                <th>Описание</th>
                                <th class="text-end">Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td class="fw-semibold">#<?= $p['id'] ?></td>
                                    <td>
                                        <?php if (!empty($p['image']) && file_exists($p['image'])): ?>
                                            <img src="<?= htmlspecialchars($p['image']) ?>" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted small" style="width: 50px; height: 50px;">✿</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-medium"><?= htmlspecialchars($p['name']) ?></td>
                                    <td class="fw-semibold"><?= number_format($p['price'], 0, '.', ' ') ?> ₽</td>
                                    <td class="text-secondary small"><?= htmlspecialchars(mb_substr($p['description'], 0, 50)) ?></td>
                                    <td class="text-end">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Удалить товар?');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <button name="delete_product" class="btn-outline-ink" style="padding: 0.4rem 1rem; font-size: 0.85rem; color: #c97b93; border-color: #c97b93;">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages_products > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center mb-0">
                            <?php for ($i = 1; $i <= $total_pages_products; $i++): ?>
                                <li class="page-item <?= $i == $page_products ? 'active' : '' ?>">
                                    <a class="page-link rounded-3 border-0" href="?page_products=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============ ПОДВАЛ ============ -->
<footer>
    <div class="container">
        <div class="footer-bottom" style="margin-top: 0; padding-top: 0; border-top: none;">
            © 2026 Цветы - и точка. Админ-панель.
        </div>
    </div>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>