<?php
require __DIR__ . '/../vendor/autoload.php';

use Sentiment\Analyzer;

// Initialize sentiment analyzer
$sentiment = new Sentiment\Analyzer();

// Load custom words (optional)
$json = file_get_contents(__DIR__ . '/words1.json');
$newWords = json_decode($json, true);
$sentiment->updateLexicon($newWords);

header('Content-Type: application/json');

// List of neutralizer words that should nullify sentiment
$neutralizers = ['naman', 'lang', 'lamang', 'po', 'ho', 'na', 'siguro', 'kasi', 'medyo', 'konti', 'kaunti', 'pa'];
$THRESHOLD = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review = trim($_POST['review'] ?? '');

    if (empty($review)) {
        echo json_encode(['error' => 'Review cannot be empty']);
        exit;
    }

    // Split review into words
    $words = preg_split('/\s+/', $review);

    $adjustedScores = [];
    foreach ($words as $index => $word) {
        // Get sentiment score of this word
        $score = $newWords[$word] ?? ($sentiment->getSentiment($word)['compound'] ?? 0);

        // If the next word is a neutralizer, set score to 0
        if (isset($words[$index + 1]) && in_array($words[$index + 1], $neutralizers)) {
            $score = 0;
        }

        $adjustedScores[] = $score;
    }

    // Compute overall score
    $overallScore = array_sum($adjustedScores) / max(1, count($adjustedScores));

    if ($overallScore > $THRESHOLD) {
        $sentimentType = 'Positive';
    } elseif ($overallScore < -$THRESHOLD) {
        $sentimentType = 'Negative';
    } else {
        $sentimentType = 'Neutral';
    }

    echo json_encode([
        'scores' => [
            'compound' => $overallScore
        ],
        'sentiment' => $sentimentType
    ]);
    exit;
}

echo json_encode(['error' => 'Invalid request']);
?>