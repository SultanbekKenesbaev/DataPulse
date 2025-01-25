<?php
session_start();
require 'db.php';

// Проверка, что пользователь авторизован как преподаватель
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $teacher_id = $_SESSION['user_id'];

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === 0) {
        $file = $_FILES['pdf_file'];
        $file_name = basename($file['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_ext === 'pdf') {
            $upload_dir = __DIR__ . '/../uploads/';
            $upload_path = $upload_dir . uniqid() . '.pdf';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Сохраняем данные в базу
                $stmt = $pdo->prepare("INSERT INTO articles (title, content, teacher_id, file_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $content, $teacher_id, $upload_path]);

                // Сообщение об успешной загрузке
                echo "<p>Статья успешно загружена!😁</p>";
            } else {
                echo "<p>Ошибка при загрузке файла.</p>";
            }
        } else {
            echo "<p>Пожалуйста, загрузите файл в формате PDF.</p>";
        }
    } else {
        echo "<p>Ошибка загрузки файла.</p>";
    }
} 
$upload_dir = 'uploads/';  // Папка для хранения файлов
$new_file_name = uniqid() . '.pdf';
$upload_path = __DIR__ . '/../' . $upload_dir . $new_file_name; // Абсолютный путь для сохранения

if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Относительный путь, который будет использоваться для скачивания
    $file_url = $upload_dir . $new_file_name;
    $stmt = $pdo->prepare("INSERT INTO articles (title, content, teacher_id, file_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $content, $teacher_id, $file_url]);
}

?>
