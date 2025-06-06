<footer class="footer">
    <div class="footer-content">
        <div class="footer-bottom">
            <div class="footer-logo"><img src="../img/logoDark.jpg" alt="">
                <p class="footer-bottom-text">© 2025 Школа вокала Чистый голос. Все права защищены.</p>
            </div>
            <nav class="footer-nav">
                <h4 class="footer-nav-title">Меню</h4>
                <a href="../index.php#about" class="footer-nav-text">О нас</a>
                <a href="../index.php#courses" class="footer-nav-text">Курсы</a>
                <a href="../index.php#price" class="footer-nav-text">Цены</a>
                <a href="../index.php#ped" class="footer-nav-text">Преподаватели</a>
                <a href="../index.php#reviews" class="footer-nav-text">Отзывы</a>
            </nav>
            <div class="footer-address">
                <h4 class="footer-address-title">Контакты</h4>
                <p class="footer-address-text">Адрес: г. Челябинск, ул. Елькина, д. 59</p>
                <p class="footer-address-text">Телефон: +7 (999) 234 45 32</p>
                <p class="footer-address-text">Почта: info@academy-vocal.ru</p>
                <p class="footer-address-text">Режим работы: Пн-Пт 10:00-21:00, Сб-Вс 11:00-18:00</p>
            </div>
        </div>
    </div>
    <script>
        function showCookieNotification(selectorAndKey, data) {
            const isTrueLocalStorage = localStorage.getItem(selectorAndKey) === "true";
            if (isTrueLocalStorage) return;

            const nodeHTML = `<div class="cookie-notification notification" hidden>
      <div class="notification__content">
        ${data.text} <a href="${data.link}">рекомендательные технологии</a>
      </div>
      <button class="notification__button btn" type="button">ОК</button>
    </div>`;
            document.querySelector("body").insertAdjacentHTML("beforeend", nodeHTML);
            const notification = document.querySelector("." + selectorAndKey);
            const button = notification?.querySelector(".notification__button");

            if (!notification || !button) return;
            notification.hidden = false;

            button.addEventListener(
                "click",
                () => {
                    notification.hidden = true;
                    localStorage.setItem(selectorAndKey, "true");
                }, {
                    once: true
                }
            );
        }

        const cookieNotificationData = {
            text: "Мы собираем файлы cookie и применяем",
            link: "cookies-policy.php", // ссылка на документ
        };
        showCookieNotification("cookie-notification", cookieNotificationData);
    </script>
</footer>
</body>

</html>