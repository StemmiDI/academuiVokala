<?php
include "../app/db.php";
// Получаем абонементы
$stmt = $pdo->prepare("SELECT * FROM subscriptions");
$stmt->execute();
$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include "../components/head_admin.php";
include "../components/header-outh.php"; ?>
<section class="pricing-section">
    <div class="pricing-container">
        <img src="../uploads/фон.png" class="background-image" alt="Background image" />
        <div class="content-wrapper-card">
            <h1 class="pricing-title-ad-s">Управление абонементами</h1>
            <div class="pricing-cards" space="40">
                <div class="container">
                    <table>
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Уровень</th>
                                <th>Кол-во занятий</th>
                                <th>Цена</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="subscriptionTable">
                            <?php foreach ($subscriptions as $sub): ?>
                                <tr id="row-<?php echo $sub['id']; ?>">
                                    <td><?php echo htmlspecialchars($sub['name_sub']); ?></td>
                                    <td><?php echo htmlspecialchars($sub['level']); ?></td>
                                    <td><?php echo $sub['number_of_lesson']; ?></td>
                                    <td><?php echo $sub['price']; ?> руб.</td>
                                    <td>
                                        <button class="edit" onclick="editSubscription(<?php echo $sub['id']; ?>)">Изменить</button>
                                        <button class="delete" onclick="deleteSubscription(<?php echo $sub['id']; ?>)">Удалить</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>


                    <div class="add-form">
                        <h2>Добавить абонемент</h2>
                        <input type="text" id="name" placeholder="Название">
                        <input type="text" id="level" placeholder="Уровень">
                        <input type="number" id="number_of_lesson" placeholder="Кол-во занятий">
                        <input type="number" id="price" placeholder="Цена">
                        <button onclick="addSubscription()">Добавить</button>
                    </div>
                </div>
                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <div class="modal-wrap">
                            <h2>Редактировать абонемент</h2>
                            <input type="hidden" id="edit-id">
                            <input type="text" id="edit-name" placeholder="Название">
                            <input type="text" id="edit-level" placeholder="Уровень">
                            <input type="number" id="edit-number_of_lesson" placeholder="Кол-во занятий">
                            <input type="number" id="edit-price" placeholder="Цена">
                        </div>
                        <button class="addd-btn" onclick="saveChanges()">Сохранить</button>
                    </div>
                </div>

            </div>
            <!-- Модальное окно для подтверждения удаления -->
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeDeleteModal()">&times;</span>
                    <h2>Вы уверены, что хотите удалить абонемент?</h2>
                    <button class="delete" onclick="deleteSubscriptionConfirmed()">Удалить</button>
                    <button class="cancel-btn" onclick="closeDeleteModal()">Отменить</button>
                </div>
            </div>


        </div>
        <script>
            function addSubscription() {
                const name = document.getElementById('name').value;
                const level = document.getElementById('level').value;
                const number_of_lesson = document.getElementById('number_of_lesson').value;
                const price = document.getElementById('price').value;

                if (!name || !level || !number_of_lesson || !price) {
                    alert('Пожалуйста, заполните все поля!');
                    return;
                }

                fetch('admin/api/subscription-handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'add',
                            name_sub: name,
                            level: level,
                            number_of_lesson: number_of_lesson,
                            price: price
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Создаем новую строку таблицы
                            const newRow = document.createElement('tr');
                            newRow.id = 'row-' + data.id; // Используем ID, который возвращается с сервера

                            // Заполняем ячейки новой строки
                            newRow.innerHTML = `
                    <td>${data.name_sub}</td>
                    <td>${data.level}</td>
                    <td>${data.number_of_lesson}</td>
                    <td>${data.price} руб.</td>
                    <td>
                        <button class="edit" onclick="editSubscription(${data.id})">Изменить</button>
                        <button class="delete" onclick="deleteSubscription(${data.id})">Удалить</button>
                    </td>
                `;

                            // Добавляем новую строку в таблицу
                            document.getElementById('subscriptionTable').appendChild(newRow);

                            // Очищаем поля ввода
                            document.getElementById('name').value = '';
                            document.getElementById('level').value = '';
                            document.getElementById('number_of_lesson').value = '';
                            document.getElementById('price').value = '';
                        } else {
                            alert('Ошибка при добавлении абонемента');
                        }
                    })
                    .catch(err => {
                        console.error('Ошибка:', err);
                        alert('Ошибка при добавлении абонемента');
                    });

            }

            function editSubscription(id) {
                // Получаем строку из таблицы по id
                const row = document.getElementById('row-' + id);
                const cells = row.getElementsByTagName('td');

                // Заполняем поля модального окна
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-name').value = cells[0].innerText;
                document.getElementById('edit-level').value = cells[1].innerText;
                document.getElementById('edit-number_of_lesson').value = cells[2].innerText;
                document.getElementById('edit-price').value = cells[3].innerText.replace(' руб.', '');

                // Показываем модалку
                document.getElementById('editModal').style.display = 'block';
            }

            function closeModal() {
                document.getElementById('editModal').style.display = 'none';
            }

            function saveChanges() {
                const id = document.getElementById('edit-id').value;
                const name = document.getElementById('edit-name').value;
                const level = document.getElementById('edit-level').value;
                const number_of_lesson = document.getElementById('edit-number_of_lesson').value;
                const price = document.getElementById('edit-price').value;
                fetch('admin/api/subscription-handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'edit',
                            id: id,
                            name_sub: name,
                            level: level,
                            number_of_lesson: number_of_lesson,
                            price: price
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Обновим строку в таблице без перезагрузки
                            const row = document.getElementById('row-' + id);
                            row.children[0].innerText = name;
                            row.children[1].innerText = level;
                            row.children[2].innerText = number_of_lesson;
                            row.children[3].innerText = price + ' руб.';
                            closeModal();
                        } else {
                            alert('Ошибка при сохранении');
                        }
                    })
                    .catch(err => {
                        console.error('Ошибка:', err);
                        alert('Ошибка при сохранении');
                    });
            }
            let subscriptionIdToDelete = null;

            function deleteSubscription(id) {
                // Сохраняем id абонемента, который нужно удалить
                subscriptionIdToDelete = id;

                // Показываем модальное окно
                document.getElementById('deleteModal').style.display = 'block';
            }

            function closeDeleteModal() {
                // Закрыть модальное окно
                document.getElementById('deleteModal').style.display = 'none';
            }

            function deleteSubscriptionConfirmed() {
                // Отправляем запрос на удаление абонемента
                fetch('admin/api/subscription-handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'delete',
                            id: subscriptionIdToDelete
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Удаляем строку из таблицы без перезагрузки
                            const row = document.getElementById('row-' + subscriptionIdToDelete);
                            row.remove();

                            // Закрываем модальное окно
                            closeDeleteModal();
                        } else {
                            alert('Ошибка при удалении');
                        }
                    })
                    .catch(err => {
                        console.error('Ошибка:', err);
                        alert('Ошибка при удалении');
                    });
            }
        </script>
</section>
<?php
include "../components/footer.php" ?>