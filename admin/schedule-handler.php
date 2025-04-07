<?php
// Подключение к базе данных
include '../app/db.php';

// Проверка, что запрос пришел методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем действие из запроса
    $action = $_POST['action'];

    // Добавление нового расписания
    if ($action === 'add') {
        $course_id = $_POST['course_id'];
        $type_schedule_id = $_POST['type_schedule_id'];
        $teacher_id = $_POST['teacher_id'];
        $day_of_week = $_POST['day_of_week'];
        $time = $_POST['time'];

        // Подготовка запроса для добавления расписания
        $stmt = $pdo->prepare("INSERT INTO schedule (course_id, type_schedule_id, teacher_id, day_of_week, time) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $type_schedule_id, $teacher_id, $day_of_week, $time]);

        // Получаем ID последней вставленной записи
        $last_id = $pdo->lastInsertId();

        // Возвращаем данные о добавленном расписании в формате JSON
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

    // Редактирование существующего расписания
    if ($action === 'edit') {
        $id = $_POST['id'];
        $course_id = $_POST['course_id'];
        $type_schedule_id = $_POST['type_schedule_id'];
        $teacher_id = $_POST['teacher_id'];
        $day_of_week = $_POST['day_of_week'];
        $time = $_POST['time'];

        // Подготовка запроса для обновления расписания
        $stmt = $pdo->prepare("UPDATE schedule 
                               SET course_id = ?, type_schedule_id = ?, teacher_id = ?, 
                                   day_of_week = ?, time = ? 
                               WHERE id = ?");
        $stmt->execute([$course_id, $type_schedule_id, $teacher_id, $day_of_week, $time, $id]);

        // Возвращаем успех
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Удаление расписания
    if ($action === 'delete') {
        $id = $_POST['id'];

        // Подготовка запроса для удаления расписания
        $stmt = $pdo->prepare("DELETE FROM schedule WHERE id = ?");
        $stmt->execute([$id]);

        // Возвращаем успех
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Если запрос не POST или не найдено нужное действие
echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit;
