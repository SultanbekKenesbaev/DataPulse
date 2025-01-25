<?php
session_start();
require '../backend/auth.php';
checkRole('admin');  // Проверка роли администратора

require '../backend/db.php';

// Статистика по преподавателям
$teachers_stmt = $pdo->prepare("
    SELECT u.username AS teacher_name, COUNT(a.id) AS articles_count, AVG(a.score) AS average_score
    FROM users u
    JOIN articles a ON u.id = a.teacher_id
    WHERE u.role = 'teacher'
    GROUP BY u.id
");
$teachers_stmt->execute();
$teachers_stats = $teachers_stmt->fetchAll(PDO::FETCH_ASSOC);

// Статистика по кафедрам
$departments_stmt = $pdo->prepare("
    SELECT u.username AS head_name, COUNT(a.id) AS articles_count, AVG(a.score) AS average_score
    FROM users u
    LEFT JOIN articles a ON u.id = a.teacher_id
    WHERE u.role = 'head_of_department'
    GROUP BY u.id
");
$departments_stmt->execute();
$departments_stats = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Общая статистика по университету
$university_stmt = $pdo->query("
    SELECT COUNT(id) AS articles_count, AVG(score) AS average_score
    FROM articles
");
$university_stats = $university_stmt->fetch(PDO::FETCH_ASSOC);
?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_role = $_SESSION['role'];
?>
<?php
// Итоги по преподавателям
$total_teacher_articles = array_sum(array_column($teachers_stats, 'articles_count'));
$total_teacher_score = array_sum(array_column($teachers_stats, 'average_score')) / count($teachers_stats);

// Итоги по кафедрам
$total_department_articles = array_sum(array_column($departments_stats, 'articles_count'));
$total_department_score = array_sum(array_column($departments_stats, 'average_score')) / count($departments_stats);

// Общий итог уже имеется: $university_stats
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика - Админ панель</title>
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
                    <li><a href="../teacher/upload_article.php">Загрузить статью</a></li>
                    <li><a href="../teacher/view_stats.php">Моя статистика</a></li>
                <?php elseif ($user_role === 'head_of_department'): ?>
                    <li><a href="../head/view_stats.php">Статистика кафедры</a></li>
                <?php elseif ($user_role === 'dean'): ?>
                    <li><a href="../dean/view_stats.php">Общая статистика</a></li>
                <?php endif; ?>
            </ul>

            <div class="b-block">
                <a class="back" href="../dashboard.php"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Назад</a>
            </div>
        </section>
        <div class="content">
            <h1>Статистика университета</h1>
            <div class="graphic">
                <div>
                    <h2>Статистика по преподавателям</h2>
                    <canvas id="teachersChart" width="300" height="300"></canvas>
                </div>

                <div>
                    <h2>Статистика по кафедрам</h2>
                    <canvas id="departmentsChart" width="300" height="300"></canvas>
                </div>

                <div>
                    <h2>Общая статистика по университету</h2>
                    <canvas id="universityChart" width="300" height="300"></canvas>
                </div>
            </div>
            <h2>Статистика по преподавателям</h2>
            <table>
                <thead>
                    <tr>
                        <th>Преподаватель</th>
                        <th>Количество статей</th>
                        <th>Средний балл</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers_stats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['teacher_name']); ?></td>
                            <td><?php echo $stat['articles_count']; ?></td>
                            <td><?php echo number_format($stat['average_score'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h2>Статистика по кафедрам</h2>
            <table>
                <thead>
                    <tr>
                        <th>Заведующий кафедрой</th>
                        <th>Количество статей</th>
                        <th>Средний балл</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments_stats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['head_name']); ?></td>
                            <td><?php echo $stat['articles_count']; ?></td>
                            <td><?php echo number_format($stat['average_score'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h2>Общая статистика по университету</h2>
            <table>
                <thead>
                    <tr>
                        <th>Общее количество статей</th>
                        <th>Средний балл</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $university_stats['articles_count']; ?></td>
                        <td><?php echo number_format($university_stats['average_score'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Получаем данные из PHP (например, через JSON-объект, переданный на страницу)
        const teachersStats = <?php echo json_encode($teachers_stats); ?>;
        const departmentsStats = <?php echo json_encode($departments_stats); ?>;
        const universityStats = <?php echo json_encode($university_stats); ?>;

        // Функция для создания графика
        function createDoughnutChart(chartId, labels, data) {
            var ctx = document.getElementById(chartId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Статистика',
                        data: data,
                        backgroundColor: ['#ff9999', '#66b3ff', '#99ff99', '#ffcc99'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Статистика по преподавателям
        const teachersLabels = teachersStats.map(stat => stat.teacher_name);
        const teachersData = teachersStats.map(stat => stat.articles_count);

        // Статистика по кафедрам
        const departmentsLabels = departmentsStats.map(stat => stat.head_name);
        const departmentsData = departmentsStats.map(stat => stat.articles_count);

        // Общая статистика по университету
        const universityLabels = ['Общее количество статей', 'Средний балл'];
        const universityData = [
            universityStats.articles_count,
            universityStats.average_score
        ];

        // Создание графиков
        // График по преподавателям
        createDoughnutChart('teachersChart', teachersLabels, teachersData);

        // График по кафедрам
        createDoughnutChart('departmentsChart', departmentsLabels, departmentsData);

        // График по университету
        createDoughnutChart('universityChart', universityLabels, universityData);
    </script>
</body>

</html>