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

    // Получаем количество занятий из выбранного абонемента
    $lessonCountQuery = $pdo->prepare("SELECT number_of_lesson FROM subscriptions WHERE id = ?");
    $lessonCountQuery->execute([$subscription_id]);
    $lessonCount = $lessonCountQuery->fetch(PDO::FETCH_ASSOC);

    if (!$lessonCount) {
        die("Ошибка: не удалось получить количество занятий.");
    }

    $number_rem_classes = $lessonCount['number_of_lesson'];

    // Записываем данные в таблицу user_subscriptions, включая number_rem_classes
    $stmt = $pdo->prepare("INSERT INTO user_subscriptions 
        (user_id, subscription_id, course_id, type_schedule_id, card_number, card_holder, expiry_date, cvv, start_date, end_date, number_rem_classes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), ?)");

    $result = $stmt->execute([
        $id_user,
        $subscription_id,
        $course_id,
        $type_id,
        $card_number,
        $card_holder,
        $expiry_date,
        $cvv,
        $number_rem_classes
    ]);

    if ($result) {
        // Сообщение об успехе с JavaScript для popup и редиректа
        echo "
        <div id='popup' class='popup'>
            <div class='popup-content'>
                <strong>Оплата прошла успешно!</strong>
            </div>
        </div>
        <script>
            document.getElementById('popup').style.display = 'flex';
            setTimeout(function() {
                document.getElementById('popup').style.display = 'none';
                window.location.href = 'user_profile.php';
            }, 3000);
        </script>";
    } else {
        $error = $stmt->errorInfo();
        echo "<div class='alert error'>
                <strong>Оплата не прошла</strong> : " . $error[2] . "
              </div>";
    }
}
?>


<?php
include "components/head.php";
include "components/header.php";
?>
<style>
    .container {
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 8px;
        max-width: 600px;
        margin: 0 auto;
        margin-bottom: 50px;
        margin-top: 50px;
    }

    h2 {
        margin-top: 20px;
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
        background-color: rgb(0, 0, 0);
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
        background-color: rgb(0, 0, 0);
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

<h2>Оплата абонемента</h2>
<div class="container">
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
<?php

include "components/footer.php";
?>