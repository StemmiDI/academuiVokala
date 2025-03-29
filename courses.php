<?php
// Подключаем базу данных
include 'app/db.php';

// Запрос курсов
$query = "SELECT name_course, description, icon FROM courses";
$stmt = $pdo->prepare($query);
$stmt->execute();
$courses = $stmt->fetchAll();
?>

<link rel="stylesheet" href="css/courses.css">
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