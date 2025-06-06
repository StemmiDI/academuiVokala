<?php
session_start();
header('Content-Type: application/json');

include "../app/db.php";

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Нет доступа']);
    exit;
}

$subscription_id = $_POST['subscription_id'] ?? null;
$lesson_number = $_POST['lesson_number'] ?? null;

if (!$subscription_id || !$lesson_number) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit;
}


// Проверка дублирования
$stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM completed_lessons WHERE user_subscription_id = ? AND lesson_number = ?");
$stmtCheck->execute([$subscription_id, $lesson_number]);
if ($stmtCheck->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Урок уже отмечен']);
    exit;
}

// Вставка + обновление
$stmtInsert = $pdo->prepare("INSERT INTO completed_lessons (user_subscription_id, lesson_number) VALUES (?, ?)");
if ($stmtInsert->execute([$subscription_id, $lesson_number])) {
    $stmtUpdate = $pdo->prepare("UPDATE user_subscriptions SET number_rem_classes = number_rem_classes - 1 WHERE id = ?");
    $stmtUpdate->execute([$subscription_id]);

    $stmtSub = $pdo->prepare("SELECT s.number_of_lesson, us.number_rem_classes FROM user_subscriptions us JOIN subscriptions s ON us.subscription_id = s.id WHERE us.id = ?");
    $stmtSub->execute([$subscription_id]);
    $subData = $stmtSub->fetch(PDO::FETCH_ASSOC);

    $totalLessons = (int)$subData['number_of_lesson'];
    $remClasses = (int)$subData['number_rem_classes'];

    $nextLesson = (int)$lesson_number + 1;
    if ($nextLesson > $totalLessons) $nextLesson = null;

    echo json_encode([
        'success' => true,
        'nextLesson' => $nextLesson,
        'remClasses' => $remClasses
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка записи в базу']);
}
