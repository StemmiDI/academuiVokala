<?php
include "components/head_reg_log.php" ?>
<?php
include "components/header.php" ?>

<section class="registration-container" aria-label="Registration Form">
  <div class="registration-wrapper">
    <div class="registration-content">
      <div class="form-column">
        <form action="app/reg.php" id="register-form" class="form-wrapper" method="post">
          <h1 class="registration-title">Регистрация</h1>

          <label for="name" class="form-label">Имя</label>
          <input
            type="text"
            name="name"
            id="name"
            class="form-input"
            placeholder="Иван"
            required
            aria-required="true"
            pattern="[A-Za-zА-Яа-яЁё]{1,}" title="Имя должно содержать только буквы" />
          <label for="full_name" class="form-label">Фамилия</label>
          <input
            type="text"
            name="full_name"
            id="full_name"
            class="form-input"
            placeholder="Иванов"
            required
            aria-required="true"
            pattern="[A-Za-zА-Яа-яЁё]{1,}" title="Фамилия должна содержать только буквы" />

          <label for="email" class="form-label">Почта</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="business@mail.com"
            required
            aria-required="true"
            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Введите корректный адрес электронной почты" />

          <label for="password" class="form-label">Пароль</label>
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

          <div class="consent-wrapper">
            <input type="checkbox" id="consent" class="visually-hidden" required aria-required="true" />
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