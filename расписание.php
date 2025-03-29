<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Расписание занятий</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      text-align: center;
      margin: 20px;
    }

    .schedule {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 10px;
      border: 1px solid #ddd;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>

<body>
  <div class="schedule">
    <h2>Расписание занятий</h2>
    <table>
      <thead>
        <tr>
          <th>День</th>
          <th>Время</th>
          <th>Предмет</th>
          <th>Преподаватель</th>
          <th>Аудитория</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $db = new PDO('mysql:host=localhost;dbname=your_db', 'user', 'password');
        $query = $db->query("SELECT day, time, subject, teacher, room FROM schedule ORDER BY day, time");
        foreach ($query as $row) {
          echo "
          <tr>
            <td>{$row['day']}</td>
            <td>{$row['time']}</td>
            <td>{$row['subject']}</td>
            <td>{$row['teacher']}</td>
          </tr>
          ";
        } ?>
      </tbody>
    </table>
  </div>
</body>

</html>