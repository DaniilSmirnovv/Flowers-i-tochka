<?php
require_once 'config.php';

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Цветы - и точка</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700;900&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2">
    
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

<!-- ============ HERO ============ -->
<section class="hero-section">
    <div class="hero-blob blob-1"></div>
    <div class="hero-blob blob-2"></div>
    <div class="hero-blob blob-3"></div>

    <div class="container">
        <div class="row g-5 align-items-center hero-content">
            <div class="col-lg-6">
                <span class="hero-tag">Сезон · Осень 2026</span>
                <h1 class="hero-title">
                    Свежие цветы<br>
                    <span class="accent">с доставкой</span><br>
                    за 2 часа
                </h1>
                <p class="hero-lead">
                    Собираем букеты вручную. Только свежие цветы — никаких заготовок с утра. Каждый букет — маленькая история любви.
                </p>
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="#catalog" class="btn-accent">Смотреть каталог →</a>
                    <a href="order.php" class="btn-outline-ink">Заказать букет</a>
                </div>

                <div class="hero-stats">
                    <div>
                        <div class="stat-num">5K+</div>
                        <div class="stat-label">довольных клиентов</div>
                    </div>
                    <div>
                        <div class="stat-num">120</div>
                        <div class="stat-label">видов цветов</div>
                    </div>
                    <div>
                        <div class="stat-num">6</div>
                        <div class="stat-label">лет на рынке</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-wrap">
                    <img src="img/logo.jpg"
                         class="hero-img"
                         alt="Цветы - и точка">
                    <div class="hero-badge-float top-left">
                        <div class="badge-icon rose">🌸</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem;">Свежесть</div>
                            <div style="font-size: 0.75rem; color: var(--ink-soft);">каждый день</div>
                        </div>
                    </div>
                    <div class="hero-badge-float bottom-right">
                        <div class="badge-icon sage">⚡</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem;">2 часа</div>
                            <div style="font-size: 0.75rem; color: var(--ink-soft);">доставка</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ MARQUEE ============ -->
<div class="marquee">
    <div class="marquee-track">
        <span>Розы <span class="dot">✿</span> Пионы <span class="dot">✿</span> Тюльпаны <span class="dot">✿</span> Хризантемы <span class="dot">✿</span> Гортензии <span class="dot">✿</span> Ранункулюсы <span class="dot">✿</span></span>
        <span>Розы <span class="dot">✿</span> Пионы <span class="dot">✿</span> Тюльпаны <span class="dot">✿</span> Хризантемы <span class="dot">✿</span> Гортензии <span class="dot">✿</span> Ранункулюсы <span class="dot">✿</span></span>
    </div>
</div>

<!-- ============ ПРЕИМУЩЕСТВА ============ -->
<section class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Почему мы</span>
            <h2 class="section-title">Что делает нас особенными</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon rose">🌸</div>
                    <h6>Свежесть каждый день</h6>
                    <p>Поставки 3 раза в неделю напрямую с плантаций Эквадора и Голландии</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon peach">🚚</div>
                    <h6>Доставка за 2 часа</h6>
                    <p>По городу — бесплатно при заказе от 3 000 ₽. Курьеры с любовью</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon sage">💐</div>
                    <h6>Авторские букеты</h6>
                    <p>Флористы с опытом от 5 лет соберут композицию под ваш повод</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ КАК МЫ РАБОТАЕМ ============ -->
<section class="steps-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Процесс</span>
            <h2 class="section-title">Как мы работаем</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <h5>Выбираете букет</h5>
                    <p>Из каталога или заказываете индивидуальную композицию у флориста</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">02</div>
                    <h5>Мы собираем</h5>
                    <p>Флорист вручную собирает букет из свежих цветов в день заказа</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">03</div>
                    <h5>Доставляем</h5>
                    <p>Курьер привозит букет в специальной упаковке за 2 часа</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ КАТАЛОГ ============ -->
<section id="catalog" class="catalog-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-5">
            <div>
                <span class="section-label">Каталог</span>
                <h2 class="section-title mb-0">Наши цветы</h2>
            </div>
            <a href="order.php" class="btn-outline-ink">
                Оформить заявку →
            </a>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-5 bg-white rounded-4" style="border: 2px dashed rgba(0,0,0,0.1);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✿</div>
                <p class="text-secondary mb-0">Товаров пока нет. Загляните позже</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php foreach ($products as $i => $p): ?>
                    <div class="col">
                        <div class="product-card">
                            <div class="product-img-wrap">
                                <?php if (!empty($p['image']) && file_exists($p['image'])): ?>
                                    <img src="<?= htmlspecialchars($p['image']) ?>"
                                         class="product-img"
                                         alt="<?= htmlspecialchars($p['name']) ?>">
                                <?php else: ?>
                                    <div class="product-img d-flex align-items-center justify-content-center text-muted" style="background: linear-gradient(135deg, #f5f1ec, #e8e0d5);">
                                        <div style="font-size: 4rem; opacity: 0.3;">✿</div>
                                    </div>
                                <?php endif; ?>
                                <span class="product-badge <?= $i % 3 === 0 ? 'hot' : '' ?>">
                                    <?= $i % 3 === 0 ? 'Хит' : 'New' ?>
                                </span>
                            </div>
                            <div class="product-body">
                                <div class="product-category">Букет</div>
                                <h6 class="product-title"><?= htmlspecialchars($p['name']) ?></h6>
                                <p class="product-desc">
                                    <?= htmlspecialchars($p['description']) ?>
                                </p>
                                <div class="product-footer">
                                    <div class="product-price">
                                        <?= number_format($p['price'], 0, '.', ' ') ?> ₽
                                    </div>
                                    <a href="order.php" class="btn-order">
                                        Заказать →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ ОТЗЫВЫ ============ -->
<section class="testimonials-section">
    <div class="container position-relative" style="z-index: 2;">
        <div class="text-center mb-5">
            <span class="section-label" style="color: var(--rose);">Отзывы</span>
            <h2 class="section-title" style="color: #fff;">Что говорят клиенты</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"Заказывала букет маме на день рождения. Привезли вовремя, цветы свежайшие, мама была в восторге! Обязательно закажу ещё."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">АК</div>
                        <div>
                            <p class="testimonial-name">Анна К.</p>
                            <p class="testimonial-role">Постоянный клиент</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"Лучший цветочный магазин в городе! Флористы настоящие художники — собрали букет именно так, как я просил. Спасибо!"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">ДМ</div>
                        <div>
                            <p class="testimonial-name">Дмитрий М.</p>
                            <p class="testimonial-role">Заказал 12 букетов</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"Быстро, красиво, качественно. Курьер вежливый, букет простоял больше недели. Рекомендую всем!"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">ЕС</div>
                        <div>
                            <p class="testimonial-name">Елена С.</p>
                            <p class="testimonial-role">Новый клиент</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Не нашли подходящий букет?</h2>
            <p>Соберём индивидуальную композицию под ваш повод и бюджет. Оставьте заявку — флорист свяжется в течение 15 минут.</p>
            <a href="order.php" class="btn-cta">Оставить заявку ✿</a>
        </div>
    </div>
</section>

<!-- ============ ПОДВАЛ ============ -->
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="footer-brand">
                    Цветы - и точка<span style="color: var(--rose-deep);">.</span>
                </div>
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