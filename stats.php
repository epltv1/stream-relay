<?php
header('Content-Type: application/json');
$db = new PDO('sqlite:db.sqlite');
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT pid,start_ts FROM streams WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { echo json_encode([]); exit; }

$log = glob("logs/*{$row['pid']}*.log")[0] ?? '';
$stats = ['fps'=>0,'bitrate'=>0,'drop'=>0,'duration'=>time()-$row['start_ts']];

if ($log && file_exists($log)) {
    $last = trim(`tail -n 20 '$log'`);
    if (preg_match('/fps=\s*([\d.]+)/', $last, $m)) $stats['fps'] = (float)$m[1];
    if (preg_match('/bitrate=\s*([\d.]+)kbits/', $last, $m)) $stats['bitrate'] = (float)$m[1];
    if (preg_match('/drop=\s*(\d+)/', $last, $m)) $stats['drop'] = (int)$m[1];
}
echo json_encode($stats);
