<?php
$db = new PDO('sqlite:db.sqlite');
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT pid FROM streams WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { http_response_code(404); echo "Not found"; exit; }

$pid = $row['pid'];
shell_exec("kill -9 $pid 2>/dev/null");
$db->prepare("DELETE FROM streams WHERE id=?")->execute([$id]);
echo "Stopped";
