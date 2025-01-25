<?php
session_start();
require_once '../backend/db.php'; // Подключение к базе данных

function checkUniquenessWithScopus($title, $content) {
    $apiKey = '132938bc931783afa4b96fbd2eac801f';
    $url = 'https://api.elsevier.com/content/search/scopus';

    // Подготовка запроса
    $query = http_build_query([
        'query' => "TITLE('$title') OR ABS('$content')",
        'apiKey' => $apiKey
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$url?$query");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!isset($data['search-results']['entry'])) {
        return 100; // Если совпадений нет, возвращаем 100% уникальности
    }

    // Считаем совпадения
    $totalResults = count($data['search-results']['entry']);
    return max(0, 100 - ($totalResults * 10)); // Пример оценки: 10% штраф за каждое совпадение
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../index.php');
        exit;
    }

    $teacherId = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Проверка уникальности через Scopus API
    $uniquenessScore = checkUniquenessWithScopus($title, $content);

    // Расчет баллов KPI
    $kpiPoints = ($uniquenessScore >= 90) ? 10 : (($uniquenessScore >= 75) ? 5 : 0);

    // Сохранение статьи в базу данных
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

    echo "Статья успешно загружена! Уникальность: $uniquenessScore%. Баллы KPI: $kpiPoints.";
}
?>
