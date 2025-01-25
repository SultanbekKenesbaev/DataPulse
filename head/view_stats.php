<?php
require '../backend/auth.php';
checkRole('head_of_department');  // Проверка, что пользователь — заведующий кафедрой

require '../backend/db.php';

// Получаем статистику по преподавателям в рамках кафедры
$stmt = $pdo->prepare("
    SELECT u.username AS teacher_name, COUNT(a.id) AS articles_count, AVG(a.score) AS average_score
    FROM users u
    JOIN articles a ON u.id = a.teacher_id
    WHERE u.role = 'teacher' 
    GROUP BY u.id
");
$stmt->execute();
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATAPULSE - Статистика кафедры</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/icon/css/font-awesome.css">
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/interface.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="header-menu">
                <img src="#" alt="" class="img-prof">
                <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                <a href="../backend/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
            </div>
        </header>
        <section class="sidebar">
            <ul class="list">
                <?php if ($user_role === 'admin'): ?>
                    <li><i class="fa fa-star" aria-hidden="true"></i><a href="../admin/approve_articles.php">Оценка статей</a></li>
                    <li><i class="fa fa-user-o" aria-hidden="true"></i><a href="../admin/manage_users.php">Управление</a></li>
                    <li><i class="fa fa-line-chart" aria-hidden="true"></i><a href="../admin/view_stats.php">Статистика</a></li>
                <?php elseif ($user_role === 'teacher'): ?>
                    <li><i class="fa fa-download" aria-hidden="true"></i><a href="../teacher/upload_article.php">Загрузить статью</a></li>
                    <li><i class="fa fa-line-chart" aria-hidden="true"></i><a href="../teacher/view_stats.php">Моя статистика</a></li>
                <?php elseif ($user_role === 'head_of_department'): ?>
                    <li><i class="fa fa-line-chart" aria-hidden="true"></i><a href="../head/view_stats.php">Статистика кафедры</a></li>
                <?php elseif ($user_role === 'dean'): ?>
                    <li><i class="fa fa-line-chart" aria-hidden="true"></i><a href="../dean/view_stats.php"> Общая статистика</a></li>
                <?php endif; ?>
            </ul>

            <div class="b-block">
                <a class="back" href="../admin/manage_users.php"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Назад</a>
            </div>
        </section>
        <div class="content">
            <h1>Статистика кафедры</h1>
            <table>
                <thead>
                    <tr>
                        <th>Преподаватель</th>
                        <th>Количество статей</th>
                        <th>Средний балл</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['teacher_name']); ?></td>
                            <td><?php echo $stat['articles_count']; ?></td>
                            <td><?php echo number_format($stat['average_score'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>