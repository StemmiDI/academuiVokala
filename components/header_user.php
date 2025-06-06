<header class="header">
  <div class="logo-img"> <a href="index.php"><img class="logo" src="../img/logo.jpg" alt=""></a> </div>
  <nav class="main-nav">
    <a href="../index.php#about" class="nav-item">О нас</a>
    <a href="../index.php#courses" class="nav-item">Курсы</a>
    <a href="../index.php#price" class="nav-item">Цены</a>
    <a href="../index.php#ped" class="nav-item">Преподаватели</a>
    <a href="../index.php#reviews" class="nav-item">Отзывы</a>
    <a href="../index.php#contact" class="nav-item">Контакты</a>
  </nav>
  <div class="auth-buttons">
    <?php if (isset($_SESSION['id_user'])): ?>
      <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
        <a href="../admin/admin.php" class="login-btn">Админ панель</a>
      <?php else: ?>
        <a href="user_profile.php" class="login-btn">Личный кабинет</a>
      <?php endif; ?>
      <a href="../app/logout.php" class="login-btn">Выход</a>
    <?php else: ?>
      <a href="register.php" class="register-btn">Регистрация</a>
      <a href="login.php" class="login-btn">Авторизация</a>
    <?php endif; ?>
  </div>
</header>