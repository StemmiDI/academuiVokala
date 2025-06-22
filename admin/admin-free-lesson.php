<?php
session_start();
include "../app/db.php";
include "../components/head_admin.php";
include "../components/header-outh.php";

// Проверка на админа
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Получаем все заявки на бесплатные занятия
$query = $pdo->prepare("
    SELECT free_lesson.*, courses.name_course
    FROM free_lesson
    LEFT JOIN courses ON free_lesson.course_id = courses.id
");
$query->execute();
$applications = $query->fetchAll();

?>

<style>
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

    .edit,
    .delete {
        padding: 5px 10px;
        border: none;
        cursor: pointer;
    }

    .edit {
        background: #ddd;
    }

    .delete {
        background: #ff6b6b;
        color: white;
    }

    .add-form {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 10px;
    }

    .add-form h2 {
        margin-bottom: 10px;
    }

    /* Стили для модального окна */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<section class="pricing-section">
    <div class="pricing-container">
        <img src="../uploads/фон.png" class="background-image" alt="Background image" />
        <div class="content-wrapper-card">
            <h1 class="pricing-title-ad-s">Управление заявками на бесплатное занятие</h1>
            <div class="container">

                <table border="1">
                    <thead>
                        <tr>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Курс</th>
                            <th>Дата занятия</th>
                            <th>Время занятия</th>
                            <th>Редактировать</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr id="row-<?php echo $application['id_free_lesson']; ?>">
                                <td class="name"><?php echo htmlspecialchars($application['free_lesson_name'] ?? 'Не указано'); ?></td>
                                <td class="email"><?php echo htmlspecialchars($application['free_lesson_email'] ?? 'Не указано'); ?></td>
                                <td class="course"><?php echo htmlspecialchars($application['name_course'] ?? 'Не указано'); ?></td> <!-- Используем name_course -->
                                <td class="date">
                                    <?php
                                    if (!empty($application['course_date'])) {
                                        $date = date("d.m.Y", strtotime($application['course_date']));
                                        echo htmlspecialchars($date);
                                    } else {
                                        echo 'Не указано';
                                    }
                                    ?>
                                </td>

                                <td class="time">
                                    <?php
                                    if (!empty($application['lesson_time'])) {
                                        echo htmlspecialchars(substr($application['lesson_time'], 0, 5));
                                    } else {
                                        echo 'Не указано';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <button class="edit" onclick="openModal(<?php echo $application['id_free_lesson']; ?>)">Редактировать</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


                <!-- Модальное окно для редактирования -->
                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <div class="modal-wrap">
                            <h2>Редактировать заявку</h2>
                            <?php
                            // Получаем все курсы из базы данных
                            $query = $pdo->prepare("SELECT * FROM courses");
                            $query->execute();
                            $courses = $query->fetchAll();
                            ?>

                            <form id="editForm">
                                <input type="hidden" id="applicationId" name="id" value="">

                                <label for="course_name">Курс:</label>
                                <select id="course_name" name="course_name" required>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['name_course']); ?> <!-- Исправили на name_course -->
                                        </option>
                                    <?php endforeach; ?>
                                </select><br><br>

                                <label for="course_date">Дата занятия:</label>
                                <input type="date" id="course_date" name="course_date" required><br><br>

                                <label for="lesson_time">Время занятия:</label>
                                <input type="time" id="lesson_time" name="lesson_time" required><br><br>
                        </div>
                        <button class="addd-btn" type="submit">Сохранить изменения</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    // Функция для открытия модального окна
    // Функция для открытия модального окна
    function openModal(id) {
        // Показываем модальное окно
        document.getElementById("editModal").style.display = "block";

        // Загрузка данных по ID заявки
        fetch('admin/api/get_application.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // alert('Ошибка: ' + data.error);
                    return;
                }

                // Заполняем поля формы
                document.getElementById("applicationId").value = data.id_free_lesson;
                document.getElementById("course_name").value = data.course_id; // Устанавливаем выбранный курс
                document.getElementById("course_date").value = data.course_date;
                document.getElementById("lesson_time").value = data.lesson_time;
            });
    }


    // Функция для закрытия модального окна
    function closeModal() {
        document.getElementById("editModal").style.display = "none";
    }

    // Обработка отправки формы
    document.getElementById("editForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const id = formData.get("id_free_lesson"); // ID заявки

        fetch('admin/api/update_application.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const updated = data.data;

                    const row = document.getElementById("row-" + updated.id);
                    if (row) {
                        row.querySelector(".name").textContent = updated.name;
                        row.querySelector(".email").textContent = updated.email;
                        row.querySelector(".course").textContent = updated.course;
                        row.querySelector(".date").textContent = updated.date;
                        row.querySelector(".time").textContent = updated.time;
                    }

                    closeModal();
                } else {
                    alert('Что-то пошло не так!');
                }
            });
    });
</script>
<?php
include "../components/footer.php" ?>