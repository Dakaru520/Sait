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

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Поиск пользователя в БД
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Проверка пароля
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: lk.php');
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}

// Если пользователь уже авторизован - перенаправляем в ЛК
if (isset($_SESSION['user_id'])) {
    header('Location: lk.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Вход в личный кабинет</title>
</head>
<body>
    <h2>Вход в личный кабинет</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Пароль" required>
        </div>
        <button type="submit">Войти</button>
    </form>
    
    <a href="forma.php" class="register-link">Нет аккаунта? Зарегистрируйтесь</a>
    <a href="index.php" class="register-link">Перейти на главную</a>
</body>
</html>