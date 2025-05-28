<?php
// Подключение к базе данных
$host = "localhost";
$Login = "root"; 
$password = "";
$dbname = "restaurant-pastry_shop";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $Login, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Получение данных из формы
        $Login = trim($_POST['Login']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Валидация данных
        if (empty($Login)) {
            $errors['Login'] = 'Введите имя пользователя';
        } elseif (strlen($Login) < 3) {
            $errors['Login'] = 'Имя пользователя должно содержать минимум 3 символа';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Введите email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный email';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Введите пароль';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Пароль должен содержать минимум 6 символов';
        }
        
        if (empty($confirm_password)) {
            $errors['confirm_password'] = 'Подтвердите пароль';
        } elseif ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Пароли не совпадают';
        }
        
        // Проверка существующего пользователя
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE Login = ? OR email = ?");
            $stmt->execute([$Login, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors['database'] = 'Пользователь с таким именем или email уже существует';
            }
        }
        
        // Регистрация пользователя
        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO Users (Login, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$Login, $email, $hashed_password]);
            
            $success = true;
        }
    } catch(PDOException $e) {
        $errors['database'] = 'Ошибка при регистрации: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/forma.css">
    <title>Регистрация | Вишневый сад</title>
</head>
<body>
    <!-- Шапка -->
    <header>
        <div class="logo-container">
            <a onclick="location.reload();"><img src="img/image1.svg" alt="Логотип"></a>
            <div class="logo-text">
                <div class="main">Гито</div>
                <div class="sub">Вишневый сад</div>
            </div>
        </div>
        
        <div class="navigation">
            <a href="#preimushestva" class="nav-link">Преимущества</a>
            <a href="#obuchenie" class="nav-link">Обучение</a>
            <a href="#otzyvy" class="nav-link">Отзывы</a>
            <a href="#kontakty" class="nav-link">Контакты</a>
        </div>
        
        <a href="index.php" class="back-btn">Назад</a>
    </header>
    
    <!-- Основное содержимое -->
    <main>
        <div class="registration-form">
            <h1 class="form-title">Регистрация</h1>
            
            <?php if ($success): ?>
                <div class="success-message">
                    Регистрация успешно завершена!<br>
                    <a href="login.php" style="color: #27ae60; font-weight: 500;">Войти в аккаунт</a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors['database'])): ?>
                    <div class="error-message" style="text-align: center; margin-bottom: 20px;">
                        <?= $errors['database'] ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="Login" class="form-label">Имя пользователя</label>
                        <input type="text" id="Login" name="Login" class="form-input" 
                               value="<?= htmlspecialchars($_POST['Login'] ?? '') ?>">
                        <?php if (!empty($errors['Login'])): ?>
                            <div class="error-message"><?= $errors['Login'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <?php if (!empty($errors['email'])): ?>
                            <div class="error-message"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" id="password" name="password" class="form-input" 
                               oninput="checkPasswordMatch()">
                        <?php if (!empty($errors['password'])): ?>
                            <div class="error-message"><?= $errors['password'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Подтвердите пароль</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                               oninput="checkPasswordMatch()">
                        <div id="password-match-error" class="error-message">
                            <?= $errors['confirm_password'] ?? '' ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Зарегистрироваться</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Подвал -->
             <div class="block5" id="kontakt"><!--Пятый блок-->
                <p class="tb5_1" >г.Кемерово, Марата 1</p><!--адрес где находится школа-->
                <p class="tb5_1">+79069858491</p><!--номер телефона-->
                <p class="tb5_2">ежедневно/ 8-20</p><!--со скольки и до скольки работает-->
                <footer><!--нижний блок-->
                <p class="tb5_3"> © Перемитин Леонид Алексеевич ПР-23</p><!--Создатель сайта-->
                <a href="https://vk.com/id661428597" target="_blank"><img src="img/vk.svg" alt="" class="svg" ></a><!--кликабельная картинка, при нажатии которой перекидывает в вк создателя-->
                <a href="https://t.me/Leontiy01" target="_blank"><img src="img/tg.svg" alt="" class="svg" ></a><!--кликабельная картинка, при нажатии которой перекидывает в тг создателя-->             
                <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" target="_blank"><img src="img/YouTube.svg" alt="" class="svg"></a><!--кликабельная картинка, при нажатии которой перекидывает в ютуб создателя-->
                <a onclick="document.getElementById('modal').style.display='block'"><img src="img/нофелет.svg" alt="" class="svg"></a><!--кликабельная картинка, при нажатии которой открывается модальное окно с номером создателя-->
                <div id="modal" class="modal-overlay">
                    <div class="modal-content">
                        <span class="modal-close" onclick="document.getElementById('modal').style.display='none'">&times;</span>
                        <p class="tel-number">8-906-985-84-91</p>
                    </div>
                </div>
                </footer>
            </div>
</body>
</html