<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM mktg_client_campaigns WHERE clientid = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("DELETE FROM mktg_client_links WHERE client_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM mktg_client_files WHERE client_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM mktg_clients WHERE id = ?")->execute([$id]);
        $dir = __DIR__ . "/uploads/clients/$id";
        if (is_dir($dir)) {
            foreach (glob("$dir/*") as $f) {
                if (is_file($f)) unlink($f);
            }
            @rmdir($dir);
        }
    }
}

header("Location: clients.php");
exit;
