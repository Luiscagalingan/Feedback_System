<?php
require __DIR__ . '/../vendor/autoload.php';

use Sentiment\Analyzer;

/**
 * Returns only the sentiment type of a given text.
 *
 * @param string $review
 * @return string 'Positive', 'Negative', or 'Neutral'
 */
function getSentimentType(string $review): string
{
    $sentiment = new Analyzer();

    // Load custom words
    $json = file_get_contents(__DIR__ . '/words1.json');
    $newWords = json_decode($json, true) ?? [];
    $sentiment->updateLexicon($newWords);

    $review = trim($review);
    if (empty($review)) {
        return 'Neutral';
    }

    // Analyze full sentence
    $overallScore = $sentiment->getSentiment($review)['compound'] ?? 0;

    $THRESHOLD = 0;
    if ($overallScore > $THRESHOLD) {
        return 'Positive';
    } elseif ($overallScore < -$THRESHOLD) {
        return 'Negative';
    } else {
        return 'Neutral';
    }
}

