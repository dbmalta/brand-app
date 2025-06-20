<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM mktg_client_campaigns WHERE campaignid = ?");
    $stmt->execute([$id]);
}

header("Location: client_campaigns.php");
exit;
