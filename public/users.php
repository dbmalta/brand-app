<?php

session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Require permission "manage_config"
require_permission($pdo, 'manage_users');


$pageTitle = 'User Management';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM mktg_users WHERE id = ?")->execute([$id]);
    header("Location: users.php");
    exit;
}

// Fetch users and their permissions
$users = $pdo->query("
    SELECT u.id, u.username, GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') AS permissions
    FROM mktg_users u
    LEFT JOIN mktg_user_permissions up ON u.id = up.user_id
    LEFT JOIN mktg_permissions p ON up.permission_id = p.id
    GROUP BY u.id
")->fetchAll();

ob_start();
?>

<div class="mb-4">
    <a href="user_form.php" class="btn btn-success">➕ Add User</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width:30%;">Username</th>
            <th>Permissions</th>
            <th style="width:20%;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['permissions'] ?? '') ?></td>
            <td>
                <a href="user_form.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?> - BitKode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/dark-mode.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/includes/layout.php'; ?>
</body>
</html>
