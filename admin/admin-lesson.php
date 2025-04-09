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
        t.name_type_schedule
    FROM user_subscriptions us
    LEFT JOIN users u ON us.user_id = u.id_user
    LEFT JOIN subscriptions s ON us.subscription_id = s.id
    LEFT JOIN courses c ON us.course_id = c.id
    LEFT JOIN type_schedule t ON us.type_schedule_id = t.id
    ORDER BY us.id DESC
");
$query->execute();
$subscriptions = $query->fetchAll();

// Получаем уникальные типы курсов из данных
$courseTypes = [];
foreach ($subscriptions as $sub) {
    if (!empty($sub['name_type_schedule']) && !in_array($sub['name_type_schedule'], $courseTypes)) {
        $courseTypes[] = $sub['name_type_schedule'];
    }
}
?>
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
</style>

<section class="pricing-section">
    <div class="pricing-container">
        <img src="../uploads/фон.png" class="background-image" alt="Background image" />
        <div class="content-wrapper-card">
            <h1 class="pricing-title-ad-s">Купленные абонементы</h1>
            <div class="container">
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