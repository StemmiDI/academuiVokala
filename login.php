<?php
include "components/head_reg_log.php" ?>
<?php
include "components/header.php" ?>

<main class="auth-container">
  <div class="auth-wrapper">
    <div class="auth-content">
      <div class="auth-form-column">
        <?php session_start(); ?>
        <form action="app/auth.php" class="auth-form" method="post">
          <h1 class="auth-title">Авторизация</h1>

          <?php if (!empty($_SESSION['errors'])): ?>
            <div style="color: red; margin-top: 30px;">
              <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
              <?php endforeach; ?>
              <?php unset($_SESSION['errors']); // Очищаем ошибки после отображения 
              ?>
            </div>
          <?php endif; ?>

          <label for="email" class="form-label">Почта</label>
          <input type="text" name="email" id="email" class="form-input" placeholder="business@mail.com" required>

          <label for="password" class="password-label">Пароль</label>
          <input type="password" id="password" name="password" class="form-input" placeholder="мин. 6 символов" required>



          <button type="submit" class="submit-button">Войти</button>

          <div class="register-wrapper">
            <span class="register-text">Нет аккаунта?</span>
            <a href="register.php" class="register-link" tabindex="0">Зарегистрироваться</a>
          </div>
        </form>

      </div>

      <div class="image-column">
        <img
          src="assets/img/img-index.jpg"
          alt=""
          class="hero-image"
          role="presentation" />
      </div>
    </div>
  </div>
</main>
<?php
include "components/footer.php" ?>