<?php
require_once 'config.php';

$errors = [];
$login = $full_name = $email = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Валидация
    if (!preg_match('/^[A-Za-z0-9]{6,}$/', $login)) {
        $errors['login'] = 'Логин должен содержать только латиницу и цифры, минимум 6 символов.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $errors['login'] = 'Этот логин уже занят.';
        }
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Пароль должен содержать минимум 8 символов.';
    }

    if (!preg_match('/^[А-Яа-яЁё\s]+$/u', $full_name)) {
        $errors['full_name'] = 'ФИО должно содержать только кириллицу и пробелы.';
    }

    if (!preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors['phone'] = 'Телефон должен быть в формате 8(XXX)XXX-XX-XX.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email.';
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center mb-4 h3">Регистрация</h1>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="login" class="form-label">Логин <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['login']) ? 'is-invalid' : '' ?>" id="login" name="login" value="<?= htmlspecialchars($login) ?>" placeholder="Только латиница и цифры, ≥6 символов" required>
                                <?php if (isset($errors['login'])): ?>
                                    <div class="invalid-feedback"><?= $errors['login'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">ФИО <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name) ?>" placeholder="Иванов Иван Иванович" required>
                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Электронная почта <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="example@mail.ru" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="8(123)456-78-90" required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Формат: 8(XXX)XXX-XX-XX</small>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Минимум 8 символов" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-2">Создать пользователя</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Уже есть аккаунт? <a href="auth.php" class="text-decoration-none">Войти</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>