<?php
include '../../app/db.php';

// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $course_id = $_POST['course_id'];
        $type_schedule_id = $_POST['type_schedule_id'];
        $teacher_id = $_POST['teacher_id'];
        $day_of_week = $_POST['day_of_week'];
        $time = $_POST['time'];

        $stmt = $pdo->prepare("INSERT INTO schedule (course_id, type_schedule_id, teacher_id, day_of_week, time) 
                                   VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $type_schedule_id, $teacher_id, $day_of_week, $time]);

        $last_id = $pdo->lastInsertId();

        echo json_encode([
            'id' => $last_id,
            'course_id' => $course_id,
            'type_schedule_id' => $type_schedule_id,
            'teacher_id' => $teacher_id,
            'day_of_week' => $day_of_week,
            'time' => $time
        ]);
        exit;
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $course_id = $_POST['course_id'];
        $type_schedule_id = $_POST['type_schedule_id'];
        $teacher_id = $_POST['teacher_id'];
        $day_of_week = $_POST['day_of_week'];
        $time = $_POST['time'];

        $stmt = $pdo->prepare("UPDATE schedule SET course_id = ?, type_schedule_id = ?, teacher_id = ?, 
                                   day_of_week = ?, time = ? WHERE id = ?");
        $stmt->execute([$course_id, $type_schedule_id, $teacher_id, $day_of_week, $time, $id]);

        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM schedule WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success']);
        exit;
    }
}
