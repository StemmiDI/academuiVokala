<?php
include '../app/db.php';
include "../components/head_admin.php";
include "../components/header-outh.php";


// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $name = $_POST['name_sub'];
        $level = $_POST['level'];
        $number_of_lesson = $_POST['number_of_lesson'];
        $price = $_POST['price'];

        $stmt = $pdo->prepare("INSERT INTO subscriptions (name_sub, level, number_of_lesson, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $level, $number_of_lesson, $price]);

        $last_id = $pdo->lastInsertId();

        echo json_encode([
            'id' => $last_id,
            'name_sub' => $name,
            'level' => $level,
            'number_of_lesson' => $number_of_lesson,
            'price' => $price
        ]);
        exit;
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name_sub'];
        $level = $_POST['level'];
        $number_of_lesson = $_POST['number_of_lesson'];
        $price = $_POST['price'];

        $stmt = $pdo->prepare("UPDATE subscriptions SET name_sub = ?, level = ?, number_of_lesson = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $level, $number_of_lesson, $price, $id]);

        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM subscriptions WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Получаем абонементы
$stmt = $pdo->prepare("SELECT * FROM subscriptions");
$stmt->execute();
$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <img
            src="https://cdn.builder.io/api/v1/image/assets/TEMP/34b35be77066cf7bde7d158057384ba2235707a1?placeholderIfAbsent=true&apiKey=6a50d615cc27474d902ce93693fa09b5"
            class="background-image"
            alt="Background image" />
        <div class="content-wrapper-card">
            <h1 class="pricing-title-ad-s">Управление абонементами</h1>
            <div class="pricing-cards" space="40">
                <div class="container">
                    <h1>Управление абонементами</h1>
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
                <!-- Модальное окно для добавления/редактирования абонемента -->
                <div id="subscriptionModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeSubscriptionModal()">&times;</span>
                        <h2 id="modalTitle">Добавить абонемент</h2>
                        <input type="hidden" id="subscriptionId">
                        <input type="text" id="modalName" placeholder="Название" required>
                        <input type="text" id="modalLevel" placeholder="Уровень" required>
                        <input type="number" id="modalLessons" placeholder="Кол-во занятий" required>
                        <input type="number" id="modalPrice" placeholder="Цена" required>
                        <button onclick="saveSubscription()">Сохранить</button>
                    </div>
                </div>

                <!-- Модальное окно для подтверждения удаления -->
                <div id="deleteSubscriptionModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeSubscriptionModal()">&times;</span>
                        <h2>Вы уверены, что хотите удалить этот абонемент?</h2>
                        <input type="hidden" id="deleteSubscriptionId">
                        <button onclick="confirmDeleteSubscription()">Удалить</button>
                        <button onclick="closeSubscriptionModal()">Отмена</button>
                    </div>
                </div>

                <script>
                    function addSubscription() {
                        let name = document.getElementById('name').value;
                        let level = document.getElementById('level').value;
                        let number_of_lesson = document.getElementById('number_of_lesson').value;
                        let price = document.getElementById('price').value;

                        fetch('', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({
                                    action: 'add',
                                    name_sub: name,
                                    level,
                                    number_of_lesson,
                                    price
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                let table = document.getElementById('subscriptionTable');
                                let row = document.createElement('tr');
                                row.id = "row-" + data.id;
                                row.innerHTML = `
            <td>${data.name_sub}</td>
            <td>${data.level}</td>
            <td>${data.number_of_lesson}</td>
            <td>${data.price} руб.</td>
            <td>
                <button class="edit" onclick="editSubscription(${data.id})">Изменить</button>
                <button class="delete" onclick="deleteSubscription(${data.id})">Удалить</button>
            </td>
        `;
                                table.appendChild(row);

                                document.getElementById('name').value = "";
                                document.getElementById('level').value = "";
                                document.getElementById('number_of_lesson').value = "";
                                document.getElementById('price').value = "";
                            });
                    }

                    function editSubscription(id) {
                        let row = document.getElementById("row-" + id);
                        let name = row.children[0].textContent;
                        let level = row.children[1].textContent;
                        let number_of_lesson = row.children[2].textContent;
                        let price = row.children[3].textContent.replace(" руб.", "");

                        // Заполняем модальное окно данными
                        document.getElementById("modalTitle").textContent = "Редактировать абонемент";
                        document.getElementById("subscriptionId").value = id;
                        document.getElementById("modalName").value = name;
                        document.getElementById("modalLevel").value = level;
                        document.getElementById("modalLessons").value = number_of_lesson;
                        document.getElementById("modalPrice").value = price;

                        // Открываем модальное окно
                        document.getElementById("subscriptionModal").style.display = "block";
                    }

                    function saveSubscription() {
                        let id = document.getElementById("subscriptionId").value;
                        let name = document.getElementById("modalName").value;
                        let level = document.getElementById("modalLevel").value;
                        let number_of_lesson = document.getElementById("modalLessons").value;
                        let price = document.getElementById("modalPrice").value;

                        fetch('', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({
                                    action: 'edit',
                                    id,
                                    name_sub: name,
                                    level,
                                    number_of_lesson,
                                    price
                                })
                            })
                            .then(response => response.json())
                            .then(() => {
                                let row = document.getElementById("row-" + id);
                                row.children[0].textContent = name;
                                row.children[1].textContent = level;
                                row.children[2].textContent = number_of_lesson;
                                row.children[3].textContent = price + " руб.";

                                closeSubscriptionModal();
                            });
                    }

                    function deleteSubscription(id) {
                        document.getElementById("deleteSubscriptionId").value = id;
                        document.getElementById("deleteSubscriptionModal").style.display = "block";
                    }

                    function confirmDeleteSubscription() {
                        let id = document.getElementById("deleteSubscriptionId").value;

                        fetch('', {
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
                                closeSubscriptionModal();
                            });
                    }

                    function closeSubscriptionModal() {
                        document.getElementById("subscriptionModal").style.display = "none";
                        document.getElementById("deleteSubscriptionModal").style.display = "none";
                    }
                </script>
            </div>
        </div>
</section>
<?php
include "../components/footer.php" ?>