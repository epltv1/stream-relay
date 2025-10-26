<?php $db = new PDO('sqlite:db.sqlite'); $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stream Relay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="index.php" class="list-group-item list-group-item-action active">Home</a>
                <a href="ongoing.php" class="list-group-item list-group-item-action">Ongoing Streams</a>
            </div>
        </div>

        <!-- Main Form -->
        <div class="col-md-9">
            <h1 class="mb-4">Start New Relay</h1>
            <form id="startForm" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Source Type</label>
                    <select class="form-select" name="type" required onchange="toggleMpd()">
                        <option value="">-- select --</option>
                        <option value="m3u8">HLS (.m3u8)</option>
                        <option value="mp4">MP4 File</option>
                        <option value="mpd">DASH (.mpd)</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Source URL</label>
                    <input type="url" class="form-control" name="url" required placeholder="https://example.com/stream.m3u8">
                </div>

                <!-- MPD ClearKey -->
                <div class="col-md-6 d-none" id="mpdKeyId">
                    <label class="form-label">Key ID (hex)</label>
                    <input type="text" class="form-control" name="key_id" placeholder="e.g. 1a2b3c4d...">
                </div>
                <div class="col-md-6 d-none" id="mpdKey">
                    <label class="form-label">Key (hex)</label>
                    <input type="text" class="form-control" name="key_hex" placeholder="e.g. 00112233...">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Stream Title</label>
                    <input type="text" class="form-control" name="title" required placeholder="My Live Event">
                </div>

                <div class="col-md-6">
                    <label class="form-label">RTMP URL</label>
                    <input type="text" class="form-control" name="rtmp_url" required placeholder="rtmp://live.twitch.tv/app">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Stream Key</label>
                    <input type="text" class="form-control" name="stream_key" required placeholder="live_123456789_abc">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success btn-lg">Start Stream</button>
                </div>
            </form>
            <div id="msg" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
function toggleMpd() {
    const show = document.querySelector('[name=type]').value === 'mpd';
    document.getElementById('mpdKeyId').classList.toggle('d-none', !show);
    document.getElementById('mpdKey').classList.toggle('d-none', !show);
}
document.getElementById('startForm').onsubmit = async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const r = await fetch('start.php', {method:'POST', body:fd});
    const txt = await r.text();
    document.getElementById('msg').innerHTML = `<div class="alert ${r.ok?'alert-success':'alert-danger'}">${txt}</div>`;
    if(r.ok) e.target.reset();
};
</script>
</body>
</html>
