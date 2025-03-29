const months = [
  "Январь",
  "Февраль",
  "Март",
  "Апрель",
  "Май",
  "Июнь",
  "Июль",
  "Август",
  "Сентябрь",
  "Октябрь",
  "Ноябрь",
  "Декабрь",
];

const today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

const monthNameElem = document.getElementById("monthName");
const calendarTableElem = document.getElementById("calendarTable").getElementsByTagName("tbody")[0];

document.getElementById("prevMonth").addEventListener("click", () => changeMonth(-1));
document.getElementById("nextMonth").addEventListener("click", () => changeMonth(1));

function changeMonth(direction) {
  currentMonth += direction;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  } else if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  renderCalendar();
}

function renderCalendar() {
  monthNameElem.textContent = `${months[currentMonth]} ${currentYear}`;

  const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
  let firstDay = firstDayOfMonth.getDay(); // день недели для 1-го числа месяца

  // Сдвигаем, чтобы понедельник был первым днем недели
  if (firstDay === 0) {
    // Если это воскресенье (0), то считаем его как последний день недели (7)
    firstDay = 6;
  } else {
    firstDay -= 1; // Иначе сдвигаем все дни на 1, чтобы понедельник был 0
  }

  const lastDateOfMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  calendarTableElem.innerHTML = ""; // очищаем таблицу

  let row = document.createElement("tr");

  // Добавляем пустые ячейки до первого дня месяца
  for (let i = 0; i < firstDay; i++) {
    row.appendChild(document.createElement("td")).classList.add("empty");
  }

  // Добавляем дни месяца
  for (let day = 1; day <= lastDateOfMonth; day++) {
    if (row.children.length === 7) {
      calendarTableElem.appendChild(row);
      row = document.createElement("tr");
    }

    const cell = document.createElement("td");
    cell.textContent = day;

    // Выделяем текущий день
    if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
      cell.classList.add("today");
    }

    row.appendChild(cell);
  }

  if (row.children.length > 0) {
    calendarTableElem.appendChild(row);
  }
}

renderCalendar();
