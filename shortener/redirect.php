<?php
$urls = file_exists("urls.json") ? json_decode(file_get_contents("urls.json"), true) : [];

$code = trim($_GET['code'] ?? '');
if ($code && isset($urls[$code])) {
    $urls[$code]['clicks']++;
    file_put_contents("urls.json", json_encode($urls, JSON_PRETTY_PRINT));
    header("Location: " . $urls[$code]['url']);
    exit;
} else {
    echo "Invalid or expired link.";
}
