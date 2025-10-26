<?php
header('Content-Type: text/plain');
$db = new PDO('sqlite:db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$type       = $_POST['type'] ?? '';
$url        = $_POST['url'] ?? '';
$title      = $_POST['title'] ?? '';
$rtmp_base  = $_POST['rtmp_url'] ?? '';
$stream_key = $_POST['stream_key'] ?? '';
$key_id     = $_POST['key_id'] ?? '';
$key_hex    = $_POST['key_hex'] ?? '';

if (!$type || !$url || !$title || !$rtmp_base || !$stream_key) {
    http_response_code(400);
    echo "Missing fields";
    exit;
}

$log = "logs/" . uniqid() . ".log";
$cmd = ['ffmpeg', '-y', '-loglevel', 'warning'];

if ($type === 'mpd' && $key_id && $key_hex) {
    $cmd[] = '-decryption_key';
    $cmd[] = $key_id . ':' . $key_hex;
}

if ($type === 'mp4') $cmd[] = '-re';
$cmd[] = '-i'; $cmd[] = escapeshellarg($url);
$cmd[] = '-c:v'; $cmd[] = 'copy';
$cmd[] = '-c:a'; $cmd[] = 'aac';
$cmd[] = '-f';   $cmd[] = 'flv';
$cmd[] = escapeshellarg($rtmp_base . '/' . $stream_key);

$fullCmd = implode(' ', $cmd) . ' > ' . $log . ' 2>&1 & echo $!';
$pid = trim(shell_exec($fullCmd));

if (!is_numeric($pid)) {
    http_response_code(500);
    echo "FFmpeg failed";
    exit;
}

$stmt = $db->prepare("INSERT INTO streams (pid,title,source,source_url,key_id,key_hex,rtmp_url,start_ts)
                      VALUES (?,?,?,?,?,?,?,?)");
$stmt->execute([$pid, $title, $type, $url, $key_id, $key_hex, $rtmp_base.'/'.$stream_key, time()]);

echo "Stream started! PID=$pid";
