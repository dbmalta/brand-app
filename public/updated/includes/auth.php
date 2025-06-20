<?php
function userHasPermission(PDO $pdo, string $permission): bool {
    if (!isset($_SESSION['username'])) return false;

    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM mktg_users u
        JOIN mktg_user_permissions up ON u.id = up.user_id
        JOIN mktg_permissions p ON up.permission_id = p.id
        WHERE u.username = ? AND p.name = ?
    ");
    $stmt->execute([$_SESSION['username'], $permission]);
    return $stmt->fetchColumn() > 0;
}

function require_permission(PDO $pdo, string $permission): void {
    if (!userHasPermission($pdo, $permission)) {
        http_response_code(403);
        echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
        exit;
    }
}
