<?php
session_start();
include "db.php";

// Проверка авторизации
if (!isset($_SESSION['id_user'])) {
    die("Ошибка: пользователь не авторизован.");
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subscription_id = $_POST['subscription_id'] ?? '';
    $course_id = $_POST['course_id'] ?? '';
    $type_id = $_POST['type_schedule_id'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $card_holder = $_POST['card_holder'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // Записываем данные в таблицу user_subscriptions
    $stmt = $pdo->prepare("INSERT INTO user_subscriptions 
(user_id, subscription_id, course_id, type_schedule_id, card_number, card_holder, expiry_date, cvv, start_date, end_date) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))");

    $result = $stmt->execute([$id_user, $subscription_id, $course_id, $type_id, $card_number, $card_holder, $expiry_date, $cvv]);

    if ($result) {
        echo "<div class='alert success'>
                <strong>✅ Успешно!</strong> Данные успешно записаны.
              </div>";
    } else {
        $error = $stmt->errorInfo();
        echo "<div class='alert error'>
                <strong>❌ Ошибка!</strong> Не удалось записать данные в базу: " . $error[2] . "
              </div>";
    }
}
