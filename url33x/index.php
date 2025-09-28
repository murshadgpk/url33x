<?php
function safe_filename($name){ return preg_replace('/[^A-Za-z0-9._-]/', '_', $name); }

$result = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $url = trim($_POST["url"] ?? "");
    $desc = trim($_POST["description"] ?? "");
    $timer = intval($_POST["timer"] ?? 5);
    $imgPath = "";

    // Validate URL basic
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        $result = "<div class='error'>Invalid URL. Please enter a full URL including http:// or https://</div>";
    } else {
        if (!empty($_FILES["image"]["name"])) {
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $fn = $_FILES["image"]["name"];
            $ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $result = "<div class='error'>Invalid image format. Allowed: jpg, jpeg, png, gif, webp</div>";
            } elseif ($_FILES["image"]["size"] > 5*1024*1024) {
                $result = "<div class='error'>Image too large. Max 5MB.</div>";
            } else {
                $updir = "uploads/";
                if (!is_dir($updir)) mkdir($updir, 0755, true);
                $newname = time() . "_" . safe_filename(basename($fn));
                $imgPath = $updir . $newname;
                move_uploaded_file($_FILES["image"]["tmp_name"], $imgPath);
            }
        }

        if (empty($result)) {
            $id = substr(md5(uniqid(rand(), true)), 0, 6);
            $data = file_exists("links.json") ? json_decode(file_get_contents("links.json"), true) : [];
            $data[$id] = [
                "url" => $url,
                "desc" => $desc,
                "timer" => max(1, $timer),
                "image" => $imgPath,
                "created" => time()
            ];
            file_put_contents("links.json", json_encode($data, JSON_PRETTY_PRINT));
            $short = "https://" . $_SERVER["HTTP_HOST"] . "/" . $id;
            $result = "<div class='result'>✅ Short Link: <input id='shortlink' readonly value='".htmlspecialchars($short, ENT_QUOTES)."' /> <button onclick='copyLink()' class='copy'>Copy</button></div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ai Vercel Link Creator</title>
<style>
:root{ --bg:#111; --card:#1e1e1e; --accent:#e53935; --muted:#cfcfcf; }
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;background:linear-gradient(180deg,#0b0b0b,#151515);color:var(--muted);display:flex;align-items:center;justify-content:center;height:100vh;margin:0;padding:20px;}
.container{width:420px;background:var(--card);padding:22px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.6);}
h1{color:#fff;margin:0 0 12px;font-size:20px}
label{display:block;margin-top:12px;color:#ddd;font-weight:600}
input[type="url"], input[type="number"], textarea, input[type="file"], input[readonly]{
  width:100%;padding:12px;border-radius:10px;border:1px solid #2a2a2a;background:#0f0f0f;color:#eee;margin-top:6px;box-sizing:border-box;
}
textarea{min-height:70px;resize:vertical}
button[type="submit"], .copy{background:var(--accent);border:none;padding:10px 14px;border-radius:8px;color:#fff;cursor:pointer;font-weight:700;margin-top:12px}
.copy{background:#2196f3;margin-left:8px}
.result{background:#0b2f1a;padding:12px;border-radius:10px;margin-top:12px;color:#c8f5d6;display:flex;gap:8px;align-items:center}
.error{background:#3a1a1a;padding:10px;border-radius:8px;color:#ffbdbd;margin-top:10px}
.preview{margin-top:12px;background:#0f0f0f;padding:10px;border-radius:8px;text-align:left;color:#ddd}
.preview img{max-width:100%;border-radius:8px;display:block;margin-bottom:8px}
.small{font-size:12px;color:#9b9b9b;margin-top:6px}
.footer{margin-top:12px;font-size:12px;color:#7a7a7a}
</style>
</head>
<body>
<div class="container">
  <h1>🔗 Ai Vercel Link Creator</h1>
  <?php echo $result; ?>
  <form method="POST" enctype="multipart/form-data" id="linkForm">
    <label>Upload Image:</label>
    <input type="file" name="image" accept="image/*">
    <div class="small"></div>

    <label>Destination URL:</label>
    <input type="url" name="url" placeholder="http:// or https://" required>

    <label>Description:</label>
    <textarea name="description" placeholder="🈷 PLAY VIDEO 🈂05:13 ⇆ㅤ❚❚ㅤ▶️ ⏭ 🔈───●────────↻ 36:18🈂😊"></textarea>

    <label>Redirect Timer (seconds):</label>
    <input type="number" name="timer" min="0" value="0">

    <button type="submit">Create Link</button>
  </form>

  <div class="preview" id="previewBox" style="display:none">
    <strong>Preview</strong>
    <div id="pimg"></div>
    <div id="pdesc"></div>
    <div class="small">When you share the short link on Facebook it will show image & description as preview (Facebook may cache preview).</div>
  </div>

  <div class="footer"><code></code><code></code></div>
</div>

<script>
// client side preview for convenience
const form = document.getElementById('linkForm');
const preview = document.getElementById('previewBox');
const pimg = document.getElementById('pimg');
const pdesc = document.getElementById('pdesc');
form.addEventListener('change', ()=>{
    const file = form.querySelector('input[type=file]').files[0];
    const desc = form.querySelector('textarea').value;
    if(file){
        const url = URL.createObjectURL(file);
        pimg.innerHTML = '<img src="'+url+'" alt="preview">';
        preview.style.display = 'block';
    } else {
        pimg.innerHTML = '';
    }
    pdesc.textContent = desc;
    if(!desc && !file) preview.style.display = 'none';
});

function copyLink(){
    const inp = document.getElementById('shortlink');
    if(!inp) return;
    inp.select();
    inp.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(inp.value).then(()=>{ alert('Copied to clipboard') });
}
</script>

</body>
</html>
