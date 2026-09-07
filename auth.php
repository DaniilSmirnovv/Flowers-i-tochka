<?php
require_once 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($login) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $login;
            // Проверяем, админ ли это (по логину Admin и паролю KanctowEst)
            if ($login === 'Admin' && $password === 'KanctowEst') {
                $_SESSION['admin'] = true;
                header('Location: admin.php');
            } else {
                header('Location: main.php');
            }
            exit;
        } else {
            $error = 'Неверный логин или пароль.';
        }
    } else {
        $error = 'Заполните все поля.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center mb-4 h3">Авторизация</h1>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="login" class="form-label">Логин <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="login" name="login" placeholder="Введите логин" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-2">Войти</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Еще не зарегистрированы? <a href="index.php" class="text-decoration-none">Регистрация</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>