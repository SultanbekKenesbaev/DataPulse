<?php
session_start();
require '../backend/auth.php';
checkRole('admin');  // Проверяем, что пользователь — администратор

require '../backend/db.php';

// Получаем ID статьи из параметра запроса
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем путь к файлу из базы данных
$stmt = $pdo->prepare("SELECT file_path FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if ($article) {
    $file_path = '/data-kpi/' . htmlspecialchars($article['file_path']);
    header("Location: $file_path");
    exit;
} else {
    echo "Файл не найден.";
}
