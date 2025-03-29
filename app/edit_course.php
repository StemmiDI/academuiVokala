<?php
// Подключаем базу данных
include 'db.php';

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Получаем курс из базы данных
    $stmt = $connect->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_course = $_POST['name_course'];
    $description = $_POST['description'];

    // Обновляем курс в базе данных
    $stmt = $connect->prepare("UPDATE courses SET name_course = ?, description = ? WHERE id = ?");
    $stmt->execute([$name_course, $description, $course_id]);

    // Перенаправление обратно на страницу управления курсами
    header("Location: ../admin/admin_cou.php");
    exit();
}
?>

<!-- Форма для редактирования курса -->
<form action="edit_course.php?id=<?php echo $course['id']; ?>" method="POST">
    <input type="text" name="name_course" value="<?php echo $course['name_course']; ?>" required>
    <input type="text" name="description" value="<?php echo $course['description']; ?>" required>
    <button type="submit">Сохранить изменения</button>
</form>