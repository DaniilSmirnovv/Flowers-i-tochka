<?php
require_once 'config.php';
// Если пользователь авторизован, можно показать приветствие, но это необязательно
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Канцтовары. Главная</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Канцтовары.Есть</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="#">Каталог</a>
        <a class="nav-link" href="#">О нас</a>
        <a class="nav-link" href="applications.php">Заявки</a>
        <a class="nav-link" href="order.php">Новая заявка</a>
        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin'])): ?>
            <a class="nav-link" href="logout.php">Выйти</a>
        <?php else: ?>
            <a class="nav-link" href="auth.php">Войти</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<div class="container">
    <?php if (isset($_SESSION['user_id'])): ?>
        <p class="text-end mt-2">Здравствуйте, <?= htmlspecialchars($_SESSION['login']) ?>!</p>
    <?php elseif (isset($_SESSION['admin'])): ?>
        <p class="text-end mt-2">Вы вошли как администратор</p>
    <?php endif; ?>
    <h1 class="text-center">Наши товары:</h1>
    <div class="row row-cols-1 row-cols-md-3 g-3 justify-content-center"> 
    <div class="col">
        <div class="card h-100">
        <img src="img/karan.jpg" class="card-img-top" style="height:150px; object-fit:contain; background:#f8f9fa;" alt="...">
        <div class="card-body d-flex flex-column p-2">
            <h6 class="card-title">Карандаш</h6>
            <h5 class="card-title">50р</h5>
            <p class="card-text flex-grow-1 small">Карандаш - это привычный нам инструмент для письма, рисования или черчения. Я подобрала для вас самое интересное: как он устроен, какие бывают виды и как появился.</p>
            <a href="#" class="btn btn-primary btn-sm mt-2">Купить</a>
        </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
        <img src="img/last.jpg" class="card-img-top" style="height:150px; object-fit:contain; background:#f8f9fa;" alt="...">
        <div class="card-body d-flex flex-column p-2">
            <h6 class="card-title">Ластик</h6>
            <h5 class="card-title">50р</h5>
            <p class="card-text flex-grow-1 small">Ластик (стирательная резинка) - это инструмент, чтобы удалять карандашные (а иногда и другие) надписи и штрихи.</p>
            <a href="#" class="btn btn-primary btn-sm mt-2">Купить</a>
        </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
        <img src="img/ruch.jpg" class="card-img-top" style="height:150px; object-fit:contain; background:#f8f9fa;" alt="...">
        <div class="card-body d-flex flex-column p-2">
            <h6 class="card-title">Ручка</h6>
            <h5 class="card-title">50р</h5>
            <p class="card-text flex-grow-1 small">Ручка - это инструмент для письма: с её помощью оставляют след на бумаге с помощью чернил.</p>
            <a href="#" class="btn btn-primary btn-sm mt-2">Купить</a>
        </div>
        </div>
    </div>
        <div class="col">
        <div class="card h-100">
        <img src="img/karan.jpg" class="card-img-top" style="height:150px; object-fit:contain; background:#f8f9fa;" alt="...">
        <div class="card-body d-flex flex-column p-2">
            <h6 class="card-title">Карандаш</h6>
            <h5 class="card-title">50р</h5>
            <p class="card-text flex-grow-1 small">Карандаш - это привычный нам инструмент для письма, рисования или черчения. Я подобрала для вас самое интересное: как он устроен, какие бывают виды и как появился.</p>
            <a href="#" class="btn btn-primary btn-sm mt-2">Купить</a>
        </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
        <img src="img/last.jpg" class="card-img-top" style="height:150px; object-fit:contain; background:#f8f9fa;" alt="...">
        <div class="card-body d-flex flex-column p-2">
            <h6 class="card-title">Ластик</h6>
            <h5 class="card-title">50р</h5>
            <p class="card-text flex-grow-1 small">Ластик (стирательная резинка) - это инструмент, чтобы удалять карандашные (а иногда и другие) надписи и штрихи.</p>
            <a href="#" class="btn btn-primary btn-sm mt-2">Купить</a>
        </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
        <img src="img/ruch.jpg" class="card-img-top" style="height:150px; object-fit:contain; background:#f8f9fa;" alt="...">
        <div class="card-body d-flex flex-column p-2">
            <h6 class="card-title">Ручка</h6>
            <h5 class="card-title">50р</h5>
            <p class="card-text flex-grow-1 small">Ручка - это инструмент для письма: с её помощью оставляют след на бумаге с помощью чернил.</p>
            <a href="#" class="btn btn-primary btn-sm mt-2">Купить</a>
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