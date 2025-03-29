<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Календарь</title>
  <link rel="stylesheet" href="my-style.css" />
</head>

<body>
  <div class="calendar-calendar">
    <header class="header-calendar-calendar">
      <button class="button-calendar-calendar" id="prevMonth">←</button>
      <span class="span-calendar-calendar" id="monthName"></span>
      <button class="button-calendar-calendar" id="nextMonth">→</button>
    </header>
    <table class="table-calendar-calendar" id="calendarTable">
      <thead class="thead-calendar-calendar">
        <tr class="tr-calendar-calendar">
          <th class="th-calendar-calendar">Пн</th>
          <th class="th-calendar-calendar">Вт</th>
          <th class="th-calendar-calendar">Ср</th>
          <th class="th-calendar-calendar">Чт</th>
          <th class="th-calendar-calendar">Пт</th>
          <th class="th-calendar-calendar">Сб</th>
          <th class="th-calendar-calendar">Вс</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script src="mainn.js"></script>
</body>

</html>