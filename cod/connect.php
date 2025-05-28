<?php
// Данные для входа в серверную часть
$host = "localhost";
$username = "root"; 
$password = "";
$dbname = "restaurant-pastry_shop";
// Вход в бд
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
// Установка кодировки
$conn->set_charset("utf8");
?>