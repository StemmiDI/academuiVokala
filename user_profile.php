<?php
session_start();
include "app/db.php";

// Проверка авторизации
if (!isset($_SESSION['id_user'])) {
  die("Ошибка: пользователь не авторизован.");
}

$id_user = $_SESSION['id_user'];

// Получаем данные пользователя
$query = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$query->execute([$id_user]);
$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  die("Ошибка: пользователь не найден.");
}

// Получаем список абонементов
$subscriptionsList = $pdo->prepare("SELECT * FROM subscriptions");
$subscriptionsList->execute();
$subscriptionsRow = $subscriptionsList->fetchAll(PDO::FETCH_ASSOC);

// Получаем список курсов
$courseList = $pdo->prepare("SELECT * FROM courses");
$courseList->execute();
$courses = $courseList->fetchAll(PDO::FETCH_ASSOC);

// Получаем список типов курсов
$typeList = $pdo->prepare("SELECT * FROM type_schedule");
$typeList->execute();
$types = $typeList->fetchAll(PDO::FETCH_ASSOC);

// Запрос на получение абонемента и курса
$subscriptionQuery = $pdo->prepare("
    SELECT 
        s.name_sub AS subscription_name, 
        c.name_course AS course_name
    FROM user_subscriptions us
    LEFT JOIN subscriptions s ON us.subscription_id = s.id
    LEFT JOIN courses c ON us.course_id = c.id
    WHERE us.user_id = ?
");

// ✅ Передаем параметр $id_user
$subscriptionQuery->execute([$id_user]);
$subscriptionData = $subscriptionQuery->fetch(PDO::FETCH_ASSOC);

// Безопасный вывод
$name = htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8');
$fullName = htmlspecialchars($row['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8');

// Данные абонемента и курса
$subscription_name = htmlspecialchars($subscriptionData['subscription_name'] ?? 'Нет данных', ENT_QUOTES, 'UTF-8');
$course_name = htmlspecialchars($subscriptionData['course_name'] ?? 'Нет данных', ENT_QUOTES, 'UTF-8');
$sql = "
    SELECT 
        s.day_of_week,
        s.time,
        s.created_at,
        c.name_course AS course_name,
        ts.name_type_schedule AS name_type_schedule,
        t.name_teacher AS teacher_name
    FROM 
        schedule s
    JOIN 
        courses c ON s.course_id = c.id
    JOIN 
        type_schedule ts ON s.type_schedule_id = ts.id
    JOIN 
        teachers t ON s.teacher_id = t.id
";

$stmt = $pdo->query($sql);
$schedules = $stmt->fetchAll();
?>

<?php
include "components/head_user.php";
include "components/header_user.php";
?>



<div class="dashboard">
  <div class="dashboard-container">
    <img src="img/user-prof-back.png" class="background-image" alt="Background" />

    <main class="main-content">


      <div class="content-wrapper user-card" space="35">
        <div class="content-grid">
          <section class="profile-section">
            <div class="profile-content">
              <div class="profile-info-container" space="50">
                <div class="profile-info-grid">
                  <div class="profile-card">
                    <div class="profile-card-content">
                      <img src="img/user-prof-foto.png" class="profile-image" alt="Profile" />
                      <h2 class="profile-name">
                        <?= $name ?> <br />
                        <?= $fullName ?>
                      </h2>
                    </div>
                  </div>

                  <div class="contact-info">
                    <div class="contact-details">
                      <h3 class="contact-label">Электронная почта</h3>
                      <p class="contact-value"><?= $email ?></p>

                    </div>
                  </div>

                  <div class="skill-info">
                    <div class="skill-details">
                      <h3 class="skill-label">Номер телефона</h3>

                      <!-- Отображаемый номер телефона -->
                      <p class="skill-value" id="phone-text"><?= $phone  ?></p>

                      <!-- Поле ввода и кнопка "Сохранить" (скрыты по умолчанию) -->
                      <input type="text" id="phone-input" value="<?= $phone ?>" style="display: none;">
                      <button id="save-btn" style="display: none;" onclick="savePhone()">Сохранить</button>

                      <!-- Кнопка "Редактировать" -->
                      <button id="edit-btn" onclick="editPhone()">Редактировать</button>
                    </div>
                    <script src="edit.js"></script>
                  </div>
                </div>
              </div>

              <div class="schedule-container">
                <table class="schedule-table" cellspacing="0" cellpadding="5">
                  <thead class="weekdays-table">
                    <tr>
                      <th>День недели</th>
                      <th>Время</th>
                      <th>Курс</th>
                      <th>Тип расписания</th>
                      <th>Учитель</th>

                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['name_type_schedule']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['teacher_name']); ?></td>

                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

          <section class="calendar-section">
            <div class="calendar-container">
              <div class="calendar-header">
              </div>
              <div id="calendar">
                <?php
                include "my-calen.php"
                ?>
              </div>


              <h3 class="subscription-title">Ваш курс и абонимент</h3>

              <div class="subscription-info">
                <div class="subscription-type">
                  <h4 class="subscription-label">Абонемент</h4>
                  <p class="subscription-value"><?= $subscription_name ?></p>
                </div>

                <div class="course-info">
                  <h4 class="course-label">Курс</h4>
                  <p class="course-value"><?= $course_name ?></p>
                </div>
              </div>

              <div class="subscription-options">
                <h3 class="options-title">Абонименты</h3>

                <div class="options-grid">
                  <div class="options-names">
                    <?php foreach ($subscriptionsRow as $subscription): ?>
                      <p class="option-name"><?= htmlspecialchars($subscription['name_sub']) ?></p>
                    <?php endforeach; ?>
                  </div>

                  <div class="options-prices">
                    <?php foreach ($subscriptionsRow as $subscription): ?>
                      <p class="option-price"><span class="price-value"><?= htmlspecialchars($subscription['price']) ?></span> руб.</p>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Кнопка "Оплатить абонемент" -->
                <a href="payment.php" class="payment-button">Оплатить абонемент</a>


                <!-- Стили -->
                <style>
                  /* Попап окно */
                  .popup {
                    display: none;
                    position: fixed;
                    left: 0;
                    top: 0;
                    color: black;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    justify-content: center;
                    align-items: center;
                  }

                  .popup-content {
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    width: 300px;
                    text-align: center;
                    position: relative;
                  }

                  .close-btn {
                    position: absolute;
                    top: 10px;
                    right: 15px;
                    font-size: 20px;
                    cursor: pointer;
                  }

                  label {
                    display: block;
                    margin-top: 10px;
                    font-weight: bold;
                  }

                  select {
                    width: 100%;
                    padding: 5px;
                    margin: 5px 0;
                  }

                  .price {
                    font-size: 18px;
                    font-weight: bold;
                    margin-top: 10px;
                  }

                  .submit-btn {
                    background: #007bff;
                    color: white;
                    padding: 10px;
                    border: none;
                    width: 100%;
                    margin-top: 10px;
                    cursor: pointer;
                    border-radius: 5px;
                  }

                  .submit-btn:hover {
                    background: #0056b3;
                  }
                </style>
                <script src="user.js"></script>

              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</div>
<?php
include "components/footer.php" ?>