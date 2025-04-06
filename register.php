<?php
include "components/head_reg_log.php" ?>
<?php
include "components/header.php" ?>

<section class="registration-container" aria-label="Registration Form">
  <div class="registration-wrapper">
    <div class="registration-content">
      <div class="form-column">
        <?php session_start(); ?>
        <form action="app/reg.php" id="register-form" class="form-wrapper" method="post">
          <h1 class="registration-title">Регистрация</h1>

          <?php if (!empty($_SESSION['errors'])): ?>
            <div style="color: red;">
              <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
              <?php endforeach; ?>
              <?php unset($_SESSION['errors']); // Очищаем ошибки после отображения 
              ?>
            </div>
          <?php endif; ?>

          <label for="name" class="form-label">Имя</label>
          <input type="text" name="name" id="name" class="form-input" placeholder="Иван" required aria-required="true">

          <label for="full_name" class="form-label">Фамилия</label>
          <input type="text" name="full_name" id="full_name" class="form-input" placeholder="Иванов" required aria-required="true">

          <label for="email" class="form-label">Почта</label>
          <input type="text" id="email" name="email" class="form-input" placeholder="business@mail.com" required aria-required="true">

          <label for="password" class="form-label">Пароль</label>
          <input type="password" id="password" name="password" class="form-input" placeholder="мин. 6 символов" required aria-required="true">

          <div class="consent-wrapper">
            <input type="checkbox" id="consent" class="visually-hidden" required aria-required="true">
            <span class="checkbox-custom" aria-hidden="true"></span>
            <label for="consent">Я согласен на <a href="#">обработку персональных данных</a></label>
          </div>

          <button type="submit" class="submit-button">Зарегистрироваться</button>
        </form>

      </div>

      <div class="image-column">
        <img
          src="img/img-index.jpg"
          class="registration-image"
          alt="Registration illustration"
          loading="lazy" />
      </div>
    </div>
  </div>
</section>
<?php
include "components/footer.php" ?>