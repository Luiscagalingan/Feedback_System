<?php

function isTagalog($text) {
    // A simple check for common Tagalog words. This is not foolproof but is a good start.
    // You can add more common words to this list.
    $tagalog_words = [
        'ang', 'ng', 'sa', 'mga', 'ay', 'ako', 'ikaw', 'siya', 'ito', 'iyan',
        'po', 'opo', 'salamat', 'maganda', 'masaya', 'hindi', 'bakit', 'paano',
        'kasi', 'talaga', 'naman', 'para', 'guro', 'estudyante'
    ];

    $text_lower = strtolower($text);
    $word_count = 0;

    foreach ($tagalog_words as $word) {
        if (strpos($text_lower, $word) !== false) {
            $word_count++;
        }
    }

    // If we find 3 or more common Tagalog words, assume it's Tagalog.
    return $word_count >= 3;
}