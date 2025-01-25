<?php
session_start();
require 'db.php';

// Проверка на существующего пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Хеширование пароля
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Проверка на существование пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<p>Пользователь с таким логином уже существует. Попробуйте другой.</p>";
        exit;
    }

    // Добавление нового пользователя
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $role]);

    // Перенаправление на страницу входа после регистрации
    header("Location: ../index.php");
    exit;
}
?>
