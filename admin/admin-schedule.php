<?php
// Подключаем базу данных
include '../app/db.php';
include "../components/head_admin.php";
include "../components/header-outh.php";

// Получаем параметры сортировки из запроса (если они есть)
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'day_of_week'; // Сортировка по умолчанию по дню недели
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Направление сортировки по умолчанию ASC

// Создаем SQL запрос с учетом сортировки
$stmt = $pdo->prepare("
    SELECT s.id, c.name_course, ts.name_type_schedule, t.name_teacher, s.day_of_week, s.time
    FROM schedule s
    JOIN courses c ON s.course_id = c.id
    JOIN teachers t ON s.teacher_id = t.id
    JOIN type_schedule ts ON s.type_schedule_id = ts.id
    ORDER BY 
        CASE s.day_of_week
            WHEN 'Понедельник' THEN 1
            WHEN 'Вторник' THEN 2
            WHEN 'Среда' THEN 3
            WHEN 'Четверг' THEN 4
            WHEN 'Пятница' THEN 5
            WHEN 'Суббота' THEN 6
            WHEN 'Воскресенье' THEN 7
        END, s.time $order
");
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Получаем все курсы, преподавателей и типы расписания для селектов
$courses_stmt = $pdo->prepare("SELECT id, name_course FROM courses");
$courses_stmt->execute();
$courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

$teachers_stmt = $pdo->prepare("SELECT id, name_teacher FROM teachers");
$teachers_stmt->execute();
$teachers = $teachers_stmt->fetchAll(PDO::FETCH_ASSOC);

$type_schedule_stmt = $pdo->prepare("SELECT id, name_type_schedule FROM type_schedule");
$type_schedule_stmt->execute();
$type_schedules = $type_schedule_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>

</style>
<section class="pricing-section">
    <div class="pricing-container">
        <img src="../uploads/фон.png" class="background-image" alt="Background image" />
        <div class="content-wrapper-card">
            <h1 class="pricing-title-ad-s">Управление расписанием</h1>

            <section class="schedule-section">
                <div class="container">

                    <table>

                        <thead>
                            <tr>
                                <th>Курс</th>
                                <th>Тип расписания</th>
                                <th>Преподаватель</th>
                                <th>День недели</th>
                                <th>Время</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTable">
                            <?php foreach ($schedules as $schedule): ?>
                                <tr id="row-<?php echo $schedule['id']; ?>">
                                    <td><?php echo htmlspecialchars($schedule['name_course']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['name_type_schedule']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['name_teacher']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($schedule['time'], 0, 5)); ?></td>

                                    <td>
                                        <button class="edit" onclick="editSchedule(<?php echo $schedule['id']; ?>)">Изменить</button>
                                        <button class="delete" onclick="deleteSchedule(<?php echo $schedule['id']; ?>)">Удалить</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <form class="add-form">
                        <h2>Добавить расписание</h2>
                        <select id="course_id" required>
                            <option value="">Выберите курс</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo $course['name_course']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="type_schedule_id" required>
                            <option value="">Выберите тип расписания</option>
                            <?php foreach ($type_schedules as $type_schedule): ?>
                                <option value="<?php echo $type_schedule['id']; ?>"><?php echo $type_schedule['name_type_schedule']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="teacher_id" required>
                            <option value="">Выберите преподавателя</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name_teacher']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="day_of_week" required>
                            <option value="">Выберите день недели</option>
                            <option value="Понедельник">Понедельник</option>
                            <option value="Вторник">Вторник</option>
                            <option value="Среда">Среда</option>
                            <option value="Четверг">Четверг</option>
                            <option value="Пятница">Пятница</option>
                            <option value="Суббота">Суббота</option>
                            <option value="Воскресенье">Воскресенье</option>
                        </select>

                        <input type="time" id="time" placeholder="Время" required>
                        <button type="submit">Добавить</button>
                    </form>
                </div>

                <!-- Модальное окно для редактирования расписания -->
                <div id="scheduleModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeScheduleModal()">&times;</span>
                        <div class="modal-wrap">
                            <h2 id="modalTitle">Редактировать расписание</h2>
                            <input type="hidden" id="scheduleId">
                            <select id="modalCourseId">
                                <option value="">Выберите курс</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>"><?php echo $course['name_course']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="modalTypeScheduleId">
                                <option value="">Выберите тип расписания</option>
                                <?php foreach ($type_schedules as $type_schedule): ?>
                                    <option value="<?php echo $type_schedule['id']; ?>"><?php echo $type_schedule['name_type_schedule']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="modalTeacherId">
                                <option value="">Выберите преподавателя</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name_teacher']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <!-- <input type="text" id="modalDayOfWeek" placeholder="День недели"> -->
                            <select id="modalDayOfWeek" required>
                                <option value="">Выберите день недели</option>
                                <option value="Понедельник">Понедельник</option>
                                <option value="Вторник">Вторник</option>
                                <option value="Среда">Среда</option>
                                <option value="Четверг">Четверг</option>
                                <option value="Пятница">Пятница</option>
                                <option value="Суббота">Суббота</option>
                                <option value="Воскресенье">Воскресенье</option>
                            </select>
                            <input type="time" id="modalTime" placeholder="Время">
                        </div>
                        <button class="addd-btn" onclick="saveSchedule()">Сохранить</button>
                    </div>
                </div>

                <!-- Модальное окно для подтверждения удаления расписания -->
                <div id="deleteScheduleModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeScheduleModal()">&times;</span>
                        <h2>Вы уверены, что хотите удалить это расписание?</h2>
                        <input type="hidden" id="deleteScheduleId">
                        <button onclick="confirmDeleteSchedule()">Удалить</button>
                        <button onclick="closeScheduleModal()">Отмена</button>
                    </div>
                </div>

                <script>
                    // Update the sorting links to update the table with the new sorting order using AJAX

                    function sortTable(sort_by, order) {
                        fetch(`?sort_by=${sort_by}&order=${order}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json()) // Обрабатываем результат как JSON
                            .then(data => {
                                let table = document.querySelector('#scheduleTable');
                                table.innerHTML = ''; // Очищаем таблицу перед обновлением
                                data.schedules.forEach(schedule => {
                                    let row = document.createElement('tr');
                                    row.id = "row-" + schedule.id;
                                    row.innerHTML = `
                    <td>${schedule.name_course}</td>
                    <td>${schedule.name_type_schedule}</td>
                    <td>${schedule.name_teacher}</td>
                    <td>${schedule.day_of_week}</td>
                    <td>${schedule.time}</td>
                    <td>
                        <button class="edit" onclick="editSchedule(${schedule.id})">Изменить</button>
                        <button class="delete" onclick="deleteSchedule(${schedule.id})">Удалить</button>
                    </td>
                `;
                                    table.appendChild(row);
                                });
                            });
                    }


                    // Attach event listeners to each sorting link
                    document.querySelectorAll('.sort-options a').forEach(link => {
                        link.addEventListener('click', function(event) {
                            event.preventDefault();
                            const url = new URL(this.href);
                            const sort_by = url.searchParams.get('sort_by');
                            const order = url.searchParams.get('order');
                            sortTable(sort_by, order);
                        });
                    });
                    document.querySelector(".add-form").addEventListener("submit",
                        function addSchedule(e) {
                            e.preventDefault()
                            let course_id = document.getElementById('course_id').value;
                            let type_schedule_id = document.getElementById('type_schedule_id').value;
                            let teacher_id = document.getElementById('teacher_id').value;
                            let day_of_week = document.getElementById('day_of_week').value;
                            let time = document.getElementById('time').value;

                            fetch('admin/api/edit-schedule.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: new URLSearchParams({
                                        action: 'add',
                                        course_id,
                                        type_schedule_id,
                                        teacher_id,
                                        day_of_week,
                                        time
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    let table = document.getElementById('scheduleTable');
                                    let row = document.createElement('tr');
                                    row.id = "row-" + data.id;
                                    row.innerHTML = `
                        <td>${document.querySelector(`#course_id option[value="${data.course_id}"]`).textContent}</td>
                        <td>${document.querySelector(`#type_schedule_id option[value="${data.type_schedule_id}"]`).textContent}</td>
                        <td>${document.querySelector(`#teacher_id option[value="${data.teacher_id}"]`).textContent}</td>
                        <td>${data.day_of_week}</td>
                        <td>${data.time}</td>
                        <td>
                            <button class="edit" onclick="editSchedule(${data.id})">Изменить</button>
                            <button class="delete" onclick="deleteSchedule(${data.id})">Удалить</button>
                        </td>
                    `;
                                    table.appendChild(row);

                                    document.getElementById('course_id').value = "";
                                    document.getElementById('type_schedule_id').value = "";
                                    document.getElementById('teacher_id').value = "";
                                    document.getElementById('day_of_week').value = "";
                                    document.getElementById('time').value = "";
                                }).catch((e) => console.log(e));
                        })

                    function editSchedule(id) {
                        let row = document.getElementById("row-" + id);
                        let course_id = row.children[0].textContent;
                        let type_schedule = row.children[1].textContent;
                        let teacher_name = row.children[2].textContent;
                        let day_of_week = row.children[3].textContent;
                        let time = row.children[4].textContent;

                        document.getElementById("scheduleId").value = id;
                        document.getElementById("modalDayOfWeek").value = day_of_week;
                        document.getElementById("modalTime").value = time;

                        // Установить курс, тип расписания и преподавателя
                        let course_select = document.getElementById("modalCourseId");
                        let type_select = document.getElementById("modalTypeScheduleId");
                        let teacher_select = document.getElementById("modalTeacherId");

                        // Установить курс
                        for (let i = 0; i < course_select.options.length; i++) {
                            if (course_select.options[i].text === course_id) {
                                course_select.selectedIndex = i;
                                break;
                            }
                        }

                        // Установить тип расписания
                        for (let i = 0; i < type_select.options.length; i++) {
                            if (type_select.options[i].text === type_schedule) {
                                type_select.selectedIndex = i;
                                break;
                            }
                        }

                        // Установить преподавателя
                        for (let i = 0; i < teacher_select.options.length; i++) {
                            if (teacher_select.options[i].text === teacher_name) {
                                teacher_select.selectedIndex = i;
                                break;
                            }
                        }

                        document.getElementById("scheduleModal").style.display = "block";
                    }

                    function saveSchedule() {
                        let id = document.getElementById("scheduleId").value;
                        let course_id = document.getElementById("modalCourseId").value;
                        let type_schedule_id = document.getElementById("modalTypeScheduleId").value;
                        let teacher_id = document.getElementById("modalTeacherId").value;
                        let day_of_week = document.getElementById("modalDayOfWeek").value;
                        let time = document.getElementById("modalTime").value;

                        fetch('admin/api/edit-schedule.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({
                                    action: 'edit',
                                    id,
                                    course_id,
                                    type_schedule_id,
                                    teacher_id,
                                    day_of_week,
                                    time
                                })
                            })
                            .then(response => response.json())
                            .then(() => {
                                let row = document.getElementById("row-" + id);
                                row.children[0].textContent = document.querySelector(`#modalCourseId option[value="${course_id}"]`).textContent;
                                row.children[1].textContent = document.querySelector(`#modalTypeScheduleId option[value="${type_schedule_id}"]`).textContent;
                                row.children[2].textContent = document.querySelector(`#modalTeacherId option[value="${teacher_id}"]`).textContent;
                                row.children[3].textContent = day_of_week;
                                row.children[4].textContent = time;

                                closeScheduleModal();
                            }).catch((e) => console.log('error save edit:', e));
                    }

                    function deleteSchedule(id) {
                        document.getElementById("deleteScheduleId").value = id;
                        document.getElementById("deleteScheduleModal").style.display = "block";
                    }

                    function confirmDeleteSchedule() {
                        let id = document.getElementById("deleteScheduleId").value;

                        fetch('admin/api/edit-schedule.php', {
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
                            .then(() => {
                                document.getElementById("row-" + id).remove();
                                closeScheduleModal();
                            });
                    }

                    function closeScheduleModal() {
                        document.getElementById("scheduleModal").style.display = "none";
                        document.getElementById("deleteScheduleModal").style.display = "none";
                    }
                </script>
        </div>

    </div>

</section>
</section>
<?php
include "../components/footer.php" ?>