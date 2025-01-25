<?php
require '../backend/auth.php';
checkRole('teacher');

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
    <title>DATAPULSE - Загрузка статьи</title>
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
                    <li><i class="fa fa-line-chart" aria-hidden="true"></i><a href="../dean/view_stats.php">Общая статистика</a></li>
                <?php endif; ?>
            </ul>

            <div class="b-block">
                <a class="back" href="../admin/manage_users.php"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Назад</a>
            </div>
        </section>
        <div class="content">
            <h1>Загрузка статьи</h1>
            <div class="form">
                <form method="POST" enctype="multipart/form-data" action="../backend/submit_article.php">
                    <input type="text" name="title" placeholder="Название статьи" required>
                    <textarea name="content" placeholder="Текст статьи" rows="10" required></textarea>
                    <input type="file" name="pdf_file" accept="application/pdf" required>
                    <button type="submit">Загрузить</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    // Обработка загрузки статьи
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once '../backend/db.php';
        require_once '../backend/scopus_api.php'; // Предполагаем, что Scopus API уже настроен

        $title = $_POST['title'];
        $content = $_POST['content'];
        $teacherId = $_SESSION['user_id'];

        // Проверка уникальности через Scopus API
        $uniquenessScore = checkUniquenessWithScopus($content);

        // Расчет баллов KPI
        $kpiPoints = calculateKpiPoints($uniquenessScore);

        // Сохранение статьи в базе данных
        $stmt = $pdo->prepare("
            INSERT INTO articles (teacher_id, title, content, uniqueness_score, kpi_points, status)
            VALUES (:teacher_id, :title, :content, :uniqueness_score, :kpi_points, 'pending')
        ");
        $stmt->execute([
            ':teacher_id' => $teacherId,
            ':title' => $title,
            ':content' => $content,
            ':uniqueness_score' => $uniquenessScore,
            ':kpi_points' => $kpiPoints
        ]);

        echo "<p>Статья успешно загружена! Уникальность: $uniquenessScore%. Баллы KPI: $kpiPoints.</p>";
    }

    // Функция расчета KPI на основе уникальности
    function calculateKpiPoints($uniquenessScore)
    {
        if ($uniquenessScore >= 90) {
            return 10;
        } elseif ($uniquenessScore >= 75) {
            return 7;
        } elseif ($uniquenessScore >= 50) {
            return 5;
        } else {
            return 0;
        }
    }
    ?>
</body>

</html>
