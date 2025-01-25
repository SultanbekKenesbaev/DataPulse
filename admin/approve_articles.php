<?php
session_start();
require '../backend/auth.php';
checkRole('admin');  // Проверяем, что пользователь — администратор

require '../backend/db.php';

// Получаем статьи, которые нужно оценить (пока score = 0)
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.file_path, u.username AS teacher_name
    FROM articles a
    JOIN users u ON a.teacher_id = u.id
    WHERE a.score = 0
");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Оценка статей</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/icon/css/font-awesome.css">
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/interface.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="role">
                <p>Ваша роль: <?php echo htmlspecialchars($user_role); ?></p>
            </div>
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
            <h1>Оценка статей</h1>

            <?php if (empty($articles)): ?>
                <p>Нет статей для оценки.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Название статьи</th>
                            <th>Преподаватель</th>
                            <th>Скачать</th>
                            <th>Оценка</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($article['title']); ?></td>
                                <td><?php echo htmlspecialchars($article['teacher_name']); ?></td>
                                <td><a href="view_pdf.php?id=<?php echo $article['id']; ?>" target="_blank">Просмотреть PDF</a></td>
                                <td>
                                    <form method="POST" action="submit_score.php">
                                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                        <input type="number" name="score" min="1" max="100" required placeholder="Введите балл">
                                        <button type="submit">Оценить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
</body>

</html>