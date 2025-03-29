<?php
include "../components/head_admin.php";
include "../components/header-admin.php"; ?>

<section class="admin-panel">
  <div class="admin-container">
    <h1 class="admin-title">Панель Администратора</h1>
    <main class="content-wrapper">
      <img src="../img/admin.png" class="background-image" alt="Content background" />
      <div class="top-row">
        <div class="top-row-container">
          <article class="column-item">
            <a href="admin/admin-course.php" class="card">Курсы вокала</a>
          </article>
          <article class="column-item">
            <a href="admin/admin-reviews.php" class="card">Отзывы</a>
          </article>
          <article class="column-item">
            <a href="admin/admin-rasp.php" class="card">Расписание</a>
          </article>
        </div>
      </div>
      <div class="top-row">
        <div class="top-row-container">
          <article class="column-item">
            <a href="admin/admin-subscription.php" class="card">Абонемент</a>
          </article>
          <article class="column-item">
            <a href="admin/admin-tach.php" class="card">Преподаватели</a>
          </article>
          <article class="column-item">
            <a href="admin/admin-free-lesson.php" class="card">Бесплатное занятие</a>
          </article>

        </div>
      </div>
    </main>
  </div>
</section>

<?php
include "../components/footer.php" ?>