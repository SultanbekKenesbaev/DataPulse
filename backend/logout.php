<?php
session_start();
session_unset();  // Удаляем все переменные сессии
session_destroy(); // Разрушаем сессию

header("Location: ../index.php");  // Перенаправляем на главную страницу
exit;
?>
