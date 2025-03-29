<?php
include '../../app/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM courses");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');

    if (!isset($_POST['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'Не указано действие!']);
        exit();
    }

    $action = $_POST['action'];

    if ($action === 'add') {
        try {
            //code...

            $name_course = trim($_POST['name_course'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (!$name_course || !$description) {
                echo json_encode(['status' => 'error', 'message' => 'Заполните все поля!']);
                exit();
            }

            // Загрузка изображения
            $icon_name = 'default.png';
            if (!empty($_FILES['icon']['name'])) {
                $upload_dir = '../../uploads/';
                $icon_name = time() . '_' . basename($_FILES['icon']['name']);
                move_uploaded_file($_FILES['icon']['tmp_name'], $upload_dir . $icon_name);
            }

            $stmt = $pdo->prepare("INSERT INTO courses (name_course, description, icon) VALUES (?, ?, ?)");
            $stmt->execute([$name_course, $description, $icon_name]);

            // Получаем ID последней вставленной записи
            $newCourseId = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'id' => $newCourseId, // Теперь ID определен
                'name_course' => $name_course,
                'description' => $description,
                'icon' => $icon_name // Исправлено: использовалась неверная переменная
            ]);
            exit();
        } catch (\Throwable $th) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка: ' . $th->getMessage()]);
        }
    }


    if ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $name_course = trim($_POST['name_course'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$id || !$name_course || !$description) {
            echo json_encode(['status' => 'error', 'message' => 'Заполните все поля!']);
            exit();
        }

        // Проверка, загружено ли новое изображение
        if (!empty($_FILES['icon']['name'])) {
            $upload_dir = '../../uploads/';
            $icon_name = time() . '_' . basename($_FILES['icon']['name']);
            move_uploaded_file($_FILES['icon']['tmp_name'], $upload_dir . $icon_name);

            // Обновляем курс с новой иконкой
            $stmt = $pdo->prepare("UPDATE courses SET name_course = ?, description = ?, icon = ? WHERE id = ?");
            $stmt->execute([$name_course, $description, $icon_name, $id]);
        } else {
            // Обновляем курс без изменения иконки
            $stmt = $pdo->prepare("UPDATE courses SET name_course = ?, description = ? WHERE id = ?");
            $stmt->execute([$name_course, $description, $id]);
        }

        echo json_encode(['status' => 'success']);
        exit();
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'Некорректный ID!']);
            exit();
        }

        // Получаем текущий icon
        $stmt = $pdo->prepare("SELECT icon FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        // Удаляем файл иконки (кроме иконки по умолчанию)
        if ($course && $course['icon'] !== 'default.png' && file_exists("../../uploads/" . $course['icon'])) {
            unlink("../../uploads/" . $course['icon']);
        }

        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success']);
        exit();
    }
}
