<?php
session_start();
include "../../app/db.php";

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_subscription_id'])) {
    $userSubscriptionId = $_POST['user_subscription_id'];
    $response = ['success' => false, 'message' => '', 'data' => []];

    try {
        $pdo->beginTransaction();

        // Получаем следующий номер занятия для отметки
        $nextLessonQuery = $pdo->prepare("
            SELECT COALESCE(MAX(lesson_number), 0) + 1 AS next_lesson 
            FROM completed_lessons 
            WHERE user_subscription_id = ?
        ");
        $nextLessonQuery->execute([$userSubscriptionId]);
        $nextLesson = $nextLessonQuery->fetchColumn();

        // Проверяем, не превышает ли номер общего количества занятий
        $totalQuery = $pdo->prepare("
            SELECT s.number_of_lesson 
            FROM user_subscriptions us 
            JOIN subscriptions s ON us.subscription_id = s.id 
            WHERE us.id = ?
        ");
        $totalQuery->execute([$userSubscriptionId]);
        $totalLessons = $totalQuery->fetchColumn();

        if ($nextLesson <= $totalLessons) {
            // Проверяем, не было ли уже отмечено это занятие
            $checkQuery = $pdo->prepare("SELECT * FROM completed_lessons WHERE user_subscription_id = ? AND lesson_number = ?");
            $checkQuery->execute([$userSubscriptionId, $nextLesson]);

            if ($checkQuery->rowCount() === 0) {
                // Добавляем запись о пройденном занятии
                $insertQuery = $pdo->prepare("INSERT INTO completed_lessons (user_subscription_id, lesson_number) VALUES (?, ?)");
                $insertQuery->execute([$userSubscriptionId, $nextLesson]);

                // Обновляем количество оставшихся занятий
                $remaining = $totalLessons - $nextLesson;
                $updateQuery = $pdo->prepare("UPDATE user_subscriptions SET number_rem_classes = ? WHERE id = ?");
                $updateQuery->execute([$remaining, $userSubscriptionId]);

                // Если оставшихся занятий 0, отмечаем абонемент как пройденный
                if ($remaining == 0) {
                    $updatePassedQuery = $pdo->prepare("UPDATE user_subscriptions SET is_passed = 1 WHERE id = ?");
                    $updatePassedQuery->execute([$userSubscriptionId]);
                }

                // Получаем обновленные данные для ответа
                $stmtLessons = $pdo->prepare("SELECT lesson_number FROM completed_lessons WHERE user_subscription_id = ? ORDER BY lesson_number");
                $stmtLessons->execute([$userSubscriptionId]);
                $completedLessons = $stmtLessons->fetchAll(PDO::FETCH_COLUMN);

                $response = [
                    'success' => true,
                    'message' => "Занятие №$nextLesson успешно отмечено как пройденное",
                    'data' => [
                        'next_lesson' => ($nextLesson < $totalLessons) ? $nextLesson + 1 : null,
                        'remaining' => $remaining,
                        'completed_lessons' => $completedLessons
                    ]
                ];
            } else {
                $response['message'] = "Это занятие уже было отмечено ранее";
            }
        } else {
            $response['message'] = "Все занятия уже пройдены";
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $response['message'] = "Ошибка: " . $e->getMessage();
    }

    echo json_encode($response);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Неверный запрос']);
