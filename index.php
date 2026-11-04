<?php
require_once 'config.php';

// Если уже авторизован — редирект
if (isset($_SESSION['user_id'])) {
    header('Location: main.php');
    exit;
}

$errors = [];
$login = $full_name = $email = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Валидация логина
    if (!preg_match('/^[A-Za-z0-9]{6,}$/', $login)) {
        $errors['login'] = 'Логин: только латиница и цифры, минимум 6 символов.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ? LIMIT 1");
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $errors['login'] = 'Этот логин уже занят.';
        }
    }

    // Валидация пароля
    if (strlen($password) < 8) {
        $errors['password'] = 'Пароль должен содержать минимум 8 символов.';
    }

    // Валидация ФИО
    if (!preg_match('/^[А-Яа-яЁё\s\-]+$/u', $full_name) || mb_strlen($full_name) < 2) {
        $errors['full_name'] = 'ФИО: только кириллица, минимум 2 символа.';
    }

    // Валидация телефона
    if (!preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors['phone'] = 'Телефон в формате 8(XXX)XXX-XX-XX.';
    }

    // Валидация email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Этот email уже зарегистрирован.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (login, password, full_name, phone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$login, $hashed, $full_name, $phone, $email]);

        $_SESSION['success'] = 'Регистрация прошла успешно! Теперь войдите.';
        header('Location: auth.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Цветы - и точка</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700;900&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=3">
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
                <li class="nav-item">
                    <a class="nav-link btn-accent ms-lg-2" href="auth.php">Войти</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ============ ФОРМА РЕГИСТРАЦИИ ============ -->
<section class="py-5" style="flex: 1;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <span class="brand-dot mb-3" style="width: 14px; height: 14px;"></span>
                        <span class="section-label d-block mt-3">Новый аккаунт</span>
                        <h1 class="section-title mb-1">Регистрация</h1>
                        <p class="text-secondary small mb-0">Заполните форму, чтобы присоединиться</p>
                    </div>

                    <form action="" method="POST" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="login" class="form-label fw-semibold small text-uppercase text-secondary">Логин</label>
                                <input type="text" class="form-control rounded-3 <?= isset($errors['login']) ? 'is-invalid' : '' ?>"
                                       id="login" name="login" value="<?= htmlspecialchars($login) ?>"
                                       placeholder="Латиница и цифры, от 6" required>
                                <?php if (isset($errors['login'])): ?>
                                    <div class="invalid-feedback"><?= $errors['login'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="full_name" class="form-label fw-semibold small text-uppercase text-secondary">ФИО</label>
                                <input type="text" class="form-control rounded-3 <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                       id="full_name" name="full_name" value="<?= htmlspecialchars($full_name) ?>"
                                       placeholder="Иванов Иван Иванович" required>
                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold small text-uppercase text-secondary">Email</label>
                                <input type="email" class="form-control rounded-3 <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                       id="email" name="email" value="<?= htmlspecialchars($email) ?>"
                                       placeholder="example@mail.ru" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold small text-uppercase text-secondary">Телефон</label>
                                <input type="tel" class="form-control rounded-3 <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                       id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>"
                                       placeholder="8(XXX)XXX-XX-XX" required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                                <?php endif; ?>
                                <small class="text-secondary">Пример: 8(999)123-45-67</small>
                            </div>
                            <div class="col-12">
                                <label for="password" class="form-label fw-semibold small text-uppercase text-secondary">Пароль</label>
                                <input type="password" class="form-control rounded-3 <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                       id="password" name="password" placeholder="Минимум 8 символов" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn-accent w-100 py-2 mt-4">Зарегистрироваться →</button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-secondary">Уже есть аккаунт? <a href="auth.php" class="text-dark fw-semibold text-decoration-none">Войти</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ ПОДВАЛ ============ -->
<footer>
    <div class="container">
        <div class="footer-bottom" style="margin-top: 0; padding-top: 0; border-top: none;">
            © 2026 Цветы - и точка. Все права защищены.
        </div>
    </div>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>