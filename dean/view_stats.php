<?php
require '../backend/auth.php';
checkRole('dean');  // Проверка, что пользователь — декан

require '../backend/db.php';

// Получаем общую статистику по университету
$stmt = $pdo->query("
    SELECT COUNT(id) AS articles_count, AVG(score) AS average_score
    FROM articles
");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>DATAPULSE - Общая статистика</title>
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
                <a class="back" href="../dashboard.php"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Назад</a>
            </div>
        </section>
        <div class="content">
            <h1>Общая статистика университета</h1>

            <table>
                <thead>
                    <tr>
                        <th>Общее количество статей</th>
                        <th>Средний балл</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $stats['articles_count']; ?></td>
                        <td><?php echo number_format($stats['average_score'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>