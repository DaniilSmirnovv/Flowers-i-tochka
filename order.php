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

                    <div id="success-message" class="alert alert-success text-center mb-4" role="alert">
                        Ваша заявка успешно отправлена на рассмотрение администратору!
                    </div>

                    <form action="#success" method="GET">
                        <div class="mb-3">
                            <label for="courseName" class="form-label">Наименование товара <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courseName" name="course" 
                                   placeholder="Введите название курса" required minlength="3" />
                            <div class="invalid-feedback">Введите название товара (минимум 3 символа).</div>
                        </div>

                        <div class="mb-3">
                            <label for="startDate" class="form-label">Желаемая дата получения <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="startDate" name="date" required />
                            <div class="invalid-feedback">Выберите дату.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Способ оплаты <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="cash" value="наличные" required />
                                <label class="form-check-label" for="cash">Наличными</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="transfer" value="перевод по номеру телефона" />
                                <label class="form-check-label" for="transfer">Перевод по номеру телефона</label>
                            </div>
                            <div class="invalid-feedback">Пожалуйста, выберите способ оплаты.</div>
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