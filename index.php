<?php
session_start();
include 'app/db.php';
include "components/head.php";
include "components/header_user.php";
?>
<main class="hero-section">
  <section class="hero-content">
    <h1 class="hero-title">Раскрой Свой Голос С Нами</h1>
    <p class="hero-description">Профессиональная Школа Вокала С Индивидуальным Подходом К Каждому Ученику.</p>
    <a href="#sign_up_free_class" class="cta-button">Записаться на пробный урок</a>
  </section>
  <section class="hero-image-container">
    <img src="assets/img/img-index.jpg" alt="Musicians performing" class="hero-image" />
  </section>
</main>
</div>
<div id="about">

  <section class="features-section">
    <img
      src="assets/img/back1.png"
      alt=""
      class="features-bg" />
    <div class="features-content">
      <h2 class="features-title">Почему стоит выбрать нас?</h2>
      <div class="features-grid">
        <article class="feature-card">
          <img
            src="assets/img/free-icon-teacher.png"
            alt="Иконка профессиональных преподавателей"
            class="feature-icon" />
          <h3 class="feature-title">Профессиональные преподаватели</h3>
          <p class="feature-description">
            Наши опытные преподаватели настоящие энтузиасты, готовые делиться знаниями и опытом.
          </p>
        </article>

        <article class="feature-card">
          <img
            src="assets/img/free-icon-user.png"
            alt="Иконка индивидуального подхода"
            class="feature-icon" />
          <h3 class="feature-title">Индивидуальный подход</h3>
          <p class="feature-description">
            Каждый ученик уникален и мы предлагаем персональные занятия, которые помогут достичь целей.
          </p>
        </article>

        <article class="feature-card">
          <img
            src="assets/img/free-icon-diversity.png"
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
        <img src="assets/img/back3.png" alt="" class="background-image" role="presentation" />
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
  SELECT teachers.id, teachers.name_teacher, teachers.photo, teachers.description, teachers.phone_number, teachers.email, courses.name_course
  FROM teachers
  JOIN courses ON teachers.course_id = courses.id
";

  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $teachers = $stmt->fetchAll();
  ?>

  <section class="teachers-section">
    <div class="teachers-container">
      <img loading="lazy" src="assets/img/back4.png" class="teachers-background-image" alt="" />
      <h2 class="teachers-section-title">Лучшие педагоги по вокалу</h2>
      <p class="teachers-section-description">
        Поставьте технику с помощью наших методик и начните петь, как наши профессионалы!
      </p>

      <div class="teachers-grid">

        <?php foreach ($teachers as $teacher): ?>
          <div class="teacher-column">
            <article class="teacher-card" onclick="togglePopup(<?= $teacher['id'] ?>)">
              <img loading="lazy" src="uploads/<?= htmlspecialchars($teacher['photo']) ?>" class="teacher-image" alt="Фото преподавателя <?= htmlspecialchars($teacher['name_teacher']) ?>" />
              <h3 class="teacher-name"><?= htmlspecialchars($teacher['name_teacher']) ?></h3>
              <p class="teacher-position">Преподаватель курса <?= htmlspecialchars($teacher['name_course']) ?></p>
            </article>
          </div>
        <?php endforeach; ?>

      </div>

      <!-- Popup окна (отдельно, чтобы не ломали сетку) -->
      <?php foreach ($teachers as $teacher): ?>
        <div class="teacher-popup" id="popup-<?= $teacher['id'] ?>">
          <div class="popup-content">
            <span class="close-btn" onclick="togglePopup(<?= $teacher['id'] ?>)">&times;</span>
            <h3><?= htmlspecialchars($teacher['name_teacher']) ?></h3>
            <p><strong>Курс:</strong> <?= htmlspecialchars($teacher['name_course']) ?></p>
            <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($teacher['description'])) ?></p>
            <p><strong>Телефон:</strong> <?= htmlspecialchars($teacher['phone_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($teacher['email']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>

      <style>
        .teacher-popup {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.6);
          z-index: 9999;
          justify-content: center;
          align-items: center;
        }

        .teacher-popup .popup-content {
          background: #fff;
          padding: 30px;
          border-radius: 12px;
          max-width: 500px;
          width: 90%;
          position: relative;
          text-align: left;
          box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .teacher-popup .close-btn {
          position: absolute;
          top: 10px;
          right: 15px;
          font-size: 24px;
          font-weight: bold;
          cursor: pointer;
        }
      </style>
    </div>
    <script>
      function togglePopup(id) {
        const popup = document.getElementById(`popup-${id}`);
        if (popup.style.display === "flex") {
          popup.style.display = "none";
        } else {
          // Скрываем все popup'ы
          document.querySelectorAll('.teacher-popup').forEach(p => p.style.display = 'none');
          popup.style.display = "flex";
        }
      }

      // Дополнительно: закрытие при клике вне popup
      window.addEventListener('click', function(e) {
        document.querySelectorAll('.teacher-popup').forEach(popup => {
          if (e.target === popup) {
            popup.style.display = 'none';
          }
        });
      });
    </script>

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
      <img loading="lazy" src="assets/img/price-back.png" class="pricing-background" alt="" aria-hidden="true" />
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
                    <span class="price-amount"><?= htmlspecialchars((int)$subscription['price']) ?></span>
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
    WHERE r.status = 'новое'
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
                  <img src="assets/img/star1.png" alt="Звезда" class="star-icon" role="presentation" />
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


    <div class="signup-wrapper">
      <img
        src="assets/img/free_class.png"
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

          <label for="phone" class="email-label">Номер телефона</label>
          <input type="phone" id="phone" name="phone" class="form-input" placeholder="+79888898989" required />

          <div class="privacy-container">
            <input type="checkbox" id="privacy" name="privacy" class="visually-hidden" required />
            <label for="privacy">Я соглашаюсь на обработку персональных данных</label>
          </div>

          <button type="submit" class="submit-button">Записаться на пробное занятие</button>
        </form>
        <script>
          const signupForm = document.querySelector('.signup-form');
          signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(signupForm);

            fetch('app/free_submit_order.php', {
                method: 'POST',
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                showPopup(data.message || 'Неизвестный ответ от сервера');
              })
              .catch(error => {
                showPopup('Ошибка при отправке формы. Попробуйте ещё раз.');
                console.error('Ошибка:', error);
              });
          })
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
        </script>
      </div>
    </div>
  </section>
</div>

<div id="contact">

  <section class="contacts">
    <div class="contacts-content">
      <h2 class="contacts-title">Контакты</h2>
      <div class="contacts-grid">
        <div style="position:relative;overflow:hidden;"><a href="https://yandex.ru/maps/56/chelyabinsk/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Челябинск</a><a href="https://yandex.ru/maps/56/chelyabinsk/house/ulitsa_yelkina_59/YkgYdQ5oQEIPQFtvfX10eXxiYQ==/?ll=61.399195%2C55.157914&utm_medium=mapframe&utm_source=maps&z=17.88" style="color:#eee;font-size:12px;position:absolute;top:14px;">Улица Елькина, 59 — Яндекс Карты</a><iframe src="https://yandex.ru/map-widget/v1/?ll=61.399195%2C55.157914&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgg1NjAyMzM1NhI_0KDQvtGB0YHQuNGPLCDQp9C10LvRj9Cx0LjQvdGB0LosINGD0LvQuNGG0LAg0JXQu9GM0LrQuNC90LAsIDU5IgoNj5h1QhXOoVxC&z=17.88" width="960" height="450" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe></div>
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
<a href="#" id="scrollToTop" class="scroll-to-top" title="Наверх">↑</a>
<script>
  const scrollBtn = document.getElementById('scrollToTop');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      scrollBtn.classList.add('show');
    } else {
      scrollBtn.classList.remove('show');
    }
  });

  scrollBtn.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
</script>

<?php
$stmt = null;
$pdo = null;
include "components/footer.php" ?>