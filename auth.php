<?php
require_once 'config.php';

// Если уже авторизован — редирект
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin.php');
    exit;
}
if (isset($_SESSION['user_id'])) {
    header('Location: main.php');
    exit;
}

$error = '';
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = 'Заполните все поля.';
    } else {
        $stmt = $pdo->prepare("SELECT id, password, login FROM users WHERE login = ? LIMIT 1");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Регенерация ID сессии для защиты от фиксации
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];

            // Админ по логину/паролю (хардкод как в оригинале)
            if ($login === 'Admin' && $password === 'Flowers') {
                $_SESSION['admin'] = true;
                header('Location: admin.php');
            } else {
                header('Location: main.php');
            }
            exit;
        } else {
            $error = 'Неверный логин или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Цветы - и точка</title>
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

<!-- ============ ФОРМА ВХОДА ============ -->
<section class="py-5" style="flex: 1; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <span class="brand-dot mb-3" style="width: 14px; height: 14px;"></span>
                        <span class="section-label d-block mt-3">Авторизация</span>
                        <h1 class="section-title mb-1">Вход в аккаунт</h1>
                        <p class="text-secondary small mb-0">Введите данные для продолжения</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-3 mb-4 text-center small"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="login" class="form-label fw-semibold small text-uppercase text-secondary">Логин</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="login" name="login"
                                   value="<?= htmlspecialchars($login) ?>" placeholder="Ваш логин" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold small text-uppercase text-secondary">Пароль</label>
                            <input type="password" class="form-control form-control-lg rounded-3" id="password" name="password"
                                   placeholder="Ваш пароль" required>
                        </div>
                        <button type="submit" class="btn-accent w-100 py-2">Войти →</button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-secondary">Еще не зарегистрированы? <a href="index.php" class="text-dark fw-semibold text-decoration-none">Создать аккаунт</a></small>
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