<?php
$id = trim($_GET["id"] ?? "");
$data = file_exists("links.json") ? json_decode(file_get_contents("links.json"), true) : [];

if (!isset($data[$id])) {
    http_response_code(404);
    echo "Link not found.";
    exit;
}

$link = $data[$id];
$url = $link["url"];
$desc = $link["desc"];
$timer = intval($link["timer"] ?? 5);
$image = $link["image"];
$host = $_SERVER["HTTP_HOST"];
$full = "https://".$host."/".htmlspecialchars($id, ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta property="og:title" content="<?php echo htmlspecialchars($desc ?: '🈷 PLAY VIDEO 🈂😊"><', ENT_QUOTES); ?>">
<?php if(!empty($image)): ?>
<meta property="og:image" content="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].'/'.$image, ENT_QUOTES); ?>">
<?php endif; ?>
<meta property="og:description" content="<?php echo htmlspecialchars($desc, ENT_QUOTES); ?>">
<meta name="twitter:card" content="summary_large_image">
<title>Opening…</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#0f0f0f;color:#eee;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;padding:20px}
.card{background:#121212;padding:20px;border-radius:12px;max-width:640px;width:100%;text-align:center;box-shadow:0 8px 30px rgba(0,0,0,.6)}
img{max-width:100%;border-radius:10px;margin-bottom:12px}
.count{font-size:22px;color:#9bd; margin-top:8px}
.button{background:#2196f3;color:#fff;padding:10px 14px;border-radius:8px;border:none;cursor:pointer;margin-top:10px}
.small{color:#9b9b9b;font-size:13px;margin-top:8px}
</style>
</head>
<body>
<div class="card">
    <?php if(!empty($image)): ?>
        <img src="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>" alt="">
    <?php endif; ?>
    <div><?php echo nl2br(htmlspecialchars($desc)); ?></div>
    <div class="count">Redirecting in <span id="c"><?php echo $timer; ?></span> seconds…</div>
    <button class="button" id="skip">Go Now</button>
    <div class="small"></div>
</div>

<script>
var t = <?php echo $timer; ?>;
var url = <?php echo json_encode($url); ?>;
var iv = setInterval(()=>{
    t--;
    document.getElementById('c').textContent = t;
    if(t<=0){
        clearInterval(iv);
        window.location.href = url;
    }
},1000);
document.getElementById('skip').addEventListener('click', ()=>{ window.location.href = url; });
</script>
</body>
</html>
