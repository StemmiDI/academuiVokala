<?php
// Подключаем базу данных
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_course = $_POST['name_course'];
    $description = $_POST['description'];

    // Подготовка SQL-запроса для добавления курса
    $stmt = $connect->prepare("INSERT INTO courses (name_course, description) VALUES (?, ?)");
    $stmt->execute([$name_course, $description]);

    // Перенаправление обратно на страницу управления курсами
    header("Location: ../admin/admin_cou.php");
    exit();
}
