<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
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
        <a class="nav-link active" aria-current="page" href="#">Заявки</a>
        <a class="nav-link active" aria-current="page" href="order.php">Новая заявка</a>
      </div>
    </div>
  </div>
</nav>

<div class="container py-4">
    <h2 class="text-center mb-4">Мои заявки</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>№</th>
                    <th>Дата</th>
                    <th>Товар</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Отзыв</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>12.03.2026</td>
                    <td>Карандаш (2 шт.)</td>
                    <td>100 ₽</td>
                    <td><span class="badge bg-success">Выполнено</span></td>
                    <td><a href="#review-1" class="btn btn-sm btn-outline-primary">Оставить отзыв</a></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>05.03.2026</td>
                    <td>Ручка синяя (3 шт.)</td>
                    <td>150 ₽</td>
                    <td><span class="badge bg-warning text-dark">В обработке</span></td>
                    <td><span class="text-muted small">Доступно после выполнения</span></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>20.02.2026</td>
                    <td>Ластик (1 шт.)</td>
                    <td>50 ₽</td>
                    <td><span class="badge bg-danger">Отменено</span></td>
                    <td><span class="text-muted small">Недоступно</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="review-block">
        <h4 class="text-center mb-3">Оставить отзыв о товаре</h4>
        <p class="text-muted text-center small">Нажмите на ссылку «Оставить отзыв» в таблице, чтобы открыть форму для нужного заказа.</p>

        <details id="review-1">
            <summary>Отзыв для заказа №1 (Карандаш)</summary>
            <form action="?" method="GET">
                <div class="mb-3">
                    <label class="form-label">Товар</label>
                    <input type="text" class="form-control" value="Карандаш (заказ №1)" readonly>
                </div>
                <div class="mb-3">
                    <label for="fullName1" class="form-label">Ваше ФИО <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="fullName1" name="fullName" 
                           placeholder="Иванов Иван Иванович" required 
                           pattern="[А-Яа-яЁё\s]+" />
                    <div class="invalid-feedback">Только кириллица и пробелы.</div>
                </div>
                <div class="mb-3">
                    <label for="rating1" class="form-label">Оценка <span class="text-danger">*</span></label>
                    <select class="form-select" id="rating1" name="rating" required>
                        <option value="" disabled selected>Выберите оценку</option>
                        <option value="5">Отлично</option>
                        <option value="4">Хорошо</option>
                        <option value="3">Удовлетворительно</option>
                        <option value="2">Плохо</option>
                        <option value="1">Ужасно</option>
                    </select>
                    <div class="invalid-feedback">Пожалуйста, выберите оценку.</div>
                </div>
                <div class="mb-3">
                    <label for="reviewText1" class="form-label">Текст отзыва <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reviewText1" name="reviewText" rows="3" 
                              placeholder="Расскажите о своём опыте с этим товаром..." required minlength="10"></textarea>
                    <div class="invalid-feedback">Минимум 10 символов.</div>
                </div>
                <button type="submit" class="btn btn-primary">Отправить отзыв</button>
            </form>
        </details>
    </div>

    <div class="text-center mt-4">
        <a href="main.php" class="btn btn-outline-secondary">На главную</a>
    </div>
</div>

<footer class="text-center py-3 border-top mt-auto">
    <p class="mb-0">&copy; 2026 Канцтовары.Есть</p>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>