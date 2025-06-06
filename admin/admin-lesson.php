<?php
session_start();
include "../app/db.php";
include "../components/head_admin.php";
include "../components/header-outh.php";

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit;
}



$query = $pdo->prepare("
    SELECT 
        us.id,
        u.name AS user_name,
        u.email,
        u.phone,
        s.name_sub AS subscription_name,
        c.name_course,
        t.name_type_schedule,
        us.number_rem_classes,
        s.number_of_lesson,
        us.is_passed
    FROM user_subscriptions us
    LEFT JOIN users u ON us.user_id = u.id_user
    LEFT JOIN subscriptions s ON us.subscription_id = s.id
    LEFT JOIN courses c ON us.course_id = c.id
    LEFT JOIN type_schedule t ON us.type_schedule_id = t.id
    ORDER BY us.id DESC
");

$query->execute();
$subscriptions = $query->fetchAll();
foreach ($subscriptions as &$sub) {
    $stmtLessons = $pdo->prepare("SELECT lesson_number FROM completed_lessons WHERE user_subscription_id = ? ORDER BY lesson_number");
    $stmtLessons->execute([$sub['id']]);
    $sub['completed_lessons'] = $stmtLessons->fetchAll(PDO::FETCH_COLUMN);

    // Определяем следующий номер занятия
    if (!empty($sub['completed_lessons'])) {
        $sub['next_lesson'] = max($sub['completed_lessons']) + 1;
    } else {
        $sub['next_lesson'] = 1;
    }

    // Проверяем, не превышает ли следующий номер общего количества
    if ($sub['next_lesson'] > $sub['number_of_lesson']) {
        $sub['next_lesson'] = null; // все занятия пройдены
    }
}

// Получаем уникальные типы курсов из данных
$courseTypes = [];
foreach ($subscriptions as $sub) {
    if (!empty($sub['name_type_schedule']) && !in_array($sub['name_type_schedule'], $courseTypes)) {
        $courseTypes[] = $sub['name_type_schedule'];
    }
}
// Обработка отметки о прохождении занятия
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_lesson'])) {
    $userSubscriptionId = $_POST['user_subscription_id'];

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
        // Проверяем, не было ли уже отмечено это занятие (на всякий случай)
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

            $_SESSION['message'] = "Занятие №$nextLesson успешно отмечено как пройденное";
        } else {
            $_SESSION['error'] = "Это занятие уже было отмечено ранее";
        }
    } else {
        $_SESSION['error'] = "Все занятия уже пройдены";
    }

    // header("Location: " . $_SERVER['PHP_SELF']);
    header("Location: http://" . $_SERVER['HTTP_HOST']  . "/admin/admin-lesson.php");
    exit;
}
?>
<!-- Остальной HTML код остается без изменений -->
<style>
    .container {
        margin: 20px auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 36px;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background: #f5f5f5;
    }

    .filter-btn {
        padding: 10px 15px;
        margin-right: 10px;
        border: none;
        border-radius: 5px;
        background-color: rgb(0, 0, 0);
        color: white;
        cursor: pointer;
        font-weight: bold;
    }

    .filter-btn:hover {
        background-color: rgb(0, 0, 0);
    }

    .mark-form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .mark-input {
        width: 50px;
        padding: 5px;
    }

    .mark-btn {
        padding: 5px 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .completed-lessons {
        font-size: 0.9em;
        color: #666;
    }

    .message {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
    }

    .success {
        background-color: #dff0d8;
        color: #3c763d;
    }

    .error {
        background-color: #f2dede;
        color: #a94442;
    }
</style>
<!-- HTML часть с изменениями в кнопке отметки -->

<section class="pricing-section">
    <div class="pricing-container">
        <img src="../uploads/фон.png" class="background-image" alt="Background image" />
        <div class="content-wrapper-card">
            <h1 class="pricing-title-ad-s">Купленные абонементы</h1>
            <div class="container">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message success"><?php echo $_SESSION['message'];
                                                    unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="message error"><?php echo $_SESSION['error'];
                                                unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div style="margin-bottom: 20px;">
                    <button onclick="filterByType('all')" class="filter-btn">Показать все</button>
                    <?php foreach ($courseTypes as $type): ?>
                        <button onclick="filterByType('<?php echo htmlspecialchars($type); ?>')" class="filter-btn">
                            <?php echo htmlspecialchars($type); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Имя пользователя</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Абонемент</th>
                            <th>Курс</th>
                            <th>Тип курса</th>
                            <th>Всего занятий</th>
                            <th>Осталось занятий</th>
                            <th>Пройденные занятия</th>
                            <th>Отметить занятие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $sub): ?>
                            <tr data-type="<?php echo htmlspecialchars($sub['name_type_schedule']); ?>">
                                <td><?php echo htmlspecialchars($sub['user_name'] ?? 'Не указано'); ?></td>
                                <td><?php echo htmlspecialchars($sub['email'] ?? 'Не указано'); ?></td>
                                <td><?php echo htmlspecialchars($sub['phone'] ?? 'Не указано'); ?></td>
                                <td><?php echo htmlspecialchars($sub['subscription_name'] ?? 'Не указано'); ?></td>
                                <td><?php echo htmlspecialchars($sub['name_course'] ?? 'Не указано'); ?></td>
                                <td><?php echo htmlspecialchars($sub['name_type_schedule'] ?? 'Не указано'); ?></td>
                                <td><?php echo htmlspecialchars($sub['number_of_lesson'] ?? 'Нет данных'); ?></td>
                                <td><?php echo htmlspecialchars($sub['number_rem_classes'] ?? 'Нет данных'); ?></td>
                                <td class="completed-lessons">
                                    <?php
                                    if (!empty($sub['completed_lessons'])) {
                                        echo implode(', ', $sub['completed_lessons']);
                                    } else {
                                        echo 'Нет пройденных';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (isset($sub['next_lesson'])): ?>
                                        <form class="mark-form" method="POST">
                                            <input type="hidden" name="user_subscription_id" value="<?php echo $sub['id']; ?>">
                                            <span style="margin-right: 10px;">Занятие №<?php echo $sub['next_lesson']; ?></span>
                                            <button type="submit" name="mark_lesson" class="mark-btn">Отметить</button>
                                        </form>
                                    <?php else: ?>
                                        Все занятия пройдены
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    function filterByType(type) {
        const rows = document.querySelectorAll("tbody tr");
        rows.forEach(row => {
            const rowType = row.getAttribute("data-type");
            if (type === "all" || rowType === type) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
</script>

<?php include "../components/footer.php"; ?>