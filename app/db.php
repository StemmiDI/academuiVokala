<?php
// Подключение к базе данных
require_once __DIR__ . '/../vendor/autoload.php'; // Подключаем Composer autoload

use Dotenv\Dotenv;

// Загружаем переменные окружения
$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$host = $_ENV['DB_HOST'];  // хост
$dbname = $_ENV['DB_NAME']; // название базы данных
$username = $_ENV['DB_USER'];  // имя пользователя
$password = $_ENV['DB_PASS'];  // пароль

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}
