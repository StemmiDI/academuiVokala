<?php
session_start();
include "app/db.php";

// Проверка авторизации
if (!isset($_SESSION['id_user'])) {
    die("Ошибка: пользователь не авторизован.");
}

$id_user = $_SESSION['id_user'];

// Получаем список абонементов
$subscriptionsList = $pdo->prepare("SELECT * FROM subscriptions");
$subscriptionsList->execute();
$subscriptions = $subscriptionsList->fetchAll(PDO::FETCH_ASSOC);

// Получаем список курсов
$courseList = $pdo->prepare("SELECT * FROM courses");
$courseList->execute();
$courses = $courseList->fetchAll(PDO::FETCH_ASSOC);

// Получаем список типов курсов
$typeList = $pdo->prepare("SELECT * FROM type_schedule");
$typeList->execute();
$types = $typeList->fetchAll(PDO::FETCH_ASSOC);

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
        // Сообщение об успехе с JavaScript для popup и редиректа
        echo "
        <div id='popup' class='popup'>
            <div class='popup-content'>
                <strong> Оплата прошла успешно!</strong>
            </div>
        </div>
        <script>
            // Показываем всплывающее сообщение
            document.getElementById('popup').style.display = 'flex';

            // Через 5 секунд скрываем попап и выполняем редирект
            setTimeout(function() {
                document.getElementById('popup').style.display = 'none';
                window.location.href = 'user_profile.php'; // Перенаправление на user_profile.php
            }, 3000); // 3 секунд
        </script>";
    } else {
        $error = $stmt->errorInfo();
        echo "<div class='alert error'>
                <strong>Оплата не прошла</strong> : " . $error[2] . "
              </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оплата абонемента</title>
    <link rel="stylesheet" href="css/pay.css">
    <style>
        /* Основные стили для страницы */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            padding: 20px;
            margin: 0;
        }

        .container {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        select,
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Стили для попапа */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            font-size: 18px;
            color: #4CAF50;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .popup-content strong {
            font-size: 22px;
            display: block;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Оплата абонемента</h2>
        <form action="" method="post">
            <label for="subscription">Выберите абонемент:</label>
            <select id="subscription" name="subscription_id" required>
                <?php foreach ($subscriptions as $subscription): ?>
                    <option value="<?= $subscription['id'] ?>">
                        <?= htmlspecialchars($subscription['name_sub']) ?> - <?= htmlspecialchars($subscription['price']) ?> руб.
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="course">Выберите курс:</label>
            <select id="course" name="course_id" required>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>">
                        <?= htmlspecialchars($course['name_course']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="type">Тип курса:</label>
            <select id="type" name="type_schedule_id" required>
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type['id'] ?>">
                        <?= htmlspecialchars($type['name_type_schedule']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="card-number">Номер карты:</label>
            <input type="text" id="card-number" name="card_number" placeholder="0000000000000000" maxlength="19" required>

            <label for="card-holder">Имя держателя карты:</label>
            <input type="text" id="card-holder" name="card_holder" placeholder="ИМЯ ФАМИЛИЯ" required>

            <label for="expiry-date">Срок действия:</label>
            <input type="text" id="expiry-date" name="expiry_date" placeholder="MMYY" maxlength="5" required>

            <label for="cvv">CVV-код:</label>
            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>

            <button type="submit">Оплатить</button>
        </form>
    </div>
</body>

</html>