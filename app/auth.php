<?php
session_start();
include "db.php";

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$_SESSION['errors'] = []; // Очищаем ошибки перед проверкой

// Проверка email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['errors'][] = "Введите корректный адрес электронной почты.";
}

// Проверка пароля
if (strlen($password) < 6) {
    $_SESSION['errors'][] = "Пароль должен содержать не менее 6 символов.";
}

if (!empty($_SESSION['errors'])) {
    header("Location: ../login.php");
    exit;
}

$query = $pdo->prepare("SELECT * FROM users WHERE email= ?");
$query->execute([$email]);
$row = $query->fetch();

if ($row && password_verify($password, $row['password'])) {
    $_SESSION['id_user'] = $row['id_user'];

    if ($password === "adminka123") {
        $_SESSION['admin'] = true;
        header("Location: ../admin/admin.php");
    } else {
        header("Location: ../user_profile.php");
    }
    exit;
} else {
    $_SESSION['errors'][] = "Неверные данные для входа.";
    header("Location: ../login.php");
    exit;
}
