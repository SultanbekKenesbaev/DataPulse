<?php
session_start();
require 'db.php';

// Авторизация пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Проверка существования пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Если пароль верен, то создаем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Перенаправляем на Dashboard
        header("Location: ../dashboard.php");
        exit;
    } else {
        echo "<p>Неверный логин или пароль.</p>";
    }
}
?>
