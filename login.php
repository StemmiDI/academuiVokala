<?php
include "components/head_reg_log.php" ?>
<?php
include "components/header.php" ?>

<main class="auth-container">
  <div class="auth-wrapper">
    <div class="auth-content">
      <div class="auth-form-column">
        <form action="app/auth.php" class="auth-form" method="post">
          <h1 class="auth-title">Авторизация</h1>

          <label for="email" class="form-label">Почта</label>
          <input
            type="email"
            name="email"
            id="email"
            class="form-input"
            placeholder="business@mail.com"
            required
            aria-required="true"
            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Введите корректный адрес электронной почты" />

          <label for="password" class="password-label">Пароль</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            placeholder="мин. 6 символов"
            required
            aria-required="true"
            minlength="6"
            pattern=".{6,}" title="Пароль должен содержать не менее 6 символов" />

          <a href="#" class="forgot-password" tabindex="0">Забыли пароль?</a>

          <button type="submit" class="submit-button">Войти</button>

          <div class="register-wrapper">
            <span class="register-text">Нет аккаунта?</span>
            <a href="register.php" class="register-link" tabindex="0">Зарегистрироваться</a>
          </div>
        </form>
      </div>

      <div class="image-column">
        <img
          src="img/img-index.jpg"
          alt=""
          class="hero-image"
          role="presentation" />
      </div>
    </div>
  </div>
</main>
<?php
include "components/footer.php" ?>