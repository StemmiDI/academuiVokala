function editPhone() {
  document.getElementById("phone-text").style.display = "none";
  document.getElementById("edit-btn").style.display = "none";

  document.getElementById("phone-input").style.display = "inline-block";
  document.getElementById("save-btn").style.display = "inline-block";
}

function savePhone() {
  let newPhone = document.getElementById("phone-input").value;

  // Отправляем AJAX-запрос на сервер
  fetch("app/update_phone.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "phone=" + encodeURIComponent(newPhone),
  })
    .then((response) => response.text())
    .then((data) => {
      if (data === "success") {
        document.getElementById("phone-text").innerText = newPhone;
        document.getElementById("phone-text").style.display = "block";

        document.getElementById("phone-input").style.display = "none";
        document.getElementById("save-btn").style.display = "none";
        document.getElementById("edit-btn").style.display = "inline-block";
      } else {
        alert("Ошибка: " + data);
      }
    })
    .catch((error) => console.error("Ошибка:", error));
}
