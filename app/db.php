<?php
$host = 'localhost';  // хост
$dbname = 'voc_academ'; // название базы данных
$username = 'root';  // имя пользователя
$password = '';  // пароль

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
