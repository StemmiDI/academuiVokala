<?php
include '../app/db.php';
include "../components/head_admin.php";
include "../components/header-outh.php";

$stmt = $pdo->prepare("SELECT * FROM courses");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<section class="section-courses-wrapper">
  <div class="courses-main-container">
    <img
      src="../assets/img/back3.png"
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
            <form id="addCourseForm" enctype="multipart/form-data">
              <input type="text" id="name_course" name="name_course" placeholder="Название курса" required>
              <input type="text" id="description" name="description" placeholder="Описание курса" required>
              <input type="file" id="icon" name="icon" accept="image/*">
              <button type="submit">Добавить</button>
            </form>
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
          <input type="file" id="editFile" name="icon" accept="image/*">
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
  document.getElementById('addCourseForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем перезагрузку страницы

    let formData = new FormData(this);
    formData.append('action', 'add');

    fetch("/admin/api/add-course.php", {
        method: 'POST',
        body: formData
      })
      .then(response => response.json()) // Преобразуем в JSON
      .then(data => {
        if (data.status === 'success') {
          // Создаем новую строку в таблице
          let newRow = document.createElement('tr');
          newRow.id = `course_${data.id}`; // Используем ID, который пришел с сервера
          newRow.innerHTML = `
                <td>${data.name_course}</td>
                <td>${data.description}</td>
                <td><img src="../../uploads/${data.icon}" alt="Иконка" width="50"></td>
                <td>
                    <button class="edit" onclick="showEditModal(${data.id})">Изменить</button>
                    <button class="delete" onclick="showDeleteModal(${data.id})">Удалить</button>
                </td>
            `;

          // Добавляем строку в таблицу
          document.querySelector("#coursesTable tbody").appendChild(newRow);

          // Очистка формы
          document.getElementById('addCourseForm').reset();

        } else {
          alert(data.message);
        }
      })
      .catch(error => console.error('Ошибка:', error));
  });



  // Редактирование курса
  function editCourse() {
    const id = document.getElementById('editCourseId').value;
    const name = document.getElementById('editName').value;
    const description = document.getElementById('editDescription').value;
    const fileInput = document.getElementById('editFile');

    const formData = new FormData();
    formData.append('action', 'edit');
    formData.append('id', id);
    formData.append('name_course', name);
    formData.append('description', description);

    // Добавляем файл, если он выбран
    if (fileInput.files.length > 0) {
      formData.append('icon', fileInput.files[0]);
    }

    fetch("/admin/api/add-course.php", {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Находим строку курса в таблице
          let row = document.getElementById(`course_${id}`);
          if (row) {
            row.children[0].textContent = name; // Обновляем название курса
            row.children[1].textContent = description; // Обновляем описание
            if (data.icon) {
              row.querySelector('img').src = '/uploads/' + data.icon;
            }
          }

          closeModal(); // Закрываем модальное окно
        } else {
          alert(data.message);
        }
      })
      .catch(error => console.error("Ошибка:", error));
  }


  // Удаление курса
  function deleteCourse() {
    var id = document.getElementById('deleteCourseId').value;

    var formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch("/admin/api/add-course.php", {
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
$stmt = null;
$pdo = null;
include "../components/footer.php" ?>