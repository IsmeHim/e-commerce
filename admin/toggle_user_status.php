<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? '';

if ($id && in_array($action, ['lock', 'unlock'])) {
    $status = $action === 'lock' ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

header("Location: users.php");
exit;
