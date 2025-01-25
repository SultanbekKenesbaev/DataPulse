<?php
session_start();
require '../backend/auth.php';
checkRole('admin');  // Проверка роли администратора

require '../backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'];
    $score = (int)$_POST['score'];

    // Обновляем балл статьи
    $stmt = $pdo->prepare("UPDATE articles SET score = ? WHERE id = ?");
    $stmt->execute([$score, $article_id]);

    // Перенаправляем обратно на страницу оценки статей
    header("Location: approve_articles.php");
    exit;
}
?>
