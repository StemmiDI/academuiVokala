<?php
include 'app/db.php';
include "components/head.php";
include "components/header.php";
?>
<main class="hero-section">
  <section class="hero-content">
    <h1 class="hero-title">Раскрой Свой Голос С Нами</h1>
    <p class="hero-description">Профессиональная Школа Вокала С Индивидуальным Подходом К Каждому Ученику.</p>
    <a href="#" class="cta-button">Записаться на пробный урок</a>
  </section>
  <section class="hero-image-container">
    <img src="img/img-index.jpg" alt="Musicians performing" class="hero-image" />
  </section>
</main>
</div>
<div id="about">

  <section class="features-section">
    <img
      src="img/back1.png"
      alt=""
      class="features-bg" />
    <div class="features-content">
      <h2 class="features-title">Почему стоит выбрать нас?</h2>
      <div class="features-grid">
        <article class="feature-card">
          <img
            src="img/img-cursu.png"
            alt="Иконка профессиональных преподавателей"
            class="feature-icon" />
          <h3 class="feature-title">Профессиональные преподаватели</h3>
          <p class="feature-description">
            Наши опытные преподаватели настоящие энтузиасты, готовые делиться знаниями и опытом.
          </p>
        </article>

        <article class="feature-card">
          <img
            src="img/img-cursu.png"
            alt="Иконка индивидуального подхода"
            class="feature-icon" />
          <h3 class="feature-title">Индивидуальный подход</h3>
          <p class="feature-description">
            Каждый ученик уникален и мы предлагаем персональные занятия, которые помогут достичь целей.
          </p>
        </article>

        <article class="feature-card">
          <img
            src="img/img-cursu.png"
            alt="Иконка музыкальных стилей"
            class="feature-icon" />
          <h3 class="feature-title">Разнообразие стилей</h3>
          <p class="feature-description">
            Вы сможете изучать различные музыкальные стили – от джаза до классики и рок-музыки.
          </p>
        </article>
      </div>
    </div>
  </section>
</div>
<div id="courses">
  <?php

  // Запрос курсов
  $query = "SELECT name_course, description, icon FROM courses";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $courses = $stmt->fetchAll();
  ?>
  <div id="courses">
    <section class="vocal-section" aria-labelledby="vocal-courses-title">
      <div class="vocal-container">
        <img src="img/back3.png" alt="" class="background-image" role="presentation" />
        <div class="content-wrapper">
          <h2 id="vocal-courses-title" class="section-title">Курсы вокала</h2>
          <p class="section-description">Погрузитесь в мир музыки и откройте свой голос с нашими курсами вокала!</p>

          <div class="courses-grid">
            <?php foreach ($courses as $course): ?>
              <article class="course-card">
                <img src="uploads/<?= htmlspecialchars($course['icon']) ?>" alt="Иконка <?= htmlspecialchars($course['name_course']) ?>" class="course-icon" />
                <div class="course-content">
                  <h3 class="course-title"><?= htmlspecialchars($course['name_course']) ?></h3>
                  <p class="course-description"><?= htmlspecialchars($course['description']) ?></p>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

        </div>
      </div>
    </section>
  </div>
</div>
<div id="ped">
  <?php

  // Запрашиваем данные преподавателей и их курсов
  $query = "
    SELECT teachers.id, teachers.name_teacher, teachers.photo, courses.name_course
    FROM teachers
    JOIN courses ON teachers.course_id = courses.id
";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $teachers = $stmt->fetchAll();
  ?>

  <section class="teachers-section">
    <div class="teachers-container">
      <img loading="lazy" src="img/back4.png" class="teachers-background-image" alt="" />
      <h2 class="teachers-section-title">Лучшие педагоги по вокалу</h2>
      <p class="teachers-section-description">
        Поставьте технику с помощью наших методик и начните петь, как наши профессионалы!
      </p>

      <div class="teachers-grid">

        <?php foreach ($teachers as $teacher): ?>
          <div class="teacher-column">
            <article class="teacher-card">
              <img loading="lazy" src="uploads/<?= htmlspecialchars($teacher['photo']) ?>" class="teacher-image" alt="Фото преподавателя <?= htmlspecialchars($teacher['name_teacher']) ?>" />

              <h3 class="teacher-name"><?= htmlspecialchars($teacher['name_teacher']) ?></h3>
              <p class="teacher-position">Преподаватель курса <?= htmlspecialchars($teacher['name_course']) ?></p>
              <!-- <button class="details-button" tabindex="0">Подробнее</button> -->
            </article>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </section>
</div>
<div id="price">
  <?php


  // Запрашиваем данные о подписках
  $query = "SELECT id, name_sub, level, number_of_lesson, price FROM subscriptions";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $subscriptions = $stmt->fetchAll();
  ?>

  <section class="pricing-section">
    <div class="pricing-container">
      <img loading="lazy" src="img/price-back.png" class="pricing-background" alt="" aria-hidden="true" />
      <div class="pricing-content">
        <h2 class="pricing-title">Цены на абонементы</h2>
        <div class="pricing-cards">
          <?php foreach ($subscriptions as $subscription): ?>
            <article class="pricing-card">
              <div class="card-wrapper">
                <div class="card-content">
                  <h3 class="card-title"><?= htmlspecialchars($subscription['name_sub']) ?></h3>
                  <div class="price-text">
                    <span class="price-prefix">от</span>
                    <span class="price-amount"><?= htmlspecialchars($subscription['price']) ?></span>
                    <span class="price-suffix">руб.</span>
                  </div>
                  <p class="card-feature card-feature-first"> <?= htmlspecialchars($subscription['level']) ?></p>
                  <p class="card-feature">Количество занятий: <?= htmlspecialchars($subscription['number_of_lesson']) ?></p>

                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<div id="reviews">
  <?php

  // Получаем последние 4 отзыва с рейтингом
  $query = "
    SELECT r.review_text, r.created_at, r.rating, u.name, u.full_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id_user
    WHERE r.status = 'опубликованное'
    ORDER BY r.created_at DESC
    LIMIT 4
  ";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $reviews = $stmt->fetchAll();
  ?>

  <section class="testimonials-section" aria-labelledby="testimonials-title">
    <div class="testimonials-container">
      <h2 id="testimonials-title" class="section-title-review">Что говорят наши ученики?</h2>

      <div class="testimonials-grid">
        <?php foreach ($reviews as $review): ?>
          <article class="testimonial-card">
            <div class="testimonial-content">
              <div class="rating-stars" aria-label="<?= $review['rating'] ?> out of 5 stars rating">
                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                  <img src="img/star1.png" alt="Звезда" class="star-icon" role="presentation" />
                <?php endfor; ?>
              </div>
              <p class="reviewer-name"><?= htmlspecialchars($review['name'] . ' ' . $review['full_name']) ?></p>
              <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
              <p class="review-date"><?= date("d.m.Y", strtotime($review['created_at'])) ?></p>
              <style>
                .review-date {
                  color: #A0A0A0;
                  /* Светло-серый */
                  font-weight: 500;
                  /* Чуть-чуть пожирнее */
                  font-size: 14px;
                  /* Можно подкорректировать размер */
                  margin-top: 5px;
                  /* Небольшой отступ сверху */
                }
              </style>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</div>

<div id="sign_up_free_class">

  <section class="signup-container">
    <?php

    $message = ''; // Initialize message variable

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Get the values from the form
      $fullName = $_POST['fullName'];
      $email = $_POST['email'];

      // Sanitize inputs to prevent SQL injection and other security issues
      $fullName = htmlspecialchars(trim($fullName));
      $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

      // Validate email format
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
      } else {
        // Prepare SQL query to insert data into the database
        $sql = "INSERT INTO free_lesson (free_lesson_name, free_lesson_email) VALUES (:fullName, :email)";
        $stmt = $pdo->prepare($sql);

        // Bind the parameters to the SQL query
        $stmt->bindParam(':fullName', $fullName);
        $stmt->bindParam(':email', $email);

        // Execute the query
        if ($stmt->execute()) {
          $message = "Ваша заявка отправлена. Ожидайте подтверждения примерно 10 минут";
        } else {
          $message = "Ошибка при отправке. Пожалуйста, повторите снова";
        }
      }
    }
    ?>


    <div class="signup-wrapper">
      <img
        src="img/free_class.png"
        alt=""
        class="background-image"
        loading="lazy" />
      <div class="form-container">
        <h1 class="signup-title">Запишитесь на первое занятие бесплатно</h1>
        <p class="signup-description">
          Приходите на занятие, чтобы узнать положение вашего голоса, научиться упражнениям дыхания и дать волю вашим
          эмоциям посредством вокала (занятие длится 1 час).
        </p>
        <form class="signup-form" method="POST" action="index.php">
          <label for="fullName" class="input-label">Имя</label>
          <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Иван Иванов" required />

          <label for="email" class="email-label">Почта</label>
          <input type="email" id="email" name="email" class="form-input" placeholder="business@mail.com" required />

          <div class="privacy-container">
            <input type="checkbox" id="privacy" name="privacy" class="visually-hidden" required />
            <label for="privacy">Я соглашаюсь на обработку персональных данных</label>
          </div>

          <button type="submit" class="submit-button">Записаться на пробное занятие</button>
        </form>
        <script>
          // Function to show popup
          function showPopup(message) {
            const popup = document.createElement('div');
            popup.classList.add('popup');
            popup.innerHTML = `
      <div class="popup-message">
        <p>${message}</p>
        <button class="popup-close">Закрыть</button>
      </div>
    `;
            document.body.appendChild(popup);

            // Close the popup when the close button is clicked
            const closeBtn = popup.querySelector('.popup-close');
            closeBtn.addEventListener('click', () => {
              popup.remove();
            });
          }

          // Check if there is a message from PHP and show the popup
          <?php if ($message): ?>
            showPopup("<?php echo $message; ?>");
          <?php endif; ?>
        </script>
        <style>
          /* Popup styles */
          .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
          }

          .popup-message {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 80%;
            max-width: 400px;
          }

          .popup-message p {
            font-size: 16px;
            margin-bottom: 20px;
          }

          .popup-close {
            background-color: rgb(0, 0, 0);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
          }

          .popup-close:hover {
            background-color: rgb(0, 0, 0);
          }
        </style>
      </div>
    </div>
  </section>
</div>

<div id="contact">

  <section class="contacts">
    <div class="contacts-content">
      <h2 class="contacts-title">Контакты</h2>
      <div class="contacts-grid">
        <img
          src="img/contact.jpg"
          alt="Карта расположения школы"
          class="contacts-map" />
        <div class="contacts-info">
          <h3 class="contacts-heading">Адрес</h3>
          <p class="contacts-text">г. Челябинск, ул. Елькина, д. 59</p>

          <h3 class="contacts-heading">Телефон</h3>
          <p class="contacts-text">+7 (999) 234 45 32</p>

          <h3 class="contacts-heading">Почта</h3>
          <p class="contacts-text">info@academy-vocal.ru</p>

          <h3 class="contacts-heading">Режим работы</h3>
          <p class="contacts-text">Пн-Пт: 10:00-21:00 <br> Сб-Вс: 11:00-18:00</p>
        </div>
      </div>
    </div>
  </section>
</div>
<?php
include "components/footer.php" ?>