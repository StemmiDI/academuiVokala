<?php
session_start();
include "../app/db.php";

// Получаем данные заявки с курсом
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $pdo->prepare("
        SELECT free_lesson.*, courses.name_course
        FROM free_lesson
        LEFT JOIN courses ON free_lesson.course_id = courses.id
        WHERE id_free_lesson = ?
    ");
    $query->execute([$id]);
    $application = $query->fetch();

    if ($application) {
        echo json_encode($application); // Возвращаем заявку и название курса
    } else {
        echo json_encode(['error' => 'Заявка не найдена']);
    }
}
