<?php
include '../app/db.php';
include "../components/head_admin.php";
include "../components/header-outh.php";

// Получаем список курсов
$stmt = $pdo->prepare("SELECT id, name_course FROM courses");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем всех преподавателей
$stmt = $pdo->prepare("SELECT teachers.*, courses.name_course FROM teachers JOIN courses ON teachers.course_id = courses.id");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* CSS стили */
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

    .add-form input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 20%;
        margin-bottom: 10px;
    }

    .add-form button {
        background: #8aff8a;
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 400px;
    }

    .close {
        float: right;
        font-size: 28px;
        cursor: pointer;
    }
</style>
<section class="pricing-section">
    <div class="pricing-container">
        <img src="../uploads/фон.png" class="background-image" alt="Background image" />
        <div class="content-wrapper-card">
            <h1 class="pricing-title-ad-s">Управление преподавателями</h1>
            <section class="teacher-management">
                <div class="container">
                    <table>
                        <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Описание</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Курс</th>
                                <th>Фото</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr id="row-<?php echo $teacher['id']; ?>">
                                    <td><?php echo htmlspecialchars($teacher['name_teacher']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['description']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['name_course']); ?></td>
                                    <td>
                                        <?php if ($teacher['photo']): ?>
                                            <img src="../uploads/<?php echo $teacher['photo']; ?>" width="50">
                                        <?php else: ?>
                                            Нет фото
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="edit" onclick="editTeacher(<?php echo $teacher['id']; ?>)">Изменить</button>
                                        <button class="delete" onclick="deleteTeacher(<?php echo $teacher['id']; ?>)">Удалить</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="add-form">
                        <h2>Добавить преподавателя</h2>
                        <input type="text" id="name_teacher" placeholder="Имя">
                        <input type="text" id="description" placeholder="Описание">
                        <input type="text" id="phone_number" placeholder="Телефон">
                        <input type="email" id="email" placeholder="Email">
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <select id="course_id">
                            <option value="">Выберите курс</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo $course['name_course']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button onclick="addTeacher()">Добавить</button>
                    </div>
                </div>

                <!-- Модальные окна для редактирования и удаления преподавателей -->
                <div id="teacherModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeTeacherModal()">&times;</span>
                        <h2 id="modalTitle">Редактировать преподавателя</h2>
                        <input type="hidden" id="teacherId">
                        <input type="text" id="modalNameTeacher" placeholder="Имя" required>
                        <input type="text" id="modalDescription" placeholder="Описание" required>
                        <input type="text" id="modalPhoneNumber" placeholder="Телефон" required>
                        <input type="email" id="modalEmail" placeholder="Почта" required>
                        <input type="file" id="modalPhoto" placeholder="Выберите фото">
                        <select id="modalCourseId">
                            <option value="">Выберите курс</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo $course['name_course']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="saveTeacher()">Сохранить</button>
                    </div>
                </div>

                <div id="deleteTeacherModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeTeacherModal()">&times;</span>
                        <h2>Вы уверены, что хотите удалить этого преподавателя?</h2>
                        <input type="hidden" id="deleteTeacherId">
                        <button onclick="confirmDeleteTeacher()">Удалить</button>
                        <button onclick="closeTeacherModal()">Отмена</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
<script>
    // Функция для добавления преподавателя
    function addTeacher() {
        let name_teacher = document.getElementById('name_teacher').value;
        let description = document.getElementById('description').value;
        let phone_number = document.getElementById('phone_number').value;
        let email = document.getElementById('email').value;
        let course_id = document.getElementById('course_id').value;
        let photo = document.getElementById('photo').files[0];

        // Создаем объект FormData для отправки данных на сервер
        let formData = new FormData();
        formData.append('action', 'add');
        formData.append('name_teacher', name_teacher);
        formData.append('description', description);
        formData.append('phone_number', phone_number);
        formData.append('email', email);
        formData.append('course_id', course_id);
        formData.append('photo', photo);

        // Отправляем данные через AJAX
        fetch('admin/api/edit-teacher.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let teacher = data;

                    let table = document.querySelector('table tbody');
                    let row = document.createElement('tr');
                    row.id = "row-" + teacher.id;
                    row.innerHTML = `
            <td>${teacher.name_teacher}</td>
            <td>${teacher.description}</td>
            <td>${teacher.phone_number}</td>
            <td>${teacher.email}</td>
            <td>${teacher.course_name}</td>
            <td><img src="../uploads/${teacher.photo}" width="50"></td>
            <td>
                <button class="edit" onclick="editTeacher(${teacher.id})">Изменить</button>
                <button class="delete" onclick="deleteTeacher(${teacher.id})">Удалить</button>
            </td>
        `;
                    table.appendChild(row);

                    // Очищаем форму
                    document.getElementById('name_teacher').value = "";
                    document.getElementById('description').value = "";
                    document.getElementById('phone_number').value = "";
                    document.getElementById('email').value = "";
                    document.getElementById('course_id').value = "";
                    document.getElementById('photo').value = "";
                } else {
                    alert('Ошибка при добавлении преподавателя');
                }
            });

    }

    // Функция для редактирования преподавателя
    function editTeacher(id) {
        let row = document.getElementById("row-" + id);
        let cells = row.getElementsByTagName('td');
        document.getElementById("modalTitle").textContent = "Редактировать преподавателя";
        document.getElementById("teacherId").value = id;
        document.getElementById("modalNameTeacher").value = cells[0].textContent;
        document.getElementById("modalDescription").value = cells[1].textContent;
        document.getElementById("modalPhoneNumber").value = cells[2].textContent;
        document.getElementById("modalEmail").value = cells[3].textContent;
        // document.getElementById("modalPhoto").value = cells[4].textContent;

        // Устанавливаем курс по названию
        let courseName = cells[4].textContent.trim();
        let select = document.getElementById("modalCourseId");
        let found = false;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].text.trim() === courseName) {
                select.selectedIndex = i;
                found = true;
                break;
            }
        }
        // Если не найден — сбросим выбор
        if (!found) {
            select.selectedIndex = 0;
        }
        document.getElementById("teacherModal").style.display = "block";
    }


    // Функция для сохранения изменений преподавателя
    // Функция для сохранения изменений преподавателя
    function saveTeacher() {

        let id = document.getElementById("teacherId").value;
        let name_teacher = document.getElementById("modalNameTeacher").value;
        let description = document.getElementById("modalDescription").value;
        let phone_number = document.getElementById("modalPhoneNumber").value;
        let email = document.getElementById("modalEmail").value;
        let course_id = document.getElementById("modalCourseId").value;
        let course_name = document.getElementById("modalCourseId").selectedOptions[0].text;
        let photo = document.getElementById("modalPhoto").files[0]; // Получаем файл фото

        let formData = new FormData();
        formData.append('action', 'edit');
        formData.append('id', id);
        formData.append('name_teacher', name_teacher);
        formData.append('description', description);
        formData.append('phone_number', phone_number);
        formData.append('email', email);
        formData.append('course_id', course_id);

        // Если новое фото было выбрано, добавляем его в FormData
        if (photo) {
            formData.append('photo', photo);
        }

        fetch('admin/api/edit-teacher.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Обновляем данные в таблице
                    let row = document.getElementById("row-" + id);
                    row.children[0].textContent = name_teacher;
                    row.children[1].textContent = description;
                    row.children[2].textContent = phone_number;
                    row.children[3].textContent = email;
                    row.children[4].textContent = course_name;

                    // Обновляем фото, если оно было загружено
                    if (photo) {
                        row.children[5].innerHTML = `<img src="../uploads/${data.photo}" width="50">`;
                    }

                    closeTeacherModal(); // Закрываем модальное окно
                } else {
                    alert("Ошибка при сохранении изменений");
                }
            })
            .catch(error => {
                console.error("Ошибка:", error);
                alert("Ошибка при сохранении изменений");
            });
    }



    // Функция для удаления преподавателя
    function deleteTeacher(id) {
        document.getElementById("deleteTeacherId").value = id;
        document.getElementById("deleteTeacherModal").style.display = "block";
    }

    // Подтверждение удаления преподавателя
    function confirmDeleteTeacher() {
        let id = document.getElementById("deleteTeacherId").value;

        fetch('admin/api/edit-teacher.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'delete',
                    id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById("row-" + id).remove();
                    closeTeacherModal();
                } else {
                    alert("Ошибка при удалении преподавателя");
                }
            });
    }


    // Закрытие модальных окон
    function closeTeacherModal() {
        document.getElementById("teacherModal").style.display = "none";
        document.getElementById("deleteTeacherModal").style.display = "none";
    }
</script>
<?php
$stmt = null;
$pdo = null;
include "../components/footer.php" ?>