<?php
$db = new PDO('sqlite:db.sqlite');
$streams = $db->query("SELECT * FROM streams ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ongoing Streams</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .timer {font-family: monospace; font-weight: bold;}
        .overlay {display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); color:#0f0; padding:2rem; z-index:9999; overflow:auto;}
    </style>
</head>
<body class="bg-dark text-light">
<div class="container py-4">
    <a href="index.php" class="btn btn-outline-light mb-3">Back to Home</a>
    <h1>Ongoing Streams</h1>
    <div id="list">
        <?php foreach($streams as $s): ?>
            <div class="card mb-3 bg-secondary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?=htmlspecialchars($s['title'])?></strong><br>
                        <small>Source: <?=strtoupper($s['source'])?></small><br>
                        <span class="timer" data-sec="<?=(time()-$s['start_ts'])?>">00:00:00</span>
                    </div>
                    <div>
                        <button class="btn btn-info btn-sm me-2" onclick="showStats(<?=$s['id']?>)">Stats</button>
                        <button class="btn btn-danger btn-sm" onclick="stopStream(<?=$s['id']?>,this)">Stop</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="overlay" id="statsOverlay">
    <button class="btn btn-danger float-end" onclick="this.parentNode.style.display='none'">Close</button>
    <pre id="statsPre"></pre>
</div>

<script>
function fmt(s){
    let d = Math.floor(s/(24*3600)); s %= 24*3600;
    let h = Math.floor(s/3600); s %= 3600;
    let m = Math.floor(s/60); s %= 60;
    return `${d?d+'d ':''}${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
}
setInterval(() => {
    document.querySelectorAll('.timer').forEach(el => {
        el.textContent = fmt(++el.dataset.sec);
    });
}, 1000);

async function showStats(id){
    const r = await fetch('stats.php?id='+id);
    const j = await r.json();
    document.getElementById('statsPre').textContent = 
        `Duration: ${fmt(j.duration)}\nFPS: ${j.fps}\nBitrate: ${j.bitrate} kbps\nDropped: ${j.drop}`;
    document.getElementById('statsOverlay').style.display = 'block';
}
async function stopStream(id, btn){
    if (!confirm('Stop this stream?')) return;
    await fetch('stop.php?id='+id);
    btn.closest('.card').remove();
}
</script>
</body>
</html>
