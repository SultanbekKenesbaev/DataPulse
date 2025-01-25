<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATAPULSE - Регистрация</title>
    <link rel="stylesheet" href="./assets/css/form.css">
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
<video autoplay muted loop class="background-video">
      <source src="./assets/video/6266-190550868_large.mp4" type="video/mp4" />
    </video>
    <div class="box">
        <div class="block">
            <div class="child">
                <h1>Регистрация в <span> DATAPULSE</span></h1>
                <form class="form" action="backend/register.php" method="POST">
                    <input type="text" name="username" placeholder="Логин" required>
                    <input type="password" name="password" placeholder="Пароль" required>
                    <select name="role" required>
                        <option value="teacher">Преподаватель</option>
                        <option value="head_of_department">Заведующий кафедрой</option>
                        <option value="dean">Декан</option>
                        <option value="admin">Администратор</option>
                    </select>
                    <button type="submit">Зарегистрироваться</button>
                </form>
                <p>Уже есть аккаунт? <a href="index.php">Войдите</a></p>
            </div>
        </div>
    </div>
</body>

</html>