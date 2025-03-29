<style>
    /* Base styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: "Inter", sans-serif;
        background-color: #fff;
    }

    /* Dashboard container */
    .dashboard-container {
        display: flex;
        min-height: 100vh;
        background-color: #fff;
        max-width: none;
        margin-left: auto;
        margin-right: auto;
    }

    /* Sidebar styles */
    .sidebar {
        width: 297px;
        display: flex;
        flex-direction: column;
        padding: 54px 27px;
        gap: 20px;
        background-color: #fff;
    }

    /* Profile card */
    .profile-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        border-radius: 10px;
        padding: 30px 20px;
        background-color: #b394ff;
    }

    .profile-image {
        width: 218px;
        height: 179px;
        border-radius: 15px;
    }

    .profile-name {
        color: #fff;
        font-family: "Inter", sans-serif;
        font-size: 20px;
        text-align: center;
        font-weight: normal;
    }

    /* User info section */
    .user-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-card {
        padding: 11px 14px;
        border-radius: 4px;
        background-color: #b394ff;
    }

    .info-label {
        color: #fff;
        font-family: "Inter", sans-serif;
        font-size: 12px;
        margin-bottom: 5px;
        font-weight: normal;
    }

    .info-value {
        color: #fff;
        font-family: "Inter", sans-serif;
        font-size: 12px;
    }

    .logout-button {
        color: #fff;
        font-family: "Inter", sans-serif;
        font-size: 15px;
        font-weight: 800;
        text-align: center;
        margin-top: 15px;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
    }

    /* Subscription card */
    .subscription-card {
        border-radius: 10px;
        padding: 18px 14px;
        background-color: #b394ff;
    }

    .subscription-title {
        color: #fff;
        font-family: "Inter", sans-serif;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 20px;
    }

    .subscription-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .subscription-item {
        display: flex;
        justify-content: space-between;
        color: #fff;
        font-family: "Inter", sans-serif;
        font-size: 12px;
    }

    .payment-button {
        color: #fff;
        font-family: "Inter", sans-serif;
        font-size: 12px;
        padding: 8px 14px;
        border-radius: 10px;
        border: none;
        width: 100%;
        margin-top: 30px;
        cursor: pointer;
        background-color: #000;
    }

    /* Main content area */
    .main-content {
        flex: 1;
        padding: 73px 33px 73px 33px;
        background-image: url("https://cdn.builder.io/api/v1/image/assets/TEMP/d0e6634ef8cd06d7731f0987e75f0b8c966a11a4");
        background-size: cover;
        background-position: center;
        opacity: 0.56;
        padding-right: 76px;
    }

    /* Welcome section */
    .welcome-section {
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 40px;
        background-color: #e5dbff;
    }

    .welcome-title {
        color: #000;
        font-family: "Inter", sans-serif;
        font-size: 40px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: normal;
    }

    .welcome-message {
        color: #000;
        font-family: "Inter", sans-serif;
        font-size: 24px;
    }

    /* Course info section */
    .course-info {
        display: flex;
        gap: 30px;
        margin-bottom: 40px;
    }

    .course-card {
        border-radius: 10px;
        padding: 20px;
        flex: 1;
        background-color: #fff;
    }

    .course-detail {
        color: #000;
        font-family: "Inter", sans-serif;
        font-size: 24px;
        text-align: center;
    }

    /* Schedule section */
    .schedule-section {
        border-radius: 10px;
        padding: 40px;
        background-color: #fff;
    }

    .schedule-title {
        color: #0d0c10;
        font-family: "Bricolage Grotesque", sans-serif;
        font-size: 48px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 40px;
    }

    .schedule-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Weekdays row */
    .weekdays-row {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        text-align: center;
        margin-bottom: 20px;
    }

    .weekday {
        color: #000;
        font-family: "Inter", sans-serif;
        font-size: 24px;
        font-weight: 700;
        text-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    }

    /* Schedule content */
    .schedule-content {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .time-row,
    .class-row {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        text-align: center;
    }

    .time-slot {
        color: #222;
        font-family: "Inter", sans-serif;
        font-size: 16px;
        font-weight: 600;
    }

    .class-name {
        color: #222;
        font-family: "Inter", sans-serif;
        font-size: 16px;
    }

    /* Responsive styles */
    @media (max-width: 991px) {
        .dashboard-container {
            max-width: 991px;
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            padding: 20px;
        }

        .main-content {
            padding: 20px;
        }
    }

    @media (max-width: 640px) {
        .dashboard-container {
            max-width: 640px;
        }

        .course-info {
            flex-direction: column;
        }

        .schedule-container {
            overflow-x: auto;
        }

        .weekdays-row,
        .time-row,
        .class-row {
            grid-template-columns: repeat(7, 150px);
        }
    }
</style>
<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vocal Training Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Bricolage+Grotesque:wght@400;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <section class="profile-card">
                <img
                    src="https://cdn.builder.io/api/v1/image/assets/TEMP/db30bb838fffc99f6ff38d48f30d1743477b5302"
                    alt="Фото профиля"
                    class="profile-image" />
                <h2 class="profile-name">Юлия Зобкова</h2>
            </section>

            <section class="user-info">
                <div class="info-card">
                    <h3 class="info-label">Номер телефона</h3>
                    <p class="info-value">+7 (999) 999 99 99</p>
                </div>

                <div class="info-card">
                    <h3 class="info-label">Электроннная почта</h3>
                    <p class="info-value">yulis.08.zob@mail.ru</p>
                </div>

                <div class="info-card">
                    <h3 class="info-label">Возраст</h3>
                    <p class="info-value">17 лет</p>
                </div>

                <div class="info-card">
                    <h3 class="info-label">Уровень владения вокалом</h3>
                    <p class="info-value">Начинающий</p>
                </div>

                <button class="logout-button">Выход</button>
            </section>

            <section class="subscription-card">
                <h3 class="subscription-title">Абонименты</h3>

                <div class="subscription-list">
                    <div class="subscription-item">
                        <p>Базовый курс</p>
                        <p>8000</p>
                    </div>

                    <div class="subscription-item">
                        <p>Ускоренный курс</p>
                        <p>6000</p>
                    </div>

                    <div class="subscription-item">
                        <p>Премиум курс</p>
                        <p>10000</p>
                    </div>
                </div>

                <button class="payment-button">Оплатить абонимент</button>
            </section>
        </aside>

        <main class="main-content">
            <section class="welcome-section">
                <h1 class="welcome-title">Добро пожаловать, Юлия!</h1>
                <p class="welcome-message">С нами вы разовьёте свои навыки вокала</p>
            </section>

            <section class="course-info">
                <div class="course-card">
                    <p class="course-detail">Направление Рок вокал</p>
                </div>

                <div class="course-card">
                    <p class="course-detail">Абонемент Базовый</p>
                </div>
            </section>

            <section class="schedule-section">
                <h2 class="schedule-title">Раписание</h2>

                <div class="schedule-container">
                    <div class="weekdays-row">
                        <div class="weekday">ПН</div>
                        <div class="weekday">ВТ</div>
                        <div class="weekday">СР</div>
                        <div class="weekday">ЧТ</div>
                        <div class="weekday">ПТ</div>
                        <div class="weekday">СБ</div>
                        <div class="weekday">ВС</div>
                    </div>

                    <div class="schedule-content">
                        <!-- Row 1 -->
                        <div class="time-row">
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot"></div>
                            <div class="time-slot"></div>
                        </div>

                        <div class="class-row">
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name"></div>
                            <div class="class-name"></div>
                        </div>

                        <!-- Row 2 -->
                        <div class="time-row">
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot"></div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot"></div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot"></div>
                        </div>

                        <div class="class-row">
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name"></div>
                            <div class="class-name">Рок вокал</div>
                            <div class="class-name"></div>
                            <div class="class-name">Рок вокал</div>
                            <div class="class-name"></div>
                        </div>

                        <!-- Row 3 -->
                        <div class="time-row">
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot"></div>
                        </div>

                        <div class="class-row">
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name"></div>
                        </div>

                        <!-- Row 4 -->
                        <div class="time-row">
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                        </div>

                        <div class="class-row">
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                        </div>

                        <!-- Row 5 -->
                        <div class="time-row">
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                            <div class="time-slot">10:00 - 10:45</div>
                        </div>

                        <div class="class-row">
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                            <div class="class-name">Джазовый вокал</div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>