<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON', 'raw' => $raw]);
    exit;
}

$action = $data['action'];

include '../app/db.php';

if ($action === 'add') {
    $name_sub = $data['name_sub'];
    $level = $data['level'];
    $number_of_lesson = $data['number_of_lesson'];
    $price = $data['price'];

    // Вставляем новый абонемент в базу данных
    $stmt = $pdo->prepare("INSERT INTO subscriptions (name_sub, level, number_of_lesson, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name_sub, $level, $number_of_lesson, $price]);

    // Получаем ID нового абонемента
    $id = $pdo->lastInsertId();

    // Возвращаем успешный ответ с данными нового абонемента
    echo json_encode([
        'status' => 'success',
        'id' => $id,
        'name_sub' => $name_sub,
        'level' => $level,
        'number_of_lesson' => $number_of_lesson,
        'price' => $price
    ]);
    exit;
}


if ($action === 'edit') {
    $id = $data['id'];
    $name = $data['name_sub'];
    $level = $data['level'];
    $number_of_lesson = $data['number_of_lesson'];
    $price = $data['price'];

    $stmt = $pdo->prepare("UPDATE subscriptions SET name_sub = ?, level = ?, number_of_lesson = ?, price = ? WHERE id = ?");
    $success = $stmt->execute([$name, $level, $number_of_lesson, $price, $id]);

    if ($success) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Не удалось обновить запись']);
    }
    exit;
}
if ($action === 'delete') {
    $id = $data['id'];

    // Удаляем абонемент из базы данных
    $stmt = $pdo->prepare("DELETE FROM subscriptions WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['status' => 'success']);
    exit;
}



echo json_encode(['error' => 'Unknown action']);
