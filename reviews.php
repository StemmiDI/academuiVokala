<link rel="stylesheet" href="css/reviews.css">
<?php
include 'app/db.php';

// Получаем отзывы с рейтингом
$query = "
    SELECT r.review_text, r.created_at, r.rating, u.name, u.full_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id_user
    WHERE r.status = 'опубликованное'
    ORDER BY r.created_at DESC
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
                <img src="img/star1.png" alt="Звезда" class="star-icon" role="presentation" />
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