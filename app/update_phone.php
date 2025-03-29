<?php
session_start();
include "db.php";

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['id_user'])) {
    die("Ошибка: пользователь не авторизован.");
}

$id_user = $_SESSION['id_user'];
$newPhone = $_POST['phone'] ?? '';

// Проверяем, что номер телефона не пустой
if (empty($newPhone)) {
    die("Ошибка: номер телефона не может быть пустым.");
}

// Обновляем номер телефона в базе данных
$query = $pdo->prepare("UPDATE users SET phone = ? WHERE id_user = ?");
if ($query->execute([$newPhone, $id_user])) {
    echo "success";
} else {
    echo "Ошибка при обновлении номера.";
}
