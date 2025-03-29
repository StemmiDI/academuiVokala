<?php
session_start();
include "db.php"; // Подключаем базу данных

$name = $_POST['name'];
$fullName = $_POST['full_name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Проверяем, существует ли уже пользователь с таким email
$query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$query->execute([$email]);
$row = $query->fetch();

if ($row) { // Если такой пользователь существует
    echo "Пользователь с такой почтой уже зарегистрирован";
} else {
    // Хешируем пароль перед сохранением в базе данных
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Вставляем нового пользователя в базу данных
    $query = $pdo->prepare("INSERT INTO users (name, full_name, email, password) VALUES (?, ?, ?, ?)");
    $query->execute([$name, $fullName, $email, $hashedPassword]);

    // Перенаправляем на страницу логина после успешной регистрации
    header("Location: ../login.php");
    exit; // Прерываем выполнение скрипта после редиректа
}
