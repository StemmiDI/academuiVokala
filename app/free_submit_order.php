<?php
include 'db.php';
$message = ''; // Initialize message variable

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the form
    $fullName = $_POST['fullName'];
    $phone = $_POST['phone'];

    // Sanitize inputs to prevent SQL injection and other security issues
    $fullName = htmlspecialchars(trim($fullName));
    $phone = htmlspecialchars(trim($phone));


    // if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
    //   $message = "Неверный формат номера телефона";
    // } else {

    $sql = "INSERT INTO free_lesson (free_lesson_name, free_lesson_email) VALUES (:fullName, :phone)";
    $stmt = $pdo->prepare($sql);

    // Bind the parameters to the SQL query
    $stmt->bindParam(':fullName', $fullName);
    $stmt->bindParam(':phone', $phone);

    // Execute the query
    if ($stmt->execute()) {
        $message = "Ваша заявка отправлена. Ожидайте подтверждения примерно 10 минут";
    } else {
        $message = "Ошибка при отправке. Пожалуйста, повторите снова";
    }
    echo json_encode(['message' => $message]);
    exit;
}
// }
