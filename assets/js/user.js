function openPopup() {
  document.getElementById("payment-popup").style.display = "flex";
}
function closePopup() {
  document.getElementById("payment-popup").style.display = "none";
}
function updatePrice() {
  let subscription = document.getElementById("subscription");
  let selectedPrice = subscription.value;
  document.getElementById("total-price").innerText = selectedPrice;
}
function updatePrice() {
  let subscription = document.getElementById("subscription");
  let selectedPrice = subscription.value;
  let priceElement = document.getElementById("total-price");
  if (!isNaN(selectedPrice) && Number(selectedPrice) > 0) {
    priceElement.innerText = selectedPrice;
  } else {
    priceElement.innerText = "Ошибка";
  }
}
document.getElementById("payment-form").addEventListener("submit", function (event) {
  event.preventDefault();
  let formData = new FormData(this);
  fetch("app/process_payment.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      alert(data);
      closePopup(); // Закрываем попап после успешной отправки
    })
    .catch((error) => console.error("Ошибка:", error));
});
