<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - Цветы - и точка</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700;900&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link px-3 active" href="about.php">О нас</a></li>
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

<!-- ============ HERO: О НАС ============ -->
<section class="about-hero">
    <div class="about-hero-bg"></div>
    <div class="container">
        <div class="about-hero-content">
            <div class="about-hero-label">Наша история</div>
            <h1 class="about-hero-title">
                Мы — <span class="italic">маленькая</span><br>
                мастерская с большой<br>
                <span class="italic">душой</span>
            </h1>
            <p class="about-hero-subtitle">
                «Цветы - и точка» — это не просто магазин. Это место, где каждый букет 
                собирается вручную, с любовью и вниманием к деталям. Мы верим, что цветы — 
                это язык, на котором можно сказать самое важное.
            </p>
            <div class="about-hero-meta">
                <div class="about-hero-meta-item">
                    <div class="about-hero-meta-num">2020</div>
                    <div class="about-hero-meta-label">Год основания</div>
                </div>
                <div class="about-hero-meta-item">
                    <div class="about-hero-meta-num">5K+</div>
                    <div class="about-hero-meta-label">Клиентов</div>
                </div>
                <div class="about-hero-meta-item">
                    <div class="about-hero-meta-num">6</div>
                    <div class="about-hero-meta-label">Лет опыта</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ МАНИФЕСТ ============ -->
<section class="manifesto-section">
    <div class="container">
        <div class="manifesto-quote">
            <p class="manifesto-text">
                Мы не продаём цветы. Мы помогаем людям говорить друг с другом 
                на языке, который понятен сердцу — без слов, только через красоту 
                и аромат живых бутонов.
            </p>
            <div class="manifesto-author">
                — <strong>Основатели мастерской</strong>, 2020
            </div>
        </div>
    </div>
</section>

<!-- ============ ТАЙМЛАЙН ============ -->
<section class="timeline-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Путь</span>
            <h2 class="section-title">Как мы росли</h2>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-year">2020</div>
                    <h3 class="timeline-title">Начало пути</h3>
                    <p class="timeline-desc">
                        Два друга-флориста открыли маленькую мастерскую в центре Москвы. 
                        Первый букет был собран для соседки — и она осталась в восторге.
                    </p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-year">2021</div>
                    <h3 class="timeline-title">Первые поставки</h3>
                    <p class="timeline-desc">
                        Наладили прямые поставки с плантаций Эквадора и Голландии. 
                        Появилась собственная команда из 3 флористов.
                    </p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-year">2023</div>
                    <h3 class="timeline-title">Онлайн-магазин</h3>
                    <p class="timeline-desc">
                        Запустили сайт и доставку по городу. За первый месяц — 200 заказов. 
                        Клиенты стали возвращаться снова и снова.
                    </p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-year">2026</div>
                    <h3 class="timeline-title">Сегодня</h3>
                    <p class="timeline-desc">
                        6 профессиональных флористов, 120 видов цветов, 5000+ довольных клиентов. 
                        Но принцип остался тем же — качество и внимание к каждому букету.
                    </p>
                </div>
                <div class="timeline-dot"></div>
            </div>
        </div>
    </div>
</section>

<!-- ============ ЦЕННОСТИ ============ -->
<section class="values-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label" style="color: var(--rose);">Принципы</span>
<h2 class="section-title" style="color: #ffffff !important;">Во что мы верим</h2>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-number">01</div>
                <h3 class="value-title">Свежесть</h3>
                <p class="value-desc">
                    Никаких заготовок с утра. Каждый букет собирается в день заказа 
                    из цветов, которые прибыли к нам не более 3 дней назад.
                </p>
            </div>
            <div class="value-card">
                <div class="value-number">02</div>
                <h3 class="value-title">Ручная работа</h3>
                <p class="value-desc">
                    Мы не используем шаблоны. Каждый букет — это индивидуальная 
                    композиция, созданная под повод и настроение клиента.
                </p>
            </div>
            <div class="value-card">
                <div class="value-number">03</div>
                <h3 class="value-title">Честность</h3>
                <p class="value-desc">
                    Прозрачные цены, реальные фото, никаких скрытых наценок. 
                    Если цветок не идеален — мы скажем об этом прямо.
                </p>
            </div>
            <div class="value-card">
                <div class="value-number">04</div>
                <h3 class="value-title">Забота</h3>
                <p class="value-desc">
                    Курьеры с любовью, упаковка с вниманием, поддержка 7 дней в неделю. 
                    Мы относимся к каждому заказу как к своему.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ============ КОМАНДА ============ -->
<section class="team-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Люди</span>
            <h2 class="section-title">Наша команда</h2>
            <p class="text-secondary" style="max-width: 600px; margin: 0 auto;">
                За каждым букетом стоят люди, которые любят своё дело. 
                Познакомьтесь с теми, кто создаёт красоту каждый день.
            </p>
        </div>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar">АМ</div>
                <h3 class="team-name">Анна Михайлова</h3>
                <div class="team-role">Главный флорист</div>
                <p class="team-bio">10 лет опыта. Специализация — свадебные композиции и авторские букеты.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">ДК</div>
                <h3 class="team-name">Дмитрий Козлов</h3>
                <div class="team-role">Сооснователь</div>
                <p class="team-bio">Отвечает за поставки и качество цветов. Знает каждого поставщика лично.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">ЕС</div>
                <h3 class="team-name">Елена Соколова</h3>
                <div class="team-role">Флорист</div>
                <p class="team-bio">Мастер минималистичных букетов. Верит, что меньше — значит больше.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">ИВ</div>
                <h3 class="team-name">Иван Волков</h3>
                <div class="team-role">Курьер</div>
                <p class="team-bio">Доставляет букеты с улыбкой. Знает все районы города наизусть.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ FAQ ============ -->
<section class="faq-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Вопросы</span>
            <h2 class="section-title">Часто спрашивают</h2>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <h3 class="faq-question">
                    Как долго стоят ваши букеты?
                    <span class="faq-icon">+</span>
                </h3>
                <div class="faq-answer">
                    При правильном уходе наши букеты стоят от 7 до 14 дней. 
                    Мы даём инструкцию по уходу с каждым заказом и используем 
                    только свежайшие цветы с плантаций.
                </div>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">
                    Можно ли заказать индивидуальный букет?
                    <span class="faq-icon">+</span>
                </h3>
                <div class="faq-answer">
                    Конечно! Это наша специализация. Расскажите флористу о поводе, 
                    предпочтениях получателя и бюджете — и мы соберём уникальную 
                    композицию специально для вас.
                </div>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">
                    Какие способы оплаты вы принимаете?
                    <span class="faq-icon">+</span>
                </h3>
                <div class="faq-answer">
                    Мы принимаем наличные при получении и переводы по номеру телефона. 
                    Оплата происходит после подтверждения заказа администратором.
                </div>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">
                    Как быстро осуществляется доставка?
                    <span class="faq-icon">+</span>
                </h3>
                <div class="faq-answer">
                    По городу — в течение 2 часов с момента подтверждения заказа. 
                    Доставка бесплатна при заказе от 3 000 ₽. Также можно выбрать 
                    удобную дату и время.
                </div>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">
                    Работаете ли вы в выходные и праздники?
                    <span class="faq-icon">+</span>
                </h3>
                <div class="faq-answer">
                    Да, мы работаем ежедневно с 9:00 до 21:00, включая выходные и праздники. 
                    В особые дни (8 Марта, 14 февраля) рекомендуем заказывать заранее.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ КОНТАКТЫ ============ -->
<section class="about-contacts">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label" style="color: #fff;">Связь</span>
            <h2 class="section-title" style="color: #fff;">Как нас найти</h2>
        </div>
        <div class="about-contacts-grid">
            <div class="contact-card">
                <div class="contact-icon">📞</div>
                <div class="contact-label">Телефон</div>
                <p class="contact-value">
                    <a href="tel:+79991234567">+7 (999) 123-45-67</a>
                </p>
            </div>
            <div class="contact-card">
                <div class="contact-icon">✉</div>
                <div class="contact-label">Email</div>
                <p class="contact-value">
                    <a href="mailto:hello@flowers-i-tochka.ru">hello@flowers-i-tochka.ru</a>
                </p>
            </div>
            <div class="contact-card">
                <div class="contact-icon">📍</div>
                <div class="contact-label">Адрес</div>
                <p class="contact-value">Москва, ул. Цветочная, 1</p>
            </div>
            <div class="contact-card">
                <div class="contact-icon">🕐</div>
                <div class="contact-label">Часы работы</div>
                <p class="contact-value">Ежедневно 9:00 – 21:00</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="about-cta">
    <div class="container">
        <h2 class="about-cta-title">Готовы заказать букет?</h2>
        <p class="about-cta-subtitle">
            Оставьте заявку — и наш флорист свяжется с вами в течение 15 минут, 
            чтобы обсудить детали.
        </p>
        <a href="order.php" class="btn-accent">Оформить заявку →</a>
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
<script>
    // FAQ аккордеон
    document.querySelectorAll('.faq-question').forEach(q => {
        q.addEventListener('click', () => {
            const item = q.parentElement;
            const isActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            if (!isActive) item.classList.add('active');
        });
    });
</script>
</body>
</html>