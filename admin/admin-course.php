<?php
include '../app/db.php';
include "../components/head_admin.php";
include "../components/header-outh.php";

$stmt = $pdo->prepare("SELECT * FROM courses");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');

  if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
      $name_course = trim($_POST['name_course'] ?? '');
      $description = trim($_POST['description'] ?? '');

      if (!$name_course || !$description) {
        echo json_encode(['status' => 'error', 'message' => 'Заполните все поля!']);
        exit();
      }

      // Загрузка изображения
      $icon_name = 'default.png';
      if (!empty($_FILES['icon']['name'])) {
        $upload_dir = '../uploads/';
        $icon_name = time() . '_' . basename($_FILES['icon']['name']);
        move_uploaded_file($_FILES['icon']['tmp_name'], $upload_dir . $icon_name);
      }

      $stmt = $pdo->prepare("INSERT INTO courses (name_course, description, icon) VALUES (?, ?, ?)");
      $stmt->execute([$name_course, $description, $icon_name]);

      echo json_encode(['status' => 'success']);
      exit();
    }

    if ($action === 'edit') {
      $id = $_POST['id'] ?? 0;
      $name_course = trim($_POST['name_course'] ?? '');
      $description = trim($_POST['description'] ?? '');

      if (!$id || !$name_course || !$description) {
        echo json_encode(['status' => 'error', 'message' => 'Заполните все поля!']);
        exit();
      }

      // Проверка, загружено ли новое изображение
      if (!empty($_FILES['icon']['name'])) {
        $upload_dir = '../uploads/';
        $icon_name = time() . '_' . basename($_FILES['icon']['name']);
        move_uploaded_file($_FILES['icon']['tmp_name'], $upload_dir . $icon_name);

        // Обновляем курс с новой иконкой
        $stmt = $pdo->prepare("UPDATE courses SET name_course = ?, description = ?, icon = ? WHERE id = ?");
        $stmt->execute([$name_course, $description, $icon_name, $id]);
      } else {
        // Обновляем курс без изменения иконки
        $stmt = $pdo->prepare("UPDATE courses SET name_course = ?, description = ? WHERE id = ?");
        $stmt->execute([$name_course, $description, $id]);
      }

      echo json_encode(['status' => 'success']);
      exit();
    }

    if ($action === 'delete') {
      $id = $_POST['id'] ?? 0;

      if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Некорректный ID!']);
        exit();
      }

      // Получаем текущий icon
      $stmt = $pdo->prepare("SELECT icon FROM courses WHERE id = ?");
      $stmt->execute([$id]);
      $course = $stmt->fetch(PDO::FETCH_ASSOC);

      // Удаляем файл иконки (кроме иконки по умолчанию)
      if ($course && $course['icon'] !== 'default.png' && file_exists("../uploads/" . $course['icon'])) {
        unlink("../uploads/" . $course['icon']);
      }

      $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
      $stmt->execute([$id]);

      echo json_encode(['status' => 'success']);
      exit();
    }
  }
}
?>


<section class="section-courses-wrapper">
  <div class="courses-main-container">
    <img
      src="../img/back3.png"
      alt=""
      class="courses-background-image" />

    <div class="courses-content-wrapper">
      <section class="course-section">
        <h1 class="course-title">Управление курсами</h1>
        <div class="container">


          <!-- Таблица курсов -->
          <table id="coursesTable">
            <thead>
              <tr>
                <th>Название курса</th>
                <th>Описание</th>
                <th>Иконка</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($courses as $course): ?>
                <tr id="course_<?php echo $course['id']; ?>">
                  <td><?php echo htmlspecialchars($course['name_course']); ?></td>
                  <td><?php echo htmlspecialchars($course['description']); ?></td>
                  <td><img src="../uploads/<?php echo htmlspecialchars($course['icon']); ?>" alt="Иконка" width="50"></td>
                  <td>
                    <button class="edit" onclick="showEditModal(<?php echo $course['id']; ?>)">Изменить</button>
                    <button class="delete" onclick="showDeleteModal(<?php echo $course['id']; ?>)">Удалить</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>


          <!-- Форма для добавления нового курса -->
          <div class="add-form">
            <h2>Добавить курс</h2>
            <input type="text" id="name_course" placeholder="Название курса" required>
            <input type="text" id="description" placeholder="Описание курса" required>
            <input type="file" id="icon" accept="image/*">
            <button onclick="addCourse()">Добавить</button>
          </div>
        </div>
      </section>

      <!-- Модальное окно для редактирования -->
      <div id="editModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeModal()">&times;</span>
          <h2>Редактировать курс</h2>
          <input type="hidden" id="editCourseId">
          <input type="text" id="editName" placeholder="Название курса" required>
          <input type="text" id="editDescription" placeholder="Описание курса" required>
          <button onclick="editCourse()">Сохранить изменения</button>
        </div>
      </div>

      <!-- Модальное окно для подтверждения удаления -->
      <div id="deleteModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeModal()">&times;</span>
          <h2>Вы уверены, что хотите удалить этот курс?</h2>
          <input type="hidden" id="deleteCourseId">
          <button onclick="deleteCourse()">Удалить</button>
          <button onclick="closeModal()">Отмена</button>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  // Открыть модальное окно для редактирования
  function showEditModal(courseId) {
    document.getElementById('editCourseId').value = courseId;
    document.getElementById('editName').value = document.getElementById('course_' + courseId).children[0].textContent;
    document.getElementById('editDescription').value = document.getElementById('course_' + courseId).children[1].textContent;
    document.getElementById('editModal').style.display = "block";
  }

  // Открыть модальное окно для удаления
  function showDeleteModal(courseId) {
    document.getElementById('deleteCourseId').value = courseId;
    document.getElementById('deleteModal').style.display = "block";
  }

  // Закрыть модальное окно
  function closeModal() {
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
      modal.style.display = "none";
    });
  }

  // Добавление нового курса
  function addCourse() {
    var name = document.getElementById('name_course').value;
    var description = document.getElementById('description').value;
    var icon = document.getElementById('icon').files[0];

    var formData = new FormData();
    formData.append('action', 'add');
    formData.append('name_course', name);
    formData.append('description', description);
    if (icon) {
      formData.append('icon', icon);
    }

    fetch('', {
        method: 'POST',
        body: formData
      }).then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          closeModal();
          location.reload();
        } else {
          alert(data.message);
        }
      });
  }

  // Редактирование курса
  function editCourse() {
    var id = document.getElementById('editCourseId').value;
    var name = document.getElementById('editName').value;
    var description = document.getElementById('editDescription').value;

    var formData = new FormData();
    formData.append('action', 'edit');
    formData.append('id', id);
    formData.append('name_course', name);
    formData.append('description', description);

    fetch('', {
        method: 'POST',
        body: formData
      }).then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          closeModal();
          location.reload();
        } else {
          alert(data.message);
        }
      });
  }

  // Удаление курса
  function deleteCourse() {
    var id = document.getElementById('deleteCourseId').value;

    var formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('', {
        method: 'POST',
        body: formData
      }).then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          document.getElementById('course_' + id).remove();
          closeModal();
        } else {
          alert(data.message);
        }
      });
  }
</script>
<?php
include "../components/footer.php" ?>