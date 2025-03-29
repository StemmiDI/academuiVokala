<?php
session_start();
include "db.php"; // Include the database connection

$email = $_POST['email'];
$password = $_POST['password'];

$query = $pdo->prepare("SELECT * FROM users WHERE email= ?"); // Using $pdo for the database connection
$query->execute([$email]);
$row = $query->fetch();
if ($row && password_verify($password, $row['password'])) { // Verifying the hashed password
    $_SESSION['id_user'] = $row['id_user'];
    if ($password == "adminka123") {
        $_SESSION['admin'] = true;
        header("Location: ../admin/admin.php");
    } else {
        header("Location: ../user_profile.php");
    }
} else {
    echo "Не верные данные для входа";
}
