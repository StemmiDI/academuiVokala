<?php
include '../../app/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // === ДОБАВЛЕНИЕ ПРЕПОДАВАТЕЛЯ ===
    if ($action === 'add') {
        $name = $_POST['name_teacher'];
        $desc = $_POST['description'];
        $phone = $_POST['phone_number'];
        $email = $_POST['email'];
        $course_id = $_POST['course_id'];
        $photo = null;

        if (!empty($_FILES['photo']['name'])) {
            $photo = time() . '_' . $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], '../../uploads/' . $photo);
        }

        $stmt = $pdo->prepare("INSERT INTO teachers (name_teacher, description, phone_number, email, course_id, photo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $phone, $email, $course_id, $photo]);
        $id = $pdo->lastInsertId();
        $course_name = $pdo->query("SELECT name_course FROM courses WHERE id = $course_id")->fetchColumn();

        echo json_encode([
            'status' => 'success',
            'id' => $id,
            'name_teacher' => $name,
            'description' => $desc,
            'phone_number' => $phone,
            'email' => $email,
            'photo' => $photo,
            'course_name' => $course_name
        ]);
        exit;
    }

    // === РЕДАКТИРОВАНИЕ ПРЕПОДАВАТЕЛЯ ===
    if ($action === 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name_teacher'];
        $desc = $_POST['description'];
        $phone = $_POST['phone_number'];
        $email = $_POST['email'];
        $course_id = $_POST['course_id'];

        if (!empty($_FILES['photo']['name'])) {
            $photo = time() . '_' . $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . $photo);
            $stmt = $pdo->prepare("UPDATE teachers SET name_teacher=?, description=?, phone_number=?, email=?, course_id=?, photo=? WHERE id=?");
            $stmt->execute([$name, $desc, $phone, $email, $course_id, $photo, $id]);

            echo json_encode(['status' => 'success', 'photo' => $photo]);
        } else {
            $stmt = $pdo->prepare("UPDATE teachers SET name_teacher=?, description=?, phone_number=?, email=?, course_id=? WHERE id=?");
            $stmt->execute([$name, $desc, $phone, $email, $course_id, $id]);

            echo json_encode(['status' => 'success']);
        }

        exit;
    }

    // === УДАЛЕНИЕ ПРЕПОДАВАТЕЛЯ ===
    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // === НЕИЗВЕСТНОЕ ДЕЙСТВИЕ ===
    echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
    exit;
}
