<?php
session_start();
include "../../app/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['course_name'], $_POST['course_date'], $_POST['lesson_time'])) {
        $id = $_POST['id'];  // ID заявки
        $courseId = $_POST['course_name'];  // ID курса
        $courseDate = $_POST['course_date'];  // Дата курса
        $lessonTime = $_POST['lesson_time'];  // Время курса

        try {
            // Обновляем данные заявки
            $query = $pdo->prepare("
                UPDATE free_lesson 
                SET course_id = ?, course_date = ?, lesson_time = ?
                WHERE id_free_lesson = ?
            ");
            $query->execute([$courseId, $courseDate, $lessonTime, $id]);

            // Получаем имя курса
            $courseQuery = $pdo->prepare("SELECT name_course FROM courses WHERE id = ?");
            $courseQuery->execute([$courseId]);
            $courseName = $courseQuery->fetchColumn();

            // Также можно вернуть имя и email, если они тоже редактируются
            $userQuery = $pdo->prepare("SELECT free_lesson_name, free_lesson_email FROM free_lesson WHERE id_free_lesson = ?");
            $userQuery->execute([$id]);
            $user = $userQuery->fetch();

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'name' => $user['free_lesson_name'],
                    'email' => $user['free_lesson_email'],
                    'course' => $courseName,
                    'date' => date("d.m.Y", strtotime($courseDate)),
                    'time' => $lessonTime
                ]
            ]);
        } catch (Exception $e) {
            error_log("Ошибка: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Отсутствуют необходимые параметры']);
    }
}
