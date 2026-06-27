<?php
require __DIR__ . '/SentimentAnalyzer.php';

$comments = "This is lovely";
$sentimentType = getSentimentType($comments);

echo "Sentiment: $sentimentType";