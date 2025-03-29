<?php
session_start();
include "../app/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['course_name'], $_POST['course_date'], $_POST['lesson_time'])) {
        $id = $_POST['id'];  // ID заявки
        $courseId = $_POST['course_name'];  // ID курса
        $courseDate = $_POST['course_date'];  // Дата курса
        $lessonTime = $_POST['lesson_time'];  // Время курса

        try {
            // Логируем пришедшие данные
            error_log("ID: $id, Course ID: $courseId, Course Date: $courseDate, Lesson Time: $lessonTime");

            // Обновляем данные заявки
            $query = $pdo->prepare("
                UPDATE free_lesson 
                SET course_id = ?, course_date = ?, lesson_time = ?
                WHERE id_free_lesson = ?
            ");
            $query->execute([$courseId, $courseDate, $lessonTime, $id]);

            echo json_encode(['success' => true]); // Возвращаем успешный ответ
        } catch (Exception $e) {
            // Если ошибка
            error_log("Ошибка: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Отсутствуют необходимые параметры']);
    }
}
