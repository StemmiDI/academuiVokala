<link rel="stylesheet" href="css/price.css" />
<?php
// Подключаем файл с подключением к базе данных
include 'app/db.php';

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
                <p class="card-feature card-feature-first">Уровень: <?= htmlspecialchars($subscription['level']) ?></p>
                <p class="card-feature">Количество занятий: <?= htmlspecialchars($subscription['number_of_lesson']) ?></p>

              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>