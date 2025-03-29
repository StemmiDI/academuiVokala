<link rel="stylesheet" href="css/ped.css">
<?php
// Подключаем файл с подключением к базе данных
include 'app/db.php';

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
            <p class="teacher-position">Должность: Преподаватель курса <?= htmlspecialchars($teacher['name_course']) ?></p>
            <button class="details-button" tabindex="0">Подробнее</button>
          </article>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>