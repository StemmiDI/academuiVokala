<?php
// Подключаем базу данных
include 'db.php';

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Удаляем курс из базы данных
    $stmt = $connect->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);

    // Перенаправление обратно на страницу управления курсами
    header("Location: ../admin/admin_cou.php");
    exit();
}
