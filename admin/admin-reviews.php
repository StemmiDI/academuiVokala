<?php
// Подключаем базу данных
include "../app/db.php";
include "../components/head_admin.php";
include "../components/header-outh.php";


// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Публикация отзыва
    if ($action === 'publish') {
        $id = $_POST['id'];

        // Обновляем статус на 'опубликованный'
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'опубликованное' WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success']);
        exit;
    }

    // Снятие с публикации отзыва
    if ($action === 'unpublish') {
        $id = $_POST['id'];

        // Обновляем статус на 'не опубликованное'
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'не опубликованное' WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success']);
        exit;
    }
    // Удаление отзыва
    if ($action === 'delete') {
        $id = $_POST['id'];

        // Удаляем отзыв из базы данных
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Получаем отзывы и сортируем их по статусу: сначала 'новые'
$stmt = $pdo->prepare("SELECT r.id, r.review_text, r.status, u.name 
FROM reviews r 
JOIN users u ON r.user_id = u.id_user
ORDER BY FIELD(r.status, 'новое', 'опубликованное', 'не опубликованное');
");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Разделим отзывы на новые и опубликованные
$newReviews = array_filter($reviews, fn($review) => $review['status'] === 'новое');
$publishedReviews = array_filter($reviews, fn($review) => $review['status'] === 'опубликованное');

?>


<style>
    /* Добавим стиль для страницы управления отзывами */
    .container {
        margin: 20px auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 40px;
        margin-bottom: 20px;
    }

    .tabs {
        display: flex;
        margin-bottom: 20px;
    }

    .tab {
        padding: 10px 20px;
        cursor: pointer;
        background-color: #f0f0f0;
        margin-right: 10px;
        border-radius: 5px;
    }

    .tab.active {
        background-color: #007bff;
        color: white;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background: #f5f5f5;
    }

    .publish,
    .unpublish {
        padding: 5px 10px;
        border: none;
        cursor: pointer;
    }

    .publish {
        background: #4CAF50;
        color: white;
    }

    .unpublish {
        background: #FF6347;
        color: white;
    }

    .delete {
        background: #FF0000;
        color: white;
    }
</style>

<section class="reviews-section">
    <div class="reviews-container">
        <h1 class>Управление отзывами</h1>
        <div class="tabs">
            <div class="tab active" id="newReviewsTab" onclick="showReviews('new')">Новые отзывы</div>
            <div class="tab" id="publishedReviewsTab" onclick="showReviews('published')">Опубликованные отзывы</div>
        </div>
        <div class="container">
            <!-- Таблица для новых отзывов -->
            <div id="newReviews" class="reviews-table">
                <table>
                    <thead>
                        <tr>
                            <th>Пользователь</th>
                            <th>Отзыв</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($newReviews as $review): ?>
                            <tr id="row-<?php echo $review['id']; ?>">
                                <td><?php echo htmlspecialchars($review['name']); ?></td>
                                <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                                <td>Новое</td>
                                <td>
                                    <button class="publish" onclick="publishReview(<?php echo $review['id']; ?>)">Опубликовать</button>
                                    <button class="delete" onclick="deleteReview(<?php echo $review['id']; ?>)">Удалить</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Таблица для опубликованных отзывов -->
            <div id="publishedReviews" class="reviews-table" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Пользователь</th>
                            <th>Отзыв</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($publishedReviews as $review): ?>
                            <tr id="row-<?php echo $review['id']; ?>">
                                <td><?php echo htmlspecialchars($review['name']); ?></td>
                                <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                                <td>Опубликован</td>
                                <td>
                                    <button class="unpublish" onclick="unpublishReview(<?php echo $review['id']; ?>)">Снять с публикации</button>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script>
    // Функция для отображения нужной вкладки
    function showReviews(type) {
        // Скрыть все таблицы
        document.getElementById("newReviews").style.display = 'none';
        document.getElementById("publishedReviews").style.display = 'none';

        // Убрать активный класс с вкладок
        document.getElementById("newReviewsTab").classList.remove('active');
        document.getElementById("publishedReviewsTab").classList.remove('active');

        // Показать выбранную таблицу
        if (type === 'new') {
            document.getElementById("newReviews").style.display = 'block';
            document.getElementById("newReviewsTab").classList.add('active');
        } else {
            document.getElementById("publishedReviews").style.display = 'block';
            document.getElementById("publishedReviewsTab").classList.add('active');
        }
    }
    // Функция для публикации отзыва
    function publishReview(id) {
        fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'publish',
                    id: id
                })
            })
            .then(response => response.json())
            .then(() => {
                let row = document.getElementById("row-" + id);
                row.cells[2].textContent = "Опубликован";
                row.querySelector(".publish").style.display = "none";
                row.querySelector(".unpublish").style.display = "inline-block";
            });
    }

    // Функция для снятия публикации отзыва
    function unpublishReview(id) {
        fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'unpublish',
                    id: id
                })
            })
            .then(response => response.json())
            .then(() => {
                let row = document.getElementById("row-" + id);
                row.cells[2].textContent = "Не опубликован";
                row.querySelector(".unpublish").style.display = "none";
                row.querySelector(".publish").style.display = "inline-block";

            });
    }
    // Функция для удаления отзыва
    function deleteReview(id) {
        if (confirm("Вы уверены, что хотите удалить этот отзыв?")) {
            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'delete',
                        id: id
                    })
                })
                .then(response => response.json())
                .then(() => {
                    let row = document.getElementById("row-" + id);
                    row.remove();
                    window.location.reload();
                });
        }
    }
</script>
<?php
include "../components/footer.php" ?>