<?php
declare(strict_types=1);

function feedback_require_student(mysqli $conn): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (($_SESSION['role'] ?? '') !== 'student' || empty($_SESSION['user_id'])) {
        header('Location: ../../index.html');
        exit;
    }

    $stmt = $conn->prepare('SELECT student_id, student_name, college FROM students WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$student) {
        http_response_code(403);
        exit('Student profile not found.');
    }
    return $student;
}

function feedback_rating_sentiment(array $answers): array
{
    $scores = array_map(static fn ($answer): int => (int) $answer, $answers);
    $average = array_sum($scores) / count($scores);
    $positive = count(array_filter($scores, static fn (int $score): bool => $score >= 4));
    $neutral = count(array_filter($scores, static fn (int $score): bool => $score === 3));
    $negative = count(array_filter($scores, static fn (int $score): bool => $score <= 2));
    $total = count($scores);
    return [$average, round(($positive / $total) * 100, 2), round(($neutral / $total) * 100, 2), round(($negative / $total) * 100, 2)];
}

function feedback_csrf_token(): string
{
    if (empty($_SESSION['feedback_csrf'])) {
        $_SESSION['feedback_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['feedback_csrf'];
}

function feedback_college_name(string $college): string
{
    $names = [
        'BSA' => 'College of Business and Accountancy', 'CBA' => 'College of Business and Accountancy',
        'COED' => 'College of Education', 'COE' => 'College of Engineering',
        'CAS' => 'College of Arts and Sciences', 'CON' => 'College of Nursing',
        'CCS' => 'College of Computer Studies', 'CIHM' => 'College of Hospitality Management',
    ];
    $code = strtoupper(trim($college));
    return $names[$code] ?? ($college ?: 'College');
}

function feedback_college_color(string $college): string
{
    $colors = [
        'BSA' => '#FFB000', 'CBA' => '#FFB000', 'COED' => '#294789',
        'COE' => '#FB7528', 'CAS' => '#7600BC', 'CON' => '#fe86cd',
        'CCS' => '#C5C6C7', 'CIHM' => '#491615',
    ];
    return $colors[strtoupper(trim($college))] ?? '#7d7d7d';
}

function feedback_dashboard_url(string $college, string $page = 'submit'): string
{
    // The endpoint determines the dashboard from the active student record.
    // This prevents a stale or manually opened dashboard URL from changing it.
    $allowedPages = ['dashboard', 'submit', 'profile'];
    $page = in_array($page, $allowedPages, true) ? $page : 'submit';
    return '../../student_dashboard.php?page=' . $page;
}
