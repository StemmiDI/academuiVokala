<?php
include 'app/db.php';
include "components/head.php";
include "components/header.php";
?>
<style>
    .container {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 8px;
        max-width: 600px;
        width: 100%;
    }

    h1 {
        font-size: 6rem;
        margin: 0;
        color: #e74c3c;
    }

    h2 {
        font-size: 1.5rem;
        color: #555;
    }

    p {
        color: #777;
        margin: 20px 0;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 20px;
        background-color: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 1.1rem;
        transition: background-color 0.3s ease;
    }

    .button:hover {
        background-color: #2980b9;
    }

    footer {
        margin-top: 40px;
        font-size: 0.8rem;
        color: #aaa;
    }
</style>
<div class="container">
    <h1>404</h1>
    <h2>Страница не найдена</h2>
    <p>К сожалению, запрашиваемая вами страница не существует или была удалена.</p>
    <a href="/" class="button">Вернуться на главную</a>
</div>
<?php
include "components/footer.php" ?>