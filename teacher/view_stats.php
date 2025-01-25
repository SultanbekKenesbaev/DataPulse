<?php
session_start();
require '../backend/auth.php';
checkRole('teacher'); // Проверяем, что пользователь — преподаватель

require '../backend/db.php';

// Получаем ID текущего преподавателя
$teacher_id = $_SESSION['user_id'];

// Запрашиваем статьи преподавателя с баллами
$stmt = $pdo->prepare("SELECT title, score, file_path FROM articles WHERE teacher_id = ?");
$stmt->execute([$teacher_id]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- <?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_role = $_SESSION['role'];
?> -->
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
                <a class="back" href="../admin/manage_users.php"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Назад</a>
            </div>
        </section>
        <div class="content">
            <h1>Моя статистика</h1>

            <?php if (empty($articles)): ?>
                <p>У вас пока нет загруженных работ.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Название работы</th>
                            <th>Баллы</th>
                            <th>Скачать</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($article['title']); ?></td>
                                <td><?php echo $article['score'] === 0 ? 'Оценка не выставлена' : $article['score']; ?></td>
                                <td><a href="<?php echo htmlspecialchars($article['file_path']); ?>" target="_blank">Скачать PDF</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>