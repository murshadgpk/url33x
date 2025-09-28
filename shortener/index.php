<?php
// Load existing URLs
$urls = file_exists("urls.json") ? json_decode(file_get_contents("urls.json"), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $longUrl = trim($_POST['url']);
    if (filter_var($longUrl, FILTER_VALIDATE_URL)) {
        $shortCode = substr(md5($longUrl . time()), 0, 6);
        $urls[$shortCode] = [
            "url" => $longUrl,
            "clicks" => 0,
            "created" => date("Y-m-d H:i:s")
        ];
        file_put_contents("urls.json", json_encode($urls, JSON_PRETTY_PRINT));
        $shortUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/" . $shortCode;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Shortener</title>
</head>
<body>
    <h2>Simple URL Shortener</h2>
    <form method="post">
        <input type="url" name="url" placeholder="Enter your long URL" required>
        <button type="submit">Shorten</button>
    </form>
    <?php if (!empty($shortUrl)): ?>
        <p><b>Short URL:</b> <a href="<?= $shortUrl ?>" target="_blank"><?= $shortUrl ?></a></p>
    <?php endif; ?>

    <h3>Existing Links</h3>
    <table border="1" cellpadding="5">
        <tr><th>Short</th><th>Original</th><th>Clicks</th><th>Created</th></tr>
        <?php foreach ($urls as $code => $data): ?>
            <tr>
                <td><a href="/<?= $code ?>" target="_blank"><?= $code ?></a></td>
                <td><?= htmlspecialchars($data['url']) ?></td>
                <td><?= $data['clicks'] ?></td>
                <td><?= $data['created'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
