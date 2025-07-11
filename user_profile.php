<?php
session_start();
include "app/db.php";

// Проверка последнего отзыва пользователя
$canLeaveReview = true;

$reviewCheck = $pdo->prepare("SELECT created_at FROM reviews WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$reviewCheck->execute([$_SESSION['id_user']]);
$lastReview = $reviewCheck->fetch(PDO::FETCH_ASSOC);

if ($lastReview) {
  $lastDate = new DateTime($lastReview['created_at']);
  $now = new DateTime();
  $interval = $lastDate->diff($now);

  if ($interval->days < 7) {
    $canLeaveReview = false;
    $remainingDays = 7 - $interval->days;
  }
}

$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $review_text = trim($_POST['review_text'] ?? '');
  $rating = (int) ($_POST['rating'] ?? 0);
  $user_id = $_SESSION['id_user'];

  if ($review_text && $rating >= 1 && $rating <= 5) {
    $stmt = $pdo->prepare("INSERT INTO reviews (review_text, user_id, rating, created_at, status) VALUES (?, ?, ?, NOW(), 'новое')");
    $stmt->execute([$review_text, $user_id, $rating]);

    $successMsg = "Спасибо за ваш отзыв.";
  } else {
    $errorMsg = "Пожалуйста, заполните все поля корректно.";
  }
}

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

// Получаем последний активный абонемент (где is_passed != 1)
$subscriptionInfo = $pdo->prepare("
    SELECT 
        us.start_date,
        us.end_date,
        us.number_rem_classes,
        us.is_passed,
        s.number_of_lesson,
        s.name_sub AS subscription_name,
        c.name_course AS course_name
    FROM user_subscriptions us
    LEFT JOIN subscriptions s ON us.subscription_id = s.id
    LEFT JOIN courses c ON us.course_id = c.id
    WHERE us.user_id = ? AND us.is_passed != 1
    ORDER BY us.start_date DESC
    LIMIT 1
");
$subscriptionInfo->execute([$id_user]);
$subscriptionData = $subscriptionInfo->fetch(PDO::FETCH_ASSOC);

$showSubscriptionBlock = true; // По умолчанию показываем блок

if ($subscriptionData) {
  $lastPayment = new DateTime($subscriptionData['start_date']);
  $now = new DateTime();
  $interval = $lastPayment->diff($now);

  // Условия для скрытия блока:
  // 1. Если с момента начала прошло меньше 30 дней
  // 2. Если остались занятия (не равно 0)
  // 3. Если дата окончания не наступила
  if (
    $interval->days < 30 ||
    (isset($subscriptionData['number_rem_classes']) &&
      $subscriptionData['number_rem_classes'] > 0) ||
    ($subscriptionData['end_date'] &&
      (new DateTime($subscriptionData['end_date'])) > $now)
  ) {
    $showSubscriptionBlock = false;
  }

  // Явное условие для показа блока при 0 оставшихся занятиях
  if (
    isset($subscriptionData['number_rem_classes']) &&
    $subscriptionData['number_rem_classes'] === 0
  ) {
    $showSubscriptionBlock = true;
  }
}

// Всегда показывать если нет данных об абонементе
if (!$subscriptionData) {
  $showSubscriptionBlock = true;
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

// Получаем course_id и type_schedule_id из последней покупки пользователя
$courseTypeQuery = $pdo->prepare("
    SELECT 
        us.course_id,
        us.type_schedule_id
    FROM user_subscriptions us
    WHERE us.user_id = ? AND us.is_passed != 1
    ORDER BY us.start_date DESC
    LIMIT 1
");
$courseTypeQuery->execute([$id_user]);
$courseTypeData = $courseTypeQuery->fetch(PDO::FETCH_ASSOC);

if ($courseTypeData) {
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
        WHERE 
            s.course_id = ? AND s.type_schedule_id = ?
        ORDER BY 
            FIELD(s.day_of_week, 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'),
            s.time
    ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$courseTypeData['course_id'], $courseTypeData['type_schedule_id']]);
  $schedules = $stmt->fetchAll();
} else {
  $schedules = [];
}

// Безопасный вывод
$name = htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8');
$fullName = htmlspecialchars($row['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8');

// Данные абонемента и курса
$subscription_name = htmlspecialchars($subscriptionData['subscription_name'] ?? 'Нет данных', ENT_QUOTES, 'UTF-8');
$course_name = htmlspecialchars($subscriptionData['course_name'] ?? 'Нет данных', ENT_QUOTES, 'UTF-8');
$number_of_lesson = htmlspecialchars($subscriptionData['number_of_lesson'] ?? 'Нет данных', ENT_QUOTES, 'UTF-8');
$number_rem_classes = htmlspecialchars($subscriptionData['number_rem_classes'] ?? 'Нет данных', ENT_QUOTES, 'UTF-8');
$start_date = isset($subscriptionData['start_date']) ? htmlspecialchars($subscriptionData['start_date']) : 'Нет данных';
$end_date = isset($subscriptionData['end_date']) ? htmlspecialchars($subscriptionData['end_date']) : 'Нет данных';
?>
<?php
include "components/head_user.php";
include "components/header_user.php";
?>
<style>
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

  #message-container {
    position: fixed;
    right: 15px;
    top: 20px;
    z-index: 9;
  }
</style>
<div id="message-container"></div>
<div class="dashboard">
  <div class="dashboard-container">
    <img src="assets/img/user-prof-back.png" class="background-image" alt="Background" />
    <main class="main-content">
      <div class="content-wrapper user-card" space="35">
        <div class="content-grid">
          <section class="profile-section">
            <div class="profile-content">
              <div class="profile-info-container" space="50">
                <div class="profile-info-grid">
                  <div class="profile-card">
                    <div class="profile-card-content">
                      <img src="assets/img/user-prof-foto.png" class="profile-image" alt="Profile" />
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
                      <button class="addd-btn" id="save-btn" style="display: none;" onclick="savePhone()">Сохранить</button>

                      <!-- Кнопка "Редактировать" -->
                      <button class="edit" id="edit-btn" onclick="editPhone()">Редактировать</button>
                    </div>
                    <script src="assets/js/edit.js"></script>
                  </div>
                </div>
              </div>
              <?php if (empty($schedules)): ?>
                <div class="schedule-container no-schedule">
                  <p>Нет расписания.</p>
                </div>
              <?php else: ?>
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
                          <td><?php echo htmlspecialchars(substr($schedule['time'], 0, 5)); ?></td>
                          <td><?php echo htmlspecialchars($schedule['course_name']); ?></td>
                          <td><?php echo htmlspecialchars($schedule['name_type_schedule']); ?></td>
                          <td><?php echo htmlspecialchars($schedule['teacher_name']); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>

            </div>
          </section>
          <section class="calendar-section">
            <div class="calendar-container">
              <div class="calendar-header">
              </div>
              <div id="calendar">
                <div class="calendar-calendar">
                  <header class="header-calendar-calendar">
                    <button class="button-calendar-calendar" id="prevMonth">←</button>
                    <span class="span-calendar-calendar" id="monthName"></span>
                    <button class="button-calendar-calendar" id="nextMonth">→</button>
                  </header>
                  <table class="table-calendar-calendar" id="calendarTable">
                    <thead class="thead-calendar-calendar">
                      <tr class="tr-calendar-calendar">
                        <th class="th-calendar-calendar">Пн</th>
                        <th class="th-calendar-calendar">Вт</th>
                        <th class="th-calendar-calendar">Ср</th>
                        <th class="th-calendar-calendar">Чт</th>
                        <th class="th-calendar-calendar">Пт</th>
                        <th class="th-calendar-calendar">Сб</th>
                        <th class="th-calendar-calendar">Вс</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>

                <script src="assets/js/calendar.js"></script>
              </div>
              <?php if ($subscriptionData): ?>
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
                <div class="subscription-info">
                  <div class="course-info">
                    <h4 class="course-label">Дата начала</h4>
                    <p class="course-value"><?= $start_date ?></p>
                  </div>
                  <div class="course-info">
                    <h4 class="course-label">Дата конца</h4>
                    <p class="course-value"><?= $end_date ?></p>
                  </div>
                </div>

                <div class="subscription-info">
                  <div class="course-info">
                    <h4 class="course-label">Общее количество занятий</h4>
                    <p class="course-value"><?= $number_of_lesson ?></p>
                  </div>
                  <div class="course-info">
                    <h4 class="course-label">Оставшееся количество занятий</h4>
                    <p class="course-value"><?= $number_rem_classes ?></p>
                  </div>
                </div>
              <?php endif; ?>


              <?php if ($showSubscriptionBlock): ?>
                <div class="subscription-options">
                  <h3 class="options-title">Абонименты</h3>

                  <div class="options-grid">
                    <div class="options-names">
                      <?php foreach ($subscriptionsRow as $subscription): ?>
                        <div class="option-name"><?= htmlspecialchars($subscription['name_sub']) ?>
                          <p class="option-price">
                            <span class="price-value"><?= htmlspecialchars($subscription['price']) ?></span> руб.
                          </p>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>

                  <!-- Кнопка, ведущая на payment.php -->
                  <form action="payment.php" method="get">
                    <button type="submit" class="payment-button">Оплатить абонемент</button>
                  </form>
                </div>
              <?php endif; ?>
              <!-- Блок с формой отзыва -->
              <div id="review-section" style="margin-top: 40px;">
                <?php if (isset($_GET['review']) && $_GET['review'] === 'success'): ?>
                  <h3>Оставить отзыв о школе</h3>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                  <div style="color: red; font-weight: bold; margin-bottom: 10px;">
                    <?= htmlspecialchars($error) ?>
                  </div>
                <?php endif; ?>

                <?php if ($canLeaveReview): ?>
                  <?php if (isset($_GET['review']) && $_GET['review'] === 'success'): ?>
                    <!-- <div id="review-success" class="popup-content" style="color: green; font-weight: bold; margin-bottom: 15px;">
                      Спасибо за ваш отзыв! ❤️
                    </div> -->
                  <?php endif; ?>

                  <form id="review-form" method="POST" action="user_profile.php">
                    <label for="review_text">Ваш отзыв:</label>
                    <textarea name="review_text" id="review_text" rows="4" required placeholder="Поделитесь впечатлениями..." style="width: 100%; padding: 10px;"></textarea>

                    <label for="rating">Оценка (1–5):</label>
                    <select name="rating" id="rating" required style="padding: 10px; width: 100px;">
                      <option value="">Выберите</option>
                      <option value="1">1 - Плохо</option>
                      <option value="2">2 - Ниже среднего</option>
                      <option value="3">3 - Нормально</option>
                      <option value="4">4 - Хорошо</option>
                      <option value="5">5 - Отлично</option>
                    </select>

                    <button type="submit" style="margin-top: 15px; padding: 10px 20px;">Отправить отзыв</button>
                  </form>
                <?php endif; ?>
                <!-- <p class="send-reviews" style="color: gray; font-style: italic; margin-top: 10px; display:none">
                  Спасибо за ваш отзыв
                </p> -->

              </div>
            </div>
        </div>
        </section>
      </div>
  </div>
  </main>
</div>
</div>
<script>
  document.getElementById('review-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем стандартную отправку формы

    const form = event.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: form.method,
        body: formData,
      })
      .then(response => response.text()) // можно заменить на response.json(), если сервер возвращает JSON
      .then(data => {
        // Показываем сообщение об успешной отправке
        // alert('Спасибо за ваш отзыв!');
        const isForm = document.querySelector('#review-form')
        isForm.remove()
        // document.querySelector('.send-reviews').style.display = 'block';
        showMessage('success', 'Спасибо за ваш отзыв!');
        form.reset(); // очищаем форму
      })
      .catch(error => {
        console.error('Ошибка при отправке формы:', error);
        alert('Произошла ошибка. Попробуйте позже.');
      });
  });

  function showMessage(type, message) {
    const container = document.getElementById('message-container');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    container.appendChild(messageDiv);

    // Удаляем сообщение через 5 секунд
    setTimeout(() => {
      messageDiv.remove();
    }, 5000);
  }
</script>

<?php
include "components/footer.php" ?>