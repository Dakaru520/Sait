<?php
session_start();

// Подключение к базе данных
$host = "localhost";
$username = "root"; 
$password = "";
$dbname = "restaurant-pastry_shop";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Обработка выхода из системы
if (isset($_GET['logout'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: login.php");
    exit;
}

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Получение данных пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Если пользователь не найден - разлогиниваем
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет | Школа гитары</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"><!--дает картиночки-->
    <link rel="stylesheet" href="css/lk.css"><!-- подключение к файлу css -->
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <a onclick="location.reload();"><img src="img/image1.svg" alt=""></a><!--лого-->
                <div class="logo-text">
                    <h1>Гито</h1><!--название школы-->
                    <p>школа гитары</p>
                </div>
            </div>
            <a href="?logout=1" class="logout-btn">Выйти</a><!--кнопка для выхода на главный экран-->
        </div>
    </header>
    <a href="lkd.php" class="luna"><img src="img/moon.svg" alt=""></a>
    <div class="container">
        <div class="account-container">
            <aside class="sidebar">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?><!--Первая буква ника-->
                    </div>
                    <div class="username"><?= htmlspecialchars($user['username']) ?></div><!--Ник-->
                    <div class="user-email"><?= htmlspecialchars($user['email']) ?></div><!--Почта-->
                </div>
                
                <ul class="nav-menu"><!--навигационное меню-->
                    <li class="nav-item"><a href="#dashboard" class="nav-link active" data-section="dashboard"><i class="fas fa-home"></i> Главная</a></li>
                </ul>
                
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> На главную</a><!--кликабельный текст для перехода на главный-->
            </aside>
                
            <main class="main-content">
                <div id="dashboard" class="section-content active">
                    <h2 class="section-title">Обзор аккаунта</h2>
                    
                    <div class="stats-cards">
                        <div class="stat-card">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="stat-value">
                                <?= floor((time() - strtotime($user['created_at'])) / (60 * 60 * 24)) ?><!--сколько прошло дней с входа-->
                            </div>
                            <div class="stat-label">Дней с нами</div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-check-circle"></i>
                            <div class="stat-value">5</div>
                            <div class="stat-label">Пройдено уроков</div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-trophy"></i>
                            <div class="stat-value">2</div>
                            <div class="stat-label">Достижения</div>
                        </div>
                    </div>
                    
                    <h2 class="section-title">Последняя активность</h2>
                    
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Вы вошли в систему</div>
                            <div class="activity-date">Сегодня в <?= date('H:i') ?></div><!--Показывает во сколько зашел пользователь-->
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Пройден урок "Основные аккорды"</div>
                            <div class="activity-date">Вчера в 18:30</div>
                        </div>
                    </div>
                </div>
               <div id="lessons" class="section-content">
                    <h2 class="section-title">Мои уроки</h2>
                    <div class="lessons-list">
                        <div class="lesson-card">
                            <h3>Основные аккорды</h3>
                            <p>Статус: <span class="completed">Пройден</span></p>
                        </div>
                        <div class="lesson-card">
                            <h3>Бой на гитаре</h3>
                            <p>Статус: <span class="in-progress">В процессе</span></p>
                        </div>
                    </div>
                </div>
                
                <div id="progress" class="section-content">
                    <h2 class="section-title">Мой прогресс</h2>
                    <div class="progress-chart">
                        <p>Ваш прогресс: 65%</p>
                    </div>
                </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html> 