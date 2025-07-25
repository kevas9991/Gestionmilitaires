<?php
require_once 'auth.php';
require_once 'db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $pdo->prepare("DELETE FROM mission_militaires WHERE mission_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM missions WHERE id=?")->execute([$id]);
}
header('Location: missions.php');
exit();