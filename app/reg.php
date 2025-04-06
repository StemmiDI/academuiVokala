<?php
session_start();
include "db.php"; // Подключаем базу данных

$name = trim($_POST['name']);
$fullName = trim($_POST['full_name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

$_SESSION['errors'] = []; // Очищаем ошибки перед проверкой

// Проверка имени
if (!preg_match("/^[A-Za-zА-Яа-яЁё]+$/u", $name)) {
    $_SESSION['errors'][] = "Имя должно содержать только буквы.";
}

// Проверка фамилии
if (!preg_match("/^[A-Za-zА-Яа-яЁё]+$/u", $fullName)) {
    $_SESSION['errors'][] = "Фамилия должна содержать только буквы.";
}

// Проверка email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['errors'][] = "Введите корректный адрес электронной почты.";
}

// Проверка пароля
if (strlen($password) < 6) {
    $_SESSION['errors'][] = "Пароль должен содержать не менее 6 символов.";
}

if (!empty($_SESSION['errors'])) {
    header("Location: ../register.php");
    exit;
}

// Проверяем, существует ли уже пользователь с таким email
$query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$query->execute([$email]);
$row = $query->fetch();

if ($row) { // Если такой пользователь существует
    $_SESSION['errors'][] = "Пользователь с такой почтой уже зарегистрирован.";
    header("Location: ../register.php");
    exit;
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
