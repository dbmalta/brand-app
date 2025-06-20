<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
$linkId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$clientId || !$linkId) {
    die("Missing required parameters.");
}

// Ensure the link exists and belongs to the client
$stmt = $pdo->prepare("SELECT * FROM mktg_client_links WHERE id = ? AND client_id = ?");
$stmt->execute([$linkId, $clientId]);
$link = $stmt->fetch();

if (!$link) {
    die("Link not found.");
}

// Delete the record
$stmt = $pdo->prepare("DELETE FROM mktg_client_links WHERE id = ? AND client_id = ?");
$stmt->execute([$linkId, $clientId]);

// Redirect back
header("Location: client_links.php?client_id=$clientId");
exit;
